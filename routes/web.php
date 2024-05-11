<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TimeSheetController;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

Route::get('/', function () {
    return view('welcome');
});
// Route::post('/api/user', [UserController::class, 'create']);
// Route::post('/api/user/login', [UserController::class, 'login']);
Route::prefix('api')->group(function () {
    // Include API routes here
    require __DIR__ . '/userRoute.php';
    require __DIR__ . '/projectRoute.php';
    require __DIR__ . '/timeSheetRoute.php';
});
Route::middleware(Authenticate::class)->group(function () {
    // User routes
    // Route::get('/api/user', [UserController::class, 'get']);
    // Route::get('/api/user/{id}', [UserController::class, 'getByID']);
    // Route::post('/api/user/update', [UserController::class, 'update']);
    // Route::post('/api/user/delete', [UserController::class, 'destroy']);

    // Project Routes
    // Route::post('/api/project', [ProjectsController::class, 'add']);
    // Route::get('/api/project', [ProjectsController::class, 'get']);
    // Route::get('/api/project/{id}', [ProjectsController::class, 'getByID']);
    // Route::post('/api/project/update', [ProjectsController::class, 'update']);
    // Route::post('/api/project/delete', [ProjectsController::class, 'destroy']);

    // TimeSheet Routes
    // Route::post('/api/timeSheet', [TimeSheetController::class, 'add']);
    // Route::get('/api/timeSheet', [TimeSheetController::class, 'get']);
    // Route::get('/api/timeSheet/{id}', [TimeSheetController::class, 'getByID']);
    // Route::post('/api/timeSheet/update', [TimeSheetController::class, 'update']);
    // Route::post('/api/timeSheet/delete', [TimeSheetController::class, 'destroy']);
});
