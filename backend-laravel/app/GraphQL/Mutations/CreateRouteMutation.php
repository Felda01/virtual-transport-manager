<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Location;
use App\Models\Route;
use App\Rules\UniqueLocationsRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateRouteMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createRoute',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Route');
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
            'location1' => [
                'string',
                'exists:locations,id',
                'different:location2',
                new UniqueLocationsRule($args['location2'])
            ],
            'location2' => [
                'string',
                'exists:locations,id',
                'different:location1',
                new UniqueLocationsRule($args['location1'])
            ],
            'time' => [
                'nullable',
                'int'
            ],
            'distance' => [
                'nullable',
                'int'
            ],
            'fee' => [
                'required',
                'numeric'
            ]
        ];
    }

    public function args(): array
    {
        return [
            'location1' => [
                'name' => 'location1',
                'type' => Type::nonNull(Type::string())
            ],
            'location2' => [
                'name' => 'location2',
                'type' => Type::nonNull(Type::string())
            ],
            'time' => [
                'name' => 'time',
                'type' => Type::string()
            ],
            'distance' => [
                'name' => 'distance',
                'type' => Type::string()
            ],
            'fee' => [
                'name' => 'fee',
                'type' => Type::nonNull(Type::string())
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (empty($args['distance'])) {
            $location1 = Location::find($args['location1']);
            $location2 = Location::find($args['location2']);
            $args['distance'] = Route::getDistanceBetween($location1, $location2);
        }

        $route = Route::create([
            'location1_id' => $args['location1'],
            'location2_id' => $args['location2'],
            'distance' => $args['distance'],
            'time' => $args['time'] ?? $args['distance'] / 75 * 60,
            'fee' => $args['fee']
        ]);
        $route->save();

        return $route;
    }
}
