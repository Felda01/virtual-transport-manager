<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\LanguageLine;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class LanguageLineType extends GraphQLType
{
    protected $attributes = [
        'name' => 'LanguageLine',
        'description' => 'A type',
        'model' => LanguageLine::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'group' => [
                'type' => Type::string(),
            ],
            'key' => [
                'type' => Type::string(),
            ],
            'text' => [
                'type' => Type::string(),
            ],
        ];
    }
}
