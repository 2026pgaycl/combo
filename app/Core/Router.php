<?php

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected array $groupMiddleware = [];

    public function get(string $path, array $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, array $action): void
    {
        $this->add('POST', $path, $action);
    }

    public function put(string $path, array $action): void
    {
        $this->add('PUT', $path, $action);
    }

    public function delete(string $path, array $action): void
    {
        $this->add('DELETE', $path, $action);
    }

    /**
     * Group routes under shared middleware, e.g.:
     * $router->group(['auth', 'role:super_admin,property_manager'], function ($r) { ... });
     */
    public function group(array $middleware, callable $callback): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($previous, $middleware);
        $callback($this);
        $this->groupMiddleware = $previous;
    }

    protected function add(string $method, string $path, array $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action,
            'middleware' => $this->groupMiddleware,
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $params = $this->match($route['path'], $path);
            if ($params === null) {
                continue;
            }

            foreach ($route['middleware'] as $mw) {
                Middleware::run($mw);
            }

            [$class, $method2] = $route['action'];
            $controller = new $class();
            call_user_func_array([$controller, $method2], $params);
            return;
        }

        http_response_code(404);
        View::render('errors.404');
    }

    /** Returns matched params array, or null if the path doesn't match. */
    protected function match(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $requestPath, $matches)) {
            return null;
        }
        array_shift($matches);
        return $matches;
    }
}
