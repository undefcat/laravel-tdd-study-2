<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;
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

Route::prefix('/users')->group(function () {
    Route::post('/sign-up', [UserController::class, 'signUp']);

    Route::post('/sign-in', [UserController::class, 'signIn']);
});

Route::prefix('/articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);

    Route::get('/{id}', [ArticleController::class, 'show'])
        ->where('id', '[0-9]+');

    Route::middleware(['auth'])->group(function () {
        Route::post('/', [ArticleController::class, 'store']);

        Route::put('/{id}', [ArticleController::class, 'update'])
            ->where('id', '[0-9]+');

        Route::delete('/{id}', [ArticleController::class, 'delete'])
            ->where('id', '[0-9]+');
    });
});
