<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
    // 'middleware' => ['auth:api', 'role:writer'],
    'middleware' => ['auth:api', 'permission:edit articles'],
], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/me', [AuthController::class, 'me'])->name('me'); // ->middleware('auth:api')
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh'); // ->middleware('auth:api')
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // ->middleware('auth:api')
});
