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

Route::get('/cm91dGVz', [\App\Http\Controllers\JsonController::class, 'routes']);

Route::middleware(['auth.basic'])->group(function() {
    Route::view('/YWRtaW4', 'admin')->name('admin');
    Route::post('/YWRtaW4/cGF5RmVlcw', [\App\Http\Controllers\AdminController::class, 'payFees'])->name('admin.payFees');
    Route::post('/YWRtaW4/Z2VuZXJhdGVNYXJrZXRz', [\App\Http\Controllers\AdminController::class, 'generateMarkets'])->name('admin.generateMarkets');
    Route::post('/YWRtaW4/dXBkYXRlUGVyc29uYWxBZ2VuY3k', [\App\Http\Controllers\AdminController::class, 'updatePersonalAgency'])->name('admin.updatePersonalAgency');
    Route::post('/YWRtaW4/bWFuYWdlRHJpdmVyU3RhdHVz', [\App\Http\Controllers\AdminController::class, 'manageDriverStatus'])->name('admin.manageDriverStatus');
    Route::post('/YWRtaW4/cmVzZXRSb3V0ZXM', [\App\Http\Controllers\AdminController::class, 'resetRoutes'])->name('admin.resetRoutes');
    Route::post('/YWRtaW4/YmFua0xvYW4', [\App\Http\Controllers\AdminController::class, 'bankLoan'])->name('admin.bankLoan');
    Route::post('/YWRtaW4/VGVzdE5vZGVKcw', [\App\Http\Controllers\AdminController::class, 'testNodeJs'])->name('admin.testNodeJs');
    Route::post('/YWRtaW4/ZmV0Y2hSb3V0ZXM', [\App\Http\Controllers\AdminController::class, 'fetchRoutes'])->name('admin.fetchRoutes');
});



