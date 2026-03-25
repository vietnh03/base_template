<?php

use App\AppMain\Application\Admin\Auth\Controllers\AuthController;
use App\AppMain\Application\Admin\User\Controllers\UserController;
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
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);          // GET /api/admin/users
        Route::post('/', [UserController::class, 'store']);         // POST /api/admin/users
        Route::get('/{id}', [UserController::class, 'show']);       // GET /api/admin/users/{id}
        Route::put('/{id}', [UserController::class, 'update']);     // PUT /api/admin/users/{id}
        Route::patch('/{id}', [UserController::class, 'update']);   // PATCH /api/admin/users/{id}
        Route::delete('/{id}', [UserController::class, 'destroy']); // DELETE /api/admin/users/{id}
    });
});
