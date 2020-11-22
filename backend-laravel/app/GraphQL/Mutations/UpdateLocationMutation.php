<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Location;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
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
                'exists:locations'
            ],
            'name' => [
                'optional',
                'string',
            ],
            'is_city' => [
                'required',
                'boolean',
            ],
            'lat' => [
                'required',
                'decimal',
            ],
            'lng' => [
                'required',
                'decimal',
            ],
            'country' => [
                'required',
                'exists:countries,id'
            ],
        ];
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
            ],
            'is_city' => [
                'name' => 'is_city',
                'type' => Type::nonNull(Type::boolean()),
            ],
            'lat' => [
                'name' => 'lat',
                'type' => Type::nonNull(Type::float()),
            ],
            'lng' => [
                'name' => 'lat',
                'type' => Type::nonNull(Type::float()),
            ],
            'country' => [
                'name' => 'country',
                'type' => GraphQL::type('Country'),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Location $location */
        $location = Location::find($args['id']);

        $location->update($args);

        return $location;
    }
}
