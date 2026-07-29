<?php

use App\Core\Response;
use App\Core\Router;
use App\Core\Request;
use App\Controllers\Middleware\JwtMiddleware;
use App\Core\Container;
use App\Storage\Database;

$container = Container::getInstance();

$container->singleton(Request::class,function(){
    return new Request();
});

$container->singleton(Database::class,function(){
    return new Database();
});
$container->singleton(Response::class,function(){
    return new Response();
});

$container->singleton(JwtMiddleware::class,function(){
    return new JwtMiddleware();
});

$db =$container->get(Database::class);
$request= $container->get(Request::class);
$router= $container->get(Router::class);
$jwtMiddleware = $container->get(JwtMiddleware::class);
$response = $container->get(Response::class);


$router->add('GET', '/', 'HomeController@index');
$router->add('POST', '/login', 'Auth\AuthController@login');
$router->add('GET','/testconnection','HomeController@testConnection');
$router->add('POST','/change-password','Auth\AuthController@changePassword');
$router->add('POST','/refresh','Auth\RefreshToken@refreshToken');
$router->add('POST','/register','Auth\AuthController@register');

$publicRoutes = [
    'GET' => [
        '/' => 'HomeController@index',
        '/login' => 'Auth\AuthController@login',
        '/testconnection' => 'HomeController@testConnection',
    ],
    'POST' => [
        '/login' => 'Auth\AuthController@login',
        '/register' => 'Auth\AuthController@register',
    ],
];

if(isset($publicRoutes[$request->getMethod()]) && isset($publicRoutes[$request->getMethod()][$router->normalizeUri($request->getUri())])){
    return [$router,$request,$response];
}

if($jwtMiddleware->handle($request, $response)=== true){
    return [$router,$request,$response];
}