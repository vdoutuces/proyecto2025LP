<?php 

namespace App\Routes;



$router->get('', 'HomeController@index');
$router->get('about', function() {
    echo "Página de Acerca de.";
});
$router->post('submit', 'FormController@store');
$router->get('user/{id}', 'UserController@show');
$router->get('productos/pagina/{pg}','ProductosController@paginar');
$router->get('productos/editar/{id}','ProductosController@editar');
$router->get('clientes/{id}','ClientesController@show');
$router->get('clientes','ClientesController@index');

