<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\BankLoanType;
use App\Rules\NotExistsRelationRule;
use App\Rules\UniqueBankLoanTypeRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class UpdateBankLoanTypeMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateBankLoanType',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('BankLoanType');
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // true, if logged in
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_STATIC, \App\Models\Permission::GUARD);
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => [
                'required',
                'exists:bank_loan_types,id,deleted_at,NULL',
                new NotExistsRelationRule('BankLoanType', 'bankLoans', 'update'),
            ],
            'value' => [
                'required',
                'numeric',
                'integer',
                'min:1'
            ],
            'payment' => [
                'required',
                'numeric',
                'integer',
                'min:1'
            ],
            'period' => [
                'required',
                'numeric',
                'integer',
                'min:1'
            ],
            'general' => [
                new UniqueBankLoanTypeRule($args, $args['id']),
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
                'type' => Type::nonNull(Type::string())
            ],
            'value' => [
                'name' => 'value',
                'type' => Type::nonNull(Type::string())
            ],
            'payment' => [
                'name' => 'payment',
                'type' => Type::nonNull(Type::string())
            ],
            'period' => [
                'name' => 'period',
                'type' => Type::nonNull(Type::string())
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var BankLoanType $bankLoanType */
        $bankLoanType = BankLoanType::find($args['id']);

        $bankLoanType->update($args);

        $bankLoanType->save();

        return $bankLoanType;
    }
}
