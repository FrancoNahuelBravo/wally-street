<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AssetController;
use App\Controllers\UserController;
use App\Controllers\TradeController; 
use App\Controllers\PortfolioController; 
use App\Middleware\AuthMiddleware;

return function ($app): void {
    $app->group('/api', function (RouteCollectorProxy $group) {
        
        // --- RUTAS PÚBLICAS (Sin login) ---
        $group->post('/login', [UserController::class, 'login']);
        $group->post('/users', [UserController::class, 'create']);
        $group->get('/assets', [AssetController::class, 'index']);

        // --- RUTAS PROTEGIDAS (Requieren Token) ---
        $group->group('', function (RouteCollectorProxy $authGroup) {
            // Usuarios
            $authGroup->post('/logout', [UserController::class, 'logout']);
            $authGroup->get('/users/{id}', [UserController::class, 'getProfile']);
            $authGroup->put('/users/{id}', [UserController::class, 'updateProfile']);
            
            // Operaciones (Trade)
            $authGroup->get('/portfolio', [\App\Controllers\PortfolioController::class, 'getPortfolio']);
            $authGroup->delete('/portfolio/{asset_id}', [\App\Controllers\PortfolioController::class, 'deleteAsset']);
            $authGroup->get('/transactions', [\App\Controllers\TradeController::class, 'getHistory']);
            
            // Portfolio
            $authGroup->post('/trade/buy', [TradeController::class, 'buy']);
            $authGroup->post('/trade/sell', [TradeController::class, 'sell']);
            
            // --- RUTA ADMIN ---
            $group->put('/assets', [AssetController::class, 'updatePrices']);

        })->add(new AuthMiddleware()); // Aplicamos el guardia aquí
    });
};