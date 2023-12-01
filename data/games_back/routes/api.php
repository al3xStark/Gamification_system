<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/signup', [\App\Http\Controllers\AuthController::class, 'signupAction']);
Route::post('/signin', [\App\Http\Controllers\AuthController::class, 'signinAction']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user-board/{userName}', [\App\Http\Controllers\UserController::class, 'userBoardAction']);
    Route::get('/user-board', [\App\Http\Controllers\UserController::class, 'userBoardAction']);

    Route::group(['prefix' => 'games'], function () {
        Route::get('my', [\App\Http\Controllers\GamesController::class, 'getMyGamesAction']);
        Route::get('achievements', [\App\Http\Controllers\GamesController::class, 'getMyAchievementsAction']);
        Route::get('{id}', [\App\Http\Controllers\GamesController::class, 'getAction'])->where('id', '[0-9]+');
        Route::get('/autocomplete-list/{mask}', [\App\Http\Controllers\GamesController::class, 'autocompleteListAction']);
        Route::post('/rating/{mask}', [\App\Http\Controllers\GamesController::class, 'setRatingAction']);
        Route::get('/comments/{id}', [\App\Http\Controllers\GamesController::class, 'commentsAction'])->where('id', '[0-9]+');
        Route::post('/setComment/{id}', [\App\Http\Controllers\GamesController::class, 'setCommentAction'])->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/autocomplete-list/{mask}', [\App\Http\Controllers\UserController::class, 'autocompleteListAction']);
    });
});

Route::post('/callbacks', [\App\Http\Controllers\CallbackController::class, 'indexAction']);

