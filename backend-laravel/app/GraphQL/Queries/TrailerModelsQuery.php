<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\TrailerModel;
use App\Utilities\FilterUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class TrailerModelsQuery extends Query
{
    protected $attributes = [
        'name' => 'trailerModels',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('TrailerModel');
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

        $query = TrailerModel::query();

        if ($args['filter'] && count($args['filter']) > 0) {
            $query = FilterUtility::handleFilter($query, new TrailerModel, TrailerModel::$searchable, $args['filter']);
        }

        if ($args['limit'] === -1) {
            $args['limit'] = TrailerModel::count();
        }

        if ($args['sort']) {
            $query = FilterUtility::handleSort($query, (new TrailerModel)->getTable(), $args['sort']);
        } else {
            $query = $query->orderBy('price');
        }

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
