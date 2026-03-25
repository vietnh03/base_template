# Route Service Provider & Configuration Guide

This document explains how routes are registered, customized, and protected in this project, following the Laravel 11 structure.

## 1. Route Registration (`bootstrap/app.php`)

In Laravel 11, the `RouteServiceProvider` has been replaced by `bootstrap/app.php`. This file configures routing, middleware, and exception handling.

### Default Routes
- **Web Routes (`routes/web.php`)**: For browser-based application logic (e.g., Admin CMS panel).
- **API Routes (`routes/api.php`)**: For End-User API endpoints.
- **Console Routes (`routes/console.php`)**: For Artisan commands.

### Custom Routes: Admin API (`routes/api-admin.php`)
Custom routes for the Admin API are manually registered using the `then` callback in `bootstrap/app.php`:

```php
// bootstrap/app.php
->withRouting(
    // ... standard routes
    then: function () {
        Route::prefix('api/admin')
            ->middleware('api')
            ->group(base_path('routes/api-admin.php'));
    }
)
```
**Effect**: All routes in `routes/api-admin.php` will be automatically prefixed with `/api/admin`.

---

## 2. Authentication Configuration (`config/auth.php`)

The project uses multiple guards and providers to separate User (End-User) and Admin logic.

### Guards

| Guard Name | Driver | Provider | Purpose |
| :--- | :--- | :--- | :--- |
| `web` | `session` | `users` | Standard web auth (not heavily used if API-first). |
| `api` | `passport` | `users` | **End-User API**. Uses OAuth2 via Laravel Passport. |
| `admin` | `sanctum` | `admins` | **Admin API**. Uses Token-based auth via Laravel Sanctum. |
| `admin-cms` | `session` | `admins` | **Admin Web CMS**. Uses Session cookies for Admin Panel UI. |

### Providers

| Provider Name | Model |
| :--- | :--- |
| `users` | `App\Models\User` |
| `admins` | `App\Models\Admin` |

---

## 3. Middleware Customization

### Middleware Aliases (`bootstrap/app.php`)
Middleware aliases are registered in `bootstrap/app.php` to make them available in routes.

```php
$middleware->alias([
    'auth' => \App\Http\Middleware\Authenticate::class,
    'admin.redirect' => \App\Http\Middleware\AdminRedirectIfAuthenticated::class,
]);
```

### Authenticate Middleware (`app/Http/Middleware/Authenticate.php`)
This middleware determines where to redirect unauthenticated users.

**Logic:**
1. **API Requests (`api/*`, `api/admin/*`)**: Returns `null`. This triggers the Exception Handler to return a JSON 401 error.
2. **Admin CMS Requests (`admin/*`)**: Redirects to `admin.login` route.
3. **Other Requests**: Returns `null` (default behavior).

### AdminRedirectIfAuthenticated (`app/Http/Middleware/AdminRedirectIfAuthenticated.php`)
This middleware handles "Guest Only" routes for the Admin CMS (like the Login page).

**Logic:**
- Checks if the user is authenticated via `admin-cms` guard.
- **If Yes**: Redirects to `admin.dashboard`.
- **If No**: Allows request to proceed (showing the login page).

---

## 4. Exception Handling (`bootstrap/app.php`)

Authentication exceptions (`AuthenticationException`) are customized to return consistent JSON responses for API routes.

```php
$exceptions->render(function (AuthenticationException $e, $request) {
    if ($request->is('api/*') || $request->is('api/admin/*')) {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }
});
```

---

## 5. Overview of Route Files

### `routes/api.php`
- **Prefix**: `/api` (automatically applied by Laravel)
- **Primary Guard**: `auth:api` (Passport)
- **Target**: End Users (Mobile App / Frontend Website)
- **Example**:
  ```php
  Route::prefix('auth')->group(function () {
      Route::post('/login', [AuthController::class, 'login']);
  });
  ```

### `routes/api-admin.php`
- **Prefix**: `/api/admin` (applied in `bootstrap/app.php`)
- **Primary Guard**: `auth:admin` (Sanctum)
- **Target**: Admin Dashboard / Management Tools (API calls)
- **Example**:
  ```php
  Route::middleware('auth:admin')->group(function () {
      Route::get('/users', [UserController::class, 'index']);
  });
  ```

### `routes/web.php`
- **Prefix**: `/`
- **Primary Guard**: `auth:admin-cms` (Session) for Admin Panel
- **Target**: HTML pages, Admin Login Form, Dashboard UI
- **Example**:
  ```php
  Route::middleware('admin.redirect')->get('/admin/login', ...);
  Route::middleware('auth:admin-cms')->get('/admin/dashboard', ...);
  ```
