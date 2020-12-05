<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Country;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

/**
 * Class CountryType
 * @package App\GraphQL\Types
 */
class CountryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Country',
        'description' => 'A type',
        'model' => Country::class
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
            'short_name' => [
                'type' => Type::string(),
            ]
        ];
    }
}
