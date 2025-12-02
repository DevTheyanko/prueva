<?php
namespace App\Controllers;

class CrudController
{
    public function index(string $entity): void
    {
        $this->loadController($entity, 'index', []);
    }

    public function create(string $entity): void
    {
        $this->loadController($entity, 'create', []);
    }

    public function store(string $entity): void
    {
        $this->loadController($entity, 'store', []);
    }

    public function edit(string $entity, int $id): void
    {
        $this->loadController($entity, 'edit', [$id]);
    }

    public function update(string $entity, int $id): void
    {
        $this->loadController($entity, 'update', [$id]);
    }

    public function delete(string $entity, int $id): void
    {
        $this->loadController($entity, 'delete', [$id]);
    }

    private function loadController(string $entity, string $action, array $params): void
    {
        // NUEVO FORMATO: nombre_controller.php (con funciones)
        $controllerFile = __DIR__ . '/../../generated/controllers/' . $entity . '_controller.php';
        
        // Si no existe el nuevo formato, buscar el formato antiguo
        if (!file_exists($controllerFile)) {
            // Formato antiguo: NombreController.php (con clases)
            $oldControllerFile = __DIR__ . '/../../generated/controllers/' . ucfirst($entity) . 'Controller.php';
            
            if (file_exists($oldControllerFile)) {
                $this->loadOldStyleController($entity, $action, $params, $oldControllerFile);
                return;
            }
            
            die("❌ CRUD no encontrado para: <strong>$entity</strong><br><br>
                 No se encontró ni el controlador nuevo ni el antiguo.<br><br>
                 <strong>¿Qué hacer?</strong><br>
                 1. Ve a: <a href='" . BASE_PATH . "/generator/table-builder'>Crear nuevo CRUD</a><br>
                 2. O verifica los CRUDs existentes en: <a href='" . BASE_PATH . "/'>Inicio</a>");
        }

        // Cargar el archivo del controlador (contiene las funciones)
        require_once $controllerFile;

        // Construir el nombre de la función: action_entity
        $functionName = $action . '_' . $entity;

        if (!function_exists($functionName)) {
            die("❌ Función no encontrada: <strong>$functionName</strong><br><br>
                 El controlador existe pero no tiene la función solicitada.<br>
                 Archivo: <code>$controllerFile</code>");
        }

        // Llamar a la función con los parámetros
        call_user_func_array($functionName, $params);
    }

    private function loadOldStyleController(string $entity, string $action, array $params, string $controllerFile): void
    {
        // Cargar controlador antiguo (con clases)
        require_once $controllerFile;
        
        $className = 'Generated\\Controllers\\' . ucfirst($entity) . 'Controller';
        
        if (!class_exists($className)) {
            die("❌ Clase no encontrada: <strong>$className</strong>");
        }

        $controller = new $className();
        call_user_func_array([$controller, $action], $params);
    }
}