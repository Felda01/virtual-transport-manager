<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Driver;
use App\Models\Order;
use App\Models\Truck;
use App\Rules\ModelFromCompanyRule;
use App\Rules\TruckForOrderRule;
use App\Utilities\PathUtility;
use App\Utilities\StatusUtility;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Rebing\GraphQL\Support\Query;

class PathsForOrderQuery extends Query
{
    protected $attributes = [
        'name' => 'pathsForOrder',
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
            'order' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'truck' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Order $order */
        $order = Order::find($args['order']);

        if (!$order || $order->roadTrip->status !== StatusUtility::WAITING_FOR_DRIVERS) {
            return [];
        }

        $market = $order->market;

        $data = PathUtility::getPaths($market->location_from, $market->location_to);

        $needTruckPath = false;
        $truckPath = [];
        $truckRoutes = [];

        if ($args['truck']) {
            /** @var Truck $truck */
            $truck = Truck::find($args['truck']);

            if ($truck && $truck->drivers()->count() > 0) {
                /** @var Driver $driver */
                $driver = $truck->drivers()->first();

                if ($driver->location_id !== $market->location_from) {
                    $dataTruck = PathUtility::getPaths($driver->location_id, $market->location_from);

                    if ($dataTruck['result'] && count($dataTruck['result']) > 0) {
                        $needTruckPath = true;
                        $truckPath = $dataTruck['result'][0];
                        $truckRoutes = PathUtility::getRoutesFromPath($truckPath['path']);
                    }
                }
            }
        }

        $results = [];

        for ($i = 0; $i < count($data['result']); $i++) {
            $item = $data['result'][$i];

            if (array_key_exists('path', $item)) {
                $routes = PathUtility::getRoutesFromPath($item['path']);

                if ($needTruckPath) {
                    $path = array_merge($truckPath['path'], $item['path']);
                    $result = [
                        'path' => $path,
                        'distance' => $truckPath['cost'] + $item['cost'],
                        'time' => $truckRoutes->sum('time') + $routes->sum('time'),
                        'fee' => $truckRoutes->sum('fee') + $routes->sum('fee'),
                        'routes' => array_merge($truckRoutes->pluck('id')->all(), $routes->pluck('id')->all()),
                        'pathModels' => PathUtility::getPathLocations($path),
                    ];
                } else {
                    $result = [
                        'path' => $item['path'],
                        'distance' => $item['cost'],
                        'time' => $routes->sum('time'),
                        'fee' => $routes->sum('fee'),
                        'routes' => $routes->pluck('id')->all(),
                        'pathModels' => PathUtility::getPathLocations($item['path']),
                    ];
                }

                $results[] = json_encode($result);
            }
        }

        return $results;
    }
}
