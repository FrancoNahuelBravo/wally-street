<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AssetController;
use App\Controllers\UserController;
// use App\Middleware\JWTMiddleware; // Esto lo hago luego

return function ($app): void {
    $app->group('/api', function (RouteCollectorProxy $group) {
        
        // Rutas de Activos
        $group->get('/assets', [AssetController::class, 'index']);
        // Actualizar precios de los activos
        $group->put('/assets', [AssetController::class, 'updatePrices']); // Solo Admin
        
        // Rutas de Usuarios
        $group->post('/users', [UserController::class, 'create']);
        $group->post('/login', [UserController::class, 'login']);

        
        
        // Acá van a ir las rutas protegidas (comprar/vender) más adelante
    }); // Acá es donde el profe agrega el ->add(Middleware::class)
};