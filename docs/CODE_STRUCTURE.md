# Code Structure - DDD Architecture Guide

## Architecture Overview

This project uses **Domain-Driven Design (DDD)** architecture combined with **Service-Repository Pattern** to clearly separate different layers:

```
app/AppMain/
├── Core/                   # Base classes and shared utilities
│   ├── Traits/            # Reusable traits
│   ├── Helpers/           # Helper functions & Services
│   │   └── FileUploadService.php
│   ├── common.php         # Shared helper functions
│   ├── response.php       # JSON response helpers
│   └── Controller.php     # Base Controller
│
├── Application/            # Application Layer - API (Controllers, Requests, Responses)
│   ├── User/              # End-User API Context
│   │   ├── Auth/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   └── Responses/
│   │   ├── Customer/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   └── Responses/
│   │   ├── Profile/
│   │   ├── Category/
│   │   ├── Upload/
│   │   ├── ProfileSection/
│   │   └── BizProfileJob/
│   └── Admin/             # Admin API Context (future)
│
├── CMS/                    # CMS Layer - Admin Web Interface (Controllers, Services)
│   ├── Auth/              # Admin authentication (login, logout)
│   │   ├── AuthController.php
│   │   └── AuthService.php
│   ├── Dashboard/         # Dashboard module
│   │   ├── DashboardController.php
│   │   └── DashboardService.php
│   ├── User/              # User management CRUD
│   │   ├── UserController.php
│   │   └── UserService.php
│   └── Component/         # UI Components showcase
│       └── ComponentController.php
│
└── Domain/                 # Domain Layer (Services, Repositories, DTOs)
    ├── Auth/
    │   ├── Services/
    │   └── Repositories/
    │       ├── UserAuthRepository.php
    │       └── AdminAuthRepository.php
    ├── User/
    │   ├── Services/
    │   │   └── UserService.php
    │   └── Repositories/
    │       └── UserRepository.php
    ├── Customer/
    │   ├── Services/
    │   ├── Repositories/
    │   └── DTOs/
    ├── Profile/
    ├── Category/
    ├── ProfileSection/
    └── BizProfileJob/
```

---

## 1. Core Base Classes

### 1.1 Controller (Base Controller)

**File:** `app/AppMain/Core/Controller.php`

**Extends:** `Illuminate\Routing\Controller`

**Description:** Base Controller provides wrapper methods to handle action logic with automatic error handling.

**Methods:**

- **`baseAction(Closure $closure, $messageSuccess, $messageError, ...$params)`**
  - Wraps action logic in try-catch
  - Automatically logs errors when debug mode is enabled
  - Returns JSON response with success/fail status
  - Supports exception message mapping based on error code

- **`baseActionTransaction(Closure $closure, $messageSuccess, $messageError, ...$params)`**
  - Same as `baseAction` but wrapped in database transaction
  - Automatically rolls back on exception

**Usage Example:**

```php
public function register(RegisterRequest $request)
{
    return $this->baseAction(function () use ($request) {
        $result = $this->userAuthService->register(
            $request->name,
            $request->email,
            $request->password,
            $request->ip()
        );

        return UserAuthResponse::fromLoginResult($result['user'], $result);
    }, 'Registration successful', 'Registration failed');
}
```

---

### 1.2 BaseFormRequest (Form Validation)

**File:** `app/AppMain/Core/BaseFormRequest.php`

**Extends:** `Illuminate\Foundation\Http\FormRequest`

**Description:** Base class for all Form Request validation with auto-binding of validated data to properties.

**Methods:**

- **`authorize()`** - Defaults to return true (no authorization check)
- **`failedValidation(Validator $validator)`** - Returns JSON response instead of HTML when validation fails
- **`passedValidation()`** - Automatically maps validated data to class properties

**Usage Example:**

```php
class RegisterRequest extends BaseFormRequest
{
    public $name;
    public $email;
    public $password;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(6)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.email' => 'Email must be a valid email address',
        ];
    }
}
```

**Note:** After validation passes, properties `$name`, `$email`, `$password` will be automatically assigned values from validated data.

---

### 1.3 BaseResponseDTO (Response Data Transfer Object)

**File:** `app/AppMain/Core/BaseResponseDTO.php`

**Description:** Base class for Response DTOs, transforming Model/Collection into standard format for API responses.

**Abstract Methods:**

- **`fromModel($model)`** - Must be implemented to map model to DTO properties

**Transformation Methods:**

- **`single($model)`** - Convert single model to DTO
- **`collection($models)`** - Convert array/Collection of models to array of DTOs
- **`paginated($paginatedData)`** - Transform paginated data with metadata (current_page, total, etc.)
- **`transform($data)`** - Smart detection, automatically identifies data type and transforms accordingly
- **`fromArray($data)`** - Create DTO from array or stdClass

**Usage Example:**

```php
class UserAuthResponse extends BaseResponseDTO
{
    public $id;
    public $name;
    public $email;
    public $role;
    public $status;
    public $accessToken;
    public $tokenType;
    public $expiresAt;

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

    public static function fromLoginResult($user, array $tokenData): self
    {
        $dto = self::fromModel($user);
        $dto->accessToken = $tokenData['access_token'];
        $dto->tokenType = $tokenData['token_type'];
        $dto->expiresAt = $tokenData['expires_at'];
        return $dto;
    }
}
```

---

### 1.4 BaseRepository (Data Access Layer)

**File:** `app/AppMain/Core/BaseRepository.php`

**Description:** Base Repository provides standard CRUD methods and filtering operations.

**Abstract Methods:**

- **`getModel()`** - Child class must implement to specify the Model

**CRUD Operations:**

- **`findById($id)`** - Find record by primary key
- **`create(array $data)`** - Create new record
- **`update($id, array $data)`** - Update record, returns boolean
- **`delete($id)`** - Delete record, returns boolean
- **`newQuery()`** - Get fresh query builder instance

**Filtering Methods:**

- **`applyExactFilters(Builder $query, array $filters)`** - Apply WHERE clause with exact match
- **`applyLikeFilters(Builder $query, array $filters)`** - Apply LIKE pattern matching
- **`applyDateFilters(Builder $query, array $dateFilters)`** - Filter by date range (from/to/exact)
- **`applyGlobalSearch(Builder $query, array $searchableFields, ?string $searchTerm)`** - Search across multiple fields

**Sorting & Pagination:**

- **`applySorting(Builder $query, ?string $sortBy, string $sortDirection)`** - Add ORDER BY clause
- **`applySortingFilter(Builder $query, $filters)`** - Apply sorting from filter array
- **`applyPagination(Builder $query, $filters)`** - Standard pagination
- **`applyCursorPagination(Builder $query, $filters)`** - Cursor-based pagination

**Usage Example:**

```php
class UserAuthRepository extends BaseRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
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
}
```

---

### 1.5 BaseDTO (Data Transfer Object)

**File:** `app/AppMain/Core/BaseDTO.php`

**Description:** Simple DTO for transferring data between layers.

**Methods:**

- **`__construct(array $data)`** - Maps array keys to object properties
- **`fromRequest($request)`** - Create DTO from validated request
- **`fromArray(array $data)`** - Create DTO from array
- **`toArray()`** - Convert DTO to array
- **`onlyFilled()`** - Returns array with only non-null values (useful for partial updates)

---

### 1.6 BaseFilterDTO (Filter Data Transfer Object)

**File:** `app/AppMain/Core/BaseFilterDTO.php`

**Description:** Specialized DTO for filtering, sorting, and pagination.

**Base Properties:**

- `$search` - Search term
- `$sort_by` - Field to sort by
- `$sort_direction` - Direction (asc/desc), defaults to 'asc'
- `$per_page` - Items per page, defaults to 15
- `$limit` - Result limit

**Key Methods:**

- **`fromRequest(Request $request)`** - Create filter DTO from request parameters
- **`validate()`** - Get combined validation rules
- **`getBaseValidationRules()`** - Base validation rules
- **`getSpecificValidationRules()`** - Override in child class for custom rules
- **`getAllowedSortFields()`** - Specify allowed sort fields
- **`toArray()`** - Convert to array
- **`getFiltersOnly()`** - Get filters excluding pagination/sorting
- **`hasFilters()`** - Check if any filter is applied
- **`getPaginationSettings()`** - Get per_page and limit
- **`getSortingSettings()`** - Get sort_by and sort_direction
- **`shouldPaginate()`** - Returns true if limit is null

---

### 1.7 BaseModel (Eloquent Model Base)

**File:** `app/AppMain/Core/BaseModel.php`

**Extends:** `Illuminate\Database\Eloquent\Model`

**Description:** Base Model with flexible database connection handling.

**Methods:**

- **`getQuery($databaseConnection = null)`** - Create query builder on specified connection

---

**Usage Example:**

```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasApiTokens;
}
```

---

### 1.8 Helper Functions

#### Response Helpers (`app/AppMain/Core/Helpers/response.php`)

**Constants:**
- `RESPONSE_STATUS_SUCCESS = 1`
- `RESPONSE_STATUS_FAIL = 0`
- `HTTP_CODE_SUCCESS = 200`
- `HTTP_CODE_UNAUTHORIZED = 401`

**Functions:**
- **`responseJsonSuccess($data, $message)`** - Return success response
- **`responseJsonFail($message, $httpCode, $errors)`** - Return failure response
- **`responseJsonFailMultipleErrors($errors, $message, $httpCode)`** - Return failure with multiple errors

#### Common Helpers (`app/AppMain/Core/Helpers/common.php`)

**Functions:**
- **`formatString($string)`** - Convert string to slug format (spaces to hyphens, remove special chars)

---

## 2. Module Structure Following DDD Pattern

When creating a new module (e.g., Customer Management), create files following this structure:

### 2.1 Application Layer (User-facing layer)

**Location:** `app/AppMain/Application/{Context}/{Module}/`

**Contains:**

#### a) Controllers (`Controllers/`)

Handle HTTP requests, call Services, return Responses.

```php
namespace App\AppMain\Application\User\Customer\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Application\User\Customer\Requests\StoreCustomerRequest;
use App\AppMain\Application\User\Customer\Requests\UpdateCustomerRequest;
use App\AppMain\Application\User\Customer\Responses\CustomerResponse;
use App\AppMain\Domain\Customer\Services\CustomerService;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function store(StoreCustomerRequest $request)
    {
        return $this->baseActionTransaction(function () use ($request) {
            $customer = $this->customerService->createCustomer($request->toArray());
            return CustomerResponse::fromModel($customer);
        }, 'Customer created successfully', 'Failed to create customer');
    }

    public function index(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $filters = CustomerFilterDTO::fromRequest($request);
            $customers = $this->customerService->getCustomers($filters);
            return CustomerResponse::transform($customers);
        }, 'Customers retrieved successfully', 'Failed to retrieve customers');
    }
}
```

#### b) Requests (`Requests/`)

Validate input data, extend from `BaseFormRequest`.

```php
namespace App\AppMain\Application\User\Customer\Requests;

use App\AppMain\Core\BaseFormRequest;

class StoreCustomerRequest extends BaseFormRequest
{
    public $name;
    public $email;
    public $phone;
    public $address;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required',
            'email.email' => 'Email must be valid',
            'email.unique' => 'This email is already registered',
        ];
    }
}
```

#### c) Responses (`Responses/`)

Transform data into standard format for API responses, extend from `BaseResponseDTO`.

```php
namespace App\AppMain\Application\User\Customer\Responses;

use App\AppMain\Core\BaseResponseDTO;

class CustomerResponse extends BaseResponseDTO
{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $status;
    public $createdAt;
    public $updatedAt;

    public static function fromModel($customer): self
    {
        $dto = new self();
        $dto->id = $customer->id;
        $dto->name = $customer->name;
        $dto->email = $customer->email;
        $dto->phone = $customer->phone;
        $dto->address = $customer->address;
        $dto->status = $customer->status;
        $dto->createdAt = $customer->created_at?->toIso8601String();
        $dto->updatedAt = $customer->updated_at?->toIso8601String();
        return $dto;
    }
}
```

---

### 2.2 Domain Layer (Business logic layer)

**Location:** `app/AppMain/Domain/{Module}/`

**Contains:**

#### a) Services (`Services/`)

Contains business logic, orchestrates between multiple repositories.

```php
namespace App\AppMain\Domain\Customer\Services;

use App\AppMain\Domain\Customer\Repositories\CustomerRepository;
use App\AppMain\Domain\Customer\DTOs\CustomerFilterDTO;

class CustomerService
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function createCustomer(array $data)
    {
        // Business logic validation
        if (empty($data['name'])) {
            throw new \Exception('Customer name cannot be empty');
        }

        // Create customer
        return $this->customerRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'status' => 'active',
        ]);
    }

    public function getCustomers(CustomerFilterDTO $filters)
    {
        $query = $this->customerRepository->newQuery();

        // Apply filters
        if ($filters->hasFilters()) {
            $exactFilters = $filters->getFiltersOnly();
            $this->customerRepository->applyExactFilters($query, $exactFilters);
        }

        // Apply search
        if ($filters->search) {
            $this->customerRepository->applyGlobalSearch(
                $query,
                ['name', 'email', 'phone'],
                $filters->search
            );
        }

        // Apply sorting
        $this->customerRepository->applySortingFilter($query, $filters);

        // Apply pagination
        if ($filters->shouldPaginate()) {
            return $this->customerRepository->applyPagination($query, $filters);
        }

        return $query->limit($filters->limit)->get();
    }

    public function updateCustomer($id, array $data)
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            throw new \Exception('Customer not found', 404);
        }

        return $this->customerRepository->update($id, $data);
    }

    public function deleteCustomer($id)
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            throw new \Exception('Customer not found', 404);
        }

        return $this->customerRepository->delete($id);
    }
}
```

#### b) Repositories (`Repositories/`)

Data access layer, interacts directly with database, extends from `BaseRepository`.

```php
namespace App\AppMain\Domain\Customer\Repositories;

use App\AppMain\Core\BaseRepository;
use App\Models\Customer;

class CustomerRepository extends BaseRepository
{
    protected Customer $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findActiveCustomers()
    {
        return $this->model->where('status', 'active')->get();
    }
}
```

#### c) DTOs (`DTOs/`)

Data Transfer Objects for domain logic, extend from `BaseDTO` or `BaseFilterDTO`.

**CustomerFilterDTO.php** (Filter DTO):

```php
namespace App\AppMain\Domain\Customer\DTOs;

use App\AppMain\Core\BaseFilterDTO;

class CustomerFilterDTO extends BaseFilterDTO
{
    public $status;
    public $email;

    protected function initializeSpecificFields(array $data): void
    {
        $this->status = $data['status'] ?? null;
        $this->email = $data['email'] ?? null;
    }

    protected function getSpecificValidationRules(): array
    {
        return [
            'status' => 'nullable|in:active,inactive',
            'email' => 'nullable|email',
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'name', 'email', 'created_at', 'updated_at'];
    }

    protected function getSpecificArray(): array
    {
        return [
            'status' => $this->status,
            'email' => $this->email,
        ];
    }
}
```

**CreateCustomerDTO.php** (Simple DTO):

```php
namespace App\AppMain\Domain\Customer\DTOs;

use App\AppMain\Core\BaseDTO;

class CreateCustomerDTO extends BaseDTO
{
    public $name;
    public $email;
    public $phone;
    public $address;
}
```

---

## 3. Complete Flow When Creating a New Module

### Step 1: Create Directory Structure

```
app/AppMain/
├── Application/
│   └── User/
│       └── Customer/
│           ├── Controllers/
│           ├── Requests/
│           └── Responses/
└── Domain/
    └── Customer/
        ├── Services/
        ├── Repositories/
        └── DTOs/
```

### Step 2: Create Model (if not exists)

```php
// app/Models/Customer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];
}
```

### Step 3: Create Domain Layer (from inside out)

1. **Repository** (`Domain/Customer/Repositories/CustomerRepository.php`)
   - Extend `BaseRepository`
   - Implement `getModel()`
   - Add custom query methods if needed

2. **DTOs** (`Domain/Customer/DTOs/`)
   - Filter DTO extends `BaseFilterDTO`
   - Data DTO extends `BaseDTO`

3. **Service** (`Domain/Customer/Services/CustomerService.php`)
   - Inject Repository in constructor
   - Implement business logic methods
   - Handle validation and orchestration

### Step 4: Create Application Layer (from outside in)

1. **Requests** (`Application/User/Customer/Requests/`)
   - Extend `BaseFormRequest`
   - Define public properties
   - Implement `rules()` and `messages()`

2. **Responses** (`Application/User/Customer/Responses/`)
   - Extend `BaseResponseDTO`
   - Define public properties
   - Implement static `fromModel()` method

3. **Controller** (`Application/User/Customer/Controllers/`)
   - Extend `Controller`
   - Inject Service in constructor
   - Use `baseAction()` or `baseActionTransaction()` wrapper
   - Return Response DTO

### Step 5: Register Routes

```php
// routes/api.php
Route::middleware('auth:api')->group(function () {
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
    });
});
```

---

## 4. Best Practices

### 4.1 Controller Layer
- Always use `baseAction()` or `baseActionTransaction()`
- Do not write business logic in Controllers
- Controllers should only: receive request → call service → return response

### 4.2 Service Layer
- Contains all business logic
- Validate business rules before calling repository
- Throw exceptions with clear messages and appropriate error codes
- Use DTOs to pass data between layers

### 4.3 Repository Layer
- Only interacts with database
- Does not contain business logic
- Extend `BaseRepository` to leverage filtering methods
- Create custom query methods when needed

### 4.4 Request Validation
- Extend `BaseFormRequest`
- Declare public properties for auto-binding
- Implement custom `messages()` for user-friendly errors
- Client-side validation for better UX (like password confirmation)

### 4.5 Response DTO
- Always extend `BaseResponseDTO`
- Implement `fromModel()` to transform model
- Use `transform()` for smart detection
- Standardize date format (ISO 8601)

### 4.6 Naming Conventions
- Controller: `{Entity}Controller` (CustomerController)
- Service: `{Entity}Service` (CustomerService)
- Repository: `{Entity}Repository` (CustomerRepository)
- Request: `{Action}{Entity}Request` (StoreCustomerRequest)
- Response: `{Entity}Response` (CustomerResponse)
- DTO: `{Purpose}{Entity}DTO` (CustomerFilterDTO, CreateCustomerDTO)

---

## 5. Complete Example: Customer Module

Refer to these files to understand the pattern:

### Application Layer:
- [CustomerController.php](app/AppMain/Application/User/Customer/Controllers/CustomerController.php)
- [StoreCustomerRequest.php](app/AppMain/Application/User/Customer/Requests/StoreCustomerRequest.php)
- [UpdateCustomerRequest.php](app/AppMain/Application/User/Customer/Requests/UpdateCustomerRequest.php)
- [CustomerResponse.php](app/AppMain/Application/User/Customer/Responses/CustomerResponse.php)

### Domain Layer:
- [CustomerService.php](app/AppMain/Domain/Customer/Services/CustomerService.php)
- [CustomerRepository.php](app/AppMain/Domain/Customer/Repositories/CustomerRepository.php)
- [CustomerFilterDTO.php](app/AppMain/Domain/Customer/DTOs/CustomerFilterDTO.php)

---

## 6. CMS Layer (Admin Web Interface)

The CMS layer provides a web-based admin interface separate from the API layer. It uses traditional Laravel MVC patterns with session-based authentication.

### 6.1 CMS Architecture

**Location:** `app/AppMain/CMS/{Module}/`

**Key Differences from Application Layer:**
- Uses session-based authentication (admin-cms guard)
- Returns Blade views instead of JSON responses
- Uses standard Laravel validation instead of FormRequest
- Direct interaction between Controller and Service (no Request/Response DTOs)

### 6.2 CMS Module Structure

Each CMS module typically contains:

```
app/AppMain/CMS/
└── {Module}/
    ├── {Module}Controller.php    # Handles HTTP requests, returns views
    └── {Module}Service.php        # Business logic (optional, for complex operations)
```

### 6.3 Authentication Module (CMS/Auth)

**Purpose:** Handle admin login and logout

**Files:**
- `AuthController.php` - Login form, login post, logout
- `AuthService.php` - Authentication business logic

**Example - AuthController.php:**

```php
namespace App\AppMain\CMS\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login(
            $credentials,
            $request->filled('remember'),
            $request->ip()
        );

        if ($result['success']) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => $result['message'],
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin-cms')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
```

**Example - AuthService.php:**

```php
namespace App\AppMain\CMS\Auth;

use App\AppMain\Domain\Auth\Repositories\AdminAuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected AdminAuthRepository $adminAuthRepository;

    public function __construct(AdminAuthRepository $adminAuthRepository)
    {
        $this->adminAuthRepository = $adminAuthRepository;
    }

    public function login(array $credentials, bool $remember = false, string $ip = null): array
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        $admin = $this->adminAuthRepository->findByEmail($email);

        if (!$admin || !$admin->isActive() || !Hash::check($password, $admin->password)) {
            return [
                'success' => false,
                'admin' => null,
                'message' => 'Invalid credentials or inactive account'
            ];
        }

        Auth::guard('admin-cms')->attempt([
            'email' => $email,
            'password' => $password
        ], $remember);

        if ($ip) {
            $this->adminAuthRepository->updateLastLogin($admin, $ip);
        }

        return [
            'success' => true,
            'admin' => $admin,
            'message' => 'Login successful'
        ];
    }
}
```

### 6.4 Dashboard Module (CMS/Dashboard)

**Purpose:** Display admin dashboard with statistics

**Files:**
- `DashboardController.php` - Display dashboard view
- `DashboardService.php` - Fetch statistics from repositories

**Example - DashboardController.php:**

```php
namespace App\AppMain\CMS\Dashboard;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $statistics = $this->dashboardService->getStatistics();
        return view('admin.dashboard.index', $statistics);
    }
}
```

### 6.5 CRUD Module Pattern (CMS/User)

**Purpose:** Full CRUD operations for managing users

**Files:**
- `UserController.php` - CRUD methods (index, create, store, edit, update, destroy)
- `UserService.php` - Business logic for user operations

**Example - UserController.php:**

```php
namespace App\AppMain\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'role' => $request->get('role'),
            'status' => $request->get('status'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc'),
            'per_page' => $request->get('per_page', 15),
        ];

        $users = $this->userService->getUsers($filters);

        return view('admin.user.index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,user',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $this->userService->createUser($validated);
            return redirect()->route('admin.user.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }
}
```

**Example - UserService.php:**

```php
namespace App\AppMain\CMS\User;

use App\AppMain\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers(array $filters)
    {
        $query = $this->userRepository->newQuery();

        // Apply search filter
        if (!empty($filters['search'])) {
            $this->userRepository->applyGlobalSearch(
                $query,
                ['name', 'email'],
                $filters['search']
            );
        }

        // Apply exact filters
        $exactFilters = [];
        if (!empty($filters['role'])) $exactFilters['role'] = $filters['role'];
        if (!empty($filters['status'])) $exactFilters['status'] = $filters['status'];

        if (!empty($exactFilters)) {
            $this->userRepository->applyExactFilters($query, $exactFilters);
        }

        // Apply sorting
        $this->userRepository->applySorting(
            $query,
            $filters['sort_by'],
            $filters['sort_direction']
        );

        return $query->paginate($filters['per_page']);
    }

    public function createUser(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);
        return ['user' => $user];
    }

    public function updateUser(string $userId, array $data): array
    {
        // Only hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $this->userRepository->update($userId, $data);
        $user = $this->userRepository->findById($userId);
        return ['user' => $user];
    }

    public function deleteUser(string $userId): bool
    {
        return $this->userRepository->delete($userId);
    }
}
```

### 6.6 CMS Views Structure

**Location:** `resources/views/admin/`

```
resources/views/admin/
├── layouts/
│   └── app.blade.php           # Base layout with sidebar, header, common CSS
├── auth/
│   └── login.blade.php         # Login form
├── dashboard/
│   └── index.blade.php         # Dashboard home
├── component/
│   └── index.blade.php         # Component showcase
└── user/
    ├── index.blade.php         # User list
    ├── create.blade.php        # Create user form
    └── edit.blade.php          # Edit user form
```

**Base Layout Pattern (layouts/app.blade.php):**

- Header with user info and logout
- Sidebar navigation menu
- Breadcrumb support with `@yield('breadcrumb')`
- Content area with `@yield('content')`
- Common component styles (buttons, forms, tables, badges, alerts)
- Stack for page-specific styles with `@push('styles')`

### 6.7 CMS Routes Pattern

**File:** `routes/web.php`

```php
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (redirect to dashboard if authenticated)
    Route::middleware('admin.redirect')->group(function () {
        Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    // Authenticated routes
    Route::middleware('auth:admin-cms')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    // Fallback for undefined admin routes
    Route::fallback(function () {
        if (auth()->guard('admin-cms')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });
});
```

### 6.8 CMS Middleware

**AdminRedirectIfAuthenticated Middleware:**

Purpose: Guest-only middleware (opposite of auth middleware)

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin-cms')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return $next($request);
    }
}
```

**Authenticate Middleware (Modified):**

Purpose: Handle unauthenticated redirects for different contexts

```php
protected function redirectTo(Request $request): ?string
{
    // API routes should return null (will return 401 JSON)
    if ($request->is('api/*') || $request->is('api/admin/*')) {
        return null;
    }

    // Admin routes redirect to admin login
    if ($request->is('admin/*')) {
        return route('admin.login');
    }

    return null;
}
```

### 6.9 Flash Messages Pattern

CMS uses Laravel session flash messages for user feedback:

```php
// Success message
return redirect()->route('admin.user.index')
    ->with('success', 'User created successfully');

// Error message
return back()->withInput()
    ->with('error', 'Failed to create user: ' . $e->getMessage());
```

**Display in Blade:**

```blade
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
```

### 6.10 CMS Best Practices

1. **Session-based Auth:** Use `admin-cms` guard for all CMS authentication
2. **View Returns:** Controllers return views, not JSON responses
3. **Flash Messages:** Use `with()` for success/error feedback
4. **Validation:** Use inline `$request->validate()` for simple forms
5. **Services:** Optional for simple CRUD, required for complex business logic
6. **Common Styles:** Place reusable CSS in base layout, not individual views
7. **Breadcrumbs:** Always provide breadcrumb navigation using `@yield('breadcrumb')`
8. **Fallback Route:** Catch undefined admin routes and redirect appropriately

---

## 7. Authentication Setup

This project uses multiple authentication systems:

### Guards Configuration

**File:** `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin-cms' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
    'admin' => [
        'driver' => 'sanctum',
        'provider' => 'admins',
    ],
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

### Authentication by Context

- **Admin CMS Web Interface (`/admin/*`)** - Session-based with `admin-cms` guard
- **Admin API (`/api/admin/*`)** - Sanctum tokens - see [SANCTUM_SETUP.md](SANCTUM_SETUP.md)
- **End-User API (`/api/*`)** - Passport OAuth - see [PASSPORT_SETUP.md](PASSPORT_SETUP.md)
- **End-User Web (future)** - Session-based with `web` guard

---

## 8. References

- [Laravel Documentation](https://laravel.com/docs)
- [Domain-Driven Design](https://en.wikipedia.org/wiki/Domain-driven_design)
- [Repository Pattern](https://docs.microsoft.com/en-us/dotnet/architecture/microservices/microservice-ddd-cqrs-patterns/infrastructure-persistence-layer-design)
- [DTO Pattern](https://en.wikipedia.org/wiki/Data_transfer_object)
