<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear teacher</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>➕ Crear teacher</h1>
        </header>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/teacher/store">
                        <div class="form-group">
            <label>first_name:</label>
            <input type="text" name="first_name" required>
        </div>
                <div class="form-group">
            <label>last_name:</label>
            <input type="text" name="last_name" required>
        </div>
                <div class="form-group">
            <label>created_at:</label>
            <input type="datetime-local" name="created_at" required>
        </div>
                <div class="form-group">
            <label>updated_at:</label>
            <input type="datetime-local" name="updated_at" required>
        </div>
        
                <button type="submit" class="btn btn-primary">💾 Guardar</button>
                <a href="<?= BASE_PATH ?>/crud/teacher" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>