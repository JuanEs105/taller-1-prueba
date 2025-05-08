<?php
require_once 'models/entities/report.php';

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
    $selectedReport = $reportModel->getById($reportId);
    
    // Obtener el ingreso asociado al reporte
    require_once 'models/entities/income.php';
    $incomeModel = new Income();
    $income = $incomeModel->getByReportId($reportId);
    
    // Obtener los gastos asociados al reporte
    require_once 'models/entities/bill.php';
    $billModel = new Bill();
    $bills = $billModel->getByReportId($reportId);
    
    // Calcular gastos totales
    $totalExpenses = 0;
foreach ($bills as $bill) {
    $totalExpenses += $bill['value'];
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
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Reportes Mensuales</h2>
        </div>
        <div class="col-auto">
            <a href="index.php?view=form_report" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Período
            </a>
        </div>
    </div>

    <!-- Selector de Reportes -->
    <div class="card mb-4">
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
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Reporte: <?= $selectedReport['month'] ?> - <?= $selectedReport['year'] ?></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Ingreso Mensual</h5>
                                <h3 class="text-success">$<?= number_format($income['value'], 2, '.', ',') ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Gastos Totales</h5>
                                <h3 class="text-danger">$<?= number_format($totalExpenses, 2, '.', ',') ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card <?= $savingsPercentage >= 10 ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                            <div class="card-body">
                                <h5 class="card-title">Ahorro</h5>
                                <h3>$<?= number_format($savings, 2, '.', ',') ?></h3>
                                <p><?= number_format($savingsPercentage, 2) ?>% de tus ingresos</p>
                                
                                <?php if ($savingsPercentage < 10): ?>
                                    <div class="alert alert-warning mt-2 p-2">
                                        <small><strong>¡Advertencia!</strong> Tu ahorro está por debajo del 10% recomendado.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (count($bills) > 0): ?>
                    <h5 class="mt-4 mb-3">Detalle de Gastos por Categoría</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
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
                                require_once 'models/Category.php';
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
                                        $categories[$bill['idCategory']] = $categoryModel->getById($bill['idCategory']);
                                    }
                                }
                                
                                $exceedingCategories = [];
                                
                                foreach ($billsByCategory as $categoryId => $amount):
                                    $category = $categories[$categoryId];
                                    $percentOfIncome = ($amount / $income['value']) * 100;
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
                                    <tr>
                                        <td><?= $category['name'] ?></td>
                                        <td>$<?= number_format($amount, 2, '.', ',') ?></td>
                                        <td><?= number_format($percentOfIncome, 2) ?>%</td>
                                        <td><?= number_format($category['percentage'], 2) ?>%</td>
                                        <td>
                                            <?php if ($isExceeding): ?>
                                                <span class="badge bg-danger">Excedido</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Dentro del límite</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (count($exceedingCategories) > 0): ?>
                        <div class="alert alert-warning mt-4">
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