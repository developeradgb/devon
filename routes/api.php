<?php

use App\Http\Controllers\Api\V1\PlayerController;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\Api\V1\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your Api!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['namespace' => 'Api\V1'], function () {
        Route::post('login', [LoginController::class, 'login'])->name('api.v1.login');
    });

    Route::get('teams/', [TeamController::class, 'index'])->name('api.v1.teams.index');
    Route::get('teams/{team}', [TeamController::class, 'show'])->name('api.v1.teams.show');
    Route::get('players/{player}', [PlayerController::class, 'show'])->name('api.v1.players.show');

    // Authenticated routes
    Route::group(['middleware' => 'api.auth'], function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('api.v1.logout');

        Route::group(['prefix' => 'teams', 'middleware' => 'check.admin.user'], function () {
            Route::post('/', [TeamController::class, 'store'])->name('api.v1.teams.store');
            Route::patch('/{team}', [TeamController::class, 'update'])->name('api.v1.teams.update');
            Route::delete('/{team}', [TeamController::class, 'destroy'])->name('api.v1.teams.destroy');
        });

        Route::group(['prefix' => 'players', 'middleware' => 'check.admin.user'], function () {
            Route::post('/', [PlayerController::class, 'store'])->name('api.v1.players.store');
            Route::patch('/{player}', [PlayerController::class, 'update'])->name('api.v1.players.update');
            Route::delete('/{player}', [PlayerController::class, 'destroy'])->name('api.v1.players.destroy');
        });
    });
});
