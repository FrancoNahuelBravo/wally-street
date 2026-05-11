<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;
use App\Models\DB;

class AuthMiddleware {
    public function __invoke(Request $request, Handler $handler) {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT id, token_expired_at FROM users WHERE token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // 1. Validar si el token existe y no expiró
        if (!$user || strtotime($user['token_expired_at']) < time()) {
            $response = new Response();
            $response->getBody()->write(json_encode(["error" => "No autorizado o sesión expirada"]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // 2. "Estirar" la vida del token 5 minutos más
        $newExpiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $update = $db->prepare("UPDATE users SET token_expired_at = :exp WHERE id = :id");
        $update->execute([':exp' => $newExpiration, ':id' => $user['id']]);
        $request = $request->withAttribute('user_id', $user['id']); 

return $handler->handle($request);
        return $handler->handle($request);
    }
}