<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- Public Routes (Bina login ke chalenge) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// --- Protected Routes (Sirf login ke baad chalenge) ---
Route::middleware('auth:sanctum')->group(function () {
    // User Management Endpoints
    Route::get('/users', [UserController::class, 'index']);      // Get List[cite: 1]
    Route::get('/users/{user}', [UserController::class, 'show']); // Single User
    Route::put('/users/{user}', [UserController::class, 'update']); // Update
    Route::delete('/users/{user}', [UserController::class, 'destroy']); // Delete

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});