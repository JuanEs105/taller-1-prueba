<?php
require_once 'models/drivers/conexDb.php';

class Bill {
    private $id;
    private $value;
    private $idCategory;
    private $idReport;
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

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getIdCategory() {
        return $this->idCategory;
    }

    public function setIdCategory($idCategory) {
        $this->idCategory = $idCategory;
    }

    public function getIdReport() {
        return $this->idReport;
    }

    public function setIdReport($idReport) {
        $this->idReport = $idReport;
    }

    public function save() {
        try {
            $query = "INSERT INTO bills (value, idCategory, idReport) VALUES (:value, :idCategory, :idReport)";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":value", $this->value);
            $statement->bindParam(":idCategory", $this->idCategory);
            $statement->bindParam(":idReport", $this->idReport);
            return $statement->execute();
        } catch (PDOException $e) {
            echo "Error al guardar gasto: " . $e->getMessage();
            return false;
        }
    }

    public function update() {
        try {
            $query = "UPDATE bills SET value = :value, idCategory = :idCategory WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":value", $this->value);
            $statement->bindParam(":idCategory", $this->idCategory);
            $statement->bindParam(":id", $this->id);
            return $statement->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar gasto: " . $e->getMessage();
            return false;
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM bills WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":id", $id);
            return $statement->execute();
        } catch (PDOException $e) {
            echo "Error al eliminar gasto: " . $e->getMessage();
            return false;
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM bills WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":id", $id);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            if($result) {
                $this->id = $result['id'];
                $this->value = $result['value'];
                $this->idCategory = $result['idCategory'];
                $this->idReport = $result['idReport'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Error al obtener gasto: " . $e->getMessage();
            return false;
        }
    }

    public function getByReportId($reportId) {
        try {
            $query = "SELECT b.*, c.name as categoryName, c.percentage 
                     FROM bills b 
                     JOIN categories c ON b.idCategory = c.id 
                     WHERE b.idReport = :reportId";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":reportId", $reportId);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener gastos: " . $e->getMessage();
            return [];
        }
    }

    public function getBillSum($reportId) {
        try {
            $query = "SELECT SUM(value) as total FROM bills WHERE idReport = :reportId";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":reportId", $reportId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?: 0;
        } catch (PDOException $e) {
            echo "Error al calcular suma de gastos: " . $e->getMessage();
            return 0;
        }
    }
}
?>