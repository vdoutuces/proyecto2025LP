<?php

namespace App\Controllers;

use App\Session\Session;
use App\Database\Conexion;
use App\Database\QueryBuilder;
//use App\Models\Cliente; // Importar el modelo de Cliente

class ClientesController {

    public function index() {
        $queryBuilder = new QueryBuilder('clientes');
        $clientes = $queryBuilder->get();

        // Obtener las compras de cada cliente (ejemplo básico)
        foreach ($clientes as &$cliente) {
            $cliente['compras'] = (new QueryBuilder('compras'))
                                    ->where('id_cliente', '=', $cliente['id'])
                                    ->get();
        }

        echo view('cliente', ['clientes' => $clientes]);
    }

    public function show($id) {
        $cliente = []; //(new Cliente())->find($id); 

        // Obtener las compras del cliente
        $compras = (new QueryBuilder('compras'))
                    ->where('id_cliente', '=', $id)
                    ->get();

        // Obtener los detalles de cada compra (productos)
        foreach ($compras as &$compra) {
            $compra['productos'] = (new QueryBuilder('compras_productos'))
                                    ->where('id_compra', '=', $compra['id_compra'])
                                    ->join('productos', 'compras_productos.id_producto = productos.id_producto')
          //                          ->select(['productos.nombre', 'compras_productos.cantidad', 'compras_productos.total'])
                                    ->get();
        }

        echo view('cliente', ['cliente' => $cliente, 'compras' => $compras]);
    }

    // ... (métodos para crear, actualizar y eliminar clientes) ...
}
