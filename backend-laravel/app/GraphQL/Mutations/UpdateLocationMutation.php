<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Location;
use App\Rules\UniqueTranslationRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateLocationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateLocation',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Location');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_STATIC, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'exists:locations,id,deleted_at,NULL'
            ],
            'name_translations.*.value' => [
                'required',
                'string',
            ],
            'name_translations.*.locale' => [
                'required',
                'string',
                Rule::in(config('translatable.available_locales'))
            ],
            'name_translations.*' => [
                new UniqueTranslationRule('countries', 'name', $args['id'])
            ],
            'name_translations' => [
                Rule::requiredIf($args['is_city'])
            ],
            'is_city' => [
                'required',
                'boolean',
            ],
            'lat' => [
                'required',
                'string',
                'regex:/'.Location::LATITUDE_REGEX.'/i'
            ],
            'lng' => [
                'required',
                'string',
                'regex:/'.Location::LONGITUDE_REGEX.'/i'
            ],
            'country' => [
                'required',
                'exists:countries,id,deleted_at,NULL'
            ],
        ];
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::string()),
            ],
            'name_translations' => [
                'name' => 'name_translations',
                'type' => Type::nonNull(Type::listOf(GraphQL::type('TranslationInput'))),
            ],
            'is_city' => [
                'name' => 'is_city',
                'type' => Type::nonNull(Type::boolean()),
            ],
            'lat' => [
                'name' => 'lat',
                'type' => Type::nonNull(Type::string()),
            ],
            'lng' => [
                'name' => 'lng',
                'type' => Type::nonNull(Type::string()),
            ],
            'country' => [
                'name' => 'country',
                'type' => Type::nonNull(Type::string()),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $nameTranslations = [];

        foreach ($args['name_translations'] as $nameTranslation) {
            $nameTranslations[$nameTranslation['locale']] = $nameTranslation['value'];
        }

        /** @var Location $location */
        $location = Location::find($args['id']);

        $location->setTranslations('name', $nameTranslations);
        $location->is_city = $args['is_city'];
        $location->lat = $args['lat'];
        $location->lng = $args['lng'];
        $location->country_id = $args['country'];
        $location->save();

        return $location;
    }
}
