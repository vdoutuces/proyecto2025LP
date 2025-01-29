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

    public function all() {
        $sql = "SELECT * FROM $this->table";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}