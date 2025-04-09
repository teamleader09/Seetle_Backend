<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
Route::post('register', [AuthController::class, 'register']);
Route::post('login_action', [AuthController::class, 'loginAction']);
Route::post('compare_nickname', [AuthController::class, 'compareNickname']);
Route::post('login_with_password', [AuthController::class, 'loginWithPassword']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    Route::post('logout', [AuthApiController::class, 'logout']);
    Route::get('user', function (Request $request) {
        return $request->user();
    });
});
