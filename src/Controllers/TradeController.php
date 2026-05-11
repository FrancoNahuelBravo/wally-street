<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\DB;

class TradeController {
    
    public function buy(Request $request, Response $response) {
        $userId = $request->getAttribute('user_id'); // Viene del Middleware
        $data = $request->getParsedBody();
        $assetId = $data['asset_id'] ?? null;
        $quantity = $data['quantity'] ?? 0;

        $db = DB::getConnection();

        // 1. Validar si el activo existe y su precio actual
        $stmtAsset = $db->prepare("SELECT current_price FROM assets WHERE id = :id");
        $stmtAsset->execute([':id' => $assetId]);
        $asset = $stmtAsset->fetch(\PDO::FETCH_ASSOC);

        if (!$asset) {
            return $this->jsonRes($response, ["error" => "El activo no existe"], 404);
        }

        $totalCost = $asset['current_price'] * $quantity;

        // 2. Validar si el usuario tiene saldo suficiente
        $stmtUser = $db->prepare("SELECT balance FROM users WHERE id = :id");
        $stmtUser->execute([':id' => $userId]);
        $user = $stmtUser->fetch(\PDO::FETCH_ASSOC);

        if ($user['balance'] < $totalCost) {
            return $this->jsonRes($response, ["error" => "Saldo insuficiente. Costo: $totalCost, Tu saldo: {$user['balance']}"], 409);
        }

        // 3. OPERACIÓN (Uso transacción SQL por seguridad)
        try {
            $db->beginTransaction();

            // A. Restar saldo al usuario
            $db->prepare("UPDATE users SET balance = balance - :cost WHERE id = :id")
               ->execute([':cost' => $totalCost, ':id' => $userId]);

            // B. Sumar unidades al portfolio (o crear si no existe)
            $db->prepare("INSERT INTO portfolio (user_id, asset_id, quantity) 
                          VALUES (:u, :a, :q) 
                          ON DUPLICATE KEY UPDATE quantity = quantity + :q")
               ->execute([':u' => $userId, ':a' => $assetId, ':q' => $quantity]);

            // C. Registrar en historial
            $db->prepare("INSERT INTO transactions (user_id, asset_id, transaction_type, quantity, price_per_unit, total_amount) 
                          VALUES (:u, :a, 'buy', :q, :p, :t)")
               ->execute([
                   ':u' => $userId, ':a' => $assetId, ':q' => $quantity, 
                   ':p' => $asset['current_price'], ':t' => $totalCost
               ]);

            $db->commit();
            return $this->jsonRes($response, ["mensaje" => "Compra exitosa"], 200);

        } catch (\Exception $e) {
            $db->rollBack();
            return $this->jsonRes($response, ["error" => "Error procesando la compra"], 409);
        }
    }

    private function jsonRes($response, $data, $status) {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }





    

public function sell(Request $request, Response $response) {
    $userId = $request->getAttribute('user_id'); 
    $data = $request->getParsedBody();
    $assetId = $data['asset_id'] ?? null;
    $quantity = $data['quantity'] ?? 0;

    $db = \App\Models\DB::getConnection();

    // 1. Validar si el activo existe y su precio actual
    $stmtAsset = $db->prepare("SELECT current_price FROM assets WHERE id = :id");
    $stmtAsset->execute([':id' => $assetId]);
    $asset = $stmtAsset->fetch(\PDO::FETCH_ASSOC);

    if (!$asset) {
        return $this->jsonRes($response, ["error" => "El activo no existe"], 404);
    }

    // 2. Validar si el usuario posee el activo y tiene cantidad suficiente
    $stmtPortfolio = $db->prepare("SELECT quantity FROM portfolio WHERE user_id = :u AND asset_id = :a");
    $stmtPortfolio->execute([':u' => $userId, ':a' => $assetId]);
    $portfolio = $stmtPortfolio->fetch(\PDO::FETCH_ASSOC);

    if (!$portfolio || $portfolio['quantity'] < $quantity) {
        return $this->jsonRes($response, ["error" => "No tienes suficientes unidades para vender."], 409);
    }

    $totalEarned = $asset['current_price'] * $quantity;

    try {
        $db->beginTransaction();

        // A. Sumar saldo al usuario
        $db->prepare("UPDATE users SET balance = balance + :earned WHERE id = :id")
           ->execute([':earned' => $totalEarned, ':id' => $userId]);

        // B. Restar unidades del portfolio
        $db->prepare("UPDATE portfolio SET quantity = quantity - :q WHERE user_id = :u AND asset_id = :a")
           ->execute([':q' => $quantity, ':u' => $userId, ':a' => $assetId]);

        // C. Registrar en historial
        $db->prepare("INSERT INTO transactions (user_id, asset_id, transaction_type, quantity, price_per_unit, total_amount) 
                      VALUES (:u, :a, 'sell', :q, :p, :t)")
           ->execute([
               ':u' => $userId, ':a' => $assetId, ':q' => $quantity, 
               ':p' => $asset['current_price'], ':t' => $totalEarned
           ]);

        $db->commit();
        return $this->jsonRes($response, ["mensaje" => "Venta exitosa. Recibiste $totalEarned USD"], 200);

    } catch (\Exception $e) {
        $db->rollBack();
        return $this->jsonRes($response, ["error" => "Error procesando la venta"], 409);
    }
}


public function getHistory(Request $request, Response $response) {
    $userId = $request->getAttribute('user_id'); // El ID que viene del Middleware
    $db = \App\Models\DB::getConnection();
    
    // Traer todas las transacciones de este usuario, las más nuevas primero
    $stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = :id ORDER BY transaction_date DESC");
    $stmt->execute([':id' => $userId]);
    $history = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return $this->jsonRes($response, $history, 200);
}


}