<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\TrailerModel;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class TrailerModelType
 * @package App\GraphQL\Types
 */
class TrailerModelType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TrailerModel',
        'description' => 'A type',
        'model' => TrailerModel::class
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
            'type' => [
                'type' => Type::string(),
            ],
            'load' => [
                'type' => Type::string(),
            ],
            'adr' => [
                'type' => Type::string(),
            ],
            'km' => [
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
