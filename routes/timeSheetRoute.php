<?php

use App\Http\Controllers\TimeSheetController;
use Tymon\JWTAuth\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::prefix('timeSheet')->group(function () {

    Route::middleware(Authenticate::class)->group(function () {
        Route::post('/', [TimeSheetController::class, 'add']);
        Route::get('/', [TimeSheetController::class, 'get']);
        Route::get('/{id}', [TimeSheetController::class, 'getByID']);
        Route::post('/update', [TimeSheetController::class, 'update']);
        Route::post('/delete', [TimeSheetController::class, 'destroy']);
    });
});
