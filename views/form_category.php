<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Gastos - Categorías</title>
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
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $type => $message): ?>
                <div class="alert alert-<?= $type ?>">
                    <?= $message ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <section class="form-container">
            <h2><?= isset($categoryToEdit) ? 'Editar Categoría' : 'Registrar Nueva Categoría' ?></h2>
            <form action="index.php?controller=category&action=<?= isset($categoryToEdit) ? 'update' : 'save' ?>" method="POST">
                <?php if (isset($categoryToEdit)): ?>
                    <input type="hidden" name="id" value="<?= $categoryToEdit->getId() ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input type="text" name="name" id="name" 
                           value="<?= isset($categoryToEdit) ? htmlspecialchars($categoryToEdit->getName()) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="percentage">Porcentaje Máximo (%):</label>
                    <input type="number" name="percentage" id="percentage" 
                           value="<?= isset($categoryToEdit) ? htmlspecialchars($categoryToEdit->getPercentage()) : '' ?>" 
                           step="0.01" min="0.01" max="100" required>
                    <small>El porcentaje indica cuánto se debe gastar en esta categoría sobre el ingreso mensual.</small>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">
                        <?= isset($categoryToEdit) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                    <?php if (isset($categoryToEdit)): ?>
                        <a href="index.php?controller=category&action=index" class="btn-cancel">Cancelar</a>
                    <?php else: ?>
                        <button type="reset" class="btn-reset">Limpiar</button>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="table-container">
            <h2>Categorías Registradas</h2>
            <?php if (count($categories) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Porcentaje (%)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= $category['percentage'] ?></td>
                                <td class="actions">
                                    <a href="index.php?controller=category&action=edit&id=<?= $category['id'] ?>" class="btn-edit">Editar</a>
                                    <a href="index.php?controller=category&action=delete&id=<?= $category['id'] ?>" class="btn-delete">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay categorías registradas.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 - Sistema de Control de Gastos</p>
    </footer>
</body>
</html>