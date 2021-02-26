<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Message;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class MessageType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Message',
        'description' => 'A type',
        'model' => Message::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'userFrom' => [
                'type' => GraphQL::type('User'),
            ],
            'userTo' => [
                'type' => GraphQL::type('User'),
            ],
            'message' => [
                'type' => Type::string(),
            ],
            'created_at' => [
                'type' => Type::string(),
            ]
        ];
    }
}
