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

    // Método para mostrar todos los ingresos (y reportes)
    public function index() {
        $reports = $this->reportModel->getAll();
        require_once 'views/form_income.php';
    }

    // Método para mostrar formulario de creación
    public function create() {
        $reports = $this->reportModel->getAll();
        require_once 'views/form_income.php';
    }

    // Método para mostrar formulario de edición
    public function edit() {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $incomeId = $_GET['id'];
            if ($this->incomeModel->getById($incomeId)) {
                $reports = $this->reportModel->getAll();
                require_once 'views/form_income.php';
            } else {
                // Si no existe el ingreso, redirigir al listado
                header('Location: index.php?controller=income&action=index');
                exit;
            }
        } else {
            header('Location: index.php?controller=income&action=index');
            exit;
        }
    }

    // Método para guardar un nuevo ingreso
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = isset($_POST['month']) ? $_POST['month'] : '';
            $year = isset($_POST['year']) ? $_POST['year'] : '';
            $value = isset($_POST['value']) ? $_POST['value'] : 0;
            
            // Verificar si ya existe un reporte para este mes y año
            if ($this->reportModel->getByMonthAndYear($month, $year)) {
                $reportId = $this->reportModel->getId();
            } else {
                // Crear un nuevo reporte
                $this->reportModel->setMonth($month);
                $this->reportModel->setYear($year);
                $reportId = $this->reportModel->save();
            }
            
            // Verificar si el reporte ya tiene un ingreso asociado
            if ($this->incomeModel->getByReportId($reportId)) {
                // Ya existe un ingreso para este reporte
                $message = "Ya existe un ingreso registrado para el periodo $month - $year";
                header("Location: index.php?controller=income&action=index&error=" . urlencode($message));
                exit;
            }
            
            // Guardar el ingreso
            $this->incomeModel->setValue($value);
            $this->incomeModel->setIdReport($reportId);
            
            if ($this->incomeModel->save()) {
                $message = "Ingreso registrado exitosamente";
                header("Location: index.php?controller=income&action=index&success=" . urlencode($message));
                exit;
            } else {
                $message = "Error al registrar el ingreso";
                header("Location: index.php?controller=income&action=index&error=" . urlencode($message));
                exit;
            }
        }
    }

    // Método para actualizar un ingreso existente
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
            $value = isset($_POST['value']) ? $_POST['value'] : 0;
            
            // Verificar que exista el ingreso
            if ($this->incomeModel->getById($id)) {
                // Actualizar sólo el valor del ingreso
                $this->incomeModel->setValue($value);
                
                if ($this->incomeModel->update()) {
                    $message = "Ingreso actualizado exitosamente";
                    header("Location: index.php?controller=income&action=index&success=" . urlencode($message));
                    exit;
                } else {
                    $message = "Error al actualizar el ingreso";
                    header("Location: index.php?controller=income&action=edit&id=$id&error=" . urlencode($message));
                    exit;
                }
            } else {
                $message = "El ingreso que intenta editar no existe";
                header("Location: index.php?controller=income&action=index&error=" . urlencode($message));
                exit;
            }
        }
    }
}
?>