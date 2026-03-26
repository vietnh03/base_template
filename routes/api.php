<?php

use App\AppMain\Application\User\Auth\Controllers\AuthController;
use App\AppMain\Application\User\Customer\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| End-User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register End-User API routes.
| These are for business/tenant users (CRM users).
| These routes will use Passport authentication.
| Prefix: /api
|
| Use cases:
| - Manage customers
| - Manage orders
| - Business operations
|
*/

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'OK'], 200);
});

// Auth Routes (Public - No authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);  // POST /api/auth/register
    Route::post('/login', [AuthController::class, 'login']);        // POST /api/auth/login
});

// Protected Routes (Require Passport authentication)
Route::middleware('auth:api')->group(function () {
    // Auth Routes (Authenticated)
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);           // GET /api/auth/me
        Route::post('/logout', [AuthController::class, 'logout']);  // POST /api/auth/logout
        Route::post('/logout-all', [AuthController::class, 'logoutAll']); // POST /api/auth/logout-all
    });

    // Admin API Routes
    Route::prefix('admin')->group(function () {
        // Product Management
        Route::prefix('products')->group(function () {
            Route::get('/', [\App\AppMain\Application\Admin\Product\Controllers\ProductController::class, 'index']);
            Route::post('/', [\App\AppMain\Application\Admin\Product\Controllers\ProductController::class, 'store']);
            Route::get('/{id}', [\App\AppMain\Application\Admin\Product\Controllers\ProductController::class, 'show']);
            Route::put('/{id}', [\App\AppMain\Application\Admin\Product\Controllers\ProductController::class, 'update']);
            Route::delete('/{id}', [\App\AppMain\Application\Admin\Product\Controllers\ProductController::class, 'destroy']);
            Route::post('/{id}/images', [\App\AppMain\Application\Admin\Product\Controllers\ProductController::class, 'uploadImage']);
            Route::put('/{id}/inventory', [\App\AppMain\Application\Admin\Product\Controllers\ProductController::class, 'updateInventory']);
        });

        // Category Management
        Route::prefix('categories')->group(function () {
            Route::get('/', [\App\AppMain\Application\Admin\Category\Controllers\CategoryController::class, 'index']);
            Route::post('/', [\App\AppMain\Application\Admin\Category\Controllers\CategoryController::class, 'store']);
            Route::get('/{id}', [\App\AppMain\Application\Admin\Category\Controllers\CategoryController::class, 'show']);
            Route::put('/{id}', [\App\AppMain\Application\Admin\Category\Controllers\CategoryController::class, 'update']);
            Route::delete('/{id}', [\App\AppMain\Application\Admin\Category\Controllers\CategoryController::class, 'destroy']);
        });
    });

    // Web API Routes
    Route::prefix('web')->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [\App\AppMain\Application\Web\Product\Controllers\ProductController::class, 'index']);
            Route::get('/{url_key}', [\App\AppMain\Application\Web\Product\Controllers\ProductController::class, 'show']);
        });
    });

    // Customer Management (for Business/Tenant users)
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);           // GET /api/customers
        Route::post('/', [CustomerController::class, 'store']);          // POST /api/customers
        Route::get('/{id}', [CustomerController::class, 'show']);        // GET /api/customers/{id}
        Route::put('/{id}', [CustomerController::class, 'update']);      // PUT /api/customers/{id}
        Route::patch('/{id}', [CustomerController::class, 'update']);    // PATCH /api/customers/{id}
        Route::delete('/{id}', [CustomerController::class, 'destroy']);  // DELETE /api/customers/{id}
    });
});
