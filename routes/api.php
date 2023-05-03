<?php

use App\Http\Controllers\API\AuthenticationController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserGroupController;
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

Route::middleware('auth:sanctum')->group(function () {

    // User routes
    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::put('user/{user}', [UserController::class, 'update']);
    Route::delete('user/{user}', [UserController::class, 'destroy']);
    Route::get('profile', [UserController::class, 'profile']);

    // Group routes
    Route::get('groups', [UserGroupController::class, 'index']);
    Route::post('groups', [UserGroupController::class, 'store']);
    Route::get('groups/{group}', [UserGroupController::class, 'show']);
    Route::put('groups/{group}', [UserGroupController::class, 'update']);
    Route::delete('groups/{group}', [UserGroupController::class, 'destroy']);

    Route::post('logout', [AuthenticationController::class, 'logout']);
});

// Authentication routes
Route::post('register', [AuthenticationController::class, 'register']);
Route::post('login', [AuthenticationController::class, 'login']);
