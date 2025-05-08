<?php
require_once 'models/drivers/conexDb.php';

class Report {
    private $id;
    private $month;
    private $year;
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

    public function getMonth() {
        return $this->month;
    }

    public function setMonth($month) {
        $this->month = $month;
    }

    public function getYear() {
        return $this->year;
    }

    public function setYear($year) {
        $this->year = $year;
    }

    public function save() {
        try {
            $query = "INSERT INTO reports (month, year) VALUES (:month, :year)";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":month", $this->month);
            $statement->bindParam(":year", $this->year);
            $statement->execute();
            return $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            echo "Error al guardar reporte: " . $e->getMessage();
            return false;
        }
    }

    public function update() {
        try {
            $query = "UPDATE reports SET month = :month, year = :year WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":month", $this->month);
            $statement->bindParam(":year", $this->year);
            $statement->bindParam(":id", $this->id);
            return $statement->execute();
        } catch (PDOException $e) {
            echo "Error al actualizar reporte: " . $e->getMessage();
            return false;
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM reports WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":id", $id);
            return $statement->execute();
        } catch (PDOException $e) {
            echo "Error al eliminar reporte: " . $e->getMessage();
            return false;
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM reports WHERE id = :id";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":id", $id);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            if($result) {
                $this->id = $result['id'];
                $this->month = $result['month'];
                $this->year = $result['year'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Error al obtener reporte: " . $e->getMessage();
            return false;
        }
    }

    public function getAll() {
        try {
            $query = "SELECT * FROM reports ORDER BY year DESC, 
                     FIELD(month, 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                     'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre')";
            $statement = $this->conexion->prepare($query);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener reportes: " . $e->getMessage();
            return [];
        }
    }

    public function getByMonthAndYear($month, $year) {
        try {
            $query = "SELECT * FROM reports WHERE month = :month AND year = :year";
            $statement = $this->conexion->prepare($query);
            $statement->bindParam(":month", $month);
            $statement->bindParam(":year", $year);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            if($result) {
                $this->id = $result['id'];
                $this->month = $result['month'];
                $this->year = $result['year'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            echo "Error al obtener reporte: " . $e->getMessage();
            return false;
        }
    }
}
?>