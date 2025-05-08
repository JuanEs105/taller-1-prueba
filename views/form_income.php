<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Gastos - Ingresos</title>
    <link rel="stylesheet" href="views/css/acciones.css">
</head>
<body>
    <header>
        <h1>Sistema de Control de Gastos</h1>
        <nav>
            <ul>
                <li><a href="index.php?controller=report&action=index">Reportes</a></li>
                <li><a href="index.php?controller=category&action=index">Categorías</a></li>
                <li><a href="index.php?controller=income&action=index">Ingresos</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="form-container">
            <?php
            // Verificar si estamos editando un ingreso existente
            $isEditing = isset($_GET['id']) && !empty($_GET['id']);
            $incomeData = null;
            
            if ($isEditing) {
                // Obtener datos del ingreso a editar
                $incomeModel = new Income();
                if ($incomeModel->getById($_GET['id'])) {
                    $incomeData = [
                        'id' => $incomeModel->getId(),
                        'value' => $incomeModel->getValue(),
                        'idReport' => $incomeModel->getIdReport()
                    ];
                    
                    // Obtener mes y año del reporte asociado
                    $reportModel = new Report();
                    if ($reportModel->getById($incomeData['idReport'])) {
                        $reportData = [
                            'month' => $reportModel->getMonth(),
                            'year' => $reportModel->getYear()
                        ];
                    }
                }
            }
            ?>
            <h2><?php echo $isEditing ? 'Editar Ingreso' : 'Registrar Nuevo Ingreso'; ?></h2>
            <form action="index.php?controller=income&action=<?php echo $isEditing ? 'update' : 'save'; ?>" method="POST">
                <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?php echo $incomeData['id']; ?>">
                    <input type="hidden" name="idReport" value="<?php echo $incomeData['idReport']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="month">Mes:</label>
                    <?php if ($isEditing): ?>
                        <input type="text" id="month" value="<?php echo $reportData['month']; ?>" readonly class="readonly-field">
                        <input type="hidden" name="month" value="<?php echo $reportData['month']; ?>">
                    <?php else: ?>
                        <select name="month" id="month" required>
                            <option value="Enero">Enero</option>
                            <option value="Febrero">Febrero</option>
                            <option value="Marzo">Marzo</option>
                            <option value="Abril">Abril</option>
                            <option value="Mayo">Mayo</option>
                            <option value="Junio">Junio</option>
                            <option value="Julio">Julio</option>
                            <option value="Agosto">Agosto</option>
                            <option value="Septiembre">Septiembre</option>
                            <option value="Octubre">Octubre</option>
                            <option value="Noviembre">Noviembre</option>
                            <option value="Diciembre">Diciembre</option>
                        </select>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="year">Año:</label>
                    <?php if ($isEditing): ?>
                        <input type="text" id="year" value="<?php echo $reportData['year']; ?>" readonly class="readonly-field">
                        <input type="hidden" name="year" value="<?php echo $reportData['year']; ?>">
                    <?php else: ?>
                        <input type="number" name="year" id="year" min="2020" max="2030" value="2025" required>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="value">Valor del Ingreso:</label>
                    <input type="number" name="value" id="value" step="0.01" min="0.01" 
                           value="<?php echo $isEditing ? $incomeData['value'] : ''; ?>" required>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">
                        <?php echo $isEditing ? 'Actualizar' : 'Guardar'; ?>
                    </button>
                    <?php if (!$isEditing): ?>
                        <button type="reset" class="btn-reset">Limpiar</button>
                    <?php else: ?>
                        <a href="index.php?controller=income&action=index" class="btn-cancel">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="table-container">
            <h2>Reportes Existentes</h2>
            <?php if (count($reports) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mes</th>
                            <th>Año</th>
                            <th>Ingreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): 
                            $incomeModel = new Income();
                            $incomeValue = 'No registrado';
                            
                            // Obtener el ingreso para este reporte si existe
                            if ($incomeModel->getByReportId($report['id'])) {
                                $incomeValue = '$' . number_format($incomeModel->getValue(), 2, '.', ',');
                                $incomeId = $incomeModel->getId();
                            }
                        ?>
                            <tr>
                                <td><?php echo $report['id']; ?></td>
                                <td><?php echo $report['month']; ?></td>
                                <td><?php echo $report['year']; ?></td>
                                <td><?php echo $incomeValue; ?></td>
                                <td class="actions">
                                    <?php if ($incomeValue !== 'No registrado'): ?>
                                        <a href="index.php?controller=income&action=edit&id=<?php echo $incomeId; ?>" class="btn-edit">Editar</a>
                                    <?php else: ?>
                                        <a href="index.php?controller=income&action=create&reportId=<?php echo $report['id']; ?>" class="btn-add">Registrar</a>
                                    <?php endif; ?>
                                    <a href="index.php?controller=report&action=view&id=<?php echo $report['id']; ?>" class="btn-view">Ver Detalle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay reportes registrados.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 - Sistema de Control de Gastos</p>
    </footer>
    
</body>
</html>