<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// INFO: PUBLICLY AVAILABLE ROUTES
Route::group([
    // EMPTY
], function () {
    Route::apiResource('posts', PostController::class)->only([
        'index', 'show',
    ]);
});

Route::group([
    'middleware' => ['auth:sanctum']
], function () {
    Route::apiResource('posts', PostController::class)->except([
        'index', 'show'
    ]);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
