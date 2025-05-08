<?php
require_once 'models/entities/report.php';
require_once 'models/entities/income.php';
require_once 'models/entities/bill.php';
require_once 'models/entities/category.php';

// Obtener los reportes disponibles
$reportModel = new Report();
$reports = $reportModel->getAll();

// Para mostrar un reporte específico
$selectedReport = null;
$income = null;
$bills = [];
$savings = 0;
$savingsPercentage = 0;
$totalExpenses = 0;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $reportId = $_GET['id'];
    if ($reportModel->getById($reportId)) {
        $selectedReport = [
            'id' => $reportModel->getId(),
            'month' => $reportModel->getMonth(),
            'year' => $reportModel->getYear()
        ];
        
        // Obtener el ingreso asociado al reporte
        $incomeModel = new Income();
        if ($incomeModel->getByReportId($reportId)) {
            $income = [
                'id' => $incomeModel->getId(),
                'value' => $incomeModel->getValue(),
                'idReport' => $incomeModel->getIdReport()
            ];
        } else {
            $income = ['value' => 0];
        }
        
        // Obtener los gastos asociados al reporte
        $billModel = new Bill();
        $bills = $billModel->getByReportId($reportId);
        
        // Calcular gastos totales
        $totalExpenses = 0;
        if (is_array($bills)) {
            foreach ($bills as $bill) {
                $totalExpenses += $bill['value'];
            }
        }

        // Calcular ahorro y porcentaje de ahorro
        if ($income && isset($income['value']) && $income['value'] > 0) {
            $savings = $income['value'] - $totalExpenses;
            $savingsPercentage = ($savings / $income['value']) * 100;
        } else {
            $savings = 0;
            $savingsPercentage = 0;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Mensuales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/acciones.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h2 class="report-header">Reportes Mensuales</h2>
            </div>
            <div class="col-auto">
                <a href="index.php?view=form_report" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Período
                </a>
            </div>
        </div>

        <!-- Selector de Reportes -->
        <div class="card mb-4 period-selector">
            <div class="card-header bg-secondary text-white">
                <h5>Seleccionar Período</h5>
            </div>
            <div class="card-body">
                <form action="index.php" method="GET">
                    <input type="hidden" name="view" value="reports">
                    <div class="row">
                        <div class="col-md-8">
                            <select class="form-select" name="id" id="reportSelect">
                                <option value="">Seleccione un período</option>
                                <?php foreach ($reports as $report): ?>
                                    <option value="<?= $report['id'] ?>" <?= (isset($_GET['id']) && $_GET['id'] == $report['id']) ? 'selected' : '' ?>>
                                        <?= $report['month'] ?> - <?= $report['year'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Ver Reporte</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($selectedReport && $income): ?>
            <!-- Detalles del Reporte -->
            <div class="card mb-4 report-container">
                <div class="card-header bg-primary text-white">
                    <h5>Reporte: <?= $selectedReport['month'] ?> - <?= $selectedReport['year'] ?></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card financial-card card-income">
                                <div class="card-body">
                                    <h5 class="card-title">Ingreso Mensual</h5>
                                    <h3 class="card-value text-success">$<?= number_format($income['value'], 2, '.', ',') ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card financial-card card-expenses">
                                <div class="card-body">
                                    <h5 class="card-title">Gastos Totales</h5>
                                    <h3 class="card-value text-danger">$<?= number_format($totalExpenses, 2, '.', ',') ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card financial-card card-savings">
                                <div class="card-body">
                                    <h5 class="card-title">Ahorro</h5>
                                    <h3 class="card-value">$<?= number_format($savings, 2, '.', ',') ?></h3>
                                    <p><?= number_format($savingsPercentage, 2) ?>% de tus ingresos</p>
                                    
                                    <?php if ($savingsPercentage < 10): ?>
                                        <div class="savings-status savings-warning mt-2 p-2">
                                            <small><strong>¡Advertencia!</strong> Tu ahorro está por debajo del 10% recomendado.</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (is_array($bills) && count($bills) > 0): ?>
                        <h5 class="mt-4 mb-3">Detalle de Gastos por Categoría</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover expenses-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Valor Gastado</th>
                                        <th>% del Ingreso</th>
                                        <th>% Máximo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $categoryModel = new Category();
                                    $categories = [];
                                    
                                    // Agrupar gastos por categoría
                                    $billsByCategory = [];
                                    foreach ($bills as $bill) {
                                        if (!isset($billsByCategory[$bill['idCategory']])) {
                                            $billsByCategory[$bill['idCategory']] = 0;
                                        }
                                        $billsByCategory[$bill['idCategory']] += $bill['value'];
                                        
                                        if (!isset($categories[$bill['idCategory']])) {
                                            if ($categoryModel->getById($bill['idCategory'])) {
                                                $categories[$bill['idCategory']] = [
                                                    'id' => $categoryModel->getId(),
                                                    'name' => $categoryModel->getName(),
                                                    'percentage' => $categoryModel->getPercentage()
                                                ];
                                            } else {
                                                $categories[$bill['idCategory']] = [
                                                    'id' => $bill['idCategory'],
                                                    'name' => $bill['categoryName'] ?? 'Categoría desconocida',
                                                    'percentage' => $bill['percentage'] ?? 0
                                                ];
                                            }
                                        }
                                    }
                                    
                                    $exceedingCategories = [];
                                    
                                    foreach ($billsByCategory as $categoryId => $amount):
                                        if (isset($categories[$categoryId])) {
                                            $category = $categories[$categoryId];
                                            $percentOfIncome = ($income['value'] > 0) ? ($amount / $income['value']) * 100 : 0;
                                            $isExceeding = $percentOfIncome > $category['percentage'];
                                            
                                            if ($isExceeding) {
                                                $exceedingCategories[] = [
                                                    'name' => $category['name'],
                                                    'amount' => $amount,
                                                    'percentage' => $percentOfIncome,
                                                    'maxPercentage' => $category['percentage'],
                                                    'excess' => $amount - ($income['value'] * $category['percentage'] / 100)
                                                ];
                                            }
                                    ?>
                                        <tr class="<?= $isExceeding ? 'exceeded' : 'within-limit' ?>">
                                            <td><?= $category['name'] ?></td>
                                            <td>$<?= number_format($amount, 2, '.', ',') ?></td>
                                            <td><?= number_format($percentOfIncome, 2) ?>%</td>
                                            <td><?= number_format($category['percentage'], 2) ?>%</td>
                                            <td>
                                                <span class="status-badge bg-<?= $isExceeding ? 'danger' : 'success' ?>">
                                                    <?= $isExceeding ? 'Excedido' : 'Dentro del límite' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php 
                                        }
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (count($exceedingCategories) > 0): ?>
                            <div class="suggestions-box alert alert-warning mt-4">
                                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Sugerencias para mejorar tu presupuesto</h5>
                                <p>Hemos detectado que has excedido los límites en las siguientes categorías:</p>
                                <ul>
                                    <?php foreach ($exceedingCategories as $category): ?>
                                        <li>
                                            <strong><?= $category['name'] ?>:</strong> Estás gastando 
                                            <?= number_format($category['percentage'], 2) ?>% de tus ingresos cuando el máximo recomendado es 
                                            <?= number_format($category['maxPercentage'], 2) ?>%. 
                                            Intenta reducir $<?= number_format($category['excess'], 2, '.', ',') ?> en esta categoría.
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <hr>
                                <p class="mb-0">Si reduces estos gastos, podrías aumentar tu ahorro al nivel recomendado del 10% o más.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info mt-4">
                            <h5 class="alert-heading">No hay gastos registrados</h5>
                            <p>No se han registrado gastos para este periodo. Puedes agregar gastos utilizando la opción "Registrar Gasto".</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif (isset($_GET['id'])): ?>
            <div class="alert alert-warning">
                <h5 class="alert-heading">No hay información disponible</h5>
                <p>No se encontró información de ingresos para el período seleccionado. Por favor, asegúrese de registrar los ingresos para este período.</p>
                <a href="index.php?view=form_income&report_id=<?= $_GET['id'] ?>" class="btn btn-primary">Registrar Ingreso</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportSelect = document.getElementById('reportSelect');
            
            reportSelect.addEventListener('change', function() {
                if (this.value) {
                    document.querySelector('form').submit();
                }
            });
        });
    </script>
</body>
</html>