<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class UserController {

    public function create(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // --- VALIDACIONES DEL PDF ---
        
        // 1. Validar nombre (Solo letras y no vacío)
        if (empty($name) || !preg_match("/^[a-zA-Z ]*$/", $name)) {
            return $this->jsonRes($response, ["error" => "El nombre solo puede contener letras."], 400);
        }

        // 2. Validar Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonRes($response, ["error" => "Formato de email inválido."], 400);
        }

        // 3. Validar Password (Minúsc., Mayúsc., Núm., Especial, min 8)
        $passRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        if (!preg_match($passRegex, $password)) {
            return $this->jsonRes($response, ["error" => "La contraseña no cumple con los requisitos de seguridad."], 400);
        }

        // 4. Verificar si el email ya existe
        if (User::findByEmail($email)) {
            return $this->jsonRes($response, ["error" => "El email ya está registrado."], 400);
        }

        // --- REGISTRO ---
        $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        if (User::create($data)) {
            return $this->jsonRes($response, ["mensaje" => "Usuario registrado con éxito. Recibiste 1000 USD de bono."], 200);
        }

        return $this->jsonRes($response, ["error" => "No se pudo crear el usuario."], 409);
    }

    public function login(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $user = User::findByEmail($data['email'] ?? '');

        if ($user && password_verify($data['password'] ?? '', $user['password'])) {
            $token = bin2hex(random_bytes(32));
            $expiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));

            User::updateToken($user['id'], $token, $expiration);

            return $this->jsonRes($response, [
                "mensaje" => "Login exitoso",
                "token" => $token,
                "user" => ["name" => $user['name'], "balance" => $user['balance']]
            ], 200);
        }

        return $this->jsonRes($response, ["error" => "Email o contraseña incorrectos."], 401);
    }

    private function jsonRes($response, $data, $status) {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }




    // src/Controllers/UserController.php

    // GET /users/{user_id} - Ver perfil y saldo
    public function getProfile(Request $request, Response $response, array $args) {
        $db = \App\Models\DB::getConnection();
        $stmt = $db->prepare("SELECT id, name, email, balance FROM users WHERE id = :id");
        $stmt->execute([':id' => $args['id']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return $this->jsonRes($response, ["error" => "Usuario no encontrado"], 404);
        }
        return $this->jsonRes($response, $user, 200);
    }

    // PUT /users/{user_id} - Editar nombre o clave
    public function updateProfile(Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $db = \App\Models\DB::getConnection();
    
    // Solo permitimos editar name y password según el PDF
        $name = $data['name'] ?? null;
        $pass = isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null;

        if ($name && $pass) {
            $stmt = $db->prepare("UPDATE users SET name = :name, password = :pass WHERE id = :id");
            $stmt->execute([':name' => $name, ':pass' => $pass, ':id' => $args['id']]);
        } elseif ($name) {
            $stmt = $db->prepare("UPDATE users SET name = :name WHERE id = :id");
            $stmt->execute([':name' => $name, ':id' => $args['id']]);
        }

        return $this->jsonRes($response, ["mensaje" => "Perfil actualizado"], 200);
    }

    // POST /logout - Borrar el token
    public function logout(Request $request, Response $response) {
        return $this->jsonRes($response, ["mensaje" => "Sesión cerrada"], 200);
    }



   

    public function listUsersAdmin(Request $request, Response $response) {
    $db = \App\Models\DB::getConnection();
    
    // Query para traer nombre y el valor total del portfolio (precio mercado * cantidad)
    $sql = "SELECT u.name, 
                   (SELECT SUM(p.quantity * a.current_price) 
                    FROM portfolio p 
                    JOIN assets a ON p.asset_id = a.id 
                    WHERE p.user_id = u.id) as total_portfolio_value
            FROM users u WHERE u.is_admin = 0"; // Solo inversores
            
    $stmt = $db->query($sql);
    $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
}



}