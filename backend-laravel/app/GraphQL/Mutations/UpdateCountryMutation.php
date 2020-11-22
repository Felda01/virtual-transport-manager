<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Country;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
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

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return true;
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:countries'
            ],
            'name' => [
                'required',
                'string',
                'unique:countries,name,'.$args['id']
            ],
            'short_name' => [
                'string',
                'unique:countries,short_name,'.$args['id']
            ]
        ];
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::string()),
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
            ],
            'short_name' => [
                'name' => 'short_name',
                'type' => Type::nonNull(Type::string()),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Country $country */
        $country = Country::find($args['id']);

        $country->update($args);

        return $country;
    }
}
