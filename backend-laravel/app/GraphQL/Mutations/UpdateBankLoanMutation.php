<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ProcessTransaction;
use App\Events\RefreshQuery;
use App\Models\BankLoan;
use App\Models\Company;
use App\Rules\ModelFromCompanyRule;
use App\Rules\MoneyCheckBankLoanRepayRule;
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

class UpdateBankLoanMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateBankLoan',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('BankLoan');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_SALARY, \App\Models\Permission::GUARD);
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
                'exists:bank_loans,id,deleted_at,NULL',
                new ModelFromCompanyRule('BankLoan'),
                new MoneyCheckBankLoanRepayRule()
            ],
        ];
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
            /** @var BankLoan $bankLoan */
            $bankLoan = BankLoan::find($args['id']);

            $bankLoanType = $bankLoan->bankLoanType;

            $price = $bankLoanType->payment * ($bankLoanType->period - $bankLoan->paid);

            $bankLoanUpdated = $bankLoan->update([
                'done' => true
            ]);

            $transaction = TransactionUtility::create($company, $bankLoan, $price, 'bank_loan_update');

            $oldMoney = $company->money;

            $company->decrement('money', $price);
            $company->save();

            if (!$bankLoanUpdated || !$transaction || $company->money !== ($oldMoney - $price)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'bankLoan' => $bankLoan
            ];
        });

        BroadcastUtility::broadcast(new ProcessTransaction($company));
        BroadcastUtility::broadcast(new RefreshQuery($company, 'BankLoan', $result['bankLoan']->id));
        return $result['bankLoan'];
    }
}
