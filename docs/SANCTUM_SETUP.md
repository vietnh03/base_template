# Laravel Sanctum Setup Guide (Admin API)

This guide covers setting up **Laravel Sanctum** for Admin API authentication (`/api/admin/*`).

Sanctum provides simple token-based authentication for SPAs and mobile applications.

---

## 📦 Step 1: Install Sanctum

```bash
composer require laravel/sanctum
```

---

## 📝 Step 2: Publish Configuration

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

This creates:
- `config/sanctum.php` - Sanctum configuration
- Migration for `personal_access_tokens` table

---

## 🔧 Step 3: Update Migration (Important for UUID)

Since this project uses **UUID** for primary keys, update the Sanctum migration:

**File:** `database/migrations/xxxx_create_personal_access_tokens_table.php`

```php
Schema::create('personal_access_tokens', function (Blueprint $table) {
    $table->id();
    $table->uuidMorphs('tokenable'); // ← Change from morphs() to uuidMorphs()
    $table->text('name');
    $table->string('token', 64)->unique();
    $table->text('abilities')->nullable();
    $table->timestamp('last_used_at')->nullable();
    $table->timestamp('expires_at')->nullable()->index();
    $table->timestamps();
});
```

---

## 🗄️ Step 4: Run Migrations

```bash
php artisan migrate
```

This creates:
- ✅ `personal_access_tokens` table
- ✅ `admins` table (already exists in project)

---

## ⚙️ Step 5: Update Admin Model

Add `HasApiTokens` trait from Sanctum:

**File:** `app/Models/Admin.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // ← Import Sanctum trait
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes; // ← Add HasApiTokens

    protected $fillable = [
        'name', 'email', 'password', 'role', 'status',
        'permissions', 'last_login_at', 'last_login_ip'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'permissions' => 'array',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
```

---

## 🎯 Step 6: Configure Auth Guard

**File:** `config/auth.php`

Add the `admin` guard using Sanctum driver:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'admin' => [
        'driver' => 'sanctum',  // ← Sanctum driver
        'provider' => 'admins', // ← Points to admins provider
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class, // ← Admin model
    ],
],
```

---

## 🛡️ Step 7: Configure Middleware

**File:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'auth' => \App\Http\Middleware\Authenticate::class,
    ]);
})
```

**File:** `app/Http/Middleware/Authenticate.php`

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, return null to trigger JSON response
        if ($request->is('api/*') || $request->is('api/admin/*')) {
            return null;
        }

        // For web requests, redirect to login route
        return route('login');
    }
}
```

---

## 🧪 Step 8: Create Test Admin

```bash
php artisan admin:create \
  --name="Super Admin" \
  --email="admin@crm.com" \
  --password="password123" \
  --role=super_admin
```

---

## 🚀 Step 9: Test Login

### Login Request

```bash
POST /api/admin/auth/login
Content-Type: application/json

{
  "email": "admin@crm.com",
  "password": "password123"
}
```

### Response

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "id": "uuid-here",
    "name": "Super Admin",
    "email": "admin@crm.com",
    "role": "super_admin",
    "status": "active",
    "permissions": null,
    "lastLoginAt": "2025-12-21 10:30:00",
    "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456789", // Sanctum token
    "tokenType": "Bearer"
  }
}
```

### Use Token in Requests

```bash
GET /api/admin/auth/me
Authorization: Bearer 1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456789
```

---

## 📚 Admin API Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/admin/auth/login` | Admin login | ❌ |
| GET | `/api/admin/auth/me` | Get admin profile | ✅ |
| POST | `/api/admin/auth/logout` | Logout current device | ✅ |
| POST | `/api/admin/auth/logout-all` | Logout all devices | ✅ |
| GET | `/api/admin/users` | List all users | ✅ |
| POST | `/api/admin/users` | Create new user | ✅ |
| GET | `/api/admin/users/{id}` | Get user details | ✅ |
| PUT | `/api/admin/users/{id}` | Update user | ✅ |
| DELETE | `/api/admin/users/{id}` | Delete user | ✅ |

---

## 🔒 Using Sanctum in Code

### In Routes

```php
// routes/api-admin.php
Route::middleware('auth:admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

### In Controllers

```php
// Get authenticated admin
$admin = auth('admin')->user();

// Check if authenticated
if (auth('admin')->check()) {
    // User is authenticated
}

// Create token (during login)
$token = $admin->createToken('admin-token')->plainTextToken;

// Revoke current token (during logout)
$request->user('admin')->currentAccessToken()->delete();

// Revoke all tokens (logout all devices)
$admin->tokens()->delete();
```

---

## ⚡ Quick Setup Commands

```bash
# 1. Install Sanctum
composer require laravel/sanctum

# 2. Publish config
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# 3. Update migration to use uuidMorphs

# 4. Run migration
php artisan migrate

# 5. Create test admin
php artisan admin:create --email="admin@crm.com" --role=super_admin

# 6. Test login endpoint
```

---

## 🐛 Troubleshooting

### "Class 'Laravel\Sanctum\HasApiTokens' not found"
```bash
composer require laravel/sanctum
composer dump-autoload
```

### "personal_access_tokens table not found"
```bash
php artisan migrate
```

### "Data truncated for column 'tokenable_id'"
The migration is using `morphs()` instead of `uuidMorphs()`. Update the migration and run:
```bash
php artisan migrate:fresh
```

### "Unauthenticated" response
Check:
- Token is in header: `Authorization: Bearer {token}`
- Token hasn't expired
- Using correct guard: `auth:admin`

---

## 📖 References

- [Laravel Sanctum Documentation](https://laravel.com/docs/11.x/sanctum)
- [Sanctum Token Authentication](https://laravel.com/docs/11.x/sanctum#spa-authentication)
