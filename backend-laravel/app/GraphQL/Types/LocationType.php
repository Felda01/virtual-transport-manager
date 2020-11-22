<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Location;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class LocationType
 * @package App\GraphQL\Types
 */
class LocationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Location',
        'description' => 'A type',
        'model' => Location::class
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
            'is_city' => [
                'type' => Type::boolean(),
            ],
            'lat' => [
                'type' => Type::float(),
            ],
            'lng' => [
                'type' => Type::float(),
            ],
            'country' => [
                'type' => GraphQL::type('Country'),
            ]
        ];
    }
}
