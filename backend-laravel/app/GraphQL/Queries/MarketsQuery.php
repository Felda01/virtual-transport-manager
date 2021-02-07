<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Market;
use App\Utilities\FilterUtility;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class MarketsQuery extends Query
{
    protected $attributes = [
        'name' => 'market',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Market');
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

        $query = Market::query();

        if ($args['filter'] && count($args['filter']) > 0) {
            $query = FilterUtility::handleFilter($query, new Market, Market::$searchable, $args['filter']);
        }

        $query = $query->where('amount', '>', 0)->where('expires_at', '>', Carbon::now('Europe/Bratislava'));

        if ($args['sort']) {
            $query = FilterUtility::handleSort($query, (new Market)->getTable(), $args['sort']);
        } else {
            $query = $query->orderBy('created_at');
        }

        return $query->with($with)
            ->select($select)
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
