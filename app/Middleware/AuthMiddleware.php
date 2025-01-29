<?php
namespace App\Middleware;
use App\Session\Session;

class AuthMiddleware {
    public function handle($next) {
        if (!Session::has("user_id")) {
            echo "Acceso no autorizado";
            http_response_code(403);
            exit;
        }
        return $next();
    }
}

