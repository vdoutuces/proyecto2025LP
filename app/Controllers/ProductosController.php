<?php
namespace App\Controllers;
use App\Session\Session;
use App\Database\Conexion;
use App\Database\QueryBuilder;

class ProductosController {

    function __construct(){
    }


	public function index() {

       echo  view("productos", ["content" => "mi primer pagina"]);
    }

    public function paginar($param){
        $pag = $param['pg'];
 
 // Crear una instancia de QueryBuilder
$queryBuilder = new QueryBuilder('productos');
// Construir la consulta
$resultados = $queryBuilder
    ->select(['*'])
    ->where('cantidad_stock', '>', 1)
    ->orderBy('nombre') // Suponiendo que quieres ordenar por nombre
    ->limit(5)
    ->offset( 0 * 5) // Página 5, 15 resultados por página
    ->get();
// Obtener la tabla HTML
$tabla_html = $queryBuilder->getAsHtmlTable('http://localhost/productos/pagina/',$pag, 5);


        echo view("paginar",["content" => $tabla_html]);
    
    }

public function editar($param) {

    $queryBuilder = new QueryBuilder('productos');

    $id= $param["id"];
    $resultados = $queryBuilder
    ->select(['*'])
    ->where('id_producto', '=',$id)
    ->get();

    $form = $queryBuilder->generateForm($resultados);
    echo view('editar',["content" => $form]);
}    


}

