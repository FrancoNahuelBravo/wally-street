<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Asset;

class AssetController {
    
    // GET /api/assets
    public function index(Request $request, Response $response) {
        $assets = Asset::getAll();
        $response->getBody()->write(json_encode($assets));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // PUT /api/assets (Solo Admin)
    public function updatePrices(Request $request, Response $response) {
        $assets = Asset::getAll();
        
        foreach ($assets as $asset) {
            $nuevoPrecio = Asset::variarPrecioPorTiempo(
                $asset['current_price'], 
                $asset['last_update']
            );

            // --- LA CORRECCIÓN VA ACÁ ---
            // Usamos max() para que si el precio baja de 0, se quede clavado en 0.01
            $nuevoPrecio = max(0.01, $nuevoPrecio);
            
            Asset::updatePrice($asset['id'], round($nuevoPrecio, 2));
        }

        $response->getBody()->write(json_encode(["mensaje" => "Mercado actualizado correctamente"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}