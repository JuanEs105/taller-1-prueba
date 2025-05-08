<?php
// Incluir los controladores
require_once 'controllers/reportController.php';
require_once 'controllers/categoryController.php';
require_once 'controllers/incomeController.php';
require_once 'controllers/billController.php';

// Definir controlador por defecto
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'report';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Crear instancia del controlador y llamar a la acción
switch ($controller) {
    case 'report':
        $controller = new ReportController();
        break;
    case 'category':
        $controller = new CategoryController();
        break;
    case 'income':
        $controller = new IncomeController();
        break;
    case 'bill':
        $controller = new BillController();
        break;
    default:
        $controller = new ReportController();
        $action = 'index';
        break;
}

// Verificar que la acción exista en el controlador
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "La acción especificada no existe";
}
?>