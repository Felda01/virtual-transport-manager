<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Market;
use App\Models\Order;
use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Query;

class DashboardQuery extends Query
{
    protected $attributes = [
        'name' => 'dashboard',
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
        /** @var User $user */
        $user = User::current();

        $orderTable = (new Order)->getTable();

        $orderCount = DB::table($orderTable)->selectRaw("COUNT(*) total, DATE_FORMAT(updated_at, '%e.%c.') date")
            ->where('company_id', $user->company_id)
            ->groupBy('date')
            ->orderBy('updated_at', 'DESC')
            ->limit(7)
            ->get();

        $marketTable = (new Market)->getTable();

        $orderSum = DB::table($orderTable)->selectRaw("SUM(" . $marketTable . ".price) price, DATE_FORMAT(" . $orderTable . ".updated_at, '%e.%c.') date")
            ->join($marketTable, $marketTable . '.id', '=', $orderTable . '.market_id')
            ->where($orderTable. '.company_id', $user->company_id)
            ->groupBy('date')
            ->orderBy($orderTable . '.updated_at', 'DESC')
            ->limit(7)
            ->get();

        return [
            'ordersCount' => json_encode(array_reverse($orderCount->toArray())),
            'ordersSum' => json_encode(array_reverse($orderSum->toArray())),
        ];
    }
}
