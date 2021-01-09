<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Truck;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TruckType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Truck',
        'description' => 'A type',
        'model' => Truck::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'truckModel' => [
                'type' => GraphQL::type('TruckModel'),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'trailer' => [
                'type' => GraphQL::type('Trailer'),
            ],
            'driver' => [
                'type' => GraphQL::type('Driver'),
            ],
            'garage' => [
                'type' => GraphQL::type('Garage'),
            ],
            'km' => [
                'type' => Type::int(),
            ],
        ];
    }

    /**
     * @param $root
     * @param $args
     * @return string
     */
    protected function resolveEndField($root, $args)
    {
        return $root->next_service->timestamp;
    }
}
