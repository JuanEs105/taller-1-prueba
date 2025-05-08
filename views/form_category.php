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
        <section class="form-container">
            <h2>Registrar Nueva Categoría</h2>
            <form action="index.php?controller=category&action=save" method="POST">
                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="percentage">Porcentaje Máximo (%):</label>
                    <input type="number" name="percentage" id="percentage" step="0.01" min="0.01" max="100" required>
                    <small>El porcentaje indica cuánto se debe gastar en esta categoría sobre el ingreso mensual.</small>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Guardar</button>
                    <button type="reset" class="btn-reset">Limpiar</button>
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
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo $category['name']; ?></td>
                                <td><?php echo $category['percentage']; ?></td>
                                <td class="actions">
                                    <button class="btn-edit" onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo $category['name']; ?>', <?php echo $category['percentage']; ?>)">Editar</button>
                                    <a href="index.php?controller=category&action=delete&id=<?php echo $category['id']; ?>" class="btn-delete" onclick="return confirm('¿Está seguro de eliminar esta categoría?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay categorías registradas.</p>
            <?php endif; ?>
        </section>

        <section id="edit-form" class="form-container hidden">
            <h2>Editar Categoría</h2>
            <form action="index.php?controller=category&action=update" method="POST">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <label for="edit-name">Nombre:</label>
                    <input type="text" name="name" id="edit-name" required>
                </div>
                <div class="form-group">
                    <label for="edit-percentage">Porcentaje Máximo (%):</label>
                    <input type="number" name="percentage" id="edit-percentage" step="0.01" min="0.01" max="100" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Actualizar</button>
                    <button type="button" class="btn-cancel" onclick="cancelEdit()">Cancelar</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 - Sistema de Control de Gastos</p>
    </footer>

    <script>
        function editCategory(id, name, percentage) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-percentage').value = percentage;
            
            document.getElementById('edit-form').classList.remove('hidden');
            window.scrollTo(0, document.getElementById('edit-form').offsetTop);
        }

        function cancelEdit() {
            document.getElementById('edit-form').classList.add('hidden');
        }
    </script>
</body>
</html>