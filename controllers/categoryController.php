<?php
require_once 'models/entities/category.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    public function index() {
        $categories = $this->categoryModel->getAll();
        $messages = [];
        
        // Manejo de mensajes de error o éxito
        if (isset($_GET['error'])) {
            $messages['danger'] = $_GET['error'];
        }
        if (isset($_GET['success'])) {
            $messages['success'] = $_GET['success'];
        }
        
        // Obtener categoría en edición si existe
        $categoryToEdit = null;
        if (isset($_GET['edit_id'])) {
            $category = new Category();
            if ($category->getById($_GET['edit_id'])) {
                $categoryToEdit = $category;
            }
        }
        
        include 'views/form_category.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $percentage = (float) str_replace(',', '.', $_POST['percentage']);

            // Validaciones
            if (empty($name)) {
                header("Location: index.php?controller=category&action=index&error=El nombre no puede estar vacío");
                exit();
            }

            if ($percentage <= 0 || $percentage > 100) {
                header("Location: index.php?controller=category&action=index&error=El porcentaje debe ser entre 0.01 y 100");
                exit();
            }

            $this->categoryModel->setName($name);
            $this->categoryModel->setPercentage($percentage);
            
            if ($this->categoryModel->save()) {
                header("Location: index.php?controller=category&action=index&success=Categoría guardada correctamente");
            } else {
                header("Location: index.php?controller=category&action=index&error=Error al guardar la categoría");
            }
            exit();
        }
        header("Location: index.php?controller=category&action=index");
        exit();
    }

    public function edit() {
        if (isset($_GET['id'])) {
            header("Location: index.php?controller=category&action=index&edit_id=".$_GET['id']);
            exit();
        }
        header("Location: index.php?controller=category&action=index");
        exit();
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $percentage = (float) str_replace(',', '.', $_POST['percentage']);

            // Validaciones
            if (empty($name)) {
                header("Location: index.php?controller=category&action=index&edit_id=".$id."&error=El nombre no puede estar vacío");
                exit();
            }

            if ($percentage <= 0 || $percentage > 100) {
                header("Location: index.php?controller=category&action=index&edit_id=".$id."&error=El porcentaje debe ser entre 0.01 y 100");
                exit();
            }

            // Verificar si la categoría tiene gastos relacionados
            if ($this->categoryModel->hasRelatedBills($id)) {
                header("Location: index.php?controller=category&action=index&edit_id=".$id."&error=No se puede modificar una categoría con gastos asociados");
                exit();
            }

            $this->categoryModel->setId($id);
            $this->categoryModel->setName($name);
            $this->categoryModel->setPercentage($percentage);

            if ($this->categoryModel->update()) {
                header("Location: index.php?controller=category&action=index&success=Categoría actualizada correctamente");
            } else {
                header("Location: index.php?controller=category&action=index&edit_id=".$id."&error=Error al actualizar la categoría");
            }
            exit();
        }
        header("Location: index.php?controller=category&action=index");
        exit();
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // Verificar si la categoría tiene gastos relacionados
            if ($this->categoryModel->hasRelatedBills($id)) {
                header("Location: index.php?controller=category&action=index&error=No se puede eliminar una categoría con gastos asociados");
                exit();
            }
            
            if ($this->categoryModel->delete($id)) {
                header("Location: index.php?controller=category&action=index&success=Categoría eliminada correctamente");
            } else {
                header("Location: index.php?controller=category&action=index&error=Error al eliminar la categoría");
            }
            exit();
        }
        header("Location: index.php?controller=category&action=index&error=ID de categoría no proporcionado");
        exit();
    }
}
?>