<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\RefreshQuery;
use App\Jobs\UpdateModelStatus;
use App\Models\Company;
use App\Models\Driver;
use App\Rules\CanUpdateDriverADRRule;
use App\Rules\DriverInGarageRule;
use App\Rules\DriverNotInGarageRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\ModelStatusRule;
use App\Utilities\BroadcastUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\PathUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\StatusUtility;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateDriverLocationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateDriverLocation',
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
                new ModelStatusRule('Driver', [StatusUtility::READY]),
                new DriverNotInGarageRule(),
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
        /** @var Company $company */
        $company = Company::currentCompany();

        /** @var Driver $driver */
        $driver = Driver::find($args['id']);
        $driverSaved = $driver->update([
            'location_id' => $driver->garage->location_id,
            'status' => StatusUtility::TRAVEL
        ]);

        if (!$driverSaved) {
            throw new \GraphQL\Error\Error(trans('validation.general_exception'));
        }

        $data = PathUtility::getPaths($driver->location_id, $driver->garage->location_id);

        if ($data['result'] && count($data['result']) > 0) {
            $path = $data['result'][0];
            $routes = PathUtility::getRoutesFromPath($path['edges']);

            $timeInMinutes = PathUtility::calculateRoadTripTime($routes->sum('time'), $driver->truck->drivers()->count());

            QueueJobUtility::dispatch(new UpdateModelStatus($driver, StatusUtility::READY), Carbon::parse(GameTimeUtility::addTimeToRealTime($timeInMinutes), 'Europe/Bratislava'));
            BroadcastUtility::broadcast(new RefreshQuery($company, 'Driver', $driver->id));

            return $driver;
        }
        throw new \GraphQL\Error\Error(trans('validation.general_exception'));
    }
}
