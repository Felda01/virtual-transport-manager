<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Trailer;
use App\Models\Truck;
use App\Rules\AvailableModelRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\ModelInGarageRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class AssignTrailerToTruckMutation extends Mutation
{
    protected $attributes = [
        'name' => 'assignTrailerToTruck',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Trailer');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_VEHICLES, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'trailer' => [
                'required',
                'string',
                'exists:trailers,id,deleted_at,NULL',
                new ModelFromCompanyRule('Trailer'),
                new AvailableModelRule('Trailer'),
            ],
            'truck' => [
                'required',
                'string',
                'exists:trucks,id,deleted_at,NULL',
                new ModelFromCompanyRule('Truck'),
                new AvailableModelRule('Truck'),
            ],
            'general' => [
                new ModelInGarageRule('Trailer', $args['trailer'], 'Truck', $args['truck']),
            ]
        ];
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [
            'trailer' => [
                'name' => 'trailer',
                'type' => Type::nonNull(Type::string()),
            ],
            'truck' => [
                'name' => 'truck',
                'type' => Type::nonNull(Type::string()),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Trailer $trailer */
        $trailer = Trailer::find($args['trailer']);

        /** @var Truck $truck */
        $truck = Truck::find($args['truck']);

        $truck->trailer()->associate($trailer);
        $truck->save();

        return $trailer;
    }
}
