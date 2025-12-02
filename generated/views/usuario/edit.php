<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>✏️ Editar usuario</h1>
        </header>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/usuario/update/<?= $item['id'] ?>">
                        <div class="form-group">
            <label>nombres:</label>
            <input type="text" name="nombres" value="<?= htmlspecialchars($item['nombres']) ?>" required>
        </div>
                <div class="form-group">
            <label>apellido:</label>
            <input type="text" name="apellido" value="<?= htmlspecialchars($item['apellido']) ?>" required>
        </div>
        
                <button type="submit" class="btn btn-primary">💾 Actualizar</button>
                <a href="<?= BASE_PATH ?>/crud/usuario" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>