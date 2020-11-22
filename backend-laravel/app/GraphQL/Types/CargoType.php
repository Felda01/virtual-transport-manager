<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Cargo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class CargoType
 * @package App\GraphQL\Types
 */
class CargoType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Cargo',
        'description' => 'A type',
        'model' => Cargo::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'adr' => [
                'type' => Type::int(),
            ],
            'weight' => [
                'type' => Type::int(),
            ],
            'engine_power' => [
                'type' => Type::int(),
            ],
            'chassis' => [
                'type' => Type::string(),
            ],
            'min_price' => [
                'type' => Type::float(),
            ],
            'max_price' => [
                'type' => Type::float(),
            ],
            'own_trailer' => [
                'type' => Type::boolean(),
            ],
            'image' => [
                'type' => Type::string(),
            ],
            'customers' => [
                'type' => Type::listOf(GraphQL::type('Customer')),
            ],
            'trailers' => [
                'type' => Type::listOf(GraphQL::type('Trailer')),
            ],
        ];
    }
}
