<?php

use Illuminate\Support\Facades\Route;
use App\AppMain\CMS\Auth\AuthController;
use App\AppMain\CMS\Dashboard\DashboardController;
use App\AppMain\CMS\Component\ComponentController;
use App\AppMain\CMS\User\UserController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Admin Routes Documentation
|--------------------------------------------------------------------------
|
| Route: GET /admin (Login Page)
| Middleware: admin.redirect (AdminRedirectIfAuthenticated)
|
| Flow Scenarios:
|
| 1. NOT logged in → Access /admin:
|    → Middleware check: Auth::guard('admin-cms')->check() = FALSE
|    → return $next($request) → Pass to Controller
|    → AuthController::showLoginForm() → return view('admin.auth.login')
|    → Display login form ✅
|
| 2. LOGGED IN → Access /admin:
|    → Middleware check: Auth::guard('admin-cms')->check() = TRUE
|    → return redirect()->route('admin.dashboard')
|    → Redirect to /admin/dashboard ✅
|    → Controller never executed
|
|--------------------------------------------------------------------------
| Route: GET /admin/dashboard (Dashboard Page)
| Middleware: auth:admin-cms (Authenticate)
|
| Flow Scenarios:
|
| 1. NOT logged in → Access /admin/dashboard:
|    → Middleware check: Auth::guard('admin-cms')->check() = FALSE
|    → Call Authenticate::redirectTo($request)
|    → Detect path 'admin/*' → return route('admin.login')
|    → Redirect to /admin (login page) ✅
|
| 2. LOGGED IN → Access /admin/dashboard:
|    → Middleware check: Auth::guard('admin-cms')->check() = TRUE
|    → Allow request to proceed
|    → DashboardController::index() executed
|    → Display dashboard ✅
|
|--------------------------------------------------------------------------
*/

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Login Routes (Guest only - redirect to dashboard if already logged in)
    Route::middleware('admin.redirect')->group(function () {
        Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    // Admin Authenticated Routes
    Route::middleware('auth:admin-cms')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/component', [ComponentController::class, 'index'])->name('component.index');

        // User Management Routes
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{userId}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{userId}', [UserController::class, 'update'])->name('update');
            Route::delete('/{userId}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    /*
    |--------------------------------------------------------------------------
    | Fallback Route (Catch-all for undefined admin paths)
    |--------------------------------------------------------------------------
    |
    | Purpose: Handle any undefined routes within /admin/* prefix
    | Examples: /admin/xyz, /admin/settings/test, /admin/random-path
    |
    | Flow Scenarios:
    |
    | 1. NOT logged in → Access /admin/xyz (undefined route):
    |    → Route not found in defined routes
    |    → Fallback route catches the request
    |    → Check: auth()->guard('admin-cms')->check() = FALSE
    |    → return redirect()->route('admin.login')
    |    → Redirect to /admin (login page) ✅
    |    → Prevents 404 error for unauthenticated users
    |
    | 2. LOGGED IN → Access /admin/xyz (undefined route):
    |    → Route not found in defined routes
    |    → Fallback route catches the request
    |    → Check: auth()->guard('admin-cms')->check() = TRUE
    |    → return redirect()->route('admin.dashboard')
    |    → Redirect to /admin/dashboard ✅
    |    → Logged-in users always land on dashboard instead of 404
    |
    | Benefits:
    | - Improves UX: No 404 errors for mistyped URLs
    | - Security: Unauthorized users redirected to login
    | - Consistency: All admin routes protected with same logic
    |
    |--------------------------------------------------------------------------
    */
    Route::fallback(function () {
        if (auth()->guard('admin-cms')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });
});
