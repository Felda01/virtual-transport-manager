<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Location;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class LocationType
 * @package App\GraphQL\Types
 */
class LocationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Location',
        'description' => 'A type',
        'model' => Location::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'name' => [
                'type' => Type::string(),
                'resolve' => function($root, $args) {
                    $user = Auth::guard('api')->user();
                    if ($user->hasRole('admin')) {
                        $locales = config('translatable.available_locales');

                        $translations = [];

                        foreach ($locales as $locale) {
                            $translations[] = $root->getTranslation('name', $locale);
                        }

                        return implode(' / ', $translations);
                    }
                    return $root->name;
                }
            ],
            'name_translations' => [
                'name' => 'name_translations',
                'type' => Type::nonNull(Type::string()),
                'privacy' => function(array $args): bool {
                    $user = Auth::guard('api')->user();
                    return $user->hasRole('admin');
                },
                'alias' => 'name',
                'resolve' => function($root, $args) {
                    return json_encode($root->getTranslations('name'));
                }
            ],
            'is_city' => [
                'type' => Type::boolean(),
            ],
            'lat' => [
                'type' => Type::string(),
            ],
            'lng' => [
                'type' => Type::string(),
            ],
            'country' => [
                'type' => GraphQL::type('Country'),
            ]
        ];
    }
}
