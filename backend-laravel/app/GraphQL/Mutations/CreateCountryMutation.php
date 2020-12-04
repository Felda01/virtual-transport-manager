<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Country;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateCountryMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createCountry',
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
        return $this->guard()->check() && $this->guard()->user()->hasRole('admin');
    }

    public function rules(array $args = []): array
    {
        return [
            'name_translations.*.value' => [
                'required',
                'string',
//                'unique_translation:countries,name'
            ],
            'short_name' => [
                'string',
                'unique:countries,short_name'
            ]
        ];
    }

    public function args(): array
    {
        return [
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

        $country = Country::create([
            'name' => $nameTranslations,
            'short_name' => $args['short_name']
        ]);
        $country->save();

        return $country;
    }
}
