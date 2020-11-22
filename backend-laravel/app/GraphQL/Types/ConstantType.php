<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Contract;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class ConstantType
 * @package App\GraphQL\Types
 */
class ConstantType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Constant',
        'description' => 'A type',
        'model' => Contract::class
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
            'group' => [
                'type' => Type::string(),
            ],
            'type' => [
                'type' => Type::string(),
            ],
            'value' => [
                'type' => Type::string(),
            ],
        ];
    }
}
