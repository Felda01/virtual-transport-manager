<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ProcessTransaction;
use App\Jobs\UpdateModelStatus;
use App\Models\Company;
use App\Models\Driver;
use App\Rules\CanUpdateDriverADRRule;
use App\Rules\DriverInGarageRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\ModelStatusRule;
use App\Utilities\BroadcastUtility;
use App\Utilities\GameTimeUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\StatusUtility;
use App\Utilities\TransactionUtility;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateDriverMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateDriver',
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
                new ModelStatusRule('Driver', [StatusUtility::IDLE, StatusUtility::READY]),
                new DriverInGarageRule(),
                new CanUpdateDriverADRRule(),
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

        $result = DB::transaction(function () use ($args, $company) {
            /** @var Driver $driver */
            $driver = Driver::find($args['id']);

            $oldStatus = $driver->status;

            $driver->increment('adr');
            $driver->status = StatusUtility::ON_TRAINING;
            $driverSaved = $driver->save();

            $price = config('constants.adr_price')[$driver->adr];

            $transaction = TransactionUtility::create($company, $driver, -1 * $price, 'training');

            $oldMoney = $company->money;

            $company->decrement('money', $price);
            $company->save();

            if (!$driverSaved || !$transaction || $company->money !== ($oldMoney - $price)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'driver' => $driver,
                'transaction' => $transaction,
                'oldStatus' => $oldStatus
            ];
        });

        QueueJobUtility::dispatch(new UpdateModelStatus($result['driver'], $result['oldStatus']), Carbon::parse(GameTimeUtility::gameTimeToRealTime(3 * 24 * 60), 'Europe/Bratislava'));
        BroadcastUtility::broadcast(new ProcessTransaction($company));
        return $result['driver'];
    }
}
