<?php
date_default_timezone_set('America/Argentina/Buenos_Aires'); //Si no tengo errores con la hora
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php'; // Mi conexión a DB

$app = AppFactory::create();

// IMPORTANTE: Para que Slim lea el Body de los POST (JSON)
$app->addBodyParsingMiddleware();

// Cargo las rutas
// En public/index.php
$routes = require __DIR__ . '/../routes/api.php';
$routes($app);

$app->run();