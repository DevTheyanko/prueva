<?php
namespace App\Core;

class Router
{
    private $routes = [];

    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover la ruta base del proyecto
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $path = str_replace($basePath, '', $uri);
        
        // Asegurar que siempre empiece con /
        $path = '/' . ltrim($path, '/');

        foreach ($this->routes as $route) {
            // Convertir {parametros} en expresiones regulares
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remover el match completo
                
                [$controller, $action] = $route['handler'];
                $controllerInstance = new $controller();
                
                call_user_func_array([$controllerInstance, $action], $matches);
                return;
            }
        }

        // Si no se encuentra la ruta
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>Ruta solicitada: <strong>$path</strong></p>";
        echo "<p>Método: <strong>$method</strong></p>";
        echo "<a href='" . BASE_PATH . "/'>Volver al inicio</a>";
    }
}