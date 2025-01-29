<?php

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
 public function getOne() {
        $this->limit(1); 
        $results = $this->get();
        return isset($results[0]) ? $results[0] : null; 
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
            $html .= '<button type="submit">Guardar</button>';

                        }
        $html .= '</div></form> ';
        return $html;
    }


}