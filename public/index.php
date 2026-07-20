<?php


require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

[$router, $request] = require __DIR__ . '/../src/Routes/web.php';

$router->dispatch($request->getMethod(), $request->getUri(), $request);
