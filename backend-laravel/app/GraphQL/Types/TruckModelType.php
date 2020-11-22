<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\TruckModel;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class TruckModelType
 * @package App\GraphQL\Types
 */
class TruckModelType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TruckModel',
        'description' => 'A type',
        'model' => TruckModel::class
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
            'brand' => [
                'type' => Type::string(),
            ],
            'engine_power' => [
                'type' => Type::int(),
            ],
            'chassis' => [
                'type' => Type::string(),
            ],
            'load' => [
                'type' => Type::int(),
            ],
            'price' => [
                'type' => Type::float(),
            ],
            'emission_class' => [
                'type' => Type::int(),
            ],
            'km' => [
                'type' => Type::int(),
            ],
            'insurance' => [
                'type' => Type::float(),
            ],
            'tax' => [
                'type' => Type::float(),
            ],
            'image' => [
                'type' => Type::string(),
            ],
        ];
    }
}
