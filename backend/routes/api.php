<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProductController;

// POST /api/register
// Sample payload:
// {
//     "name": "Jane Doe",
//     "email": "jane@example.com",
//     "password": "password"
// }
Route::post('/register', [AuthController::class, 'register']);
// POST /api/login
// Sample payload:
// {
//     "email": "jane@example.com",
//     "password": "password"
// }
Route::post('/login', [AuthController::class, 'login']);

// GET /api/products
// Sample query: /api/products?search=lamp&category=Electronics
Route::get('/products', [ProductController::class, 'index']);
// GET /api/products/{id}
// Example: /api/products/1
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // POST /api/logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // GET /api/user
    Route::get('/user', [AuthController::class, 'user']);

    // POST /api/product/{id}/visit
    // Example: /api/product/1/visit
    // Headers: Authorization: Bearer <token>
    Route::post('/product/{id}/visit', [InteractionController::class, 'visit']);
    // POST /api/product/{id}/buy
    // Example: /api/product/1/buy
    // Headers: Authorization: Bearer <token>
    Route::post('/product/{id}/buy', [InteractionController::class, 'buy']);

    // GET /api/recommendations
    Route::get('/recommendations', [RecommendationController::class, 'index']);

    Route::middleware('admin')->group(function () {
        // POST /api/admin/sync-model
        // Sample payload:
        // {}
        Route::post('/admin/sync-model', [AdminController::class, 'syncModel']);

        // Admin Product Management Routes
        // GET /api/admin/products - List all products (with pagination, search, filter)
        // POST /api/admin/products - Create a new product
        // GET /api/admin/products/{id} - Get a specific product
        // PUT /api/admin/products/{id} - Update a product
        // DELETE /api/admin/products/{id} - Delete a product
        Route::apiResource('admin/products', AdminProductController::class);
        // GET /api/admin/categories - Get all categories
        Route::get('/admin/categories', [AdminProductController::class, 'categories']);
    });
});


