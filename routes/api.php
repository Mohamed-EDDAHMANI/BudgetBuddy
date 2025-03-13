<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user/name', [AuthController::class, 'getUserName']);

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']); // Register a new user
Route::post('/login', [AuthController::class, 'login']); // Login and get access token
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']); // Logout and invalidate token
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']); // Get authenticated user details

// Expense Management Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/expenses', [ExpenseController::class, 'store']); // Create a new expense
    Route::get('/expenses', [ExpenseController::class, 'index']); // List all expenses for the authenticated user
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']); // Show a specific expense
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']); // Update a specific expense
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']); // Delete a specific expense
});

// Tag Management Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tags', [TagController::class, 'store']); // Create a new tag
    Route::get('/tags', [TagController::class, 'index']); // List all tags
    Route::get('/tags/{id}', [TagController::class, 'show']); // Show a specific tag
    Route::put('/tags/{id}', [TagController::class, 'update']); // Update a specific tag
    Route::delete('/tags/{id}', [TagController::class, 'destroy']); // Delete a specific tag
    Route::post('/expenses/{id}/tags', [ExpenseController::class, 'attachTags']); // Attach tags to an expense
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
