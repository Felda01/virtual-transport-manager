<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Models\User;
use App\Utilities\FilterUtility;
use App\Utilities\StatusUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class OrdersQuery extends Query
{
    protected $attributes = [
        'name' => 'orders',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Order');
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
            'limit' => [
                'type' => Type::int(),
                'defaultValue' => 50,
            ],
            'page' => [
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
            'filter' => [
                'type' => Type::listOf(GraphQL::type('FilterInput')),
                'defaultValue' => [],
            ],
            'sort' => [
                'type' => Type::string(),
                'defaultValue' => ''
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var SelectFields $fields */
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $query = Order::query();

        if ($args['filter'] && count($args['filter']) > 0) {
            $query = FilterUtility::handleFilter($query, new Order, Order::$searchable, $args['filter']);
        }

        $query = FilterUtility::filterCompany($query, User::current());

        $query = $query = $query->whereHas('roadTrip', function (Builder $query) {
            $query->where('status', '!=', StatusUtility::FINISHED);
        });

        if ($args['limit'] === -1) {
            $args['limit'] = Order::count();
        }

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
