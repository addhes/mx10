<?php

use App\Http\Controllers\API\ApplicationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\JobsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Users Routes - Public
Route::post('/users', [UsersController::class, 'register']); // Registrasi
Route::post('/login', [UsersController::class, 'login']); // Login

// Protected Users Routes - memerlukan authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UsersController::class, 'index']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
});

// Jobs Routes - semua memerlukan authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jobs', [JobsController::class, 'index']);
    Route::post('/jobs', [JobsController::class, 'store']);

});

// Jobs Routes - semua memerlukan authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/jobs/{id}/apply', [ApplicationsController::class, 'apply']);
    Route::get('/jobs/{id}/getJobApplications', [ApplicationsController::class, 'getJobApplications']);
});