<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>✏️ Editar producto</h1>
        </header>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/producto/update/<?= $item['id'] ?>">
                        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="Nombre" value="<?= htmlspecialchars($item['Nombre']) ?>" required>
        </div>
                <div class="form-group">
            <label>stock:</label>
            <input type="text" name="stock" value="<?= htmlspecialchars($item['stock']) ?>" required>
        </div>
        
                <button type="submit" class="btn btn-primary">💾 Actualizar</button>
                <a href="<?= BASE_PATH ?>/crud/producto" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>