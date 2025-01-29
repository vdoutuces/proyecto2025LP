<?php

namespace App\Routes;
use App\Routes\Request;


class Router {
    protected $routes = [];

    public function get($uri, $action) {
        $uri = $this->convertToRegex($uri);
        $this->routes['GET'][$uri] = $action;
    }

    public function post($uri, $action) {
        $uri = $this->convertToRegex($uri);
        $this->routes['POST'][$uri] = $action;
    }

    protected function convertToRegex($uri) {
        return '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[\w-]+)', $uri) . '$#';
    }

    public function dispatch() {
        $uri = Request::uri();
        $method = Request::method();

        foreach ($this->routes[$method] as $route => $action) {
            if (preg_match($route, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if (is_callable($action)) {
                    return $action($params);
                }

                if (is_string($action)) {
                    list($controller, $method) = explode('@', $action);
                    $controller = "App\\Controllers\\{$controller}";
                    $controllerInstance = new $controller();
                    return $controllerInstance->$method($params);
                }
            }
        }
        
        $mensaje = "Página no encontrada.";
        echo  errorView($mensaje);
    }
}

