<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Trailer;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TrailerType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Trailer',
        'description' => 'A type',
        'model' => Trailer::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'status' => [
                'type' => Type::int(),
            ],
            'trailerModel' => [
                'type' => GraphQL::type('TrailerModel')
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'garage' => [
                'type' => GraphQL::type('Garage'),
            ],
            'truck' => [
                'type' => GraphQL::type('Truck'),
            ],
            'km' => [
                'type' => Type::int(),
            ],
        ];
    }

    /**
     * @param $root
     * @param $args
     * @return string
     */
    protected function resolveEndField($root, $args)
    {
        return $root->next_service->timestamp;
    }
}
