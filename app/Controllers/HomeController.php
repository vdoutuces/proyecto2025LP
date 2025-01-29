<?php
namespace App\Controllers;
use App\Session\Session;

class HomeController {
	public function index() {

       echo  view("home", ["content" => "mi primer pagina"]);
    }

    public function protectedRoute(){
        echo "Ruta protegida, usuario id: ".Session::get("user_id");
    }
}

