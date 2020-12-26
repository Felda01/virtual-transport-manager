<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Garage;
use App\Models\GarageModel;
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
        'model' => GarageModel::class
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
                'type' => Type::string(),
            ],
            'trailer_count' => [
                'type' => Type::string(),
            ],
            'insurance' => [
                'type' => Type::string(),
            ],
            'tax' => [
                'type' => Type::string(),
            ],
            'image' => [
                'type' => Type::string(),
            ],
            'price' => [
                'type' => Type::string(),
            ],
        ];
    }
}
