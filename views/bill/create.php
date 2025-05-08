<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Gastos - Nuevo Gasto</title>
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
            <h2>Registrar Nuevo Gasto</h2>
            <h3>Reporte: <?php echo $this->reportModel->getMonth() . ' ' . $this->reportModel->getYear(); ?></h3>
            
            <form action="index.php?controller=bill&action=save" method="POST">
                <input type="hidden" name="reportId" value="<?php echo $reportId; ?>">
                
                <div class="form-group">
                    <label for="categoryId">Categoría:</label>
                    <select name="categoryId" id="categoryId" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?> (<?php echo $category['percentage']; ?>%)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="value">Valor del Gasto:</label>
                    <input type="number" name="value" id="value" step="0.01" min="0.01" required>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Guardar</button>
                    <a href="index.php?controller=report&action=view&id=<?php echo $reportId; ?>" class="btn-cancel">Cancelar</a>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 - Sistema de Control de Gastos</p>
    </footer>
</body>
</html>