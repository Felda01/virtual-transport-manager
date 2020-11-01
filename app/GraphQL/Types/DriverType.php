<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Driver;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DriverType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Driver',
        'description' => 'A type',
        'model' => Driver::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'first_name' => [
                'type' => Type::string(),
            ],
            'last_name' => [
                'type' => Type::string(),
            ],
            'gender' => [
                'type' => Type::boolean(),
            ],
            'status' => [
                'type' => Type::string(),
            ],
            'image' => [
                'type' => Type::string(),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'truck' => [
                'type' => GraphQL::type('Truck'),
            ],
            'location' => [
                'type' => GraphQL::type('Location'),
            ],
            'garage' => [
                'type' => GraphQL::type('Garage'),
            ],
            'salary' => [
                'type' => Type::int(),
            ],
            'satisfaction' => [
                'type' => Type::int(),
            ],
            'preferred_road_trips' => [
                'type' => Type::string(),
            ],
            'days_on_road' => [
                'type' => Type::int(),
            ],
            'expires_at' => [
                'type' => Type::int(),
            ],
        ];
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
