<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users'; // Nombre de la tabla en tu DB
    public $timestamps = false; // El script SQL ya maneja el created_at automático

    // Campos que permitimos cargar desde la API
    protected $fillable = ['name', 'email', 'password', 'balance'];
}