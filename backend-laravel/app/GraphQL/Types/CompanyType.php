<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Company;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class CompanyType
 * @package App\GraphQL\Types
 */
class CompanyType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Company',
        'description' => 'A type',
        'model' => Company::class
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
            'money' => [
                'type' => Type::string(),
            ],
            'trucks' => [
                'type' => Type::listOf(GraphQL::type('Truck')),
            ],
            'garages' => [
                'type' => Type::listOf(GraphQL::type('Garage')),
            ],
            'drivers' => [
                'type' => Type::listOf(GraphQL::type('Driver')),
            ],
            'trailers' => [
                'type' => Type::listOf(GraphQL::type('Trailer')),
            ],
            'contracts' => [
                'type' => Type::listOf(GraphQL::type('Contract')),
            ],
            'orders' => [
                'type' => Type::listOf(GraphQL::type('Order')),
            ],
            'markets' => [
                'type' => Type::listOf(GraphQL::type('Market')),
            ],
            'transactions' => [
                'type' => Type::listOf(GraphQL::type('Transaction')),
            ],
            'users' => [
                'type' => Type::listOf(GraphQL::type('User')),
            ],
            'bankLoans' => [
                'type' => Type::listOf(GraphQL::type('bankLoan')),
            ],
        ];
    }
}
