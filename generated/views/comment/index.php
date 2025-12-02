<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de comment</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>📋 Gestión de comment</h1>
        </header>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <a href="<?= BASE_PATH ?>/crud/comment/create" class="btn btn-primary">➕ Crear Nuevo</a>
                    <a href="<?= BASE_PATH ?>/" class="btn btn-secondary">🏠 Inicio</a>
                </div>
                <div>
                    <a href="<?= BASE_PATH ?>/export/comment" class="btn btn-success">📦 Exportar Proyecto</a>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Post</th>
                <th>content</th>
                <th>created_at</th>
                <th>updated_at</th>
                
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="100" style="text-align: center; padding: 40px; color: #999;">
                                📭 No hay registros aún. <a href="<?= BASE_PATH ?>/crud/comment/create">Crear el primero</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= isset($item['post_data']['name']) ? htmlspecialchars($item['post_data']['name']) : 'N/A' ?></td>
                <td><?= htmlspecialchars($item['content']) ?></td>
                <td><?= htmlspecialchars($item['created_at']) ?></td>
                <td><?= htmlspecialchars($item['updated_at']) ?></td>
                
                            <td>
                                <a href="<?= BASE_PATH ?>/crud/comment/edit/<?= $item['id'] ?>" class="btn btn-sm">✏️ Editar</a>
                                <form method="POST" action="<?= BASE_PATH ?>/crud/comment/delete/<?= $item['id'] ?>" style="display:inline;">
                                    <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('¿Seguro que deseas eliminar este registro?')">🗑️ Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>