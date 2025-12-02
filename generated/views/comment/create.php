<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear comment</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>➕ Crear comment</h1>
        </header>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/comment/store">
                        <div class="form-group">
            <label>post:</label>
            <select name="post_id" required>
                <option value="">Seleccionar...</option>
                <?php
                // Cargar opciones de la tabla relacionada
                $relatedModel = new Generated\Models\{ucfirst(post)}();
                $options = $relatedModel->getAll();
                foreach ($options as $option):
                ?>
                    <option value="<?= $option['id'] ?>"><?= $option['name'] ?? $option['title'] ?? $option['id'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
                <div class="form-group">
            <label>content:</label>
            <input type="textarea" name="content" required>
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
                <a href="<?= BASE_PATH ?>/crud/comment" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>