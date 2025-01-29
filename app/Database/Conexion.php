<?php

namespace App\Database;

use PDO;
use PDOException;

class Conexion {

    private static $instancia;
    private $pdo;

    private function __construct() {

        // Leer datos de configuración desde un archivo
        $config = parse_ini_file('config.ini', TRUE);

        try {
            // Crear la conexión a la base de datos
            $this->pdo = new PDO('mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'],
                                    $config['database']['usuario'], $config['database']['password']);
            // Habilitar excepciones para manejar errores de conexión
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
           
        } catch (PDOException $e) {
            // Manejar la excepción de conexión
            echo "Error de conexión a la base de datos: " . $e->getMessage();
            die(); 
        }
    }

    public static function obtenerInstancia() {

        if (!isset(self::$instancia)) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    public function getPdo(){
        return  $this->pdo;
    }


}