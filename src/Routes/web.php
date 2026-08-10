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

$router->add('GET', '/', [HomeController::class, 'index']);
$router->add('POST', '/login', [AuthController::class, 'login']);
$router->add('GET', '/testconnection', [HomeController::class, 'testConnection']);
$router->add('POST', '/change-password', [AuthController::class, 'changePassword']);
$router->add('POST', '/refresh', [RefreshToken::class, 'refreshToken']);
$router->add('POST', '/register', [AuthController::class, 'register']);
$router->add('POST', '/logout', [AuthController::class, 'logout']);
$router->add('POST', '/reset-password', [AuthController::class, 'resetPassword']);


$publicRoutes = [
    'GET' => [
        '/' => [HomeController::class, 'index'],
        '/login' => [AuthController::class, 'login'],
        '/testconnection' => [HomeController::class, 'testConnection'],
    ],
    'POST' => [
        '/login' => [AuthController::class, 'login'],
        '/register' => [AuthController::class, 'register'],
        '/logout' => [AuthController::class, 'logout'],
        '/refresh' => [AuthController::class, 'refreshToken'],
        '/reset-password' => [AuthController::class, 'resetPassword'],
        '/' => [HomeController::class, 'index']
    ],
];

if (isset($publicRoutes[$request->getMethod()]) && isset($publicRoutes[$request->getMethod()][$router->normalizeUri($request->getUri())])) {
    return [$router, $request, $response, $container];
}

if ($jwtMiddleware->handle() === true) {
    return [$router, $request, $response, $container];
}
