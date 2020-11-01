<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Country;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class CountryType
 * @package App\GraphQL\Types
 */
class CountryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Country',
        'description' => 'A type',
        'model' => Country::class
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
            'short_name' => [
                'type' => Type::string(),
            ]
        ];
    }
}
