<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Utilities\FilterUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class UsersQuery extends Query
{
    protected $attributes = [
        'name' => 'users',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('User');
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
        return 'You are not authorized to perform this action';
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
                'type' => Type::listOf(GraphQL::type('FilterInput'))
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var SelectFields $fields */
        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $query = User::query();

        $query = FilterUtility::handleFilter($query, new User, User::$searchable, $args['filter']);

        if ($args['limit'] === -1) {
            $args['limit'] = User::count();
        }

        return $query->with($with)
            ->select($select)
            ->whereHas('roles', function($q){
                $q->where('name', '!=', 'admin');
            })
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
