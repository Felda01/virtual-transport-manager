<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Garage;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class GarageModelType
 * @package App\GraphQL\Types
 */
class GarageModelType extends GraphQLType
{
    protected $attributes = [
        'name' => 'GarageModel',
        'description' => 'A type',
        'model' => Garage::class
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
            'truck_count' => [
                'type' => Type::int(),
            ],
            'trailer_count' => [
                'type' => Type::int(),
            ],
            'location' => [
                'type' => GraphQL::type('Location'),
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
