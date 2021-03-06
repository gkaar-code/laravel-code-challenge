<?php

use App\Http\Controllers\UserCommentsController;
use App\Http\Controllers\CommentController;
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
    Route::get('/users', function () {
        return \App\Models\User::withCount('comments')
        ->with('comments:id,author_id,post_id,is_published')
        ->get();
    });
    Route::apiResource('posts', PostController::class)->only([
        'index', 'show',
    ]);
    Route::apiResource('posts.comments', CommentController::class)->only([
        'index', 'show',
    ])->shallow();
    Route::apiResource('users.comments', UserCommentsController::class)->only([
        'index',
    ]);
});

Route::group([
    'middleware' => ['auth:sanctum']
], function () {
    Route::apiResource('posts', PostController::class)->except([
        'index', 'show'
    ]);
    Route::apiResource('posts.comments', CommentController::class)->except([
        'index', 'show',
    ])->shallow();
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
