<?php
require_once 'models/entities/report.php';
require_once 'models/entities/income.php';
require_once 'models/entities/bill.php';
require_once 'models/entities/category.php';

class ReportController {
    private $reportModel;
    private $incomeModel;
    private $billModel;
    private $categoryModel;

    public function __construct() {
        $this->reportModel = new Report();
        $this->incomeModel = new Income();
        $this->billModel = new Bill();
        $this->categoryModel = new Category();
    }

    public function index() {
        $reports = $this->reportModel->getAll();
        include 'views/form_report.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];
            $value = $_POST['value'];

            // Validar que no exista un reporte para el mismo mes y año
            if ($this->reportModel->getByMonthAndYear($month, $year)) {
                echo "<script>alert('Ya existe un reporte para este mes y año');</script>";
                echo "<script>window.location.href = 'index.php?controller=report&action=index';</script>";
                return;
            }

            // Validar que el ingreso sea mayor a cero
            if ($value <= 0) {
                echo "<script>alert('El ingreso debe ser mayor a cero');</script>";
                echo "<script>window.location.href = 'index.php?controller=report&action=index';</script>";
                return;
            }

            $this->reportModel->setMonth($month);
            $this->reportModel->setYear($year);
            $reportId = $this->reportModel->save();

            if ($reportId) {
                $this->incomeModel->setValue($value);
                $this->incomeModel->setIdReport($reportId);
                if ($this->incomeModel->save()) {
                    echo "<script>alert('Reporte e ingreso guardados correctamente');</script>";
                } else {
                    echo "<script>alert('Error al guardar el ingreso');</script>";
                }
            } else {
                echo "<script>alert('Error al guardar el reporte');</script>";
            }
        }
        echo "<script>window.location.href = 'index.php?controller=report&action=index';</script>";
    }

    public function view() {
        if (isset($_GET['id'])) {
            $reportId = $_GET['id'];
            $this->reportModel->getById($reportId);
            $this->incomeModel->getByReportId($reportId);
            $bills = $this->billModel->getByReportId($reportId);
            $categories = $this->categoryModel->getAll();
            $income = $this->incomeModel->getValue();
            $billSum = $this->billModel->getBillSum($reportId);
            $savings = $income - $billSum;
            $savingsPercentage = ($savings / $income) * 100;

            include 'views/reports.php';
        } else {
            echo "<script>alert('ID de reporte no proporcionado');</script>";
            echo "<script>window.location.href = 'index.php?controller=report&action=index';</script>";
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $reportId = $_GET['id'];
            $this->reportModel->delete($reportId);
            echo "<script>alert('Reporte eliminado correctamente');</script>";
        } else {
            echo "<script>alert('ID de reporte no proporcionado');</script>";
        }
        echo "<script>window.location.href = 'index.php?controller=report&action=index';</script>";
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reportId = $_POST['reportId'];
            $value = $_POST['value'];

            // Validar que el ingreso sea mayor a cero
            if ($value <= 0) {
                echo "<script>alert('El ingreso debe ser mayor a cero');</script>";
                echo "<script>window.location.href = 'index.php?controller=report&action=view&id=" . $reportId . "';</script>";
                return;
            }

            $this->incomeModel->getByReportId($reportId);
            $this->incomeModel->setValue($value);
            if ($this->incomeModel->update()) {
                echo "<script>alert('Ingreso actualizado correctamente');</script>";
            } else {
                echo "<script>alert('Error al actualizar el ingreso');</script>";
            }
            echo "<script>window.location.href = 'index.php?controller=report&action=view&id=" . $reportId . "';</script>";
        }
    }
}
?>