# Laro-Zero

A lightweight, API-only Laravel framework fork optimized for building RESTful APIs and microservices.

## About Laro-Zero

Laro-Zero is a streamlined version of Laravel, specifically designed for API development. Built on top of the [hamza-wakrim/api-framework](https://packagist.org/packages/hamza-wakrim/api-framework), it removes all frontend dependencies and view-related components, making it perfect for:

- RESTful API development
- Microservices architecture
- Mobile app backends
- Single Page Application (SPA) backends
- Headless CMS implementations

## Features

- **API-First Design**: Optimized exclusively for API endpoints
- **Lightweight**: Removed all frontend build tools and dependencies
- **Fast Setup**: Minimal configuration required
- **Laravel Compatibility**: Built on Laravel's robust foundation
- **Modern PHP**: Requires PHP 8.2 or higher
- **Enforced Design Pattern**: Strict Route → Controller → Service → Model architecture

## Enforced Design Pattern

Laro-Zero **enforces** a strict layered architecture pattern that all developers must follow:

```
Route → Controller → Service → Model
```

### Pattern Rules

1. **Routes** (`routes/api.php`)
   - MUST only call Controllers
   - NEVER call Services or Models directly
   - Handle HTTP routing only

2. **Controllers** (`app/Http/Controllers/`)
   - MUST only call Services
   - NEVER call Models directly
   - Handle HTTP concerns (request/response, validation)
   - All Controllers extend `App\Http\Controllers\Controller`

3. **Services** (`app/Services/`)
   - Handle ALL business logic
   - Interact with Models
   - All Services extend `App\Services\Service`
   - Must be in `App\Services` namespace and end with `Service`

4. **Models** (`app/Models/`)
   - Represent database entities only
   - No business logic
   - Eloquent ORM models

### Enforcement Mechanisms

- **Base Service Class**: All services must extend `App\Services\Service`
- **Base Controller Class**: Includes `validateService()` method to ensure proper service injection
- **Code Structure**: Directory structure and naming conventions enforce the pattern
- **Documentation**: Extensive PHPDoc comments explain the pattern

### Example Implementation

```php
// routes/api.php
Route::get('/users', [UserController::class, 'index']);

// app/Http/Controllers/UserController.php
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

// app/Services/UserService.php
class UserService extends Service
{
    public function getAllUsers()
    {
        return User::all(); // Interacts with Model
    }
}

// app/Models/User.php
class User extends Model
{
    // Model definition only
}
```

### Why This Pattern?

- **Separation of Concerns**: Each layer has a single responsibility
- **Testability**: Easy to mock services in controllers, models in services
- **Maintainability**: Business logic is centralized in services
- **Scalability**: Easy to add new features following the same pattern
- **Consistency**: All code follows the same structure

### Creating New Features

When adding new features, follow these steps:

1. **Create the Model** (if needed)
   ```bash
   php artisan make:model Product
   ```

2. **Create the Service**
   ```bash
   php artisan make:service ProductService
   ```
   Then extend `App\Services\Service` and add business logic methods.

3. **Create the Controller**
   ```bash
   php artisan make:controller ProductController
   ```
   Then inject the service in the constructor and call service methods.

4. **Add Routes**
   ```php
   Route::prefix('products')->group(function () {
       Route::get('/', [ProductController::class, 'index']);
       // ... more routes
   });
   ```

## Requirements

- PHP >= 8.2
- Composer
- Node.js (optional, only for development scripts)

## Installation

### Via Composer

```bash
composer create-project hamza-wakrim/laro-zero your-project-name
```

### Manual Installation

1. Clone the repository:
```bash
git clone https://github.com/hamza-wakrim/laro-zero.git
cd laro-zero
```

2. Install dependencies:
```bash
composer install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations:
```bash
php artisan migrate
```

## Quick Start

1. **Start the development server:**
```bash
php artisan serve
```

2. **Access the API:**
   - Health check: `http://localhost:8000/api/health`
   - API routes: `http://localhost:8000/api/*`

3. **Development mode** (with queue and logs):
```bash
composer run dev
```

## Project Structure

```
laro-zero/
├── app/                    # Application code
│   ├── Http/
│   │   └── Controllers/    # API Controllers (call Services only)
│   ├── Services/           # Business logic layer (call Models)
│   ├── Models/             # Eloquent Models (database entities)
│   └── Providers/          # Service Providers
├── routes/
│   ├── api.php             # API routes (call Controllers only)
│   └── console.php         # Artisan commands
├── config/                  # Configuration files
├── database/                # Migrations, factories, seeders
└── tests/                   # PHPUnit tests
```

**Important**: The directory structure enforces the design pattern:
- `routes/api.php` → calls `app/Http/Controllers/`
- `app/Http/Controllers/` → calls `app/Services/`
- `app/Services/` → calls `app/Models/`

## API Routes

All API routes are defined in `routes/api.php`. By default, routes are prefixed with `/api`.

**Remember**: Routes MUST only call Controllers, never Services or Models directly.

Example:
```php
// ✅ CORRECT: Route calls Controller
Route::get('/users', [UserController::class, 'index']);

// ❌ WRONG: Route calls Service directly
Route::get('/users', function () {
    return UserService::getAllUsers(); // DON'T DO THIS
});

// ❌ WRONG: Route calls Model directly
Route::get('/users', function () {
    return User::all(); // DON'T DO THIS
});
```

## Available Commands

- `composer setup` - Install dependencies and set up the project
- `composer dev` - Start development server with queue and logs
- `composer test` - Run PHPUnit tests
- `php artisan serve` - Start the development server
- `php artisan migrate` - Run database migrations

## Configuration

Key configuration files:
- `config/app.php` - Application configuration
- `config/database.php` - Database settings
- `config/auth.php` - Authentication settings

## Testing

Run tests with:
```bash
composer test
```

Or directly with PHPUnit:
```bash
php artisan test
```

## Framework

This project uses [hamza-wakrim/api-framework](https://packagist.org/packages/hamza-wakrim/api-framework) as its core framework, which is a Laravel fork optimized for API development.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The Laro-Zero framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please open an issue on the GitHub repository.
