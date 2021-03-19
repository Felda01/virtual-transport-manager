<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\RoadTrip;
use App\Utilities\GameTimeUtility;
use App\Utilities\StatusUtility;
use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoadTripType extends GraphQLType
{
    protected $attributes = [
        'name' => 'RoadTrip',
        'description' => 'A type',
        'model' => RoadTrip::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'km' => [
                'type' => Type::int(),
            ],
            'time' => [
                'type' => Type::int(),
            ],
            'fees' => [
                'type' => Type::float(),
            ],
            'damage' => [
                'type' => Type::float(),
            ],
            'routes' => [
                'type' => Type::string(),
            ],
            'status' => [
                'type' => Type::int(),
            ],
            'order' => [
                'type' => GraphQL::type('Order'),
            ],
            'arrival' => [
                'type' => Type::string(),
                'selectable' => false,
                'resolve' => function($root, $args) {
                    /** @var RoadTrip $root  */
                    if ($root->time && $root->updated_at && $root->status === StatusUtility::ON_ROAD) {
                        $customTime = $root->time + random_int(0, 30) - 15;
                        $time = GameTimeUtility::gameTime(GameTimeUtility::addTimeToRealTime($customTime, Carbon::parse($root->updated_at, 'Europe/Bratislava')->toDateTimeString()));
                        return Carbon::parse($time, 'Europe/Bratislava')->format('d.m.Y H:i');
                    }
                    return '-';
                }
            ]
        ];
    }
}
