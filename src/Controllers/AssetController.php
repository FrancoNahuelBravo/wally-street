<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Asset;

class AssetController {
    public function index(Request $request, Response $response) {
        $assets = Asset::all();
        $response->getBody()->write(json_encode($assets));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // NUEVA FUNCIÓN PARA EL PUT /assets 
    public function updatePrices(Request $request, Response $response) {
        $assets = Asset::all();

        foreach ($assets as $asset) {
            // Uso la lógica matemática que pide el PDF
            $nuevoPrecio = Asset::variarPrecioPorTiempo(
                $asset->current_price, 
                $asset->last_update
            );

            // Actualizo en la base de datos
            $asset->update([
                'current_price' => round($nuevoPrecio, 2),
                'last_update' => date('Y-m-d H:i:s')
            ]);
        }

        $response->getBody()->write(json_encode(["mensaje" => "Mercado actualizado correctamente"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}