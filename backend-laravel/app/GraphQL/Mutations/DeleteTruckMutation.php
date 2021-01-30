<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Company;
use App\Models\Truck;
use App\Rules\CanDeleteTruckRule;
use App\Rules\ModelFromCompanyRule;
use App\Rules\ModelStatusRule;
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

class DeleteTruckMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteTruck',
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
            'id' => [
                'required',
                'string',
                'exists:trucks,id,deleted_at,NULL',
                new ModelFromCompanyRule('Truck'),
                new ModelStatusRule('Truck', [StatusUtility::IDLE]),
                new CanDeleteTruckRule()
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
            /** @var Truck $truck */
            $truck = Truck::find($args['id']);

            $deletedTruck = $truck;

            $company = Company::currentCompany();

            $price = $deletedTruck->truckModel->price / 2;

            $transaction = TransactionUtility::create($company, $truck, $price, 'sell');

            try {
                $truck->delete();
            } catch (\Exception $e) {
            }

            $oldMoney = $company->money;

            $company->increment('money', $price);
            $company->save();

            if (!$truck->trashed() || !$transaction || $company->money !== $oldMoney + $price) {
                throw new \Exception(trans('validation.general_exception'));
            }

            return [
                'truck' => $deletedTruck,
                'transaction' => $transaction
            ];
        });

        return $result['truck'];
    }
}
