<?php

use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\LogoutController;
use App\Http\Controllers\Api\v1\Auth\RegisterController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/logout', [LogoutController::class, 'logout']);

        Route::post('/register', [RegisterController::class, 'register'])->middleware('permission:create-users');
        Route::get('/users/{id}', [UserController::class, 'show'])->middleware('permission:view-users');
        Route::post('/users/{id}', [UserController::class, 'update'])->middleware('permission:edit-users');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('permission:delete-users');

        
    });
});
