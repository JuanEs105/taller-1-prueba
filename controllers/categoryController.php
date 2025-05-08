<?php
require_once 'models/entities/category.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    public function index() {
        $categories = $this->categoryModel->getAll();
        include 'views/form_category.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $percentage = $_POST['percentage'];

            // Validar que el porcentaje sea mayor a cero y no supere el 100%
            if ($percentage <= 0 || $percentage > 100) {
                echo "<script>alert('El porcentaje debe ser mayor a cero y no puede superar el 100%');</script>";
                echo "<script>window.location.href = 'index.php?controller=category&action=index';</script>";
                return;
            }

            $this->categoryModel->setName($name);
            $this->categoryModel->setPercentage($percentage);
            
            if ($this->categoryModel->save()) {
                echo "<script>alert('Categoría guardada correctamente');</script>";
            } else {
                echo "<script>alert('Error al guardar la categoría');</script>";
            }
        }
        echo "<script>window.location.href = 'index.php?controller=category&action=index';</script>";
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $percentage = $_POST['percentage'];

            // Validar que el porcentaje sea mayor a cero y no supere el 100%
            if ($percentage <= 0 || $percentage > 100) {
                echo "<script>alert('El porcentaje debe ser mayor a cero y no puede superar el 100%');</script>";
                echo "<script>window.location.href = 'index.php?controller=category&action=index';</script>";
                return;
            }

            // Verificar si la categoría tiene gastos relacionados
            if ($this->categoryModel->hasRelatedBills($id)) {
                echo "<script>alert('No se puede modificar la categoría porque tiene gastos relacionados');</script>";
                echo "<script>window.location.href = 'index.php?controller=category&action=index';</script>";
                return;
            }

            $this->categoryModel->getById($id);
            $this->categoryModel->setName($name);
            $this->categoryModel->setPercentage($percentage);
            
            if ($this->categoryModel->update()) {
                echo "<script>alert('Categoría actualizada correctamente');</script>";
            } else {
                echo "<script>alert('Error al actualizar la categoría');</script>";
            }
            echo "<script>window.location.href = 'index.php?controller=category&action=index';</script>";
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // Verificar si la categoría tiene gastos relacionados
            if ($this->categoryModel->hasRelatedBills($id)) {
                echo "<script>alert('No se puede eliminar la categoría porque tiene gastos relacionados');</script>";
                echo "<script>window.location.href = 'index.php?controller=category&action=index';</script>";
                return;
            }
            
            if ($this->categoryModel->delete($id)) {
                echo "<script>alert('Categoría eliminada correctamente');</script>";
            } else {
                echo "<script>alert('Error al eliminar la categoría');</script>";
            }
        } else {
            echo "<script>alert('ID de categoría no proporcionado');</script>";
        }
        echo "<script>window.location.href = 'index.php?controller=category&action=index';</script>";
    }
}
?>