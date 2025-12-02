<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear course</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>➕ Crear course</h1>
        </header>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/course/store">
                        <div class="form-group">
            <label>code:</label>
            <input type="text" name="code" required>
        </div>
                <div class="form-group">
            <label>name:</label>
            <input type="text" name="name" required>
        </div>
        
                <button type="submit" class="btn btn-primary">💾 Guardar</button>
                <a href="<?= BASE_PATH ?>/crud/course" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>