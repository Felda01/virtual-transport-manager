<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Route;
use App\Rules\UniqueLocationsRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class UpdateRouteMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateRoute',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Route');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_STATIC, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'exists:routes,id,deleted_at,NULL'
            ],
            'time' => [
                'required',
                'int'
            ],
            'distance' => [
                'required',
                'int'
            ],
            'fee' => [
                'required',
                'numeric'
            ]
        ];
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::string())
            ],
            'time' => [
                'name' => 'time',
                'type' => Type::nonNull(Type::string())
            ],
            'distance' => [
                'name' => 'distance',
                'type' => Type::nonNull(Type::string())
            ],
            'fee' => [
                'name' => 'fee',
                'type' => Type::nonNull(Type::string())
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Route $route */
        $route = Route::find($args['id']);

        $route->distance = $args['distance'];
        $route->time = $args['time'];
        $route->fee = $args['fee'];

        $route->save();

        return $route;
    }
}
