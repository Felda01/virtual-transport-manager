<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Market;
use App\Models\Order;
use App\Models\RoadTrip;
use App\Models\User;
use App\Rules\AvailableMarketRule;
use App\Utilities\StatusUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateOrderMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createOrder',
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

    public function rules(array $args = []): array
    {
        return [
            'market' => [
                'required',
                'exists:markets,id,deleted_at,NULL',
                new AvailableMarketRule()
            ],
        ];
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [
            'market' => [
                'name' => 'market',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $result = DB::transaction(function() use ($args) {
            /** @var User $user */
            $user = User::current();

            /** @var RoadTrip $roadTrip */
            $roadTrip = RoadTrip::create([
                'km' => 0,
                'time' => 0,
                'routes' => json_encode([]),
                'status' => StatusUtility::WAITING_FOR_DRIVERS
            ]);

            /** @var Order $order */
            $order = Order::create([
                'market_id' => $args['market'],
                'company_id' => $user->company_id,
                'road_trip_id' => $roadTrip->id
            ]);

            /** @var Market $market */
            $market = Market::find($args['market']);

            $cargo = $market->cargo;

            $amount = $market->amount;

            $market->decrement('amount', $cargo->weight);
            $marketSaved = $market->save();

            if (!$marketSaved || !$order || $market->amount !== ($amount - $cargo->weight)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'order' => $order
            ];
        });

        return $result['order'];
    }
}
