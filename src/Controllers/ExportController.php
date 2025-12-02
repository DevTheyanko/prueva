<?php
namespace App\Controllers;

use App\Core\View;
use App\Services\ExportService;

class ExportController
{
    private $exportService;

    public function __construct()
    {
        $this->exportService = new ExportService();
    }

    public function showExportOptions(string $entity): void
    {
        // Verificar que el CRUD existe
        $controllerFile = __DIR__ . '/../../generated/controllers/' . $entity . '_controller.php';
        $oldControllerFile = __DIR__ . '/../../generated/controllers/' . ucfirst($entity) . 'Controller.php';
        
        if (!file_exists($controllerFile) && !file_exists($oldControllerFile)) {
            $_SESSION['error'] = "CRUD '$entity' no encontrado.";
            header('Location: ' . BASE_PATH . '/');
            exit;
        }

        View::render('generator/export_options', ['entity' => $entity]);
    }

    public function export(string $entity): void
    {
        try {
            $options = [
                'include_styles' => isset($_POST['include_styles']),
                'include_base_files' => isset($_POST['include_base_files']),
                'style_framework' => $_POST['style_framework'] ?? 'custom',
                'libraries' => $_POST['libraries'] ?? [],
                'project_name' => $_POST['project_name'] ?? 'crud-' . $entity,
                'base_path' => $_POST['base_path'] ?? '/' . ($_POST['project_name'] ?? 'crud-' . $entity)
            ];

            $zipFile = $this->exportService->exportFullProject($entity, $options);
            
            if (file_exists($zipFile)) {
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $options['project_name'] . '_' . date('Y-m-d_His') . '.zip"');
                header('Content-Length: ' . filesize($zipFile));
                
                readfile($zipFile);
                
                // Eliminar el archivo temporal
                unlink($zipFile);
                exit;
            } else {
                throw new \Exception("No se pudo crear el archivo ZIP");
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_PATH . '/export/' . $entity);
            exit;
        }
    }
}