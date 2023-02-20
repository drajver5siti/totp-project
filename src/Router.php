<?php

declare(strict_types=1);

namespace App;

use App\Attributes\Route;
use App\Enums\RequestType;
use App\Exceptions\RouteNotFoundException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class Router
{
    private array $routes = [];
    private array $dynamicRoutes = [];

    public function __construct(private Container $container)
    {
    }

    private function isDynamicRoute(string $route): bool
    {
        // Check for presence of curly braces
        if (strpos($route, '{') === false || strpos($route, '}') === false) {
            return false;
        }

        // Extract parameter name from curly braces
        preg_match('/\{([a-zA-Z0-9_]+)\}/', $route, $matches);
        $paramName = $matches[1] ?? null;

        // Check for valid parameter name
        if (!$paramName || !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $paramName)) {
            return false;
        }

        return true;
    }
    public function register(string $route, callable|array $action, RequestType $type = RequestType::GET): self
    {
        $this->routes[$type->value][$route] = $action;
        return $this;
    }

    public function registerDynamic(string $route, callable|array $action, RequestType $type = RequestType::GET): self
    {
        $this->dynamicRoutes[$type->value][] = [
            'pattern' => $route,
            'action' => $action
        ];

        return $this;
    }

    public function registerControllers(array $controllers): void
    {
        foreach ($controllers as $controller) {
            $reflectionController = new ReflectionClass($controller);

            foreach ($reflectionController->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

                foreach ($method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                    /** @var Route $route */
                    $route = $attribute->newInstance();

                    $this->register($route->path, [$controller, $method->getName()], $route->method);
                }
            }
        }
    }

    public function resolve(string $requestMethod, string $requestUri)
    {
        $route = explode('?', $requestUri)[0];
        $action = $this->routes[$requestMethod][$route] ?? null;

        if (!$action) {
            throw new RouteNotFoundException();
        }

        if (is_callable($action)) {
            return call_user_func($action);
        }

        [$class, $method] = $action;

        if (class_exists($class)) {
            $class = $this->container->get($class);
            if (method_exists($class, $method)) {
                return call_user_func_array([$class, $method], []);
            }
        }
    }
}
