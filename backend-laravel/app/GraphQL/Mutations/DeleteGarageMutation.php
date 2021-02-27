<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ProcessTransaction;
use App\Events\RefreshQuery;
use App\Models\Company;
use App\Models\Garage;
use App\Rules\EmptyGarageRule;
use App\Rules\ModelFromCompanyRule;
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

class DeleteGarageMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteGarage',
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
            'id' => [
                'required',
                'string',
                'exists:garages,id,deleted_at,NULL',
                new ModelFromCompanyRule('Garage'),
                new EmptyGarageRule(),
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
        $company = Company::currentCompany();

        $result = DB::transaction(function () use ($args, $company) {
            /** @var Garage $garage */
            $garage = Garage::find($args['id']);

            $deletedGarage = $garage;

            try {
                $garage->delete();
            } catch (\Exception $e) {
            }

            $price = $deletedGarage->garageModel->price / 2;

            $transaction = TransactionUtility::create($company, $garage, $price, 'garage_sell');

            $oldMoney = $company->money;

            $company->increment('money', $price);
            $company->save();

            if (!$garage->trashed() || !$transaction || $company->money !== $oldMoney + $price) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'garage' => $deletedGarage
            ];
        });

        BroadcastUtility::broadcast(new ProcessTransaction($company));
        BroadcastUtility::broadcast(new RefreshQuery($company, 'Garage', $result['garage']->id));
        return $result['garage'];
    }
}
