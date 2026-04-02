<?php

use App\AppMain\Application\User\Auth\Controllers\UserAuthController;
use App\AppMain\Application\Admin\User\Controllers\UserController;
use App\AppMain\Application\User\Checkout\Controllers\CartController;
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
| - Manage users
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
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
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

// Protected Routes (Require Passport authentication - now uses users table)
Route::middleware('auth:api')->group(function () {
    // Auth Routes (Authenticated)
    Route::prefix('auth')->group(function () {
        Route::get('/me', [UserAuthController::class, 'me']);
        Route::post('/logout', [UserAuthController::class, 'logout']);
    });

    // User Management (Actions on other users, if any, or self-management)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);           // GET /api/users
        Route::post('/', [UserController::class, 'store']);          // POST /api/users
        Route::get('/{id}', [UserController::class, 'show']);        // GET /api/users/{id}
        Route::put('/{id}', [UserController::class, 'update']);      // PUT /api/users/{id}
        Route::patch('/{id}', [UserController::class, 'update']);    // PATCH /api/users/{id}
        Route::delete('/{id}', [UserController::class, 'destroy']);  // DELETE /api/users/{id}
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
