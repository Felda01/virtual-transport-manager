<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\BankLoanType;
use App\Models\Cargo;
use App\Models\Country;
use App\Models\GarageModel;
use App\Models\Location;
use App\Models\Route;
use App\Models\TrailerModel;
use App\Models\TruckModel;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class AdminDashboardQuery extends Query
{
    protected $attributes = [
        'name' => 'adminDashboard',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return Type::listOf(Type::string());
    }

    private function guard()
    {
        return Auth::guard('api');
    }

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        return $this->guard()->check() && $this->guard()->user()->hasPermissionTo(\App\Models\Permission::MANAGE_STATIC, \App\Models\Permission::GUARD);;
    }

    public function getAuthorizationMessage(): string
    {
        return trans('validation.unauthorized');
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return [
            Country::count(),
            Location::count(),
            Route::count(),
            BankLoanType::count(),
            GarageModel::count(),
            TrailerModel::count(),
            TruckModel::count(),
            Cargo::count(),
        ];
    }
}
