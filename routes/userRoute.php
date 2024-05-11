<?php

use App\Http\Controllers\UserController;
use Tymon\JWTAuth\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::post('/', [UserController::class, 'create']);
    Route::post('/login', [UserController::class, 'login']);

    Route::middleware(Authenticate::class)->group(function () {
        Route::get('/', [UserController::class, 'get']);
        Route::get('/{id}', [UserController::class, 'getByID']);
        Route::post('/update', [UserController::class, 'update']);
        Route::post('/delete', [UserController::class, 'destroy']);
    });
});
