<?php
namespace App\Services;

class CrudGenerator
{
    private $generatedPath;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->generatedPath = $config['app']['generated_path'];
    }

    public function generateModel(string $entity, string $tableName): void
    {
        // 1. Generar la interfaz del repositorio
        $this->generateRepositoryInterface($entity);
        
        // 2. Generar el modelo con implementación de la interfaz
        $this->generateModelClass($entity, $tableName);
    }

    private function generateRepositoryInterface(string $entity): void
    {
        $className = ucfirst($entity);
        $content = <<<PHP
<?php
namespace Generated\Interfaces;

interface {$className}RepositoryInterface
{
    public function getAll(): array;
    public function getById(int \$id): ?array;
    public function create(array \$data): bool;
    public function update(int \$id, array \$data): bool;
    public function delete(int \$id): bool;
}
PHP;

        $this->saveFile('interfaces', "{$className}RepositoryInterface.php", $content);
    }

    private function generateModelClass(string $entity, string $tableName): void
    {
        $className = ucfirst($entity);
        $content = <<<PHP
<?php
namespace Generated\Models;

use Generated\Interfaces\\{$className}RepositoryInterface;
use App\Config\Database;
use PDO;

class {$className} implements {$className}RepositoryInterface
{
    private \$db;
    private \$table = '$tableName';
    private \$allowedFields = [];

    public function __construct()
    {
        \$this->db = Database::getInstance()->getConnection();
        \$this->loadAllowedFields();
    }

    private function loadAllowedFields(): void
    {
        try {
            \$stmt = \$this->db->query("DESCRIBE {\$this->table}");
            \$columns = \$stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Excluir el campo ID (auto_increment)
            \$this->allowedFields = array_filter(\$columns, function(\$col) {
                return strtolower(\$col) !== 'id';
            });
        } catch (\PDOException \$e) {
            \$this->allowedFields = [];
        }
    }

    private function filterData(array \$data): array
    {
        return array_filter(\$data, function(\$key) {
            return in_array(\$key, \$this->allowedFields);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getAll(): array
    {
        \$stmt = \$this->db->query("SELECT * FROM {\$this->table}");
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int \$id): ?array
    {
        \$stmt = \$this->db->prepare("SELECT * FROM {\$this->table} WHERE id = ?");
        \$stmt->execute([\$id]);
        \$result = \$stmt->fetch(PDO::FETCH_ASSOC);
        return \$result ?: null;
    }

    public function create(array \$data): bool
    {
        // Filtrar solo campos permitidos
        \$data = \$this->filterData(\$data);
        
        if (empty(\$data)) {
            return false;
        }

        \$columns = implode(', ', array_keys(\$data));
        \$placeholders = implode(', ', array_fill(0, count(\$data), '?'));
        
        \$sql = "INSERT INTO {\$this->table} (\$columns) VALUES (\$placeholders)";
        \$stmt = \$this->db->prepare(\$sql);
        
        return \$stmt->execute(array_values(\$data));
    }

    public function update(int \$id, array \$data): bool
    {
        // Filtrar solo campos permitidos
        \$data = \$this->filterData(\$data);
        
        if (empty(\$data)) {
            return false;
        }

        \$fields = implode(' = ?, ', array_keys(\$data)) . ' = ?';
        \$sql = "UPDATE {\$this->table} SET \$fields WHERE id = ?";
        
        \$stmt = \$this->db->prepare(\$sql);
        \$values = array_values(\$data);
        \$values[] = \$id;
        
        return \$stmt->execute(\$values);
    }

    public function delete(int \$id): bool
    {
        \$stmt = \$this->db->prepare("DELETE FROM {\$this->table} WHERE id = ?");
        return \$stmt->execute([\$id]);
    }

    public function getAllowedFields(): array
    {
        return \$this->allowedFields;
    }
}
PHP;

        $this->saveFile('models', "{$className}.php", $content);
    }

    public function generateController(string $entity, array $fields): void
    {
        $className = ucfirst($entity);
        $content = <<<PHP
<?php
// Controlador funcional para: $entity
// Sin clases - Solo funciones puras

use Generated\Models\\$className;

// Instanciar el modelo
function get{$className}Model(): $className
{
    return new $className();
}

// Función helper para limpiar datos del formulario
function cleanFormData(array \$data): array
{
    // Eliminar campos no deseados
    \$unwanted = ['submit', 'csrf_token', '_method'];
    foreach (\$unwanted as \$field) {
        unset(\$data[\$field]);
    }
    
    // Limpiar valores vacíos opcionales
    return array_filter(\$data, function(\$value) {
        return \$value !== '';
    });
}

// Listar todos
function index_{$entity}(): void
{
    \$model = get{$className}Model();
    \$items = \$model->getAll();
    require __DIR__ . '/../views/{$entity}/index.php';
}

// Mostrar formulario de creación
function create_{$entity}(): void
{
    require __DIR__ . '/../views/{$entity}/create.php';
}

// Guardar nuevo registro
function store_{$entity}(): void
{
    \$model = get{$className}Model();
    \$data = cleanFormData(\$_POST);
    
    if (empty(\$data)) {
        \$_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/$entity/create');
        exit;
    }
    
    if (\$model->create(\$data)) {
        \$_SESSION['success'] = 'Registro creado correctamente';
        header('Location: ' . BASE_PATH . '/crud/$entity');
        exit;
    } else {
        \$_SESSION['error'] = 'Error al crear el registro';
        header('Location: ' . BASE_PATH . '/crud/$entity/create');
        exit;
    }
}

// Mostrar formulario de edición
function edit_{$entity}(int \$id): void
{
    \$model = get{$className}Model();
    \$item = \$model->getById(\$id);
    
    if (!\$item) {
        \$_SESSION['error'] = 'Registro no encontrado';
        header('Location: ' . BASE_PATH . '/crud/$entity');
        exit;
    }
    
    require __DIR__ . '/../views/{$entity}/edit.php';
}

// Actualizar registro
function update_{$entity}(int \$id): void
{
    \$model = get{$className}Model();
    \$data = cleanFormData(\$_POST);
    
    if (empty(\$data)) {
        \$_SESSION['error'] = 'No se recibieron datos válidos';
        header('Location: ' . BASE_PATH . '/crud/$entity/edit/' . \$id);
        exit;
    }
    
    if (\$model->update(\$id, \$data)) {
        \$_SESSION['success'] = 'Registro actualizado correctamente';
        header('Location: ' . BASE_PATH . '/crud/$entity');
        exit;
    } else {
        \$_SESSION['error'] = 'Error al actualizar el registro';
        header('Location: ' . BASE_PATH . '/crud/$entity/edit/' . \$id);
        exit;
    }
}

// Eliminar registro
function delete_{$entity}(int \$id): void
{
    \$model = get{$className}Model();
    
    if (\$model->delete(\$id)) {
        \$_SESSION['success'] = 'Registro eliminado correctamente';
    } else {
        \$_SESSION['error'] = 'Error al eliminar el registro';
    }
    
    header('Location: ' . BASE_PATH . '/crud/$entity');
    exit;
}
PHP;

        $this->saveFile('controllers', "{$entity}_controller.php", $content);
    }

    public function generateViews(string $entity, array $fields): void
    {
        $this->generateIndexView($entity, $fields);
        $this->generateCreateView($entity, $fields);
        $this->generateEditView($entity, $fields);
    }

    private function generateIndexView(string $entity, array $fields): void
    {
        $headers = '';
        $rows = '';
        
        foreach ($fields as $field) {
            $headers .= "<th>{$field['name']}</th>\n                ";
            $rows .= "<td><?= htmlspecialchars(\$item['{$field['name']}']) ?></td>\n                ";
        }

        $content = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de $entity</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>📋 Gestión de $entity</h1>
        </header>

        <?php if (isset(\$_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= \$_SESSION['success']; unset(\$_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset(\$_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= \$_SESSION['error']; unset(\$_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <a href="<?= BASE_PATH ?>/crud/$entity/create" class="btn btn-primary">➕ Crear Nuevo</a>
                    <a href="<?= BASE_PATH ?>/" class="btn btn-secondary">🏠 Inicio</a>
                </div>
                <div>
                    <a href="<?= BASE_PATH ?>/export/$entity" class="btn btn-success">📦 Exportar Proyecto</a>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        $headers
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty(\$items)): ?>
                        <tr>
                            <td colspan="100" style="text-align: center; padding: 40px; color: #999;">
                                📭 No hay registros aún. <a href="<?= BASE_PATH ?>/crud/$entity/create">Crear el primero</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (\$items as \$item): ?>
                        <tr>
                            <td><?= \$item['id'] ?></td>
                            $rows
                            <td>
                                <a href="<?= BASE_PATH ?>/crud/$entity/edit/<?= \$item['id'] ?>" class="btn btn-sm">✏️ Editar</a>
                                <form method="POST" action="<?= BASE_PATH ?>/crud/$entity/delete/<?= \$item['id'] ?>" style="display:inline;">
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
HTML;

        $this->saveFile("views/$entity", 'index.php', $content);
    }

    private function generateCreateView(string $entity, array $fields): void
    {
        $formFields = '';
        
        foreach ($fields as $field) {
            $type = $this->getInputType($field['type']);
            $formFields .= <<<HTML
        <div class="form-group">
            <label>{$field['name']}:</label>
            <input type="$type" name="{$field['name']}" required>
        </div>
        
HTML;
        }

        $content = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear $entity</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>➕ Crear $entity</h1>
        </header>

        <?php if (isset(\$_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= \$_SESSION['error']; unset(\$_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/$entity/store">
                $formFields
                <button type="submit" class="btn btn-primary">💾 Guardar</button>
                <a href="<?= BASE_PATH ?>/crud/$entity" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
HTML;

        $this->saveFile("views/$entity", 'create.php', $content);
    }

    private function generateEditView(string $entity, array $fields): void
    {
        $formFields = '';
        
        foreach ($fields as $field) {
            $type = $this->getInputType($field['type']);
            $formFields .= <<<HTML
        <div class="form-group">
            <label>{$field['name']}:</label>
            <input type="$type" name="{$field['name']}" value="<?= htmlspecialchars(\$item['{$field['name']}']) ?>" required>
        </div>
        
HTML;
        }

        $content = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar $entity</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>✏️ Editar $entity</h1>
        </header>

        <?php if (isset(\$_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= \$_SESSION['error']; unset(\$_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="<?= BASE_PATH ?>/crud/$entity/update/<?= \$item['id'] ?>">
                $formFields
                <button type="submit" class="btn btn-primary">💾 Actualizar</button>
                <a href="<?= BASE_PATH ?>/crud/$entity" class="btn btn-secondary">❌ Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
HTML;

        $this->saveFile("views/$entity", 'edit.php', $content);
    }

    private function getInputType(string $type): string
    {
        $types = [
            'string' => 'text',
            'text' => 'textarea',
            'integer' => 'number',
            'bigint' => 'number',
            'float' => 'number',
            'decimal' => 'number',
            'boolean' => 'checkbox',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'timestamp' => 'datetime-local'
        ];

        return $types[$type] ?? 'text';
    }

    private function saveFile(string $directory, string $filename, string $content): void
    {
        $fullPath = $this->generatedPath . $directory;
        
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        file_put_contents($fullPath . '/' . $filename, $content);
    }
}