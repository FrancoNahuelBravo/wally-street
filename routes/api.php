<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AssetController;
use App\Controllers\UserController;
// use App\Middleware\JWTMiddleware; // Esto lo usaremos luego

return function ($app): void {
    $app->group('/api', function (RouteCollectorProxy $group) {
        
        // Rutas de Activos
        $group->get('/assets', [AssetController::class, 'index']);
        
        // Rutas de Usuarios
        $group->post('/users', [UserController::class, 'create']);
        $group->post('/login', [UserController::class, 'login']);
        
        // Aquí irán las rutas protegidas (comprar/vender) más adelante
    }); // Aquí es donde el profe agrega el ->add(Middleware::class)
};