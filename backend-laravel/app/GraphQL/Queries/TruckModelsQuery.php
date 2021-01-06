<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\TruckModel;
use App\Utilities\FilterUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class TruckModelsQuery extends Query
{
    protected $attributes = [
        'name' => 'truckModels',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('TruckModel');
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

        $query = TruckModel::query();

        if ($args['filter'] && count($args['filter']) > 0) {
            $query = FilterUtility::handleFilter($query, new TruckModel, TruckModel::$searchable, $args['filter']);
        }

        if ($args['limit'] === -1) {
            $args['limit'] = TruckModel::count();
        }

        if ($args['sort']) {
            $query = FilterUtility::handleSort($query, 'truck_models', $args['sort']);
        } else {
            $query = $query->orderBy('price');
        }

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
