<?php

use App\AppMain\Application\Admin\Auth\Controllers\AuthController;
use App\AppMain\Application\Admin\Cms\Controllers\CmsSectionController;
use App\AppMain\Application\Admin\User\Controllers\UserController;
use App\AppMain\Application\Admin\Catalog\Controllers\TagController;
use App\AppMain\Application\Admin\Catalog\Controllers\CategoryController;
use App\AppMain\Application\Admin\Catalog\Controllers\ProductController;
use App\AppMain\Application\Admin\Catalog\Controllers\AttributeController;
use App\AppMain\Application\Admin\Catalog\Controllers\AttributeFamilyController;
use App\AppMain\Application\Admin\Sales\Controllers\OrderController;
use App\AppMain\Application\Admin\Sales\Controllers\InvoiceController;
use App\AppMain\Application\Admin\Sales\Controllers\OrderTransactionController;
use App\AppMain\Application\Admin\Post\Controllers\PostController;
use App\AppMain\Application\Admin\Post\Controllers\PostCategoryController;
use App\AppMain\Application\Admin\Post\Controllers\PostTagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Platform Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Platform Admin API routes.
| These are for CRM platform administrators (super admin).
| These routes will use Sanctum authentication.
| Prefix: /api/admin
|
| Use cases:
| - Manage users
| - Manage tenants/organizations
| - System configuration
| - Platform-wide analytics
|
*/

// Auth Routes (Public - No authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);        // POST /api/admin/auth/login
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // POST /api/admin/auth/forgot-password
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);   // POST /api/admin/auth/reset-password
});

// Protected Routes (Require Sanctum authentication)
Route::middleware('auth:admin')->group(function () {
    // Auth Routes (Authenticated)
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);           // GET /api/admin/auth/me
        Route::post('/logout', [AuthController::class, 'logout']);  // POST /api/admin/auth/logout
        Route::post('/logout-all', [AuthController::class, 'logoutAll']); // POST /api/admin/auth/logout-all
    });

    // User Management (for Platform Admins)
    Route::middleware('auth:admin')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);          // GET /api/admin/users
        Route::post('/', [UserController::class, 'store']);         // POST /api/admin/users
        Route::get('/{id}', [UserController::class, 'show']);       // GET /api/admin/users/{id}
        Route::put('/{id}', [UserController::class, 'update']);     // PUT /api/admin/users/{id}
        Route::patch('/{id}', [UserController::class, 'update']);   // PATCH /api/admin/users/{id}
        Route::delete('/{id}', [UserController::class, 'destroy']); // DELETE /api/admin/users/{id}
    });
});

// Catalog Management (Temporarily PUBLIC for testing)
Route::middleware('auth:admin')->group(function () {
    Route::prefix('catalog')->group(function () {
        // Tag Management
        Route::prefix('tags')->group(function () {
            Route::get('/', [TagController::class, 'index']);          // GET /api/admin/catalog/tags
            Route::post('/', [TagController::class, 'store']);         // POST /api/admin/catalog/tags
            Route::get('/{id}', [TagController::class, 'show']);       // GET /api/admin/catalog/tags/{id}
            Route::put('/{id}', [TagController::class, 'update']);     // PUT /api/admin/catalog/tags/{id}
            Route::delete('/{id}', [TagController::class, 'destroy']); // DELETE /api/admin/catalog/tags/{id}
        });

        // Category Management
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);          // GET /api/admin/catalog/categories
            Route::post('/', [CategoryController::class, 'store']);         // POST /api/admin/catalog/categories
            Route::get('/{id}', [CategoryController::class, 'show']);       // GET /api/admin/catalog/categories/{id}
            Route::put('/{id}', [CategoryController::class, 'update']);     // PUT /api/admin/catalog/categories/{id}
            Route::delete('/{id}', [CategoryController::class, 'destroy']); // DELETE /api/admin/catalog/categories/{id}
        });

        // Product Management
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);          // GET /api/admin/catalog/products
            Route::post('/', [ProductController::class, 'store']);         // POST /api/admin/catalog/products
            Route::get('/{id}', [ProductController::class, 'show']);       // GET /api/admin/catalog/products/{id}
            Route::put('/{id}', [ProductController::class, 'update']);     // PUT /api/admin/catalog/products/{id}
            Route::delete('/{id}', [ProductController::class, 'destroy']); // DELETE /api/admin/catalog/products/{id}
        });

        // Attribute Management
        Route::prefix('attributes')->group(function () {
            Route::get('/', [AttributeController::class, 'index']);          // GET /api/admin/catalog/attributes
            Route::post('/', [AttributeController::class, 'store']);         // POST /api/admin/catalog/attributes
            Route::get('/{id}', [AttributeController::class, 'show']);       // GET /api/admin/catalog/attributes/{id}
            Route::put('/{id}', [AttributeController::class, 'update']);     // PUT /api/admin/catalog/attributes/{id}
            Route::delete('/{id}', [AttributeController::class, 'destroy']); // DELETE /api/admin/catalog/attributes/{id}
        });

        // Attribute Family Management
        Route::prefix('attribute-families')->group(function () {
            Route::get('/', [AttributeFamilyController::class, 'index']);          // GET /api/admin/catalog/attribute-families
            Route::post('/', [AttributeFamilyController::class, 'store']);         // POST /api/admin/catalog/attribute-families
            Route::get('/{id}', [AttributeFamilyController::class, 'show']);       // GET /api/admin/catalog/attribute-families/{id}
            Route::put('/{id}', [AttributeFamilyController::class, 'update']);     // PUT /api/admin/catalog/attribute-families/{id}
            Route::delete('/{id}', [AttributeFamilyController::class, 'destroy']); // DELETE /api/admin/catalog/attribute-families/{id}
        });
    });

    // Sales Management
    Route::prefix('sales')->group(function () {
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);           // GET /api/admin/sales/orders
            Route::post('/', [OrderController::class, 'store']);          // POST /api/admin/sales/orders
            Route::get('/{id}', [OrderController::class, 'show']);        // GET /api/admin/sales/orders/{id}
            Route::put('/{id}/status', [OrderController::class, 'updateStatus']); // PUT /api/admin/sales/orders/{id}/status
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']);   // POST /api/admin/sales/orders/{id}/cancel
        });

        Route::prefix('invoices')->group(function () {
            Route::get('/', [InvoiceController::class, 'index']);
            Route::post('/', [InvoiceController::class, 'store']);
            Route::get('/{id}', [InvoiceController::class, 'show']);
        });

        Route::prefix('transactions')->group(function () {
            Route::get('/', [OrderTransactionController::class, 'index']);
            Route::post('/', [OrderTransactionController::class, 'store']);
            Route::get('/{id}', [OrderTransactionController::class, 'show']);
        });
    });

    // CMS Management
    Route::prefix('cms')->group(function () {
        Route::prefix('sections')->group(function () {
            Route::put('/{id}', [CmsSectionController::class, 'update']);
        });
    });

    // Post Management
    Route::prefix('posts')->group(function () {
        // Post Category Management
        Route::prefix('categories')->group(function () {
            Route::get('/', [PostCategoryController::class, 'index']);
            Route::post('/', [PostCategoryController::class, 'store']);
            Route::get('/{id}', [PostCategoryController::class, 'show']);
            Route::put('/{id}', [PostCategoryController::class, 'update']);
            Route::delete('/{id}', [PostCategoryController::class, 'destroy']);
        });

        // Post Tag Management
        Route::prefix('tags')->group(function () {
            Route::get('/', [PostTagController::class, 'index']);
            Route::post('/', [PostTagController::class, 'store']);
            Route::get('/{id}', [PostTagController::class, 'show']);
            Route::put('/{id}', [PostTagController::class, 'update']);
            Route::delete('/{id}', [PostTagController::class, 'destroy']);
        });

        // Post Management
        Route::get('/', [PostController::class, 'index']);
        Route::post('/', [PostController::class, 'store']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });
});