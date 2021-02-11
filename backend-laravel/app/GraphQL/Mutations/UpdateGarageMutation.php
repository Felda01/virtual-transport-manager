<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ProcessTransaction;
use App\Models\Company;
use App\Models\Garage;
use App\Models\GarageModel;
use App\Rules\AvailableGarageModelUpgradeRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\MoneyCheckUpgradeGarageRule;
use App\Utilities\BroadcastUtility;
use App\Utilities\TransactionUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class UpdateGarageMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateGarage',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Garage');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_GARAGES, \App\Models\Permission::GUARD);
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:garages,id,deleted_at,NULL',
                new ModelFromCompanyRule('Garage')
            ],
            'garage_model' => [
                'required',
                'string',
                'exists:garage_models,id,deleted_at,NULL',
                new AvailableGarageModelUpgradeRule($args['id']),
                new MoneyCheckUpgradeGarageRule($args['id']),
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
            'garage_model' => [
                'name' => 'garage_model',
                'type' => Type::nonNull(Type::string()),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $company = Company::currentCompany();

        $result = DB::transaction(function () use ($args, $company) {
            /** @var Garage $garage */
            $garage = Garage::find($args['id']);

            $currentGarageModel = $garage->garageModel;

            $garage->garage_model_id = $args['garage_model'];
            $garage->save();

            /** @var GarageModel $garageModel */
            $garageModel = GarageModel::find($args['garage_model']);

            $price = $garageModel->price - $currentGarageModel->price;

            $transaction = TransactionUtility::create($company, $garage, -1 * $price, 'upgrade');

            $oldMoney = $company->money;

            $company->decrement('money', $price);
            $company->save();

            if ($garage->garage_model_id !== $garageModel->id || !$transaction || $company->money !== ($oldMoney - $price)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'garage' => $garage,
                'transaction' => $transaction
            ];
        });

        BroadcastUtility::broadcast(new ProcessTransaction($result['transaction']));
        return $result['garage'];
    }
}
