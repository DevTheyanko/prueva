<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear producto</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>➕ Crear producto</h1>
        </header>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/producto/store">
                        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="Nombre" required>
        </div>
                <div class="form-group">
            <label>stock:</label>
            <input type="text" name="stock" required>
        </div>
        
                <button type="submit" class="btn btn-primary">💾 Guardar</button>
                <a href="<?= BASE_PATH ?>/crud/producto" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>