<?php

namespace App\Services;

/**
 * Base Service Class
 * 
 * All services MUST extend this class.
 * 
 * Design Pattern: Route -> Controller -> Service -> Model
 * 
 * Services handle all business logic and interact with models.
 * Controllers should ONLY call services, never interact with models directly.
 * 
 * @package App\Services
 */
abstract class Service
{
    /**
     * Handle the service operation
     * 
     * @param mixed ...$args
     * @return mixed
     */
    abstract public function handle(...$args);
}

