<?php
require_once 'models/drivers/conexDb.php';

class Income {
    private $id;
    private $value;
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

    public function getIdReport() {
        return $this->idReport;
    }

    public function setIdReport($idReport) {
        $this->idReport = $idReport;
    }

    public function save() {
        try {
            $query = "INSERT INTO income (value, idReport) VALUES (:value, :idReport)";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":value", $this->value);
            $statement->bindParam(":idReport", $this->idReport);
            return $statement->execute();
        } catch (PDOException $e) {
            echo "Error al guardar ingreso: " . $e->getMessage();
            return false;
        }
    }

    public function update() {
    try {
        // Solo actualizamos el valor, no el idReport
        $query = "UPDATE income SET value = :value WHERE id = :id";
        $statement = $this->conexion->prepare($query);
        $statement->bindParam(":value", $this->value);
        $statement->bindParam(":id", $this->id);
        return $statement->execute();
    } catch (PDOException $e) {
        echo "Error al actualizar ingreso: " . $e->getMessage();
        return false;
    }
}

    public function getByReportId($reportId) {
        try {
            $query = "SELECT * FROM income WHERE idReport = :reportId";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":reportId", $reportId);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            if($result) {
                $this->id = $result['id'];
                $this->value = $result['value'];
                $this->idReport = $result['idReport'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Error al obtener ingreso: " . $e->getMessage();
            return false;
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM income WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":id", $id);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            if($result) {
                $this->id = $result['id'];
                $this->value = $result['value'];
                $this->idReport = $result['idReport'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Error al obtener ingreso: " . $e->getMessage();
            return false;
        }
    }
}
?>