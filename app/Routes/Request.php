<?php

namespace App\Routes;


class Request {
    // Método HTTP (GET, POST, etc.)
    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    // URI de la solicitud
    public static function uri() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return trim($uri, '/');
    }

    // Datos enviados (GET o POST)
    public static function input($key = null) {
        $data = array_merge($_GET, $_POST);
        return $key ? ($data[$key] ?? null) : $data;
    }
}


