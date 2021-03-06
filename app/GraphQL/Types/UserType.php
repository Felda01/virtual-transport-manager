<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
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
            'email' => [
                'type' => Type::string(),
            ],
            'image' => [
                'type' => Type::string(),
            ],
            'salary' => [
                'type' => Type::float(),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'messages' => [
                'type' => Type::listOf(GraphQL::type('Message')),
            ],
            'notifications' => [
                'type' => Type::listOf(GraphQL::type('Notification')),
            ],
        ];
    }
}
