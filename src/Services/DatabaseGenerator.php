<?php
namespace App\Services;

use App\Config\Database;

class DatabaseGenerator
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createTable(string $tableName, array $fields): bool
    {
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (";
        $sql .= "`id` INT AUTO_INCREMENT PRIMARY KEY,";

        foreach ($fields as $field) {
            $type = $this->mapFieldType($field['type'], $field['length'] ?? null);
            $null = $field['nullable'] ? 'NULL' : 'NOT NULL';
            $sql .= "`{$field['name']}` $type $null,";
        }

        $sql = rtrim($sql, ',');
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        try {
            $this->db->exec($sql);
            return true;
        } catch (\PDOException $e) {
            throw new \Exception("Error creando tabla: " . $e->getMessage());
        }
    }

    private function mapFieldType(string $type, ?int $length): string
    {
        $types = [
            'string' => 'VARCHAR(' . ($length ?? 255) . ')',
            'text' => 'TEXT',
            'integer' => 'INT',
            'bigint' => 'BIGINT',
            'float' => 'FLOAT',
            'decimal' => 'DECIMAL(10,2)',
            'boolean' => 'TINYINT(1)',
            'date' => 'DATE',
            'datetime' => 'DATETIME',
            'timestamp' => 'TIMESTAMP'
        ];

        return $types[$type] ?? 'VARCHAR(255)';
    }
}
