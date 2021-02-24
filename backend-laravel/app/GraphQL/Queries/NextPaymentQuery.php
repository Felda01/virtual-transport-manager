<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Company;
use App\Models\User;
use App\Utilities\CompanyUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Query;

class NextPaymentQuery extends Query
{
    protected $attributes = [
        'name' => 'nextPayment',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return Type::listOf(Type::float());
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->guard()->check();
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $data = CompanyUtility::nextPayment(Company::currentCompany());

        return array_values($data);
    }
}
