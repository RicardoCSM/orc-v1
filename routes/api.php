<?php

use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\LogoutController;
use App\Http\Controllers\Api\v1\Auth\RegisterController;
use App\Http\Controllers\Api\v1\DocumentAi\CnhController;
use App\Http\Controllers\Api\v1\DocumentAi\CpfController;
use App\Http\Controllers\Api\v1\DocumentAi\IdentidadeController;
use App\Http\Controllers\Api\v1\DocumentAi\IdentificadorController;
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

        Route::post('/cpf', [CpfController::class, 'getCpf']);
        Route::post('/identidade', [IdentidadeController::class, 'getIdentity']);
        Route::post('/cnh', [CnhController::class, 'getCnh']);
        Route::post('/identificador', [IdentificadorController::class, 'getType']);
    });
});
