<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Gastos - Reportes</title>
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
            <h2>Registrar Nuevo Reporte</h2>
            <form action="index.php?controller=report&action=save" method="POST">
                <div class="form-group">
                    <label for="month">Mes:</label>
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
                </div>
                <div class="form-group">
                    <label for="year">Año:</label>
                    <input type="number" name="year" id="year" min="2020" max="2030" value="2025" required>
                </div>
                <div class="form-group">
                    <label for="value">Ingreso Mensual:</label>
                    <input type="number" name="value" id="value" step="0.01" min="0.01" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Guardar</button>
                    <button type="reset" class="btn-reset">Limpiar</button>
                </div>
            </form>
        </section>

        <section class="table-container">
            <h2>Reportes Registrados</h2>
            <?php if (count($reports) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mes</th>
                            <th>Año</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo $report['id']; ?></td>
                                <td><?php echo $report['month']; ?></td>
                                <td><?php echo $report['year']; ?></td>
                                <td class="actions">
                                    <a href="index.php?controller=report&action=view&id=<?php echo $report['id']; ?>" class="btn-view">Ver</a>
                                    <a href="index.php?controller=report&action=delete&id=<?php echo $report['id']; ?>" class="btn-delete" onclick="return confirm('¿Está seguro de eliminar este reporte?')">Eliminar</a>
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