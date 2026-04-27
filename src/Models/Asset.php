<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model {
    protected $table = 'assets';
    public $timestamps = false;
    protected $fillable = ['name', 'current_price', 'last_update'];

    
    //Función requerida por el punto 1 del PDF
    public static function variarPrecioPorTiempo($precioActual, $timestampUltimaVez, $volatilidadPorSegundo = 0.05) {
        // 1. Calcular segundos pasados 
        $tiempoPasado = time() - strtotime($timestampUltimaVez); 
        
        if ($tiempoPasado <= 0) return $precioActual;

        // 2. Cambio aleatorio entre -1.0 y 1.0
        $direccion = mt_rand(-100, 100) / 100;

        // 3. El cambio total depende del tiempo
        $delta = $direccion * $volatilidadPorSegundo * $tiempoPasado;

        return $precioActual + $delta;
    }
}