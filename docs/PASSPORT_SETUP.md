# Laravel Passport Setup Guide (End-User API)

This guide covers setting up **Laravel Passport** for End-User API authentication (`/api/*`).

Passport provides full OAuth2 server implementation for more complex authentication scenarios.

---

## 📦 Step 1: Install Passport

```bash
composer require laravel/passport
```

---

## 📝 Step 2: Publish Migrations (Optional)

Passport automatically loads migrations, but you can publish them if you need to customize:

```bash
php artisan vendor:publish --tag=passport-migrations
```

---

## 🔧 Step 3: Update Migrations (Important for UUID)

Since this project uses **UUID** for primary keys, you need to update Passport migrations to use `foreignUuid` instead of `foreignId`:

### Files to Update:

1. **`xxxx_create_oauth_auth_codes_table.php`**
```php
Schema::create('oauth_auth_codes', function (Blueprint $table) {
    $table->char('id', 80)->primary();
    $table->foreignUuid('user_id')->index(); // ← Change from foreignId
    $table->foreignUuid('client_id');
    $table->text('scopes')->nullable();
    $table->boolean('revoked');
    $table->dateTime('expires_at')->nullable();
});
```

2. **`xxxx_create_oauth_access_tokens_table.php`**
```php
Schema::create('oauth_access_tokens', function (Blueprint $table) {
    $table->char('id', 80)->primary();
    $table->foreignUuid('user_id')->nullable()->index(); // ← Change from foreignId
    $table->foreignUuid('client_id');
    $table->string('name')->nullable();
    $table->text('scopes')->nullable();
    $table->boolean('revoked');
    $table->timestamps();
    $table->dateTime('expires_at')->nullable();
});
```

3. **`xxxx_create_oauth_device_codes_table.php`**
```php
Schema::create('oauth_device_codes', function (Blueprint $table) {
    $table->char('id', 80)->primary();
    $table->foreignUuid('user_id')->nullable()->index(); // ← Change from foreignId
    $table->foreignUuid('client_id')->index();
    $table->char('user_code', 8)->unique();
    $table->text('scopes');
    $table->boolean('revoked');
    $table->dateTime('user_approved_at')->nullable();
    $table->dateTime('last_polled_at')->nullable();
    $table->dateTime('expires_at')->nullable();
});
```

---

## 🗄️ Step 4: Run Migrations

```bash
php artisan migrate
```

This creates:
- ✅ `oauth_auth_codes`
- ✅ `oauth_access_tokens`
- ✅ `oauth_refresh_tokens`
- ✅ `oauth_clients`
- ✅ `oauth_device_codes`
- ✅ `oauth_personal_access_clients`

---

## 🔐 Step 5: Install Passport Keys

Generate encryption keys and create OAuth clients:

```bash
php artisan passport:keys
php artisan passport:client --personal
```

**Output:**
```
What should we name the personal access client? [Laravel Personal Access Client]:
> CRM Personal Access Client

Personal access client created successfully.
Client ID: 1
Client secret: xxxxxxxxxxxxxxxxxxxx
```

**Save these credentials** - you'll need them for password grant authentication.

---

## ⚙️ Step 6: Update User Model

Add `HasApiTokens` trait from Passport:

**File:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens; // ← Import Passport trait (NOT Sanctum!)

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuid; // ← Add HasApiTokens

    protected $fillable = [
        'name', 'email', 'password', 'role', 'status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

---

## 🎯 Step 7: Configure Auth Guard

**File:** `config/auth.php`

Add the `api` guard using Passport driver:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'admin' => [
        'driver' => 'sanctum',
        'provider' => 'admins',
    ],

    'api' => [
        'driver' => 'passport', // ← Passport driver
        'provider' => 'users',  // ← Points to users provider
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class,
    ],
],
```

---

## 🛡️ Step 8: Configure Token Expiration (Optional)

**File:** `app/Providers/AppServiceProvider.php`

```php
use Laravel\Passport\Passport;

public function boot(): void
{
    Passport::tokensExpireIn(now()->addDays(15));
    Passport::refreshTokensExpireIn(now()->addDays(30));
    Passport::personalAccessTokensExpireIn(now()->addMonths(6));
}
```

---

## 🧪 Step 9: Create Test User

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Test User',
    'email' => 'user@example.com',
    'password' => \Hash::make('password123'),
    'role' => 'user',
    'status' => 'active',
]);
```

---

## 🚀 Step 10: Implement Authentication

### Create User Auth Repository

**File:** `app/AppMain/Domain/Auth/Repositories/UserAuthRepository.php`

```php
<?php

namespace App\AppMain\Domain\Auth\Repositories;

use App\Models\User;

class UserAuthRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function updateLastLogin(User $user, string $ip): void
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function revokeToken($token): bool
    {
        return $token->delete();
    }
}
```

### Create User Auth Service

**File:** `app/AppMain/Domain/Auth/Services/UserAuthService.php`

```php
<?php

namespace App\AppMain\Domain\Auth\Services;

use App\AppMain\Domain\Auth\Repositories\UserAuthRepository;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\PersonalAccessTokenResult;

class UserAuthService
{
    protected UserAuthRepository $userAuthRepository;

    public function __construct(UserAuthRepository $userAuthRepository)
    {
        $this->userAuthRepository = $userAuthRepository;
    }

    public function login(string $email, string $password, string $ip): array
    {
        $user = $this->userAuthRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        if ($user->status !== 'active') {
            throw new \Exception('Account is inactive');
        }

        // Create Passport token
        $tokenResult = $user->createToken('user-token');
        $token = $tokenResult->accessToken;

        // Update last login
        $this->userAuthRepository->updateLastLogin($user, $ip);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $tokenResult->token->expires_at->diffInSeconds(now()),
        ];
    }

    public function logout($currentToken): bool
    {
        return $this->userAuthRepository->revokeToken($currentToken);
    }

    public function logoutAll($user): void
    {
        $this->userAuthRepository->revokeAllTokens($user);
    }
}
```

### Create Auth Response

**File:** `app/AppMain/Application/User/Auth/Responses/UserAuthResponse.php`

```php
<?php

namespace App\AppMain\Application\User\Auth\Responses;

class UserAuthResponse
{
    public $id;
    public $name;
    public $email;
    public $role;
    public $status;
    public $accessToken;
    public $tokenType;
    public $expiresIn;

    public static function fromLoginResult($user, array $tokenData): self
    {
        $dto = new self();

        $dto->id = $user->id;
        $dto->name = $user->name;
        $dto->email = $user->email;
        $dto->role = $user->role;
        $dto->status = $user->status;
        $dto->accessToken = $tokenData['access_token'];
        $dto->tokenType = $tokenData['token_type'];
        $dto->expiresIn = $tokenData['expires_in'];

        return $dto;
    }

    public static function fromModel($user): self
    {
        $dto = new self();

        $dto->id = $user->id;
        $dto->name = $user->name;
        $dto->email = $user->email;
        $dto->role = $user->role;
        $dto->status = $user->status;

        return $dto;
    }
}
```

### Create Auth Controller

**File:** `app/AppMain/Application/User/Auth/Controllers/AuthController.php`

```php
<?php

namespace App\AppMain\Application\User\Auth\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Application\User\Auth\Requests\LoginRequest;
use App\AppMain\Application\User\Auth\Responses\UserAuthResponse;
use App\AppMain\Domain\Auth\Services\UserAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected UserAuthService $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function login(LoginRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            $result = $this->userAuthService->login(
                $request->email,
                $request->password,
                $request->ip()
            );

            return UserAuthResponse::fromLoginResult($result['user'], $result);
        }, 'Login successful', 'Login failed');
    }

    public function me(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $user = auth('api')->user();
            return UserAuthResponse::fromModel($user);
        }, 'Profile retrieved successfully', 'Failed to retrieve profile');
    }

    public function logout(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $this->userAuthService->logout($request->user('api')->token());
            return ['message' => 'Logged out successfully'];
        }, 'Logout successful', 'Logout failed');
    }

    public function logoutAll(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $this->userAuthService->logoutAll($request->user('api'));
            return ['message' => 'Logged out from all devices successfully'];
        }, 'Logout from all devices successful', 'Logout failed');
    }
}
```

### Add Routes

**File:** `routes/api.php`

```php
use App\AppMain\Application\User\Auth\Controllers\AuthController;

// Auth Routes (Public)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Routes (Require Passport authentication)
Route::middleware('auth:api')->group(function () {
    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    });

    // Other protected routes...
});
```

---

## 🧪 Step 11: Test Login

### Login Request

```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
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
    "name": "Test User",
    "email": "user@example.com",
    "role": "user",
    "status": "active",
    "accessToken": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "tokenType": "Bearer",
    "expiresIn": 1296000
  }
}
```

### Use Token in Requests

```bash
GET /api/auth/me
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

---

## 📚 End-User API Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/login` | User login | ❌ |
| GET | `/api/auth/me` | Get user profile | ✅ |
| POST | `/api/auth/logout` | Logout current device | ✅ |
| POST | `/api/auth/logout-all` | Logout all devices | ✅ |
| GET | `/api/customers` | List customers | ✅ |
| POST | `/api/customers` | Create customer | ✅ |
| GET | `/api/customers/{id}` | Get customer | ✅ |
| PUT | `/api/customers/{id}` | Update customer | ✅ |
| DELETE | `/api/customers/{id}` | Delete customer | ✅ |

---

## 🔒 Using Passport in Code

### In Routes

```php
// routes/api.php
Route::middleware('auth:api')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
});
```

### In Controllers

```php
// Get authenticated user
$user = auth('api')->user();

// Check if authenticated
if (auth('api')->check()) {
    // User is authenticated
}

// Create token (during login)
$tokenResult = $user->createToken('user-token');
$accessToken = $tokenResult->accessToken;

// Revoke current token (during logout)
$request->user('api')->token()->delete();

// Revoke all tokens (logout all devices)
$user->tokens()->delete();
```

---

## ⚡ Quick Setup Commands

```bash
# 1. Install Passport
composer require laravel/passport

# 2. Publish migrations (optional)
php artisan vendor:publish --tag=passport-migrations

# 3. Update migrations to use foreignUuid

# 4. Run migrations
php artisan migrate

# 5. Install keys and create clients
php artisan passport:keys
php artisan passport:client --personal

# 6. Create test user (via tinker)
php artisan tinker
> User::create([...])

# 7. Test login endpoint
```

---

## 🐛 Troubleshooting

### "Class 'Laravel\Passport\HasApiTokens' not found"
```bash
composer require laravel/passport
composer dump-autoload
```

### "oauth_clients table not found"
```bash
php artisan migrate
```

### "Data truncated for column 'user_id'"
Migrations are using `foreignId()` instead of `foreignUuid()`. Update migrations and run:
```bash
php artisan migrate:fresh
```

### "Unauthenticated" response
Check:
- Token is in header: `Authorization: Bearer {token}`
- Token hasn't expired
- Using correct guard: `auth:api`

### "Key path does not exist"
```bash
php artisan passport:keys
```

---

## 📖 References

- [Laravel Passport Documentation](https://laravel.com/docs/11.x/passport)
- [OAuth2 Password Grant](https://laravel.com/docs/11.x/passport#password-grant-tokens)
