<?php
class ConexDb {
    private $host = 'localhost';
    private $db = 'proyecto_1_db';
    private $user = 'root';
    private $password = '';
    private $conexion;

    function __construct() {
        try {
            $this->conexion = new PDO("mysql:host=$this->host;dbname=$this->db", $this->user, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
    }

    function getConexion() {
        return $this->conexion;
    }
}
?>