<?php
namespace App\Controllers;

use App\Core\View;
use App\Services\DatabaseGenerator;
use App\Services\CrudGenerator;

class GeneratorController
{
    public function index(): void
    {
        View::render('generator/index');
    }

    public function tableBuilder(): void
    {
        View::render('generator/table_builder');
    }

    public function generate(): void
    {
        try {
            $entity = $_POST['entity'] ?? '';
            $tableName = $_POST['table_name'] ?? '';
            $fields = json_decode($_POST['fields'], true);

            if (empty($entity) || empty($tableName) || empty($fields)) {
                throw new \Exception('Datos incompletos');
            }

            // Crear tabla en BD
            $dbGenerator = new DatabaseGenerator();
            $dbGenerator->createTable($tableName, $fields);

            // Generar archivos
            $crudGenerator = new CrudGenerator();
            $crudGenerator->generateModel($entity, $tableName);
            $crudGenerator->generateController($entity, $fields);
            $crudGenerator->generateViews($entity, $fields);

            $_SESSION['success'] = "CRUD generado exitosamente para: $entity";
            header('Location: ' . BASE_PATH . '/');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_PATH . '/generator/table-builder');
            exit;
        }
    }

    public function diagnostic(): void
    {
        View::render('generator/diagnostic');
    }
}