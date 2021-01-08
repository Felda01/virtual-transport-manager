<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Country;
use App\Rules\UniqueTranslationRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateCountryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCountry',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Country');
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
                'string',
                'exists:countries,id,deleted_at,NULL'
            ],
            'short_name' => [
                'string',
                Rule::unique('countries', 'short_name')->ignore($args['id'])->whereNull('deleted_at')
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
            'short_name' => [
                'name' => 'short_name',
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

        /** @var Country $country */
        $country = Country::find($args['id']);

        $country->setTranslations('name', $nameTranslations);
        $country->short_name = $args['short_name'];
        $country->save();

        return $country;
    }
}
