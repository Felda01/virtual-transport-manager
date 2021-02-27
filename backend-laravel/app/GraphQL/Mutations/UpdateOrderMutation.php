<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\RefreshQuery;
use App\Jobs\FinishOrder;
use App\Models\Company;
use App\Models\Order;
use App\Models\RoadTrip;
use App\Models\Truck;
use App\Rules\AvailableOrderRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\TruckForOrderRule;
use App\Utilities\BroadcastUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\PathUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\StatusUtility;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateOrderMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateOrder',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Order');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_JOBS, \App\Models\Permission::GUARD);
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'exists:orders,id',
                new AvailableOrderRule()
            ],
            'truck' => [
                'required',
                'exists:trucks,id,deleted_at,NULL',
                new ModelFromCompanyRule('Truck'),
                new TruckForOrderRule($args['id'])
            ],
            'path' => [
                'required',
                'integer',
                Rule::in([1, 2, 3])
            ]
        ];
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::string()),
            ],
            'truck' => [
                'name' => 'truck',
                'type' => Type::nonNull(Type::string()),
            ],
            'path' => [
                'name' => 'path',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Order $order */
        $order = Order::find($args['id']);
        $data = PathUtility::getPaths($order->market->location_from, $order->market->location_to);

        $path = $data['result'][$args['path'] - 1];

        $result = DB::transaction(function () use ($args, $path, $order) {

            /** @var RoadTrip $roadTrip */
            $roadTrip = RoadTrip::find($order->road_trip_id);

            /** @var Truck $truck */
            $truck = Truck::find($args['truck']);

            $routes = PathUtility::getRoutesFromPath($path['path']);

            $roadTrip->km = $path['cost'];
            $roadTrip->time = $routes->sum('time');
            $roadTrip->fees = $routes->sum('fee');
            $roadTrip->status = StatusUtility::ON_ROAD;
            $roadTrip->routes = json_encode($path['path']);
            $roadTripSaved = $roadTrip->save();

            $order->truck()->associate($truck);
            $order->trailer()->associate($truck->trailer);
            $order->drivers()->saveMany($truck->drivers->all());
            $orderSaved = $order->save();

            $driversUpdated = $order->drivers()->update([
                'status' => StatusUtility::ON_ROAD,
            ]);

            if (!$roadTripSaved || !$orderSaved || !$driversUpdated) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'order' => $order,
                'roadTrip' => $roadTrip
            ];
        });

        $timeInMinutes = PathUtility::calculateRoadTripTime($result['roadTrip']->time);
        QueueJobUtility::dispatch(new FinishOrder($result['order']), Carbon::parse(GameTimeUtility::gameTimeToRealTime($timeInMinutes), 'Europe/Bratislava'));
        $company = Company::currentCompany();
        BroadcastUtility::broadcast(new RefreshQuery($company, 'Order', $result['order']->id));

        return $result['order'];
    }
}
