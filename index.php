<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Router;
use App\Controllers\GeneratorController;
use App\Controllers\CrudController;
use App\Controllers\ExportController;
use App\Controllers\DatabaseGeneratorController;
use App\Controllers\FullExportController;


session_start();

// Configurar la ruta base del proyecto
define('BASE_PATH', '/crud-generator');

$router = new Router();

// ============================================
// RUTAS DEL GENERADOR
// ============================================
$router->add('GET', '/', [GeneratorController::class, 'index']);
$router->add('GET', '/generator/table-builder', [GeneratorController::class, 'tableBuilder']);
$router->add('POST', '/generator/generate', [GeneratorController::class, 'generate']);
$router->add('GET', '/diagnostic', [GeneratorController::class, 'diagnostic']);

// ============================================
// RUTAS DEL DATABASE BUILDER (CRÍTICAS - DEBEN ESTAR ANTES DE /CRUD)
// ============================================
$router->add('GET', '/generator/database-builder', [DatabaseGeneratorController::class, 'showBuilder']);
$router->add('POST', '/api/generate-full-database', [DatabaseGeneratorController::class, 'generateFullDatabase']);
$router->add('POST', '/api/import-schema', [DatabaseGeneratorController::class, 'importSchema']);
$router->add('GET', '/api/list-databases', [DatabaseGeneratorController::class, 'listDatabases']);


// ============================================
// RUTAS DE EXPORTACIÓN INDIVIDUAL
// ============================================
$router->add('GET', '/export/{entity}', [ExportController::class, 'showExportOptions']);
$router->add('POST', '/export/{entity}/download', [ExportController::class, 'export']);

// ============================================
// RUTAS DE EXPORTACIÓN COMPLETA
// ============================================
$router->add('GET', '/export/full-project', [FullExportController::class, 'showExportOptions']);
$router->add('POST', '/export/full-project/download', [FullExportController::class, 'exportFullProject']);

// ============================================
// RUTAS DINÁMICAS DEL CRUD (DEBEN IR AL FINAL)
// ============================================
$router->add('GET', '/crud/{entity}', [CrudController::class, 'index']);
$router->add('GET', '/crud/{entity}/create', [CrudController::class, 'create']);
$router->add('POST', '/crud/{entity}/store', [CrudController::class, 'store']);
$router->add('GET', '/crud/{entity}/edit/{id}', [CrudController::class, 'edit']);
$router->add('POST', '/crud/{entity}/update/{id}', [CrudController::class, 'update']);
$router->add('POST', '/crud/{entity}/delete/{id}', [CrudController::class, 'delete']);

// ============================================
// DISPATCH
// ============================================
$router->dispatch();