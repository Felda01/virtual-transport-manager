<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Permission;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PermissionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Permission',
        'description' => 'A type',
        'model' => Permission::class
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
