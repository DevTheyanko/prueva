<?php
namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);
        
        // ORDEN DE BÚSQUEDA:
        // 1. Buscar en src/Views/ (vistas del sistema)
        $systemViewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        // 2. Buscar en generated/views/ (vistas generadas por CRUDs)
        $generatedViewPath = __DIR__ . '/../../generated/views/' . $view . '.php';
        
        if (file_exists($systemViewPath)) {
            require_once $systemViewPath;
        } elseif (file_exists($generatedViewPath)) {
            require_once $generatedViewPath;
        } else {
            // Mostrar error detallado
            $searchPaths = [
                'Sistema' => $systemViewPath,
                'Generado' => $generatedViewPath
            ];
            
            $errorMsg = "<h1>❌ Vista no encontrada: <code>$view</code></h1>";
            $errorMsg .= "<h3>Rutas buscadas:</h3><ul>";
            
            foreach ($searchPaths as $type => $path) {
                $exists = file_exists($path) ? '✅ EXISTE' : '❌ NO EXISTE';
                $errorMsg .= "<li><strong>$type:</strong> $exists<br><code>$path</code></li>";
            }
            
            $errorMsg .= "</ul>";
            $errorMsg .= "<h3>💡 Solución:</h3>";
            $errorMsg .= "<p>Crea el archivo en: <code>$systemViewPath</code></p>";
            
            die($errorMsg);
        }
    }
}