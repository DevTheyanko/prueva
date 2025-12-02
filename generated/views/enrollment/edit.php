<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar enrollment</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>✏️ Editar enrollment</h1>
        </header>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/enrollment/update/<?= $item['id'] ?>">
                        <div class="form-group">
            <label>student:</label>
            <select name="student_id" required>
                <option value="">Seleccionar...</option>
                <?php
                $relatedModel = new Generated\Models\{ucfirst(student)}();
                $options = $relatedModel->getAll();
                foreach ($options as $option):
                ?>
                    <option value="<?= $option['id'] ?>" <?= $item['student_id'] == $option['id'] ? 'selected' : '' ?>>
                        <?= $option['name'] ?? $option['title'] ?? $option['id'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
                <div class="form-group">
            <label>course:</label>
            <select name="course_id" required>
                <option value="">Seleccionar...</option>
                <?php
                $relatedModel = new Generated\Models\{ucfirst(course)}();
                $options = $relatedModel->getAll();
                foreach ($options as $option):
                ?>
                    <option value="<?= $option['id'] ?>" <?= $item['course_id'] == $option['id'] ? 'selected' : '' ?>>
                        <?= $option['name'] ?? $option['title'] ?? $option['id'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
                <div class="form-group">
            <label>grade:</label>
            <input type="number" name="grade" value="<?= htmlspecialchars($item['grade']) ?>" required>
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
                <a href="<?= BASE_PATH ?>/crud/enrollment" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>