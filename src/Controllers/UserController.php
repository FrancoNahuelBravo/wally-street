<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class UserController {
    
    // Método para registrar un nuevo usuario
    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();

        // Encriptamos la clave y ponemos el balance inicial
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['balance'] = 1000.00;

        try {
            $user = User::create($data);
            $res = ["mensaje" => "Usuario registrado con éxito", "user_id" => $user->id];
            $status = 201;
        } catch (\Exception $e) {
            $res = ["error" => "No se pudo registrar (email ya existe o datos incompletos)"];
            $status = 400;
        }

        $response->getBody()->write(json_encode($res));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    // Método para el login
    public function login(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $user = User::where('email', $email)->first();

    if ($user && password_verify($password, $user->password)) {
        // Genero un token aleatorio (bin2hex genera un string seguro) 
        $token = bin2hex(random_bytes(32));
        
        // Calculo la expiración: ahora + 5 minutos 
        $expiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Guardo en la base de datos 
        $user->update([
            'token' => $token,
            'token_expired_at' => $expiration
        ]);

        $res = [
            "mensaje" => "Login exitoso",
            "token" => $token,
            "expires_at" => $expiration,
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "balance" => $user->balance 
            ]
        ];
        $status = 200;
    } else {
        $res = ["error" => "Credenciales incorrectas. Verificá tu email y contraseña."];
        $status = 401;
    }

    $response->getBody()->write(json_encode($res));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
}
}