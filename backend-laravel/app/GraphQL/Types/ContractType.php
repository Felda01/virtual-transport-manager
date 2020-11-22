<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Contract;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ContractType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Contract',
        'description' => 'A type',
        'model' => Contract::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'market' => [
                'type' => GraphQL::type('Market'),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'customer' => [
                'type' => GraphQL::type('Customer'),
            ],
            'expires_at' => [
                'type' => Type::int(),
            ],
        ];
    }

    /**
     * @param $root
     * @param $args
     * @return string
     */
    protected function resolveExpiresAtField($root, $args)
    {
        return $root->expires_at->timestamp;
    }
}
