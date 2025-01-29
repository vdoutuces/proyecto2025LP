<?php


require __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../app/Helpers/functions.php";
require_once __DIR__ . "/../app/Session/Session.php";
require_once __DIR__ . "/../app/Middleware/StartSession.php";
require_once __DIR__ . "/../app/Routes/Request.php";
require_once __DIR__ . "/../app/Routes/Router.php";

use App\Routes\Router;

// Cargar rutas
$router = new Router();
require __DIR__ . "/../app/Routes/web.php";

// Despachar la solicitud
$router->dispatch();


