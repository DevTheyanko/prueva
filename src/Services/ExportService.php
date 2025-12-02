<?php
namespace App\Services;

use App\Config\Database;
use ZipArchive;

class ExportService
{
    private $generatedPath;
    private $db;
    private $availableLibraries = [
        'tcpdf' => ['package' => 'tecnickcom/tcpdf', 'version' => '^6.6'],
        'phpmailer' => ['package' => 'phpmailer/phpmailer', 'version' => '^6.8'],
        'phpspreadsheet' => ['package' => 'phpoffice/phpspreadsheet', 'version' => '^1.29'],
        'guzzle' => ['package' => 'guzzlehttp/guzzle', 'version' => '^7.8'],
        'monolog' => ['package' => 'monolog/monolog', 'version' => '^3.5'],
        'dotenv' => ['package' => 'vlucas/phpdotenv', 'version' => '^5.6'],
        'intervention' => ['package' => 'intervention/image', 'version' => '^2.7'],
        'carbon' => ['package' => 'nesbot/carbon', 'version' => '^2.72'],
        'jwt' => ['package' => 'firebase/php-jwt', 'version' => '^6.9'],
        'faker' => ['package' => 'fakerphp/faker', 'version' => '^1.23'],
        'respect-validation' => ['package' => 'respect/validation', 'version' => '^2.3'],
        'mpdf' => ['package' => 'mpdf/mpdf', 'version' => '^8.2']
    ];

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->generatedPath = $config['app']['generated_path'];
        $this->db = Database::getInstance()->getConnection();
    }

    public function exportFullProject(string $entity, array $options): string
    {
        $className = ucfirst($entity);
        $projectName = $options['project_name'];
        $basePath = $options['base_path'];
        
        // Verificar que el CRUD existe (nuevo o antiguo formato)
        $controllerFile = $this->generatedPath . "controllers/{$entity}_controller.php";
        $oldControllerFile = $this->generatedPath . "controllers/{$className}Controller.php";
        
        if (!file_exists($controllerFile) && !file_exists($oldControllerFile)) {
            throw new \Exception("CRUD '$entity' no encontrado.");
        }

        // Obtener información de la tabla
        $modelFile = $this->generatedPath . "models/{$className}.php";
        $tableName = $this->getTableNameFromModel($modelFile);

        // Crear archivo ZIP temporal
        $zipFilename = sys_get_temp_dir() . '/' . $projectName . '_' . uniqid() . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("No se pudo crear el archivo ZIP");
        }

        // Agregar archivos del CRUD
        $this->addCrudFilesToSrc($zip, $entity, $className);

        // Agregar archivos base (Core)
        $this->addCoreFiles($zip);

        // Agregar estilos y JS si se solicita
        if ($options['include_styles']) {
            $this->addStyleFiles($zip, $options['style_framework']);
        }

        // Generar y agregar composer.json
        $composerContent = $this->generateComposerJson($projectName, $options['libraries']);
        $zip->addFromString('composer.json', $composerContent);

        // Agregar SQL
        $sqlContent = $this->generateSQL($tableName);
        $zip->addFromString('database.sql', $sqlContent);

        // Generar README
        $readmeContent = $this->generateFullReadme($entity, $tableName, $projectName, $options);
        $zip->addFromString('README.md', $readmeContent);

        // Agregar configuración
        $configContent = $this->generateConfig($basePath);
        $zip->addFromString('config/config.php', $configContent);

        // Agregar Database.php
        $databaseContent = $this->generateDatabase();
        $zip->addFromString('config/Database.php', $databaseContent);

        // Generar index.php
        $indexContent = $this->generateStandaloneIndexPhp($entity, $className, $basePath);
        $zip->addFromString('index.php', $indexContent);

        // Agregar .htaccess
        $htaccessContent = $this->generateHtaccess($basePath);
        $zip->addFromString('.htaccess', $htaccessContent);

        // Agregar .env.example si se incluye dotenv
        if (in_array('dotenv', $options['libraries'])) {
            $zip->addFromString('.env.example', $this->generateEnvExample());
        }

        // Agregar .gitignore
        $zip->addFromString('.gitignore', $this->generateGitignore());

        $zip->close();

        return $zipFilename;
    }

    private function addCrudFilesToSrc(ZipArchive $zip, string $entity, string $className): void
    {
        // Controlador funcional
        $controllerFile = $this->generatedPath . "controllers/{$entity}_controller.php";
        if (file_exists($controllerFile)) {
            $controllerContent = file_get_contents($controllerFile);
            
            // ACTUALIZAR: Cambiar namespace Generated\Models a src\Models
            $controllerContent = str_replace('use Generated\\Models\\', 'use src\\Models\\', $controllerContent);
            // Actualizar rutas en el controlador
            $controllerContent = str_replace("BASE_PATH . '/crud/$entity'", "BASE_PATH . '/$entity'", $controllerContent);
            
            $zip->addFromString("src/Controllers/{$entity}_controller.php", $controllerContent);
        }

        // Modelo - Actualizar namespace
        $modelFile = $this->generatedPath . "models/{$className}.php";
        if (file_exists($modelFile)) {
            $modelContent = file_get_contents($modelFile);
            
            // Cambiar namespace Generated\Models a src\Models
            $modelContent = str_replace('namespace Generated\\Models;', 'namespace src\\Models;', $modelContent);
            // Cambiar use de la interfaz
            $modelContent = str_replace('use Generated\\Interfaces\\', 'use src\\Interfaces\\', $modelContent);
            // Actualizar referencia a Database
            $modelContent = str_replace('use App\\Config\\Database;', 'use config\\Database;', $modelContent);
            
            $zip->addFromString("src/Models/{$className}.php", $modelContent);
        }

        // Interfaz - Actualizar namespace
        $interfaceFile = $this->generatedPath . "interfaces/{$className}RepositoryInterface.php";
        if (file_exists($interfaceFile)) {
            $interfaceContent = file_get_contents($interfaceFile);
            
            // Cambiar namespace Generated\Interfaces a src\Interfaces
            $interfaceContent = str_replace('namespace Generated\\Interfaces;', 'namespace src\\Interfaces;', $interfaceContent);
            
            $zip->addFromString("src/Interfaces/{$className}RepositoryInterface.php", $interfaceContent);
        }

        // Vistas
        $viewsPath = $this->generatedPath . "views/{$entity}/";
        if (is_dir($viewsPath)) {
            $views = ['index.php', 'create.php', 'edit.php'];
            foreach ($views as $view) {
                $viewFile = $viewsPath . $view;
                if (file_exists($viewFile)) {
                    $viewContent = file_get_contents($viewFile);
                    // Actualizar rutas: /crud/entity -> /entity
                    $viewContent = str_replace("BASE_PATH ?>/crud/$entity", "BASE_PATH ?>/$entity", $viewContent);
                    $zip->addFromString("src/Views/{$entity}/{$view}", $viewContent);
                }
            }
        }
    }

    private function addCoreFiles(ZipArchive $zip): void
    {
        // Router
        $routerContent = $this->generateRouter();
        $zip->addFromString('src/Core/Router.php', $routerContent);
        
        // View
        $viewContent = $this->generateView();
        $zip->addFromString('src/Core/View.php', $viewContent);
    }

    private function addStyleFiles(ZipArchive $zip, string $framework): void
    {
        if ($framework === 'custom') {
            $cssFile = __DIR__ . '/../../public/css/style.css';
            if (file_exists($cssFile)) {
                $zip->addFile($cssFile, 'public/css/style.css');
            }
            
            $jsFile = __DIR__ . '/../../public/js/app.js';
            if (file_exists($jsFile)) {
                $zip->addFile($jsFile, 'public/js/app.js');
            }
        } elseif ($framework === 'bootstrap5') {
            $zip->addFromString('public/css/bootstrap-info.txt', 
                'Incluye en tus vistas: <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
        }
    }

    private function generateStandaloneIndexPhp(string $entity, string $className, string $basePath): string
    {
        return <<<PHP
<?php
require_once __DIR__ . '/vendor/autoload.php';

use src\Core\Router;

session_start();

define('BASE_PATH', '$basePath');

// Cargar el controlador funcional
require_once __DIR__ . '/src/Controllers/{$entity}_controller.php';

\$router = new Router();

// Rutas del CRUD
\$router->add('GET', '/', function() { index_{$entity}(); });
\$router->add('GET', '/$entity', function() { index_{$entity}(); });
\$router->add('GET', '/$entity/create', function() { create_{$entity}(); });
\$router->add('POST', '/$entity/store', function() { store_{$entity}(); });
\$router->add('GET', '/$entity/edit/{id}', function(\$id) { edit_{$entity}((int)\$id); });
\$router->add('POST', '/$entity/update/{id}', function(\$id) { update_{$entity}((int)\$id); });
\$router->add('POST', '/$entity/delete/{id}', function(\$id) { delete_{$entity}((int)\$id); });

\$router->dispatch();
PHP;
    }

    private function generateComposerJson(string $projectName, array $libraries): string
    {
        $require = [
            'php' => '>=7.4',
            'ext-pdo' => '*',
            'ext-json' => '*'
        ];

        foreach ($libraries as $lib) {
            if (isset($this->availableLibraries[$lib])) {
                $libInfo = $this->availableLibraries[$lib];
                $require[$libInfo['package']] = $libInfo['version'];
            }
        }

        $composer = [
            'name' => strtolower(str_replace(' ', '-', $projectName)),
            'description' => 'Proyecto CRUD generado automáticamente',
            'type' => 'project',
            'require' => $require,
            'autoload' => [
                'psr-4' => [
                    'src\\' => 'src/',
                    'src\\Models\\' => 'src/Models/',
                    'src\\Interfaces\\' => 'src/Interfaces/',
                    'src\\Controllers\\' => 'src/Controllers/',
                    'src\\Core\\' => 'src/Core/',
                    'config\\' => 'config/'
                ]
            ]
        ];

        return json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function generateRouter(): string
    {
        return <<<'PHP'
<?php
namespace src\Core;

class Router
{
    private $routes = [];

    public function add(string $method, string $path, callable $handler): void
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
        
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $path = str_replace($basePath, '', $uri);
        $path = '/' . ltrim($path, '/');

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
    }
}
PHP;
    }

    private function generateView(): string
    {
        return <<<'PHP'
<?php
namespace src\Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Vista no encontrada: $view");
        }
    }
}
PHP;
    }

    private function generateDatabase(): string
    {
        return <<<'PHP'
<?php
namespace config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $config = require __DIR__ . '/config.php';
        $db = $config['db'];

        try {
            $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
            $this->connection = new PDO($dsn, $db['username'], $db['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
PHP;
    }

    private function generateConfig(string $basePath): string
    {
        return <<<PHP
<?php
return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'nombre_base_datos',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'name' => 'Mi Aplicación CRUD',
        'base_url' => 'http://localhost$basePath'
    ]
];
PHP;
    }

    private function generateHtaccess(string $basePath): string
    {
        $basePathClean = trim($basePath, '/');
        
        return <<<HTACCESS
RewriteEngine On
RewriteBase /$basePathClean/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
HTACCESS;
    }

    private function generateEnvExample(): string
    {
        return <<<ENV
DB_HOST=localhost
DB_NAME=nombre_base_datos
DB_USER=root
DB_PASS=

APP_NAME="Mi Aplicación CRUD"
APP_ENV=development
ENV;
    }

    private function generateGitignore(): string
    {
        return <<<GITIGNORE
/vendor/
.env
.DS_Store
*.log
GITIGNORE;
    }

    private function getTableNameFromModel(string $modelFile): string
    {
        if (!file_exists($modelFile)) {
            return '';
        }

        $content = file_get_contents($modelFile);
        if (preg_match("/private \\\$table = '([^']+)'/", $content, $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function generateSQL(string $tableName): string
    {
        if (empty($tableName)) {
            return "-- No se pudo obtener el nombre de la tabla\n";
        }

        try {
            $stmt = $this->db->query("SHOW CREATE TABLE `$tableName`");
            $row = $stmt->fetch();

            if ($row && isset($row['Create Table'])) {
                $sql = "-- Tabla: $tableName\n";
                $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
                $sql .= "DROP TABLE IF EXISTS `$tableName`;\n\n";
                $sql .= $row['Create Table'] . ";\n";
                return $sql;
            }

            return "-- No se pudo exportar la tabla\n";
        } catch (\Exception $e) {
            return "-- Error: " . $e->getMessage() . "\n";
        }
    }

    private function generateFullReadme(string $entity, string $tableName, string $projectName, array $options): string
    {
        return <<<README
# $projectName

Proyecto CRUD generado automáticamente con PHP.

## Instalación

1. Extraer archivos
2. `composer install`
3. Importar `database.sql`
4. Configurar `config/config.php`
5. Acceder a `http://localhost{$options['base_path']}/`

## Tabla: $tableName

CRUD para la entidad: $entity

Generado: ` . date('Y-m-d H:i:s') . `
README;
    }
}