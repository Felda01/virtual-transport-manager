<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Notification;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class NotificationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Notification',
        'description' => 'A type',
        'model' => Notification::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'user' => [
                'type' => GraphQL::type('User'),
            ],
            'message' => [
                'type' => Type::string(),
            ],
            'seen' => [
                'type' => Type::boolean(),
            ]
        ];
    }
}
