<?php
namespace App\Controllers;

use App\Core\View;
use App\Services\FullProjectExporter;
use App\Config\Database;
use PDO;

class FullExportController
{
    private $exporter;
    private $db;

    public function __construct()
    {
        $this->exporter = new FullProjectExporter();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Mostrar opciones de exportación del proyecto completo
     */
    public function showExportOptions(): void
    {
        try {
            // Obtener bases de datos disponibles
            $stmt = $this->db->query("SHOW DATABASES");
            $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Filtrar bases de datos del sistema
            $systemDbs = ['information_schema', 'mysql', 'performance_schema', 'sys', 'phpmyadmin'];
            $databases = array_filter($databases, function($db) use ($systemDbs) {
                return !in_array(strtolower($db), $systemDbs);
            });

            View::render('generator/full_export_options', [
                'databases' => array_values($databases)
            ]);
        } catch (\Exception $e) {
            die("Error al cargar bases de datos: " . $e->getMessage());
        }
    }

    /**
     * Exportar proyecto completo
     */
    public function exportFullProject(): void
    {
        try {
            $dbName = $_POST['database_name'] ?? '';
            
            if (empty($dbName)) {
                throw new \Exception("Debes seleccionar una base de datos");
            }

            $options = [
                'include_styles' => isset($_POST['include_styles']),
                'style_framework' => $_POST['style_framework'] ?? 'custom',
                'libraries' => $_POST['libraries'] ?? [],
                'project_name' => $_POST['project_name'] ?? $dbName . '_project',
                'base_path' => $_POST['base_path'] ?? '/' . ($_POST['project_name'] ?? $dbName . '_project')
            ];

            $zipFile = $this->exporter->exportFullProject($dbName, $options);
            
            if (file_exists($zipFile)) {
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $options['project_name'] . '_' . date('Y-m-d_His') . '.zip"');
                header('Content-Length: ' . filesize($zipFile));
                
                readfile($zipFile);
                
                // Eliminar archivo temporal
                unlink($zipFile);
                exit;
            } else {
                throw new \Exception("No se pudo crear el archivo ZIP");
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_PATH . '/export/full-project');
            exit;
        }
    }
}