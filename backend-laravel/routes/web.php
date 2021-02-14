<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(config('services.frontend.url'), 301);
});

Route::get('/routes', [\App\Http\Controllers\JsonController::class, 'routes']);
Route::get('/reset-routes', function() {
    \Illuminate\Support\Facades\Cache::forget('routes');
});

Route::view('/YWRtaW4', 'admin')->middleware('auth.basic');

Route::get('/sleep', function() {
    dispatch(new \App\Jobs\ManageDriverStatus(false));
});

Route::get('/payment', function() {
    dispatch(new \App\Jobs\PayFees());
});


