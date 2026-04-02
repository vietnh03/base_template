<?php

use App\AppMain\Application\User\Auth\Controllers\AuthController;
use App\AppMain\Application\User\Checkout\Controllers\CartController;
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

// CMS Routes (Public - serves frontend page data)
Route::prefix('cms')->group(function () {
    Route::get('/pages/{slug}', [\App\AppMain\Application\Api\Cms\Controllers\CmsController::class, 'getPageData']); // GET /api/cms/pages/{slug}
});

// Post Routes (Public - serves frontend post data)
Route::prefix('posts')->group(function () {
    Route::get('/', [\App\AppMain\Application\Api\Post\Controllers\PostController::class, 'index']);      // GET /api/posts
    Route::get('/categories', [\App\AppMain\Application\Api\Post\Controllers\PostController::class, 'categories']); // GET /api/posts/categories
    Route::get('/{id}', [\App\AppMain\Application\Api\Post\Controllers\PostController::class, 'show']); // GET /api/posts/{id}
});

// Protected Routes (Require Passport authentication)
Route::middleware('auth:api')->group(function () {
    // Auth Routes (Authenticated)
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);           // GET /api/auth/me
        Route::post('/logout', [AuthController::class, 'logout']);  // POST /api/auth/logout
        Route::post('/logout-all', [AuthController::class, 'logoutAll']); // POST /api/auth/logout-all
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

    // Cart and Checkout endpoints
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'get']);
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/update/{itemId}', [CartController::class, 'update']);
        Route::delete('/remove/{itemId}', [CartController::class, 'remove']);
        Route::post('/checkout', [CartController::class, 'checkout']);
    });
});
