<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Route;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class RouteType
 * @package App\GraphQL\Types
 */
class RouteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Route',
        'description' => 'A type',
        'model' => Route::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'location1' => [
                'type' => GraphQL::type('Location'),
            ],
            'location2' => [
                'type' => GraphQL::type('Location'),
            ],
            'time' => [
                'type' => Type::int(),
            ],
            'distance' => [
                'type' => Type::int(),
            ],
            'fee' => [
                'type' => Type::float(),
            ],
            'type' => [
                'type' => Type::string(),
            ],
        ];
    }
}
