<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\DB;
use PDO;

class PortfolioController {

    // GET /api/portfolio: Ver mis activos y su valor actual
    public function getPortfolio(Request $request, Response $response) {
        $userId = $request->getAttribute('user_id'); // Viene del Middleware
        $db = DB::getConnection();

        // Buscamos los activos del usuario y traemos el precio actual del mercado para calcular el total
        $sql = "SELECT a.id as asset_id, a.name, p.quantity, a.current_price, 
                       (p.quantity * a.current_price) as total_value_usd
                FROM portfolio p 
                JOIN assets a ON p.asset_id = a.id 
                WHERE p.user_id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $portfolio = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($portfolio));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // DELETE /api/portfolio/{asset_id}: Quitar un activo del inventario
    public function deleteAsset(Request $request, Response $response, array $args) {
        $userId = $request->getAttribute('user_id');
        $assetId = $args['asset_id'];
        $db = DB::getConnection();

        // 1. Validar si el activo existe en su portfolio y qué cantidad tiene
        $stmt = $db->prepare("SELECT quantity FROM portfolio WHERE user_id = :u AND asset_id = :a");
        $stmt->execute([':u' => $userId, ':a' => $assetId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $response->getBody()->write(json_encode(["error" => "El activo no está en tu portfolio"]));
            return $response->withStatus(404);
        }

        // 2. Validación Crítica: Solo borrar si la cantidad es 0
        if ($row['quantity'] > 0) {
            $response->getBody()->write(json_encode([
                "error" => "No puedes quitar un activo de tu portfolio si aún tienes unidades. Debes venderlas primero."
            ]));
            return $response->withStatus(409);
        }

        // 3. Si es 0, borramos el registro
        $del = $db->prepare("DELETE FROM portfolio WHERE user_id = :u AND asset_id = :a");
        $del->execute([':u' => $userId, ':a' => $assetId]);

        $response->getBody()->write(json_encode(["mensaje" => "Activo eliminado del portfolio con éxito"]));
        return $response->withStatus(200);
    }
}