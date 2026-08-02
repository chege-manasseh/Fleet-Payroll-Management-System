<?php

namespace App\Core;

use App\Core\Response;
use App\Core\Container;

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => [],
    ];


    public function add($method, $route, $action)
    {
        $method = strtoupper($method);
        $route = '/' . ltrim($route, '/');
        $this->routes[$method][$route] = $action;
    }


    public function normalizeUri(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = preg_replace('#^/public(?:/index\.php)?#', '', $path) ?? $path;
        $path = preg_replace('#^/index\.php#', '', $path) ?? $path;
        $path = '/' . ltrim($path, '/');

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }


    public function dispatch(Request $request,string $method, string $uri,Response $response,Container $container)
    {
        $method = strtoupper($method);
        $routeMethod = $method === 'HEAD' ? 'GET' : $method;
        $uri = $this->normalizeUri($uri);

        if (!isset($this->routes[$routeMethod][$uri])) {
            $message = 'Route not found';
            $data = null;
            $statusCode = 404;
            return $response->json($message, $data, $statusCode);
        }

        [$controller, $handler] = $this->routes[$routeMethod][$uri];

        if (!class_exists($controller)) {
            $message = "Controller {$controller} not found";
            $data = null;
            $statusCode = 500;
            return $response->json($message, $data, $statusCode);
        }

        $instance = $container->make($controller);

        if (!method_exists($instance, $handler)) {
            $message = "Method {$handler} not found on {$controller}";
            $data = null;
            $statusCode = 500;
            return $response->json($message, $data, $statusCode);
        }

        return $instance->$handler($request);
    }
}
