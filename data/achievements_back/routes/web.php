<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::post('/signup', [\App\Http\Controllers\AuthController::class, 'signupAction']);
Route::post('/signin', [\App\Http\Controllers\AuthController::class, 'signinAction']);
Route::post('/forgot', [\App\Http\Controllers\AuthController::class, 'sendResetLinkEmailAction']);
Route::post('/reset', [\App\Http\Controllers\AuthController::class, 'resetPasswordAction'])->middleware('guest');
