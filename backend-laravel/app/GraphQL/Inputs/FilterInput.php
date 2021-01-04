<?php

declare(strict_types=1);

namespace App\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class FilterInput extends InputType
{
    protected $attributes = [
        'name' => 'FilterInput',
        'description' => 'An example input',
    ];

    public function fields(): array
    {
        return [
            'column' => [
                'type' => Type::string(),
            ],
            'value' => [
                'type' => Type::string(),
            ],
            '__typename' => [
                'type' => Type::string(),
            ],
        ];
    }
}
