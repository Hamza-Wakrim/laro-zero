<?php

namespace App\Http\Controllers;

/**
 * Base Controller Class
 * 
 * Design Pattern Enforcement: Route -> Controller -> Service -> Model
 * 
 * IMPORTANT: Controllers MUST follow this pattern:
 * 1. Controllers receive HTTP requests from Routes
 * 2. Controllers call Services (never call Models directly)
 * 3. Services handle business logic and interact with Models
 * 4. Models represent database entities
 * 
 * Example:
 * ```php
 * class UserController extends Controller
 * {
 *     public function __construct(
 *         private UserService $userService
 *     ) {}
 * 
 *     public function index()
 *     {
 *         $users = $this->userService->getAllUsers();
 *         return response()->json($users);
 *     }
 * }
 * ```
 * 
 * @package App\Http\Controllers
 */
abstract class Controller
{
    /**
     * Validate that a service is being used
     * 
     * This method helps enforce the pattern by ensuring services are injected
     * 
     * @param mixed $service
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function validateService($service): bool
    {
        if (!is_object($service)) {
            throw new \InvalidArgumentException('Service must be an object');
        }

        $serviceClass = get_class($service);
        if (!str_contains($serviceClass, 'Service')) {
            throw new \InvalidArgumentException(
                "Invalid service class: {$serviceClass}. Services must be in App\\Services namespace and end with 'Service'"
            );
        }

        if (!is_subclass_of($service, \App\Services\Service::class)) {
            throw new \InvalidArgumentException(
                "Service {$serviceClass} must extend App\\Services\\Service"
            );
        }

        return true;
    }
}
