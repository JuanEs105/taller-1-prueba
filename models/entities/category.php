<?php
require_once 'models/drivers/conexDb.php';

class Category {
    private $id;
    private $name;
    private $percentage;
    private $conexion;

    public function __construct() {
        $db = new ConexDb();
        $this->conexion = $db->getConexion();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPercentage() {
        return $this->percentage;
    }

    public function setPercentage($percentage) {
        $this->percentage = $percentage;
    }

    public function save() {
        try {
            $query = "INSERT INTO categories (name, percentage) VALUES (:name, :percentage)";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":name", $this->name);
            $statement->bindParam(":percentage", $this->percentage);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar categoría: " . $e->getMessage());
            return false;
        }
    }

    public function update() {
        try {
            $query = "UPDATE categories SET name = :name, percentage = :percentage WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":name", $this->name);
            $statement->bindParam(":percentage", $this->percentage);
            $statement->bindParam(":id", $this->id);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar categoría: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM categories WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":id", $id);
            return $statement->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar categoría: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM categories WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":id", $id);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            if($result) {
                $this->id = $result['id'];
                $this->name = $result['name'];
                $this->percentage = $result['percentage'];
                return $this; // Devuelve el objeto actual con los datos cargados
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al obtener categoría: " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        try {
            $query = "SELECT * FROM categories ORDER BY name";
            $statement = $this->conexion->prepare($query);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener categorías: " . $e->getMessage());
            return [];
        }
    }

    public function hasRelatedBills($categoryId) {
        try {
            $query = "SELECT COUNT(*) as count FROM bills WHERE idCategory = :categoryId";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":categoryId", $categoryId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar relaciones: " . $e->getMessage());
            return true; // Por seguridad, si hay un error, asumimos que hay relaciones
        }
    }
}
?>