<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A type',
        'model' => User::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'first_name' => [
                'type' => Type::string(),
            ],
            'last_name' => [
                'type' => Type::string(),
            ],
            'image' => [
                'type' => Type::string(),
            ],
            'email' => [
                'type' => Type::string(),
                'privacy' => function(array $args): bool {
                    return Auth::guard('api')->check() && array_key_exists('id', $args) ? Auth::guard('api')->id() === $args['id'] : false;
                }
            ],
            'salary' => [
                'type' => Type::string(),
                'privacy' => function(array $args): bool {
                    return Auth::guard('api')->check() && array_key_exists('id', $args) ? (Auth::guard('api')->id() === $args['id'] || Auth::guard('api')->user()->hasPermissionTo(\App\Models\Permission::MANAGE_SALARY, \App\Models\Permission::GUARD)) : false;
                }
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'roles' => [
                'type' => Type::listOf(GraphQL::type('Role')),
            ],
            'permissions' => [
                'type' => Type::listOf(GraphQL::type('Permission')),
                'resolve' => function($root, $args) {
                    return $root->getAllPermissions();
                }
            ],
        ];
    }
}
