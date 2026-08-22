<?php

use App\Core\Response;
use App\Core\Router;
use App\Core\Request;
use App\Controllers\Middleware\JwtMiddleware;
use App\Core\Container;
use App\Storage\Database;
use App\Controllers\HomeController;
use App\Controllers\Auth\AuthController;
use App\Services\Auth\RefreshToken;

$container = Container::getInstance();

$container->singleton(Request::class, function () {
    return new Request();
});

$container->singleton(Database::class, function () {
    return new Database();
});


$container->singleton(Router::class, function () {
    return new Router();
});

$container->singleton(JwtMiddleware::class, function () use ($container) {
    return $container->build(JwtMiddleware::class);
});

$container->singleton(Response::class, function () {
    return new Response();
});

$router = $container->make(Router::class);
$request = $container->make(Request::class);
$response = $container->make(Response::class);
$jwtMiddleware = $container->make(JwtMiddleware::class);

$router->add('GET', '/api', [HomeController::class, 'index']);
$router->add('POST', '/api/login', [AuthController::class, 'login']);
$router->add('GET', '/api/testconnection', [HomeController::class, 'testConnection']);
$router->add('POST', '/api/change-password', [AuthController::class, 'changePassword']);
$router->add('POST', '/api/register', [AuthController::class, 'register']);
$router->add('POST', '/api/logout', [AuthController::class, 'logout']);
$router->add('POST', '/api/reset-password', [AuthController::class, 'resetPassword']);
$router->add('POST', '/api/refresh', [AuthController::class, 'refresh']);

$publicRoutes = [
    'GET' => [
        '/api' => [HomeController::class, 'index'],
        '/api/login' => [AuthController::class, 'login'],
        '/api/testconnection' => [HomeController::class, 'testConnection'],
    ],
    'POST' => [
        '/api/login' => [AuthController::class, 'login'],
        '/api/register' => [AuthController::class, 'register'],
        '/api/logout' => [AuthController::class, 'logout'],

        '/api/refresh' => [AuthController::class, 'refresh'],
        '/api/reset-password' => [AuthController::class, 'resetPassword'],
        '/api' => [HomeController::class, 'index']
    ],
];

if (isset($publicRoutes[$request->getMethod()]) && isset($publicRoutes[$request->getMethod()][$router->normalizeUri($request->getUri())])) {
    return [$router, $request, $response, $container];
}

if ($jwtMiddleware->handle() !== true) {
    exit;
}
return [$router, $request, $response, $container];
