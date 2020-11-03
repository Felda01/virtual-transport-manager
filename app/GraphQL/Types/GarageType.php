<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Garage;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class GarageType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Garage',
        'description' => 'A type',
        'model' => Garage::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'location' => [
                'type' => GraphQL::type('Location'),
            ],
            'garageModel' => [
                'type' => GraphQL::type('GarageModel'),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'trucks' => [
                'type' => Type::listOf(GraphQL::type('Truck')),
            ],
            'drivers' => [
                'type' => Type::listOf(GraphQL::type('Driver')),
            ],
            'trailers' => [
                'type' => Type::listOf(GraphQL::type('Trailer')),
            ],
        ];
    }
}
