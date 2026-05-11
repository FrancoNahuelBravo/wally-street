<?php
namespace App\Models;

use PDO;

class Asset {
    // Listar todos los activos
    public static function getAll() {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT * FROM assets");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar el precio de un activo
    public static function updatePrice($id, $newPrice) {
        $db = DB::getConnection();
        $stmt = $db->prepare("UPDATE assets SET current_price = :price, last_update = NOW() WHERE id = :id");
        return $stmt->execute([
            ':price' => $newPrice,
            ':id' => $id
        ]);
    }

    // La función matemática del TP
    public static function variarPrecioPorTiempo($precioActual, $timestampUltimaVez, $volatilidadPorSegundo = 0.05) {
        $tiempoPasado = time() - strtotime($timestampUltimaVez);
        if ($tiempoPasado <= 0) return $precioActual;

        $direccion = mt_rand(-100, 100) / 100;
        $delta = $direccion * $volatilidadPorSegundo * $tiempoPasado;

        return $precioActual + $delta;
    }
}