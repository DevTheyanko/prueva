<?php
namespace App\Controllers;

use App\Core\View;
use App\Services\DatabaseSchemaBuilder;
use App\Services\EnhancedCrudGenerator;

class DatabaseGeneratorController
{
    private $schemaBuilder;
    private $crudGenerator;

    public function __construct()
    {
        $this->schemaBuilder = new DatabaseSchemaBuilder();
        $this->crudGenerator = new EnhancedCrudGenerator();
    }

    /**
     * Mostrar el constructor visual de bases de datos
     */
    public function showBuilder(): void
    {
        View::render('generator/database_builder');
    }

    /**
     * Generar base de datos completa con todas las tablas y relaciones
     */
    public function generateFullDatabase(): void
    {
        try {
            // Obtener el esquema JSON del request
            $json = file_get_contents('php://input');
            $schema = json_decode($json, true);

            if (!$schema) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Esquema JSON inválido'
                ]);
                return;
            }

            // Validar esquema
            $errors = $this->schemaBuilder->validateSchema($schema);
            if (!empty($errors)) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Errores de validación: ' . implode(', ', $errors)
                ]);
                return;
            }

            // 1. Crear la base de datos con todas las tablas
            $dbResult = $this->schemaBuilder->createFullDatabase($schema);

            if (!$dbResult['success']) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => $dbResult['error']
                ]);
                return;
            }

            // 2. Generar CRUDs para todas las tablas
            $crudResult = $this->crudGenerator->generateAllCruds(
                $schema['database_name'],
                [
                    'include_relations' => true,
                    'generate_api' => false
                ]
            );

            if (!$crudResult['success']) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => $crudResult['error']
                ]);
                return;
            }

            // 3. Exportar SQL de la base de datos
            $sqlExport = $this->schemaBuilder->exportSchema($schema['database_name']);
            
            $exportsDir = __DIR__ . '/../../exports/';
            if (!is_dir($exportsDir)) {
                mkdir($exportsDir, 0777, true);
            }
            
            $sqlFilePath = $exportsDir . $schema['database_name'] . '_export_' . date('Ymd_His') . '.sql';
            file_put_contents($sqlFilePath, $sqlExport);

            // Respuesta exitosa
            $this->jsonResponse([
                'success' => true,
                'message' => 'Base de datos y CRUDs generados correctamente',
                'results' => [
                    'database' => $dbResult['results'],
                    'cruds' => $crudResult['results'],
                    'sql_export' => basename($sqlFilePath)
                ],
                'tables_count' => count($schema['tables']),
                'relations_count' => isset($schema['relationships']) ? count($schema['relationships']) : 0
            ]);

        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Error general: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Importar esquema desde archivo JSON
     */
    public function importSchema(): void
    {
        try {
            if (!isset($_FILES['schema_file'])) {
                throw new \Exception('No se recibió ningún archivo');
            }

            $file = $_FILES['schema_file'];
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Error al subir el archivo');
            }

            $json = file_get_contents($file['tmp_name']);
            $schema = json_decode($json, true);

            if (!$schema) {
                throw new \Exception('Archivo JSON inválido');
            }

            // Validar esquema
            $errors = $this->schemaBuilder->validateSchema($schema);
            if (!empty($errors)) {
                throw new \Exception('Errores: ' . implode(', ', $errors));
            }

            // Guardar temporalmente para usarlo en el builder
            $_SESSION['imported_schema'] = $schema;

            $this->jsonResponse([
                'success' => true,
                'message' => 'Esquema importado correctamente',
                'schema' => $schema
            ]);

        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Listar bases de datos disponibles
     */
    public function listDatabases(): void
    {
        try {
            $db = \App\Config\Database::getInstance()->getConnection();
            $stmt = $db->query("SHOW DATABASES");
            $databases = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            // Filtrar bases de datos del sistema
            $systemDbs = ['information_schema', 'mysql', 'performance_schema', 'sys'];
            $databases = array_filter($databases, function($db) use ($systemDbs) {
                return !in_array($db, $systemDbs);
            });

            $this->jsonResponse([
                'success' => true,
                'databases' => array_values($databases)
            ]);

        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Respuesta JSON
     */
    private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}