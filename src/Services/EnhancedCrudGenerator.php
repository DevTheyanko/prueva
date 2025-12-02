<?php
namespace App\Services;

use App\Config\Database;

class EnhancedCrudGenerator
{
    private $generatedPath;
    private $db;
    private $relations = [];

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->generatedPath = $config['app']['generated_path'];
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Generar CRUDs para todas las tablas de una base de datos
     */
    public function generateAllCruds(string $dbName, array $options = []): array
    {
        $results = [];
        
        try {
            // Obtener todas las tablas
            $tables = $this->getTablesFromDatabase($dbName);
            
            // Obtener relaciones
            $this->relations = $this->getAllRelations($dbName);

            foreach ($tables as $table) {
                $entity = $this->tableNameToEntity($table);
                $fields = $this->getTableFields($dbName, $table);
                
                // Generar archivos del CRUD
                $this->generateModel($entity, $table, $dbName);
                $this->generateController($entity, $fields, $table);
                $this->generateViews($entity, $fields, $table);
                
                $results[] = [
                    'entity' => $entity,
                    'table' => $table,
                    'status' => 'success'
                ];
            }

            return [
                'success' => true,
                'results' => $results
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener tablas de la base de datos
     */
    private function getTablesFromDatabase(string $dbName): array
    {
        $stmt = $this->db->query("SHOW TABLES FROM `$dbName`");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Obtener campos de una tabla
     */
    private function getTableFields(string $dbName, string $tableName): array
    {
        $stmt = $this->db->query("DESCRIBE `$dbName`.`$tableName`");
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $fields = [];
        foreach ($columns as $column) {
            if (strtolower($column['Field']) === 'id') continue;
            
            $fields[] = [
                'name' => $column['Field'],
                'type' => $this->mysqlTypeToPhp($column['Type']),
                'nullable' => $column['Null'] === 'YES',
                'default' => $column['Default'],
                'key' => $column['Key']
            ];
        }
        
        return $fields;
    }

    /**
     * Obtener todas las relaciones de la base de datos
     */
    private function getAllRelations(string $dbName): array
    {
        $sql = "SELECT 
                    TABLE_NAME as from_table,
                    COLUMN_NAME as from_column,
                    REFERENCED_TABLE_NAME as to_table,
                    REFERENCED_COLUMN_NAME as to_column,
                    CONSTRAINT_NAME as constraint_name
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dbName]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Generar modelo mejorado con relaciones
     */
    private function generateModel(string $entity, string $tableName, string $dbName): void
    {
        $className = ucfirst($entity);
        
        // Obtener relaciones de esta tabla
        $tableRelations = array_filter($this->relations, function($rel) use ($tableName) {
            return $rel['from_table'] === $tableName;
        });

        // Generar métodos de relación
        $relationMethods = $this->generateRelationMethods($tableRelations);

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
            \$stmt = \$this->db->query("DESCRIBE `$dbName`.`{\$this->table}`");
            \$columns = \$stmt->fetchAll(PDO::FETCH_COLUMN);
            
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
        \$stmt = \$this->db->query("SELECT * FROM `$dbName`.`{\$this->table}`");
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithRelations(): array
    {
        \$items = \$this->getAll();
        
        foreach (\$items as &\$item) {
            \$item = \$this->loadRelations(\$item);
        }
        
        return \$items;
    }

    public function getById(int \$id): ?array
    {
        \$stmt = \$this->db->prepare("SELECT * FROM `$dbName`.`{\$this->table}` WHERE id = ?");
        \$stmt->execute([\$id]);
        \$result = \$stmt->fetch(PDO::FETCH_ASSOC);
        
        if (\$result) {
            \$result = \$this->loadRelations(\$result);
        }
        
        return \$result ?: null;
    }

    private function loadRelations(array \$item): array
    {
        $relationMethods
        return \$item;
    }

    public function create(array \$data): bool
    {
        \$data = \$this->filterData(\$data);
        
        if (empty(\$data)) {
            return false;
        }

        \$columns = implode(', ', array_keys(\$data));
        \$placeholders = implode(', ', array_fill(0, count(\$data), '?'));
        
        \$sql = "INSERT INTO `$dbName`.`{\$this->table}` (\$columns) VALUES (\$placeholders)";
        \$stmt = \$this->db->prepare(\$sql);
        
        return \$stmt->execute(array_values(\$data));
    }

    public function update(int \$id, array \$data): bool
    {
        \$data = \$this->filterData(\$data);
        
        if (empty(\$data)) {
            return false;
        }

        \$fields = implode(' = ?, ', array_keys(\$data)) . ' = ?';
        \$sql = "UPDATE `$dbName`.`{\$this->table}` SET \$fields WHERE id = ?";
        
        \$stmt = \$this->db->prepare(\$sql);
        \$values = array_values(\$data);
        \$values[] = \$id;
        
        return \$stmt->execute(\$values);
    }

    public function delete(int \$id): bool
    {
        \$stmt = \$this->db->prepare("DELETE FROM `$dbName`.`{\$this->table}` WHERE id = ?");
        return \$stmt->execute([\$id]);
    }

    public function getAllowedFields(): array
    {
        return \$this->allowedFields;
    }
}
PHP;

        $this->saveFile('models', "{$className}.php", $content);
        $this->generateRepositoryInterface($entity);
    }

    /**
     * Generar métodos de relación
     */
    private function generateRelationMethods(array $relations): string
    {
        if (empty($relations)) {
            return "// Sin relaciones";
        }

        $methods = "";
        foreach ($relations as $relation) {
            $relatedTable = $relation['to_table'];
            $relatedEntity = $this->tableNameToEntity($relatedTable);
            $foreignKey = $relation['from_column'];

            $methods .= <<<PHP

        // Relación: {$relation['from_table']}.{$foreignKey} -> {$relatedTable}
        if (isset(\$item['$foreignKey']) && \$item['$foreignKey']) {
            \$stmt = \$this->db->prepare("SELECT * FROM `$relatedTable` WHERE id = ?");
            \$stmt->execute([\$item['$foreignKey']]);
            \$item['{$relatedEntity}_data'] = \$stmt->fetch(PDO::FETCH_ASSOC);
        }

PHP;
        }

        return $methods;
    }

    /**
     * Convertir nombre de tabla a entidad
     */
    private function tableNameToEntity(string $tableName): string
    {
        // Remover plurales comunes
        $singular = rtrim($tableName, 's');
        if ($singular === $tableName) {
            $singular = rtrim($tableName, 'es');
        }
        
        return strtolower($singular);
    }

    /**
     * Mapear tipo MySQL a PHP
     */
    private function mysqlTypeToPhp(string $mysqlType): string
    {
        if (preg_match('/int/i', $mysqlType)) return 'integer';
        if (preg_match('/varchar|char/i', $mysqlType)) return 'string';
        if (preg_match('/text/i', $mysqlType)) return 'text';
        if (preg_match('/decimal|float|double/i', $mysqlType)) return 'decimal';
        if (preg_match('/date/i', $mysqlType)) return 'date';
        if (preg_match('/datetime|timestamp/i', $mysqlType)) return 'datetime';
        if (preg_match('/tinyint\(1\)/i', $mysqlType)) return 'boolean';
        
        return 'string';
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
    public function getAllWithRelations(): array;
    public function getById(int \$id): ?array;
    public function create(array \$data): bool;
    public function update(int \$id, array \$data): bool;
    public function delete(int \$id): bool;
}
PHP;

        $this->saveFile('interfaces', "{$className}RepositoryInterface.php", $content);
    }

    private function generateController(string $entity, array $fields, string $tableName): void
    {
        $className = ucfirst($entity);
        
        $content = <<<PHP
<?php
use Generated\Models\\$className;

function get{$className}Model(): $className
{
    return new $className();
}

function cleanFormData(array \$data): array
{
    \$unwanted = ['submit', 'csrf_token', '_method'];
    foreach (\$unwanted as \$field) {
        unset(\$data[\$field]);
    }
    
    return array_filter(\$data, function(\$value) {
        return \$value !== '';
    });
}

function index_{$entity}(): void
{
    \$model = get{$className}Model();
    \$items = \$model->getAllWithRelations();
    require __DIR__ . '/../views/{$entity}/index.php';
}

function create_{$entity}(): void
{
    require __DIR__ . '/../views/{$entity}/create.php';
}

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

    private function generateViews(string $entity, array $fields, string $tableName): void
    {
        // Generar las vistas (index, create, edit) similar al código anterior
        // pero con soporte para relaciones
        
        $this->generateIndexViewWithRelations($entity, $fields);
        $this->generateCreateViewWithRelations($entity, $fields);
        $this->generateEditViewWithRelations($entity, $fields);
    }

    private function generateIndexViewWithRelations(string $entity, array $fields): void
    {
        $headers = '';
        $rows = '';
        
        foreach ($fields as $field) {
            // Detectar si es foreign key
            $isForeignKey = strpos($field['name'], '_id') !== false;
            
            if ($isForeignKey) {
                $relatedEntity = str_replace('_id', '', $field['name']);
                $headers .= "<th>" . ucfirst($relatedEntity) . "</th>\n                ";
                $rows .= "<td><?= isset(\$item['{$relatedEntity}_data']['name']) ? htmlspecialchars(\$item['{$relatedEntity}_data']['name']) : 'N/A' ?></td>\n                ";
            } else {
                $headers .= "<th>{$field['name']}</th>\n                ";
                $rows .= "<td><?= htmlspecialchars(\$item['{$field['name']}']) ?></td>\n                ";
            }
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

    private function generateCreateViewWithRelations(string $entity, array $fields): void
    {
        // Similar al anterior pero detectando foreign keys para mostrar selects
        $formFields = '';
        
        foreach ($fields as $field) {
            $isForeignKey = strpos($field['name'], '_id') !== false;
            
            if ($isForeignKey) {
                $relatedEntity = str_replace('_id', '', $field['name']);
                $relatedTable = $relatedEntity . 's'; // Pluralizar
                
                $formFields .= <<<HTML
        <div class="form-group">
            <label>{$relatedEntity}:</label>
            <select name="{$field['name']}" required>
                <option value="">Seleccionar...</option>
                <?php
                // Cargar opciones de la tabla relacionada
                \$relatedModel = new Generated\Models\\{ucfirst($relatedEntity)}();
                \$options = \$relatedModel->getAll();
                foreach (\$options as \$option):
                ?>
                    <option value="<?= \$option['id'] ?>"><?= \$option['name'] ?? \$option['title'] ?? \$option['id'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
HTML;
            } else {
                $type = $this->getInputType($field['type']);
                $formFields .= <<<HTML
        <div class="form-group">
            <label>{$field['name']}:</label>
            <input type="$type" name="{$field['name']}" required>
        </div>
        
HTML;
            }
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

    private function generateEditViewWithRelations(string $entity, array $fields): void
    {
        // Similar al create pero con valores precargados
        $formFields = '';
        
        foreach ($fields as $field) {
            $isForeignKey = strpos($field['name'], '_id') !== false;
            
            if ($isForeignKey) {
                $relatedEntity = str_replace('_id', '', $field['name']);
                
                $formFields .= <<<HTML
        <div class="form-group">
            <label>{$relatedEntity}:</label>
            <select name="{$field['name']}" required>
                <option value="">Seleccionar...</option>
                <?php
                \$relatedModel = new Generated\Models\\{ucfirst($relatedEntity)}();
                \$options = \$relatedModel->getAll();
                foreach (\$options as \$option):
                ?>
                    <option value="<?= \$option['id'] ?>" <?= \$item['{$field['name']}'] == \$option['id'] ? 'selected' : '' ?>>
                        <?= \$option['name'] ?? \$option['title'] ?? \$option['id'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
HTML;
            } else {
                $type = $this->getInputType($field['type']);
                $formFields .= <<<HTML
        <div class="form-group">
            <label>{$field['name']}:</label>
            <input type="$type" name="{$field['name']}" value="<?= htmlspecialchars(\$item['{$field['name']}']) ?>" required>
        </div>
        
HTML;
            }
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
            'decimal' => 'number',
            'boolean' => 'checkbox',
            'date' => 'date',
            'datetime' => 'datetime-local',
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