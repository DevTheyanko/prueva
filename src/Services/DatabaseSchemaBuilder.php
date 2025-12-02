<?php
namespace App\Services;

use App\Config\Database;
use PDO;

class DatabaseSchemaBuilder
{
    private $db;
    private $config;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->config = require __DIR__ . '/../../config/config.php';
    }

    /**
     * Crear base de datos completa con todas las tablas y relaciones
     */
    public function createFullDatabase(array $schema): array
    {
        $results = [];
        
        try {
            // 1. Crear la base de datos si no existe
            $dbName = $schema['database_name'];
            $this->createDatabase($dbName);
            $results['database'] = "Base de datos '$dbName' creada correctamente";

            // 2. Usar la base de datos
            $this->db->exec("USE `$dbName`");

            // 3. Crear todas las tablas (primero sin relaciones)
            foreach ($schema['tables'] as $table) {
                $this->createTable($table);
                $results['tables'][] = "Tabla '{$table['name']}' creada";
            }

            // 4. Agregar las relaciones (foreign keys)
            if (isset($schema['relationships']) && !empty($schema['relationships'])) {
                foreach ($schema['relationships'] as $relation) {
                    if (!empty($relation['from_table']) && !empty($relation['to_table'])) {
                        $this->addForeignKey($relation);
                        $results['relationships'][] = "Relación creada: {$relation['from_table']}.{$relation['from_column']} -> {$relation['to_table']}.{$relation['to_column']}";
                    }
                }
            }

            // 5. Insertar datos de ejemplo si se solicita
            if (!empty($schema['insert_sample_data'])) {
                $this->insertSampleData($schema['tables']);
                $results['sample_data'] = "Datos de ejemplo insertados";
            }

            return [
                'success' => true,
                'results' => $results
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }

    /**
     * Crear base de datos
     */
    private function createDatabase(string $dbName): void
    {
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbName` 
                CHARACTER SET utf8mb4 
                COLLATE utf8mb4_unicode_ci";
        
        $this->db->exec($sql);
    }

    /**
     * Crear tabla individual
     */
    private function createTable(array $table): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$table['name']}` (";
        
        // Campo ID automático (si no se especifica lo contrario)
        if (!isset($table['no_auto_id']) || !$table['no_auto_id']) {
            $sql .= "`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,";
        }

        // Agregar campos
        if (isset($table['fields']) && !empty($table['fields'])) {
            foreach ($table['fields'] as $field) {
                if (!empty($field['name'])) {
                    $sql .= $this->buildFieldDefinition($field) . ",";
                }
            }
        }

        // Timestamps automáticos (created_at, updated_at)
        if (!empty($table['timestamps'])) {
            $sql .= "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,";
            $sql .= "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,";
        }

        // Soft deletes (deleted_at)
        if (!empty($table['soft_deletes'])) {
            $sql .= "`deleted_at` TIMESTAMP NULL DEFAULT NULL,";
        }

        // Índices únicos
        if (isset($table['unique_keys'])) {
            foreach ($table['unique_keys'] as $uniqueKey) {
                $fields = is_array($uniqueKey) ? implode('`, `', $uniqueKey) : $uniqueKey;
                $sql .= "UNIQUE KEY `unique_{$fields}` (`{$fields}`),";
            }
        }

        // Índices normales
        if (isset($table['indexes'])) {
            foreach ($table['indexes'] as $index) {
                $fields = is_array($index) ? implode('`, `', $index) : $index;
                $sql .= "INDEX `idx_{$fields}` (`{$fields}`),";
            }
        }

        $sql = rtrim($sql, ',');
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->db->exec($sql);
    }

    /**
     * Construir definición de campo
     */
    private function buildFieldDefinition(array $field): string
    {
        $sql = "`{$field['name']}` ";
        
        // Tipo de dato
        $sql .= $this->mapFieldType($field['type'], $field['length'] ?? null);

        // Unsigned (solo para números)
        if (!empty($field['unsigned']) && in_array($field['type'], ['integer', 'bigint', 'float', 'decimal'])) {
            $sql .= " UNSIGNED";
        }

        // NULL o NOT NULL
        $nullable = isset($field['nullable']) ? (bool)$field['nullable'] : false;
        $sql .= $nullable ? ' NULL' : ' NOT NULL';

        // Valor por defecto
        if (isset($field['default'])) {
            if ($field['default'] === 'NULL') {
                $sql .= " DEFAULT NULL";
            } elseif ($field['default'] === 'CURRENT_TIMESTAMP') {
                $sql .= " DEFAULT CURRENT_TIMESTAMP";
            } else {
                $sql .= " DEFAULT '{$field['default']}'";
            }
        }

        // Auto increment
        if (!empty($field['auto_increment'])) {
            $sql .= " AUTO_INCREMENT";
        }

        // Comentario
        if (!empty($field['comment'])) {
            $sql .= " COMMENT '{$field['comment']}'";
        }

        return $sql;
    }

    /**
     * Mapear tipos de datos
     */
    private function mapFieldType(string $type, $length): string
    {
        // Si length es null o vacío, usar valores por defecto
        if ($length === null || $length === '') {
            $length = match($type) {
                'string' => 255,
                'char' => 10,
                'decimal' => '10,2',
                'enum' => "('active','inactive')",
                'set' => "('option1','option2')",
                default => null
            };
        }

        $types = [
            'string' => 'VARCHAR(' . $length . ')',
            'char' => 'CHAR(' . $length . ')',
            'text' => 'TEXT',
            'mediumtext' => 'MEDIUMTEXT',
            'longtext' => 'LONGTEXT',
            'integer' => 'INT',
            'tinyint' => 'TINYINT',
            'smallint' => 'SMALLINT',
            'mediumint' => 'MEDIUMINT',
            'bigint' => 'BIGINT',
            'float' => 'FLOAT',
            'double' => 'DOUBLE',
            'decimal' => 'DECIMAL(' . $length . ')',
            'boolean' => 'TINYINT(1)',
            'date' => 'DATE',
            'datetime' => 'DATETIME',
            'timestamp' => 'TIMESTAMP',
            'time' => 'TIME',
            'year' => 'YEAR',
            'json' => 'JSON',
            'enum' => 'ENUM' . $length,
            'set' => 'SET' . $length
        ];

        return $types[$type] ?? 'VARCHAR(255)';
    }

    /**
     * Agregar clave foránea (relación)
     */
    private function addForeignKey(array $relation): void
    {
        // Validar que todos los campos necesarios estén presentes
        if (empty($relation['from_table']) || empty($relation['from_column']) || 
            empty($relation['to_table']) || empty($relation['to_column'])) {
            throw new \Exception("Relación incompleta: faltan campos requeridos");
        }

        $constraintName = "fk_{$relation['from_table']}_{$relation['from_column']}";
        
        $onDelete = $relation['on_delete'] ?? 'CASCADE';
        $onUpdate = $relation['on_update'] ?? 'CASCADE';

        // Verificar si la foreign key ya existe
        $checkSql = "SELECT COUNT(*) as count FROM information_schema.TABLE_CONSTRAINTS 
                     WHERE CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY'";
        $stmt = $this->db->prepare($checkSql);
        $stmt->execute([$constraintName]);
        $exists = $stmt->fetchColumn() > 0;

        if (!$exists) {
            $sql = "ALTER TABLE `{$relation['from_table']}` 
                    ADD CONSTRAINT `{$constraintName}` 
                    FOREIGN KEY (`{$relation['from_column']}`) 
                    REFERENCES `{$relation['to_table']}`(`{$relation['to_column']}`)
                    ON DELETE {$onDelete}
                    ON UPDATE {$onUpdate}";

            $this->db->exec($sql);
        }
    }

    /**
     * Insertar datos de ejemplo
     */
    private function insertSampleData(array $tables): void
    {
        foreach ($tables as $table) {
            if (!empty($table['sample_data'])) {
                foreach ($table['sample_data'] as $row) {
                    $columns = implode('`, `', array_keys($row));
                    $placeholders = implode(', ', array_fill(0, count($row), '?'));
                    
                    $sql = "INSERT INTO `{$table['name']}` (`{$columns}`) VALUES ({$placeholders})";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(array_values($row));
                }
            }
        }
    }

    /**
     * Exportar esquema SQL completo
     */
    public function exportSchema(string $dbName): string
    {
        $sql = "-- Base de datos: $dbName\n";
        $sql .= "-- Generado: " . date('Y-m-d H:i:s') . "\n\n";
        
        $sql .= "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
        $sql .= "USE `$dbName`;\n\n";

        // Obtener todas las tablas
        $stmt = $this->db->query("SHOW TABLES FROM `$dbName`");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $stmt = $this->db->query("SHOW CREATE TABLE `$dbName`.`$table`");
            $row = $stmt->fetch();
            
            $sql .= "-- Tabla: $table\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $row['Create Table'] . ";\n\n";
        }

        return $sql;
    }

    /**
     * Obtener información de relaciones existentes
     */
    public function getTableRelations(string $dbName, string $tableName): array
    {
        $sql = "SELECT 
                    CONSTRAINT_NAME as constraint_name,
                    COLUMN_NAME as from_column,
                    REFERENCED_TABLE_NAME as to_table,
                    REFERENCED_COLUMN_NAME as to_column
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dbName, $tableName]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Validar esquema antes de crear
     */
    public function validateSchema(array $schema): array
    {
        $errors = [];

        // Validar nombre de base de datos
        if (empty($schema['database_name'])) {
            $errors[] = "El nombre de la base de datos es requerido";
        }

        // Validar tablas
        if (empty($schema['tables']) || !is_array($schema['tables'])) {
            $errors[] = "Debe haber al menos una tabla";
        } else {
            foreach ($schema['tables'] as $index => $table) {
                if (empty($table['name'])) {
                    $errors[] = "La tabla #$index no tiene nombre";
                }
                
                // Validar que tenga campos
                if (!isset($table['fields']) || empty($table['fields'])) {
                    $errors[] = "La tabla '{$table['name']}' no tiene campos definidos";
                }
            }
        }

        // Validar relaciones
        if (isset($schema['relationships'])) {
            foreach ($schema['relationships'] as $index => $relation) {
                if (empty($relation['from_table']) || empty($relation['to_table'])) {
                    $errors[] = "La relación #$index está incompleta (falta from_table o to_table)";
                }
                if (empty($relation['from_column']) || empty($relation['to_column'])) {
                    $errors[] = "La relación #$index está incompleta (falta from_column o to_column)";
                }
            }
        }

        return $errors;
    }
}