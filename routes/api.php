<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ProjetApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/dashboard', DashboardApiController::class);

    Route::prefix('projet')->group(function () {
        Route::get('/board', [ProjetApiController::class, 'board']);
        Route::post('/cartes', [ProjetApiController::class, 'store']);
        Route::get('/cartes/{projet}', [ProjetApiController::class, 'show']);
        Route::patch('/cartes/{projet}', [ProjetApiController::class, 'update']);
        Route::post('/move', [ProjetApiController::class, 'move']);
        Route::post('/cartes/{projet}/commentaires', [ProjetApiController::class, 'storeCommentaire']);
    });
});
