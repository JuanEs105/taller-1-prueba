<?php
require_once 'models/Category.php';
require_once 'models/Report.php';

// Obtener todas las categorías para el dropdown
$categoryModel = new Category();
$categories = $categoryModel->getAll();

// Obtener todos los reportes para el dropdown
$reportModel = new Report();
$reports = $reportModel->getAll();

// Variables para el formulario (edición o creación)
$action = 'actions/save_bill.php';
$buttonText = 'Registrar Gasto';
$bill = null;

// Si se está editando un gasto existente
if (isset($_GET['id']) && !empty($_GET['id'])) {
    require_once 'models/Bill.php';
    $billModel = new Bill();
    $bill = $billModel->getById($_GET['id']);
    
    if ($bill) {
        $action = 'actions/update_bill.php';
        $buttonText = 'Actualizar Gasto';
    }
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5><?= $bill ? 'Editar Gasto' : 'Registrar Gasto' ?></h5>
        </div>
        <div class="card-body">
            <form action="<?= $action ?>" method="POST">
                <?php if ($bill): ?>
                    <input type="hidden" name="id" value="<?= $bill['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="idCategory" class="form-label">Categoría:</label>
                    <select class="form-select" id="idCategory" name="idCategory" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= ($bill && $bill['idCategory'] == $category['id']) ? 'selected' : '' ?>>
                                <?= $category['name'] ?> (<?= $category['percentage'] ?>%)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="idReport" class="form-label">Periodo (Mes/Año):</label>
                    <select class="form-select" id="idReport" name="idReport" required <?= $bill ? 'disabled' : '' ?>>
                        <option value="">Seleccione un periodo</option>
                        <?php foreach ($reports as $report): ?>
                            <option value="<?= $report['id'] ?>" <?= ($bill && $bill['idReport'] == $report['id']) ? 'selected' : '' ?>>
                                <?= $report['month'] ?> - <?= $report['year'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($bill): ?>
                        <input type="hidden" name="idReport" value="<?= $bill['idReport'] ?>">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="value" class="form-label">Valor del Gasto:</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="value" name="value" step="0.01" min="0" required
                               value="<?= $bill ? $bill['value'] : '' ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php?view=bills" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><?= $buttonText ?></button>
                </div>
            </form>
        </div>
    </div>
</div>