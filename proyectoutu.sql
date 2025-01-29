CREATE TABLE `categorias` (
  `id_categoria` int(5) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `estado` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `clientes` (
  `id` varchar(20) NOT NULL,
  `nombre` varchar(40) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `direccion` varchar(80) DEFAULT NULL,
  `correo_electronico` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `compras` (
  `id_compra` int(5) NOT NULL AUTO_INCREMENT,
  `id_cliente` varchar(20) NOT NULL,
  `fecha` timestamp NULL DEFAULT NULL,
  `medio_pago` char(1) DEFAULT NULL,
  `comentario` varchar(300) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`id_compra`),
  KEY `fk_COMPRAS_CLIENTES1` (`id_cliente`),
  CONSTRAINT `fk_COMPRAS_CLIENTES1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `compras_productos` (
  `id_compra` int(5) NOT NULL,
  `id_producto` int(5) NOT NULL,
  `cantidad` int(5) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_compra`,`id_producto`),
  KEY `fk_COMPRAS_PRODUCTOS_PRODUCTOS1` (`id_producto`),
  CONSTRAINT `fk_COMPRAS_PRODUCTOS_COMPRAS1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_COMPRAS_PRODUCTOS_PRODUCTOS1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `productos` (
  `id_producto` int(5) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `codigo_barras` varchar(150) DEFAULT NULL,
  `precio_venta` decimal(16,2) DEFAULT NULL,
  `cantidad_stock` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `fk_PRODUCTOS_CATEGORIAS` (`id_categoria`),
  CONSTRAINT `fk_PRODUCTOS_CATEGORIAS` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
<?php

namespace App\Database;

use PDO;
use PDOException;

abstract class BaseModel {
    protected $pdo;
    protected $table;
    protected $primaryKey = 'id'; 

    public function __construct(string $table) {

        $con = Conexion::obtenerInstancia();
        $this->pdo = $con->getPdo();
        $this->table = $table;
    }

    public function save() {
        $data = $this->getData(); 
        $keys = array_keys($data);
        $columns = implode(', ', $keys);
        $placeholders = ':' . implode(', :', $keys);

        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders) ON DUPLICATE KEY UPDATE ";

        $updateSet = [];
        foreach ($keys as $key) {
            $updateSet[] = "$key=VALUES($key)";
        }
        $sql .= implode(', ', $updateSet);

        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        try {
            $stmt->execute();
            return true; 
        } catch (PDOException $e) {
            echo "Error al guardar: " . $e->getMessage();
            return false;
        }
    }

    public function update() {
        if (empty($this->{$this->primaryKey})) {
            throw new Exception("El ID del registro es requerido para actualizar.");
        }

        $data = $this->getData(); 
        unset($data[$this->primaryKey]); // Excluir el ID de los datos a actualizar

        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key=:$key";
        }

        $sql = "UPDATE $this->table SET " . implode(', ', $set) . " WHERE $this->primaryKey = :id";

        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(':id', $this->{$this->primaryKey});

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error al actualizar: " . $e->getMessage();
            return false;
        }
    }

    public function delete() {
        if (empty($this->{$this->primaryKey})) {
            throw new Exception("El ID del registro es requerido para eliminar.");
        }

        $sql = "DELETE FROM $this->table WHERE $this->primaryKey = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $this->{$this->primaryKey});

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error al eliminar: " . $e->getMessage();
            return false;
        }
    }

    public function find($id) {
        $sql = "SELECT * FROM $this->table WHERE $this->primaryKey = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $this->hydrate($result);
            return $this;
        }

        return null;
    }

    protected function getData() {
        $data = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== 'pdo' && $key !== 'table' && $key !== 'primaryKey') {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    protected function hydrate($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}<?php

namespace App\Database;

use PDO;
use PDOException;

class QueryBuilder extends BaseModel
{
    protected $select = ['*'];
    protected $join = [];
    protected $where = [];
    protected $whereValues = [];
    protected $groupBy = [];
    protected $having = [];
    protected $havingValues = [];
    protected $orderBy = [];
    protected $limit;
    protected $offset;
    protected $sql;
    protected $sqlCount;
    protected $rowCount = 0;
    protected $columnTypes = []; // Arreglo para almacenar los tipos de datos de las columnas

    public function __construct ( string $table)
    {
        parent::__construct($table);

    }

    public function select(array $columns)
    {
        $this->select = $columns;
        return $this;
    }

    public function join(string $table, string $on)
    {
        $this->join[] = "JOIN $table ON $on";
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->where[] = "$column $operator :$column";
        $this->whereValues[$column] = $value;
        return $this;
    }

    public function groupBy($column)
    {
        $this->groupBy[] = $column;
        return $this;
    }

    public function having($column, $operator, $value)
    {
        $this->having[] = "$column $operator :$column";
        $this->havingValues[$column] = $value;
        return $this;
    }

    public function orderBy($column, $order = 'ASC')
    {
        $this->orderBy[] = "$column $order";
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = (int) $limit; // Convertir a entero
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = (int) $offset; // Convertir a entero
        return $this;
    }

    protected function buildSql()
    {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM $this->table";

        if (!empty($this->join)) {
            $sql .= ' ' . implode(' ', $this->join);
        }
        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }
        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }
        if (!empty($this->having)) {
            $sql .= ' HAVING ' . implode(' AND ', $this->having);
        }

        $this->sqlCount = "SELECT COUNT(*) FROM (" . $sql . ") subquery";

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }
        if ($this->limit !== null) {
            $sql .= " LIMIT :limit";
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET :offset";
        }

        $this->sql = $sql;
        return $sql;
    }

    public function get()
    {
        $sql = $this->buildSql();
        $stmt = $this->pdo->prepare($sql);

        $stmtCount = $this->pdo->prepare($this->sqlCount);

        // Bind values for WHERE, HAVING, LIMIT, and OFFSET
        foreach ($this->whereValues as $key => $value) {
            $stmt->bindValue(":$key", $value);
            $stmtCount->bindValue(":$key", $value);
        }
        foreach ($this->havingValues as $key => $value) {
            $stmt->bindValue(":$key", $value);
            $stmtCount->bindValue(":$key", $value);
        }
        if ($this->limit !== null) {
            $stmt->bindValue(':limit', $this->limit, PDO::PARAM_INT);
        }
        if ($this->offset !== null) {
            $stmt->bindValue(':offset', $this->offset, PDO::PARAM_INT);
        }

        try {
            $stmt->execute();
            $stmtCount->execute();
            $this->rowCount = (int) $stmtCount->fetchColumn();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->columnTypes = [];

            foreach ($result as $row) {
                $this->columnTypes = array_merge($this->columnTypes, array_keys($row));
                break; // Solo necesitamos los nombres de las columnas de la primera fila
            }

            return $result;

        } catch (PDOException $e) {
            // Manejo de excepciones
            echo "Error en la consulta: " . $e->getMessage();
            return [];
        }
    }

    public function getAsHtmlTable($url, $pagina_actual, $elementos_por_pagina)
    {
        $this->limit($elementos_por_pagina)->offset(($pagina_actual - 1) * $elementos_por_pagina);
        $results = $this->get();
        // Obtener los nombres de las columnas
        $columns = array_keys($results[0]);
        // Obtener el total de registros

        $total_paginas = ceil($this->rowCount / $elementos_por_pagina);

        // Crear la cabecera de la tabla

        $html = "<div class='dtabla'> <table><thead><tr>";
        foreach ($columns as $column) {
            $html .= "<th>$column</th>";
        }
        $html .= "</tr></thead><tbody>";

        // Crear las filas de la tabla
        foreach ($results as $row) {
            $html .= "<tr>";
            foreach ($columns as $column) {
                $html .= "<td>" . htmlspecialchars($row[$column]) . "</td>";
            }
            $html .= "</tr>";
        }

        // Crear los enlaces de paginación
        $paginacion = '';
        for ($i = 1; $i <= $total_paginas; $i++) {
            $paginacion .= "<a class='boton_centrado' href='" . $url . "$i'" . " >$i</a> ";
        }

        // Agregar la paginación al final de la tabla
        $html .= "</div></tbody></table><div class='paginacion'>$paginacion</div>";

        return $html;
    }


    public function getColumnType($column)
    {
        return $this->columnTypes[$column] ?? 'text'; // Valor por defecto si no se encuentra el tipo
    }


    protected function mapTypeToInputType($dataType)
    {
        $mapping = [
            'int' => 'number',
            'integer' => 'number',
            'float' => 'number',
            'double' => 'number',
            'decimal' => 'number',
            'varchar' => 'text',
            'text' => 'textarea',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'boolean' => 'checkbox',
            // ... agregar más mapeos según tus necesidades
        ];

        return $mapping[$dataType] ?? 'text';
    }

    public function generateForm($data)
    {

        $html = '<form> <div class="form-group">';
        foreach ($data[0] as $column => $value) {
      $inputType = $this->mapTypeToInputType($this->getColumnType($column));
            $html .= "<div class= 'row'>
                        <label class='col-form-label col-2' for='$column'>$column:</label>";

            $html .= "<input type='$inputType' class='col-form-control col-5' name='$column' value='$value'><br> </div>";
        
                        }
        $html .= '</div></form> ';
        return $html;
    }


}<?php
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

