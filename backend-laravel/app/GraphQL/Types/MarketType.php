<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Market;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class MarketType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Market',
        'description' => 'A type',
        'model' => Market::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'locationFrom' => [
                'type' => GraphQL::type('Location'),
            ],
            'locationTo' => [
                'type' => GraphQL::type('Location'),
            ],
            'cargo' => [
                'type' => GraphQL::type('Cargo'),
            ],
            'customerFrom' => [
                'type' => GraphQL::type('Customer'),
            ],
            'customerTo' => [
                'type' => GraphQL::type('Customer'),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'price' => [
                'type' => Type::float(),
            ],
            'frequency' => [
                'type' => Type::string(),
            ],
            'amount' => [
                'type' => Type::int(),
            ],
            'count_of_repetitions' => [
                'type' => Type::int(),
            ],
            'start' => [
                'type' => Type::int(),
            ],
            'end' => [
                'type' => Type::int(),
            ],
            'expires_at' => [
                'type' => Type::int(),
            ],
            'orders' => [
                'type' => Type::listOf(GraphQL::type('Order')),
            ],
        ];
    }

    /**
     * @param $root
     * @param $args
     * @return string
     */
    protected function resolveStartField($root, $args)
    {
        return $root->start->timestamp;
    }

    /**
     * @param $root
     * @param $args
     * @return string
     */
    protected function resolveEndField($root, $args)
    {
        return $root->end->timestamp;
    }

    /**
     * @param $root
     * @param $args
     * @return string
     */
    protected function resolveExpiresAtField($root, $args)
    {
        return $root->expires_at->timestamp;
    }
}
