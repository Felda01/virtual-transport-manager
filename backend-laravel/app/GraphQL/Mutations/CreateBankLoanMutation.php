<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ProcessTransaction;
use App\Models\BankLoan;
use App\Models\BankLoanType;
use App\Models\Company;
use App\Rules\MaximumBankLoansRule;
use App\Utilities\BroadcastUtility;
use App\Utilities\TransactionUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateBankLoanMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createBankLoan',
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
            'bank_loan_type' => [
                'required',
                'string',
                'exists:bank_loan_types,id,deleted_at,NULL',
            ],
            'general' => [
                new MaximumBankLoansRule()
            ]
        ];
    }

    public function args(): array
    {
        return [
            'bank_loan_type' => [
                'name' => 'bank_loan_type',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $company = Company::currentCompany();

        $result = DB::transaction(function () use ($args, $company) {
            /** @var BankLoanType $bankLoanType */
            $bankLoanType = BankLoanType::find($args['bank_loan_type']);

            /** @var BankLoan $bankLoan */
            $bankLoan = BankLoan::create([
                'bank_loan_type_id' => $bankLoanType->id,
                'company_id' => $company->id
            ]);

            $price = $bankLoanType->value;

            $transaction = TransactionUtility::create($company, $bankLoan, $price, 'bank_loan_create');

            $oldMoney = $company->money;

            $company->increment('money', $price);
            $company->save();

            if (!$bankLoan || !$transaction || $company->money !== ($oldMoney + $price)) {
                throw new \GraphQL\Error\Error(trans('validation.general_exception'));
            }

            return [
                'bankLoan' => $bankLoan
            ];
        });

        BroadcastUtility::broadcast(new ProcessTransaction($company));
        return $result['bankLoan'];
    }
}
