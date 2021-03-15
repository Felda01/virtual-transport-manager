<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\RefreshQuery;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Truck;
use App\Rules\DriverHasTruckRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\ModelsInGarageRule;
use App\Rules\ModelStatusRule;
use App\Rules\TruckHasDriverRule;
use App\Utilities\BroadcastUtility;
use App\Utilities\StatusUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UnassignDriverFromTruckMutation extends Mutation
{
    protected $attributes = [
        'name' => 'unassignDriverFromTruck',
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
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_DRIVERS, \App\Models\Permission::GUARD) && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_VEHICLES, \App\Models\Permission::GUARD);
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function rules(array $args = []): array
    {
        return [
            'driver' => [
                'required',
                'string',
                'exists:drivers,id,deleted_at,NULL',
                new ModelFromCompanyRule('Driver'),
                new ModelStatusRule('Driver', [StatusUtility::READY, StatusUtility::IDLE]),
                new DriverHasTruckRule($args['truck'])
            ],
            'truck' => [
                'required',
                'string',
                'exists:trucks,id,deleted_at,NULL',
                new ModelFromCompanyRule('Truck'),
                new ModelStatusRule('Truck', [StatusUtility::ON_DUTY]),
                new TruckHasDriverRule($args['driver'])
            ],
            'general' => [
                new ModelsInGarageRule('Driver', $args['driver'], 'Truck', $args['truck']),
            ]
        ];
    }

    public function args(): array
    {
        return [
            'driver' => [
                'name' => 'driver',
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
        $result = DB::transaction(function () use($args) {
            /** @var Driver $driver */
            $driver = Driver::find($args['driver']);

            /** @var Truck $truck */
            $truck = Truck::find($args['truck']);

            $driversCount = $truck->drivers()->count();

            $driver->truck()->dissociate();
            $driver->status = StatusUtility::IDLE;
            $driverSaved =$driver->save();

            $truckSaved = true;
            if ($driversCount === 1) {
                $truck->status = StatusUtility::IDLE;
                $truckSaved = $truck->save();
            }

            if (!$driverSaved || !$truckSaved) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'driver' => $driver,
                'truck' => $truck
            ];
        });

        $company = Company::currentCompany();
        BroadcastUtility::broadcast(new RefreshQuery($company, 'Driver', $result['driver']->id));
        BroadcastUtility::broadcast(new RefreshQuery($company, 'Truck', $result['truck']->id));

        return $result['driver'];
    }
}
