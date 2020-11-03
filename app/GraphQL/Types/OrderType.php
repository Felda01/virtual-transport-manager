<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Order;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Order',
        'description' => 'A type',
        'model' => Order::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'market' => [
                'type' => GraphQL::type('Market')
            ],
            'driver1' => [
                'type' => GraphQL::type('Driver')
            ],
            'driver2' => [
                'type' => GraphQL::type('Driver')
            ],
            'truck' => [
                'type' => GraphQL::type('Truck')
            ],
            'trailer' => [
                'type' => GraphQL::type('Trailer')
            ],
            'roadTrip' => [
                'type' => GraphQL::type('RoadTrip')
            ]
        ];
    }
}
