<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php'; // Tu conexión a DB

$app = AppFactory::create();

// IMPORTANTE: Para que Slim lea el Body de los POST (JSON)
$app->addBodyParsingMiddleware();

// Cargamos las rutas
// En public/index.php
$routes = require __DIR__ . '/../routes/api.php';
$routes($app);

$app->run();