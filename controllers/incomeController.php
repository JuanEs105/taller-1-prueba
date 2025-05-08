<?php
require_once 'models/entities/income.php';
require_once 'models/entities/report.php';

class IncomeController {
    private $incomeModel;
    private $reportModel;

    public function __construct() {
        $this->incomeModel = new Income();
        $this->reportModel = new Report();
    }

    public function index() {
        $reports = $this->reportModel->getAll();
        include 'views/form_income.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];
            $value = $_POST['value'];

            // Validar que el ingreso sea mayor a cero
            if ($value <= 0) {
                echo "<script>alert('El ingreso debe ser mayor a cero');</script>";
                echo "<script>window.location.href = 'index.php?controller=income&action=index';</script>";
                return;
            }

            // Verificar si ya existe un reporte para este mes y año
            if (!$this->reportModel->getByMonthAndYear($month, $year)) {
                // Si no existe, crear uno nuevo
                $this->reportModel->setMonth($month);
                $this->reportModel->setYear($year);
                $reportId = $this->reportModel->save();
                
                if (!$reportId) {
                    echo "<script>alert('Error al crear el reporte');</script>";
                    echo "<script>window.location.href = 'index.php?controller=income&action=index';</script>";
                    return;
                }
            } else {
                $reportId = $this->reportModel->getId();
                
                // Verificar si ya existe un ingreso para este reporte
                if ($this->incomeModel->getByReportId($reportId)) {
                    echo "<script>alert('Ya existe un ingreso registrado para este mes y año');</script>";
                    echo "<script>window.location.href = 'index.php?controller=income&action=index';</script>";
                    return;
                }
            }
            
            $this->incomeModel->setValue($value);
            $this->incomeModel->setIdReport($reportId);
            
            if ($this->incomeModel->save()) {
                echo "<script>alert('Ingreso guardado correctamente');</script>";
            } else {
                echo "<script>alert('Error al guardar el ingreso');</script>";
            }
        }
        echo "<script>window.location.href = 'index.php?controller=income&action=index';</script>";
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $value = $_POST['value'];

            // Validar que el ingreso sea mayor a cero
            if ($value <= 0) {
                echo "<script>alert('El ingreso debe ser mayor a cero');</script>";
                echo "<script>window.location.href = 'index.php?controller=report&action=view&id=" . $_POST['reportId'] . "';</script>";
                return;
            }

            $this->incomeModel->getById($id);
            $this->incomeModel->setValue($value);
            
            if ($this->incomeModel->update()) {
                echo "<script>alert('Ingreso actualizado correctamente');</script>";
            } else {
                echo "<script>alert('Error al actualizar el ingreso');</script>";
            }
            echo "<script>window.location.href = 'index.php?controller=report&action=view&id=" . $_POST['reportId'] . "';</script>";
        }
    }
}
?>