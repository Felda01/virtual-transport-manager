<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Models\Truck;
use App\Models\User;
use App\Rules\ModelFromCompanyRule;
use App\Utilities\FilterUtility;
use App\Utilities\StatusUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class TrucksForOrderQuery extends Query
{
    protected $attributes = [
        'name' => 'trucksForOrder',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Truck');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->guard()->check();
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [
            'order' => [
                'type' => Type::string(),
            ],
            'limit' => [
                'type' => Type::int(),
                'defaultValue' => 50,
            ],
            'page' => [
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var SelectFields $fields */
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $order = null;

        if (array_key_exists('order', $args)) {
            /** @var Order $order */
            $order = Order::with(['roadTrip', 'market.cargo.trailerModels'])
                ->where('id', $args['order'])
                ->first();
        }

        if ($order && $order->roadTrip->status !== StatusUtility::WAITING_FOR_DRIVERS) {
            return Truck::where('id', null)
                ->paginate(10, ['*'], 'page', $args['page']);
        }

        $query = Truck::query();

        $query = FilterUtility::filterCompany($query, User::current());

        if ($order) {
            $cargo = $order->market->cargo;
            $query = Truck::trucksForOrder($query, $cargo);
        } else {
            $query = $query->whereHas('trailer')->whereHas('drivers', function (Builder $query) {
                $query->where('sleep', 0)->where('status', StatusUtility::READY);
            })->whereHas('drivers', function (Builder $query) {
                $query->where('sleep', 1)->orWhere('status', '!=', StatusUtility::READY);
            }, '=', 0);
        }

        if ($args['limit'] === -1) {
            $args['limit'] = Truck::count();
        }

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
