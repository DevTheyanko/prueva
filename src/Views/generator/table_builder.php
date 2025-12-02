<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constructor de Tablas</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🔨 Constructor de Tablas</h1>
            <a href="<?= BASE_PATH ?>/" class="btn btn-secondary">← Volver</a>
        </header>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form id="tableBuilderForm" method="POST" action="<?= BASE_PATH ?>/generator/generate">
                <div class="form-group">
                    <label>Nombre de la Entidad (Singular):</label>
                    <input type="text" name="entity" id="entity" placeholder="Ej: producto, usuario, cliente" required>
                    <small>Este será el nombre del modelo, controlador y vistas</small>
                </div>

                <div class="form-group">
                    <label>Nombre de la Tabla en BD:</label>
                    <input type="text" name="table_name" id="table_name" placeholder="Ej: productos, usuarios" required>
                </div>

                <hr>

                <h3>Campos de la Tabla</h3>
                <p class="text-muted">El campo ID se agrega automáticamente</p>

                <div id="fieldsContainer">
                    <!-- Los campos se agregarán aquí dinámicamente -->
                </div>

                <button type="button" id="addFieldBtn" class="btn btn-secondary">+ Agregar Campo</button>

                <hr>

                <input type="hidden" name="fields" id="fieldsData">
                <button type="submit" class="btn btn-primary btn-lg">🚀 Generar CRUD Completo</button>
            </form>
        </div>
    </div>

    <script src="<?= BASE_PATH ?>/public/js/app.js"></script>
</body>
</html>