<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Customer;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class CustomerType
 * @package App\GraphQL\Types
 */
class CustomerType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Customer',
        'description' => 'A type',
        'model' => Customer::class
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
            'is_company' => [
                'type' => Type::boolean(),
            ],
            'contracts' => [
                'type' => Type::listOf(GraphQL::type('Contract')),
            ],
            'cargos' => [
                'type' => Type::listOf(GraphQL::type('Cargo')),
            ],
        ];
    }
}
