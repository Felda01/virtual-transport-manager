<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\RoadEvent;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoadEventType extends GraphQLType
{
    protected $attributes = [
        'name' => 'RoadEvent',
        'description' => 'A type',
        'model' => RoadEvent::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'type' => [
                'type' => Type::string(),
            ],
            'delay' => [
                'type' => Type::int(),
            ],
            'end' => [
                'type' => Type::int(),
            ],
            'locationFrom' => [
                'type' => GraphQL::type('Location'),
            ],
            'locationTo' => [
                'type' => GraphQL::type('Location'),
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
        return $root->end->timestamp;
    }
}
