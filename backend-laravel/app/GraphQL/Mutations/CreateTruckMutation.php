<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ProcessTransaction;
use App\Jobs\UpdateModelStatus;
use App\Models\Company;
use App\Models\Truck;
use App\Models\TruckModel;
use App\Rules\AvailableGarageSpotRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\MoneyCheckRule;
use App\Utilities\BroadcastUtility;
use App\Utilities\QueueJobUtility;
use App\Utilities\StatusUtility;
use App\Utilities\TransactionUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class CreateTruckMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createTruck',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Truck');
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
            'truck_model' => [
                'required',
                'exists:truck_models,id,deleted_at,NULL',
            ],
            'garage' => [
                'required',
                'exists:garages,id,deleted_at,NULL',
                new ModelFromCompanyRule('Garage'),
                new AvailableGarageSpotRule('truck')
            ],
            'general' => [
                new MoneyCheckRule('TruckModel', $args['truck_model'])
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
            'truck_model' => [
                'name' => 'truck_model',
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
            /** @var TruckModel $truckModel */
            $truckModel = TruckModel::find($args['truck_model']);

            /** @var Truck $truck */
            $truck = Truck::create([
                'trailer_id' => null,
                'truck_model_id' => $args['truck_model'],
                'company_id' => $company->id,
                'garage_id' => $args['garage'],
                'km' => $truckModel->km,
                'status' => StatusUtility::DELIVERY_FROM_SHOP
            ]);

            $price = $truckModel->price;

            $transaction = TransactionUtility::create($company, $truck, -1 * $price, 'create');

            $oldMoney = $company->money;

            $company->decrement('money', $price);
            $company->save();

            if (!$truck || !$transaction || $company->money !== ($oldMoney - $price)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'truck' => $truck,
                'transaction' => $transaction
            ];
        });

        BroadcastUtility::broadcast(new ProcessTransaction($result['transaction']));
        QueueJobUtility::dispatch(new UpdateModelStatus($result['truck'], StatusUtility::IDLE), 60 * 6);
        return $result['truck'];
    }
}
