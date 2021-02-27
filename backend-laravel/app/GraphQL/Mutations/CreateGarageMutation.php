<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ProcessTransaction;
use App\Events\RefreshQuery;
use App\Models\Company;
use App\Models\Garage;
use App\Rules\AvailableLocationRule;
use App\Rules\MoneyCheckRule;
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

class CreateGarageMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createGarage',
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

    public function rules(array $args = []): array
    {
        return [
            'garage_model' => [
                'required',
                'exists:garage_models,id,deleted_at,NULL',
            ],
            'location' => [
                'required',
                'exists:locations,id,deleted_at,NULL',
                new AvailableLocationRule(),
            ],
            'general' => [
                new MoneyCheckRule('GarageModel', $args['garage_model'])
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
            'garage_model' => [
                'name' => 'garage_model',
                'type' => Type::nonNull(Type::string()),
            ],
            'location' => [
                'name' => 'location',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $company = Company::currentCompany();

        $result = DB::transaction(function () use ($args, $company) {
            /** @var Garage $garage */
            $garage = Garage::create([
                'location_id' => $args['location'],
                'garage_model_id' => $args['garage_model'],
                'company_id' => $company->id
            ]);

            $price = $garage->garageModel->price;

            $transaction = TransactionUtility::create($company, $garage, -1 * $price, 'garage_create');

            $oldMoney = $company->money;

            $company->decrement('money', $price);
            $company->save();

            if (!$garage || !$transaction || $company->money !== ($oldMoney - $price)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'garage' => $garage
            ];
        });

        BroadcastUtility::broadcast(new ProcessTransaction($company));
        BroadcastUtility::broadcast(new RefreshQuery($company, 'Garage', $result['garage']->id));
        return $result['garage'];
    }
}
