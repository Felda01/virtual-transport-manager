<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Role;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class RoleType
 * @package App\GraphQL\Types
 */
class RoleType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Role',
        'description' => 'A type',
        'model' => Role::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'name' => [
                'type' => Type::string()
            ],
        ];
    }
}
