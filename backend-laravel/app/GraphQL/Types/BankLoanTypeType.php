<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\BankLoanType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BankLoanTypeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BankLoanType',
        'description' => 'A type',
        'model' => BankLoanType::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'value' => [
                'type' => Type::string(),
            ],
            'payment' => [
                'type' => Type::string(),
            ],
            'period' => [
                'type' => Type::string(),
            ],
        ];
    }
}
