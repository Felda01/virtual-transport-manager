<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Jobs\UpdateModelStatus;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Garage;
use App\Rules\AvailableDriverRecruitmentAgencyRule;
use App\Rules\AvailableGarageSpotRule;
use App\Rules\ModelFromCompanyRule;
use App\Utilities\QueueJobUtility;
use App\Utilities\StatusUtility;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateDriverMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createDriver',
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
            'driver' => [
                'required',
                'exists:drivers,id,deleted_at,NULL',
            ],
            'garage' => [
                'required',
                'exists:garages,id,deleted_at,NULL',
                new ModelFromCompanyRule('Garage'),
                new AvailableGarageSpotRule('driver')
            ],
            'general' => [
                new AvailableDriverRecruitmentAgencyRule($args['driver']),
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
            'driver' => [
                'name' => 'driver',
                'type' => Type::nonNull(Type::string()),
            ],
            'garage' => [
                'name' => 'garage',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $company = Company::currentCompany();

        $result = DB::transaction(function () use ($args, $company) {
            /** @var Driver $driver */
            $driver = Driver::find($args['driver']);

            /** @var Garage $garage */
            $garage = Garage::find($args['garage']);

            $driver->company_id = $company->id;
            $driver->garage_id = $garage->id;
            $driver->location_id = $garage->location_id;
            $driver->last_in_garage_at = Carbon::now();

            $driver->save();

            if (!$driver) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'driver' => $driver
            ];
        });

        QueueJobUtility::dispatch(new UpdateModelStatus($result['driver'], StatusUtility::IDLE), 60 * 6);
        return $result['driver'];
    }
}
