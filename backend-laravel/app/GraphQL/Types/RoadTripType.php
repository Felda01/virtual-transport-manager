<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\RoadTrip;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoadTripType extends GraphQLType
{
    protected $attributes = [
        'name' => 'RoadTrip',
        'description' => 'A type',
        'model' => RoadTrip::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'km' => [
                'type' => Type::int(),
            ],
            'time' => [
                'type' => Type::int(),
            ],
            'routes' => [
                'type' => Type::string(),
            ],
            'status' => [
                'type' => Type::int(),
            ],
            'order' => [
                'type' => GraphQL::type('Order'),
            ],
        ];
    }
}
