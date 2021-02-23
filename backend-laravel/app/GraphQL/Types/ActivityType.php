<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Utilities\GameTimeUtility;
use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Spatie\Activitylog\Models\Activity;

class ActivityType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Activity',
        'description' => 'A type',
        'model' => Activity::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'subject' => [
                'type' => GraphQL::type('SubjectUnion'),
            ],
            'description' => [
                'type' => Type::string()
            ],
            'properties' => [
                'type' => Type::string(),
                'resolve' => function($root, $args) {
                    /** @var Activity $root */
                    return $root->properties->toJson();
                }
            ],
            'created_at' => [
                'type' => Type::string(),
                'resolve' => function($root, $args) {
                    /** @var Activity $root  */
                    $time = GameTimeUtility::gameTime($root->created_at);
                    return Carbon::parse($time)->format('d.m.Y H:i');
                }
            ],
            'causer' => [
                'type' => GraphQL::type('User')
            ],
        ];
    }
}
