<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model {
    protected $table = 'assets'; // Nombre de la tabla en MySQL
    public $timestamps = false;  // Porque usamos last_update manual
    
    // Aquí podrías meter la función de variar precio que probamos antes
    public function aplicarVariacion() {
        // Lógica para actualizar el precio en base al tiempo
    }
}