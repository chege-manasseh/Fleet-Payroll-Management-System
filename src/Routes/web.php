<?php

use App\Core\Response;
use App\Core\Router;
use App\Core\Request;
use App\Controllers\Middleware\JwtMiddleware;

$request = new Request();
$router = new Router();
$jwtMiddleware = new JwtMiddleware();

$response = new Response('Route not found', null, 404);
$router->add('GET', '/', 'HomeController@index');
$router->add('POST', '/login', 'Auth\AuthController@login');
$router->add('GET','/testconnection','HomeController@testConnection');
$router->add('POST','/change-password','Auth\AuthController@changePassword');
$router->add('POST','/refresh','Auth\RefreshToken@refreshToken');

$publicRoutes = [
    'GET' => [
        '/' => 'HomeController@index',
        '/login' => 'Auth\AuthController@login',
        '/testconnection' => 'HomeController@testConnection',
    ],
    'POST' => [
        '/login' => 'Auth\AuthController@login',
    ],
];

if(isset($publicRoutes[$request->getMethod()]) && isset($publicRoutes[$request->getMethod()][$router->normalizeUri($request->getUri())])){
    return [$router,$request,$response];
}

if($jwtMiddleware->handle($request, $response)=== true){
    return [$router,$request,$response];
}