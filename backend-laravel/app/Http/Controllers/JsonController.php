<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Support\Facades\Cache;

/**
 * Class JsonController
 * @package App\Http\Controllers
 */
class JsonController extends Controller
{

    /**
     * routes - json
     */
    public function routes()
    {
        $routes = Cache::rememberForever('routes', function() {
            return Route::all(['id', 'location1_id', 'location2_id', 'distance']);
        });

        return response()->json([
            'routes' => $routes
        ]);
    }
}
