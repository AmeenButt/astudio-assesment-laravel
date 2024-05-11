<?php

use App\Http\Controllers\ProjectsController;
use Tymon\JWTAuth\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::prefix('project')->group(function () {

    Route::middleware(Authenticate::class)->group(function () {
        Route::post('/', [ProjectsController::class, 'add']);
        Route::get('/', [ProjectsController::class, 'get']);
        Route::get('/{id}', [ProjectsController::class, 'getByID']);
        Route::post('/update', [ProjectsController::class, 'update']);
        Route::post('/delete', [ProjectsController::class, 'destroy']);
    });
});
