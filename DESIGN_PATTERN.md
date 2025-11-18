# Design Pattern Guide

## Enforced Pattern: Route → Controller → Service → Model

This framework **enforces** a strict layered architecture. All code must follow this pattern.

## Quick Reference

```
┌─────────┐
│ Routes  │  →  Only calls Controllers
└─────────┘
     │
     ▼
┌─────────────┐
│ Controllers │  →  Only calls Services
└─────────────┘
     │
     ▼
┌──────────┐
│ Services │  →  Only calls Models
└──────────┘
     │
     ▼
┌────────┐
│ Models │  →  Database entities only
└────────┘
```

## Rules

### ✅ DO

- Routes call Controllers
- Controllers call Services
- Services call Models
- All Services extend `App\Services\Service`
- All Controllers extend `App\Http\Controllers\Controller`
- Use dependency injection in Controllers

### ❌ DON'T

- Routes calling Services directly
- Routes calling Models directly
- Controllers calling Models directly
- Business logic in Controllers
- Business logic in Models
- Services calling other Services (unless absolutely necessary)

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Controller.php          # Base controller
│       └── UserController.php      # Example controller
├── Services/
│   ├── Service.php                 # Base service (MUST extend)
│   └── UserService.php            # Example service
└── Models/
    └── User.php                    # Example model

routes/
└── api.php                         # All API routes
```

## Code Examples

### Route (routes/api.php)
```php
// ✅ CORRECT
Route::get('/users', [UserController::class, 'index']);

// ❌ WRONG
Route::get('/users', function () {
    return UserService::getAllUsers();
});

// ❌ WRONG
Route::get('/users', function () {
    return User::all();
});
```

### Controller (app/Http/Controllers/UserController.php)
```php
// ✅ CORRECT
class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
        $this->validateService($userService);
    }

    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        return response()->json($users);
    }
}

// ❌ WRONG - Calling Model directly
class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::all(); // DON'T DO THIS
        return response()->json($users);
    }
}
```

### Service (app/Services/UserService.php)
```php
// ✅ CORRECT
class UserService extends Service
{
    public function getAllUsers()
    {
        return User::all();
    }
}

// ❌ WRONG - Not extending Service
class UserService
{
    public function getAllUsers()
    {
        return User::all();
    }
}
```

### Model (app/Models/User.php)
```php
// ✅ CORRECT - Model only, no business logic
class User extends Model
{
    protected $fillable = ['name', 'email'];
}

// ❌ WRONG - Business logic in Model
class User extends Model
{
    public function getAllActiveUsers() // DON'T DO THIS
    {
        return $this->where('active', true)->get();
    }
}
```

## Creating New Features

### Step 1: Create Model
```bash
php artisan make:model Product -m
```

### Step 2: Create Service
```bash
# Create manually: app/Services/ProductService.php
```

```php
<?php

namespace App\Services;

use App\Models\Product;

class ProductService extends Service
{
    public function getAllProducts()
    {
        return Product::all();
    }

    public function handle(...$args)
    {
        return $this->getAllProducts();
    }
}
```

### Step 3: Create Controller
```bash
php artisan make:controller ProductController
```

```php
<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
        $this->validateService($productService);
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return response()->json($products);
    }
}
```

### Step 4: Add Routes
```php
// routes/api.php
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
});
```

## Validation

The base `Controller` class includes `validateService()` method that:
- Ensures service is an object
- Checks service name contains "Service"
- Verifies service extends `App\Services\Service`

This validation runs automatically when you inject a service in the constructor.

## Benefits

1. **Separation of Concerns**: Each layer has one responsibility
2. **Testability**: Easy to mock services and models
3. **Maintainability**: Business logic centralized in services
4. **Consistency**: All code follows the same structure
5. **Scalability**: Easy to add features following the pattern

## Common Mistakes

1. ❌ Putting business logic in Controllers
2. ❌ Calling Models from Controllers
3. ❌ Creating Services that don't extend `App\Services\Service`
4. ❌ Putting business logic in Models
5. ❌ Calling Services from Routes

## Questions?

Refer to:
- `README.md` - Full documentation
- `app/Http/Controllers/UserController.php` - Example controller
- `app/Services/UserService.php` - Example service
- `routes/api.php` - Example routes

