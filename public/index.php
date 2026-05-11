<?php
use Slim\Factory\AppFactory;
require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');
$app = AppFactory::create();

// 1. Configuración de BasePath (Como uso wally.test, Slim lo detecta solo )
$app->addRoutingMiddleware();

// 2. Middlewares
$app->addBodyParsingMiddleware();

// Middleware para manejar CORS y headers (El del profe)
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PUT, PATCH, DELETE')
        ->withHeader('Content-Type', 'application/json');
});

// Middleware de errores (Muy importante para ver qué falla en consola)
$app->addErrorMiddleware(true, true, true);

// 3. Carga de rutas
$routes = require __DIR__ . '/../routes/api.php';
$routes($app);

// 4. EL BOTÓN DE PLAY (Siempre al final)
$app->run();