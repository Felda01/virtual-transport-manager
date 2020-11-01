<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\BankLoan;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BankLoanType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BankLoan',
        'description' => 'A type',
        'model' => BankLoan::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'bankLoanType' => [
                'type' => GraphQL::type('BankLoanType'),
            ],
            'done' => [
                'type' => Type::boolean()
            ],
        ];
    }
}
