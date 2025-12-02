<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar test_tabl</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>✏️ Editar test_tabl</h1>
        </header>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/test_tabl/update/<?= $item['id'] ?>">
                        <div class="form-group">
            <label>nombre:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($item['nombre']) ?>" required>
        </div>
                <div class="form-group">
            <label>edad:</label>
            <input type="number" name="edad" value="<?= htmlspecialchars($item['edad']) ?>" required>
        </div>
                <div class="form-group">
            <label>created_at:</label>
            <input type="datetime-local" name="created_at" value="<?= htmlspecialchars($item['created_at']) ?>" required>
        </div>
                <div class="form-group">
            <label>updated_at:</label>
            <input type="datetime-local" name="updated_at" value="<?= htmlspecialchars($item['updated_at']) ?>" required>
        </div>
        
                <button type="submit" class="btn btn-primary">💾 Actualizar</button>
                <a href="<?= BASE_PATH ?>/crud/test_tabl" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>