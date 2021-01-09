<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Transaction;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TransactionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Transaction',
        'description' => 'A type',
        'model' => Transaction::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'value' => [
                'type' => Type::float(),
            ],
            'activity' => [
                'type' => Type::string(),
            ],
            'productable' => [
                'type' => GraphQL::type('Productable'),
            ],
            'user' => [
                'type' => GraphQL::type('User'),
            ],
            'created_at' => [
                'type' => Type::string(),
                'resolve' => function($root, $args) {
                    /** @var Transaction $root  */
                    return $root->created_at->format('d.m.Y H:i');
                }
            ]
        ];
    }
}
