<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar student</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>✏️ Editar student</h1>
        </header>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/student/update/<?= $item['id'] ?>">
                        <div class="form-group">
            <label>first_name:</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($item['first_name']) ?>" required>
        </div>
                <div class="form-group">
            <label>last_name:</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($item['last_name']) ?>" required>
        </div>
                <div class="form-group">
            <label>email:</label>
            <input type="text" name="email" value="<?= htmlspecialchars($item['email']) ?>" required>
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
                <a href="<?= BASE_PATH ?>/crud/student" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>