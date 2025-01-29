<?php
namespace App\Middleware;
use App\Session\Session;
class StartSession {
    public function handle($next) {
        Session::start();
        return $next();
    }
}

