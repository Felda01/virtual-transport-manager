<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Order;
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
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var Order $order */
        $order = Order::find($args['order']);

        if (!$order || $order->roadTrip->status !== StatusUtility::WAITING_FOR_DRIVERS) {
            return [];
        }

//        $response = Http::withBasicAuth(config('services.nodejs.user'), config('services.nodejs.password'))->post(config('services.nodejs.url'), [
//            'from' => $order->market->location_from,
//            'to' => $order->market->location_to,
//        ]);
//
//        if (!$response->successful()) {
//            return [];
//        }
//
//        $data = $response->json();

        $data = ["result" => [["path" => ["6eb19875-7dba-4407-9605-3dabf4972c63","e05b0a43-0cfb-4bf6-80be-e2718b68e712","05932cb4-ec20-4745-9d01-0a4918d0a12a"],"cost" => 4404],["path" => ["6eb19875-7dba-4407-9605-3dabf4972c63","e05b0a43-0cfb-4bf6-80be-e2718b68e712","023eb1af-7df5-49e5-aed4-aab7548a183c","05932cb4-ec20-4745-9d01-0a4918d0a12a"],"cost" => 4678], ["cost" => 9007199254740991]]];

        $results = [];

        foreach ($data['result'] as $item) {

            if (array_key_exists('path', $item)) {
                $routes = PathUtility::getRoutesFromPath($item['path']);

                $result = [
                    'path' => $item['path'],
                    'distance' => $item['cost'],
                    'time' => $routes->sum('time'),
                    'fee' => $routes->sum('fee'),
                    'routes' => $routes->pluck('id')->all()
                ];

                $results[] = json_encode($result);
            }
        }

        return $results;
    }
}
