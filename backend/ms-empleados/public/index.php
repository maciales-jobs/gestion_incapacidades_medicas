<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Config/database.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    
    $response = $response
    ->withHeader('Access-Control-Allow-Origin', '*')
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')  
    ->withHeader('Access-Control-Allow-Credentials', 'true');
    
    if ($request->getMethod() === 'OPTIONS') {
        return $response->withStatus(200);
    }
    
    return $response;
});

$routes = require __DIR__ . '/../app/Routes/routes.php';
$routes($app);

$app->run();