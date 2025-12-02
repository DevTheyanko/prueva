<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear user</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>➕ Crear user</h1>
        </header>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/user/store">
                        <div class="form-group">
            <label>name:</label>
            <input type="text" name="name" required>
        </div>
                <div class="form-group">
            <label>email:</label>
            <input type="text" name="email" required>
        </div>
                <div class="form-group">
            <label>password:</label>
            <input type="text" name="password" required>
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
                <a href="<?= BASE_PATH ?>/crud/user" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>