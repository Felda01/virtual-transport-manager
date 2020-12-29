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
