<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Driver;
use App\Rules\CanDeleteDriverRule;
use App\Rules\DriverEmptyTruckSpotRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\ModelStatusRule;
use App\Utilities\StatusUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteDriverMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteDriver',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Driver');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_DRIVERS, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:drivers,id,deleted_at,NULL',
                new ModelFromCompanyRule('Driver'),
                new ModelStatusRule('Driver', [StatusUtility::IDLE]),
                new CanDeleteDriverRule()
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
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $result = DB::transaction(function () use ($args) {
            /** @var Driver $driver */
            $driver = Driver::find($args['id']);

            $deletedDriver = $driver;

            try {
                $deleted = $driver->delete();
                if (!$deleted) {
                    throw new \GraphQL\Error\Error(trans('validation.general_exception'));
                }
            } catch (\Exception $e) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'driver' => $deletedDriver
            ];
        });

        return $result['driver'];
    }
}
