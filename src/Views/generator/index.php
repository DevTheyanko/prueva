<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Generator - Inicio</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
    <style>
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .action-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .action-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.5em;
        }
        .action-card p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9em;
        }
        .export-all-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .database-builder-btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🚀 Generador de CRUD PHP</h1>
            <p>Sistema MVC con Composer y POO + SOLID</p>
        </header>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Acciones Principales</h2>
            <div class="action-buttons">
                <a href="<?= BASE_PATH ?>/generator/table-builder" class="action-card">
                    <h3>🔨 Crear CRUD Simple</h3>
                    <p>Genera un CRUD para una sola tabla</p>
                </a>

                <a href="<?= BASE_PATH ?>/generator/database-builder" class="action-card database-builder-btn">
                    <h3>🏗️ Constructor de BD</h3>
                    <p>Crea múltiples tablas con relaciones</p>
                </a>

                <a href="<?= BASE_PATH ?>/export/full-project" class="action-card export-all-btn">
                    <h3>📦 Exportar Todo</h3>
                    <p>Descarga proyecto completo listo para usar</p>
                </a>
            </div>
        </div>

        <div class="card">
            <h2>Bienvenido al Generador de Proyectos CRUD</h2>
            <p>Este sistema te permite crear CRUDs completos de forma automática con:</p>
            <ul>
                <li>✅ Arquitectura MVC</li>
                <li>✅ Composer con Namespaces PSR-4</li>
                <li>✅ Programación Orientada a Objetos</li>
                <li>✅ Principios SOLID</li>
                <li>✅ Constructor visual de tablas</li>
                <li>✅ Generación automática de base de datos</li>
                <li>✅ Controladores funcionales (sin clases)</li>
                <li>✅ Modelos con interfaces y PDO directo</li>
                <li>✅ Exportación de proyecto completo</li>
            </ul>
        </div>

        <div class="card">
            <h3>📋 CRUDs Generados</h3>
            <?php
            $generatedPath = __DIR__ . '/../../../generated/controllers/';
            if (is_dir($generatedPath)) {
                $files = scandir($generatedPath);
                $controllers = array_filter($files, function($file) {
                    return strpos($file, '_controller.php') !== false;
                });

                if (!empty($controllers)):
            ?>
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>Entidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($controllers as $controller): 
                            $entity = strtolower(str_replace('_controller.php', '', $controller));
                        ?>
                            <tr>
                                <td><strong><?= ucfirst($entity) ?></strong></td>
                                <td>
                                    <a href="<?= BASE_PATH ?>/crud/<?= $entity ?>" class="btn btn-sm btn-primary">👁️ Ver CRUD</a>
                                    <a href="<?= BASE_PATH ?>/export/<?= $entity ?>" class="btn btn-sm btn-success">📤 Exportar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No hay CRUDs generados aún. ¡Crea tu primer CRUD!</p>
            <?php endif; } ?>
        </div>
    </div>
</body>
</html>