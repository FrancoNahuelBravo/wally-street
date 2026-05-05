<?php
namespace App\Models;

use PDO;

class User {
    public static function findByEmail($email) {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = DB::getConnection();
        $stmt = $db->prepare("INSERT INTO users (name, email, password, balance) VALUES (:name, :email, :password, :balance)");
        return $stmt->execute([
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':password' => $data['password'],
            ':balance'  => 1000.00
        ]);
    }

    public static function updateToken($id, $token, $expiration) {
        $db = DB::getConnection();
        $stmt = $db->prepare("UPDATE users SET token = :token, token_expired_at = :expiration WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':token' => $token,
            ':expiration' => $expiration
        ]);
    }
}