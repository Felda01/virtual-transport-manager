<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Market;
use App\Utilities\GameTimeUtility;
use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class MarketType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Market',
        'description' => 'A type',
        'model' => Market::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'locationFrom' => [
                'type' => GraphQL::type('Location'),
            ],
            'locationTo' => [
                'type' => GraphQL::type('Location'),
            ],
            'cargo' => [
                'type' => GraphQL::type('Cargo'),
            ],
            'customerFrom' => [
                'type' => GraphQL::type('Customer'),
            ],
            'customerTo' => [
                'type' => GraphQL::type('Customer'),
            ],
            'company' => [
                'type' => GraphQL::type('Company'),
            ],
            'price' => [
                'type' => Type::float(),
            ],
            'frequency' => [
                'type' => Type::string(),
            ],
            'amount' => [
                'type' => Type::int(),
            ],
            'count_of_repetitions' => [
                'type' => Type::int(),
            ],
            'expires_at' => [
                'type' => Type::string(),
                'resolve' => function($root, $args) {
                    /** @var Market $root  */
                    if ($root->expires_at) {
                        $time = GameTimeUtility::gameTime($root->expires_at);
                        return Carbon::parse($time, 'Europe/Bratislava')->format('d.m.Y H:i');
                    }
                    return '';
                }
            ],
        ];
    }

    /**
     * @param $root
     * @param $args
     * @return string
     */
    protected function resolveStartField($root, $args)
    {
        return $root->start->timestamp;
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
