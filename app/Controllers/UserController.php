<?php
namespace App\Controllers;
use App\Session\Session;

class UserController {
	public function show($parm) {
		var_dump($parm);
	$id = $parm['id'];	
       echo  view("home", ["content" => "El id es $id"]);
    }

    public function protectedRoute(){
        echo "Ruta protegida, usuario id: ".Session::get("user_id");
    }
}

