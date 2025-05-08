<?php
require_once 'models/entities/bill.php';
require_once 'models/entities/category.php';
require_once 'models/entities/report.php';

class BillController {
    private $billModel;
    private $categoryModel;
    private $reportModel;

    public function __construct() {
        $this->billModel = new Bill();
        $this->categoryModel = new Category();
        $this->reportModel = new Report();
    }

    public function index() {
        $categories = $this->categoryModel->getAll();
        $reports = $this->reportModel->getAll();
        include 'views/form_bill.php';
    }

    public function create() {
        if (isset($_GET['reportId'])) {
            $reportId = $_GET['reportId'];
            $this->reportModel->getById($reportId);
            $categories = $this->categoryModel->getAll();
            include 'views/bill/create.php';
        } else {
            echo "<script>alert('ID de reporte no proporcionado');</script>";
            echo "<script>window.location.href = 'index.php?controller=report&action=index';</script>";
        }
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reportId = $_POST['reportId'];
            $categoryId = $_POST['categoryId'];
            $value = $_POST['value'];

            // Validar que el valor sea mayor a cero
            if ($value <= 0) {
                echo "<script>alert('El valor del gasto debe ser mayor a cero');</script>";
                echo "<script>window.location.href = 'index.php?controller=bill&action=create&reportId=" . $reportId . "';</script>";
                return;
            }

            $this->billModel->setValue($value);
            $this->billModel->setIdCategory($categoryId);
            $this->billModel->setIdReport($reportId);
            
            if ($this->billModel->save()) {
                echo "<script>alert('Gasto guardado correctamente');</script>";
                echo "<script>window.location.href = 'index.php?controller=report&action=view&id=" . $reportId . "';</script>";
            } else {
                echo "<script>alert('Error al guardar el gasto');</script>";
                echo "<script>window.location.href = 'index.php?controller=bill&action=create&reportId=" . $reportId . "';</script>";
            }
        }
    }

    public function edit() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->billModel->getById($id);
            $categories = $this->categoryModel->getAll();
            include 'views/bill/edit.php';
        } else {
            echo "<script>alert('ID de gasto no proporcionado');</script>";
            echo "<script>window.location.href = 'index.php?controller=report&action=index';</script>";
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $categoryId = $_POST['categoryId'];
            $value = $_POST['value'];
            $reportId = $_POST['reportId'];

            // Validar que el valor sea mayor a cero
            if ($value <= 0) {
                echo "<script>alert('El valor del gasto debe ser mayor a cero');</script>";
                echo "<script>window.location.href = 'index.php?controller=bill&action=edit&id=" . $id . "';</script>";
                return;
            }

            $this->billModel->getById($id);
            $this->billModel->setValue($value);
            $this->billModel->setIdCategory($categoryId);
            
            if ($this->billModel->update()) {
                echo "<script>alert('Gasto actualizado correctamente');</script>";
                echo "<script>window.location.href = 'index.php?controller=report&action=view&id=" . $reportId . "';</script>";
            } else {
                echo "<script>alert('Error al actualizar el gasto');</script>";
                echo "<script>window.location.href = 'index.php?controller=bill&action=edit&id=" . $id . "';</script>";
            }
        }
    }

    public function delete() {
        if (isset($_GET['id']) && isset($_GET['reportId'])) {
            $id = $_GET['id'];
            $reportId = $_GET['reportId'];
            
            if ($this->billModel->delete($id)) {
                echo "<script>alert('Gasto eliminado correctamente');</script>";
            } else {
                echo "<script>alert('Error al eliminar el gasto');</script>";
            }
            echo "<script>window.location.href = 'index.php?controller=report&action=view&id=" . $reportId . "';</script>";
        } else {
            echo "<script>alert('ID de gasto o reporte no proporcionado');</script>";
            echo "<script>window.location.href = 'index.php?controller=report&action=index';</script>";
        }
    }
}
?>