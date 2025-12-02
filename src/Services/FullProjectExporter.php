<?php
namespace App\Services;

use App\Config\Database;
use ZipArchive;

class FullProjectExporter
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

    /**
     * Exportar proyecto completo con todos los CRUDs
     */
    public function exportFullProject(string $dbName, array $options): string
    {
        $projectName = $options['project_name'] ?? $dbName . '_project';
        $basePath = $options['base_path'] ?? '/' . $projectName;

        // Obtener todas las entidades (tablas con CRUDs generados)
        $entities = $this->getAllEntities();

        if (empty($entities)) {
            throw new \Exception("No hay CRUDs generados para exportar");
        }

        // Crear archivo ZIP
        $zipFilename = sys_get_temp_dir() . '/' . $projectName . '_' . uniqid() . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("No se pudo crear el archivo ZIP");
        }

        // Agregar todos los CRUDs
        foreach ($entities as $entity) {
            $this->addCrudFilesToZip($zip, $entity);
        }

        // Agregar archivos base del sistema
        $this->addCoreFiles($zip);

        // Agregar estilos
        if ($options['include_styles']) {
            $this->addStyleFiles($zip, $options['style_framework']);
        }

        // Generar composer.json
        $composerContent = $this->generateComposerJson($projectName, $options['libraries']);
        $zip->addFromString('composer.json', $composerContent);

        // Generar SQL de toda la base de datos
        $sqlContent = $this->generateFullDatabaseSQL($dbName);
        $zip->addFromString('database.sql', $sqlContent);

        // Generar index.php con todas las rutas
        $indexContent = $this->generateIndexPhp($entities, $basePath);
        $zip->addFromString('index.php', $indexContent);

        // Archivos de configuración
        $zip->addFromString('config/config.php', $this->generateConfig($basePath, $dbName));
        $zip->addFromString('config/Database.php', $this->generateDatabase());
        $zip->addFromString('.htaccess', $this->generateHtaccess($basePath));
        $zip->addFromString('.gitignore', $this->generateGitignore());
        $zip->addFromString('README.md', $this->generateReadme($projectName, $dbName, $entities, $options));

        if (in_array('dotenv', $options['libraries'])) {
            $zip->addFromString('.env.example', $this->generateEnvExample($dbName));
        }

        // Agregar página de inicio
        $zip->addFromString('src/Views/home.php', $this->generateHomePage($entities, $projectName));

        $zip->close();

        return $zipFilename;
    }

    /**
     * Obtener todas las entidades con CRUDs generados
     */
    private function getAllEntities(): array
    {
        $entities = [];
        $controllersPath = $this->generatedPath . 'controllers/';

        if (!is_dir($controllersPath)) {
            return $entities;
        }

        $files = scandir($controllersPath);
        foreach ($files as $file) {
            if (strpos($file, '_controller.php') !== false) {
                $entity = str_replace('_controller.php', '', $file);
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    /**
     * Agregar archivos de un CRUD al ZIP
     */
    private function addCrudFilesToZip(ZipArchive $zip, string $entity): void
    {
        $className = ucfirst($entity);

        // Controlador
        $controllerFile = $this->generatedPath . "controllers/{$entity}_controller.php";
        if (file_exists($controllerFile)) {
            $content = file_get_contents($controllerFile);
            $content = str_replace('use Generated\\Models\\', 'use src\\Models\\', $content);
            $content = str_replace("BASE_PATH . '/crud/$entity'", "BASE_PATH . '/$entity'", $content);
            $zip->addFromString("src/Controllers/{$entity}_controller.php", $content);
        }

        // Modelo
        $modelFile = $this->generatedPath . "models/{$className}.php";
        if (file_exists($modelFile)) {
            $content = file_get_contents($modelFile);
            $content = str_replace('namespace Generated\\Models;', 'namespace src\\Models;', $content);
            $content = str_replace('use Generated\\Interfaces\\', 'use src\\Interfaces\\', $content);
            $content = str_replace('use App\\Config\\Database;', 'use config\\Database;', $content);
            $zip->addFromString("src/Models/{$className}.php", $content);
        }

        // Interfaz
        $interfaceFile = $this->generatedPath . "interfaces/{$className}RepositoryInterface.php";
        if (file_exists($interfaceFile)) {
            $content = file_get_contents($interfaceFile);
            $content = str_replace('namespace Generated\\Interfaces;', 'namespace src\\Interfaces;', $content);
            $zip->addFromString("src/Interfaces/{$className}RepositoryInterface.php", $content);
        }

        // Vistas
        $viewsPath = $this->generatedPath . "views/{$entity}/";
        if (is_dir($viewsPath)) {
            $views = ['index.php', 'create.php', 'edit.php'];
            foreach ($views as $view) {
                $viewFile = $viewsPath . $view;
                if (file_exists($viewFile)) {
                    $content = file_get_contents($viewFile);
                    $content = str_replace("BASE_PATH ?>/crud/$entity", "BASE_PATH ?>/$entity", $content);
                    $zip->addFromString("src/Views/{$entity}/{$view}", $content);
                }
            }
        }
    }

    /**
     * Agregar archivos core del sistema
     */
    private function addCoreFiles(ZipArchive $zip): void
    {
        // Router
        $zip->addFromString('src/Core/Router.php', $this->generateRouter());
        
        // View
        $zip->addFromString('src/Core/View.php', $this->generateView());
    }

    /**
     * Agregar archivos de estilos
     */
    private function addStyleFiles(ZipArchive $zip, string $framework): void
    {
        if ($framework === 'custom') {
            $cssFile = __DIR__ . '/../../public/css/style.css';
            if (file_exists($cssFile)) {
                $zip->addFile($cssFile, 'public/css/style.css');
            }
        } elseif ($framework === 'bootstrap5') {
            $zip->addFromString('public/css/bootstrap-info.txt', 
                'Incluye Bootstrap 5 en tus vistas: <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
        }
    }

    /**
     * Generar index.php con todas las rutas
     */
    private function generateIndexPhp(array $entities, string $basePath): string
    {
        $requires = '';
        $routes = '';

        foreach ($entities as $entity) {
            $requires .= "require_once __DIR__ . '/src/Controllers/{$entity}_controller.php';\n";

            $routes .= <<<PHP

// Rutas para: $entity
\$router->add('GET', '/$entity', function() { index_{$entity}(); });
\$router->add('GET', '/$entity/create', function() { create_{$entity}(); });
\$router->add('POST', '/$entity/store', function() { store_{$entity}(); });
\$router->add('GET', '/$entity/edit/{id}', function(\$id) { edit_{$entity}((int)\$id); });
\$router->add('POST', '/$entity/update/{id}', function(\$id) { update_{$entity}((int)\$id); });
\$router->add('POST', '/$entity/delete/{id}', function(\$id) { delete_{$entity}((int)\$id); });

PHP;
        }

        return <<<PHP
<?php
require_once __DIR__ . '/vendor/autoload.php';

use src\Core\Router;
use src\Core\View;

session_start();

define('BASE_PATH', '$basePath');

// Cargar todos los controladores
$requires

\$router = new Router();

// Ruta principal
\$router->add('GET', '/', function() {
    View::render('home');
});

$routes

\$router->dispatch();
PHP;
    }

    /**
     * Generar SQL de toda la base de datos
     */
    private function generateFullDatabaseSQL(string $dbName): string
    {
        $sql = "-- Base de datos completa: $dbName\n";
        $sql .= "-- Generado: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Sistema: CRUD Generator\n\n";
        
        $sql .= "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
        $sql .= "USE `$dbName`;\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Obtener todas las tablas
        $stmt = $this->db->query("SHOW TABLES FROM `$dbName`");
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $stmt = $this->db->query("SHOW CREATE TABLE `$dbName`.`$table`");
            $row = $stmt->fetch();
            
            $sql .= "-- ============================================\n";
            $sql .= "-- Tabla: $table\n";
            $sql .= "-- ============================================\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $row['Create Table'] . ";\n\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    /**
     * Generar página de inicio
     */
    private function generateHomePage(array $entities, string $projectName): string
    {
        $links = '';
        foreach ($entities as $entity) {
            $entityTitle = ucfirst($entity);
            $links .= <<<HTML
                    <div class="crud-card">
                        <h3>📋 $entityTitle</h3>
                        <a href="<?= BASE_PATH ?>/$entity" class="btn btn-primary">Ver $entityTitle</a>
                    </div>

HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$projectName</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
    <style>
        .crud-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .crud-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }
        .crud-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }
        .crud-card h3 {
            margin-top: 0;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🚀 $projectName</h1>
            <p>Sistema de gestión completo</p>
        </header>

        <?php if (isset(\$_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= \$_SESSION['success']; unset(\$_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Módulos Disponibles</h2>
            <div class="crud-grid">
$links
            </div>
        </div>

        <div class="card">
            <h3>📚 Información del Proyecto</h3>
            <p><strong>Total de módulos:</strong> <?= count([
HTML;
        foreach ($entities as $entity) {
            $entityTitle = ucfirst($entity);
            return $entityTitle;
        }

        return $HTML . <<<'HTML'
]) ?></p>
            <p><strong>Generado con:</strong> CRUD Generator PHP</p>
            <p><strong>Tecnologías:</strong> PHP, MySQL, MVC, Composer</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Generar composer.json
     */
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
            'description' => 'Proyecto completo generado con CRUD Generator',
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

    private function generateConfig(string $basePath, string $dbName): string
    {
        return <<<PHP
<?php
return [
    'db' => [
        'host' => 'localhost',
        'dbname' => '$dbName',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'name' => 'Mi Aplicación',
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

    private function generateGitignore(): string
    {
        return <<<GITIGNORE
/vendor/
.env
.DS_Store
*.log
.idea/
.vscode/
GITIGNORE;
    }

    private function generateEnvExample(string $dbName): string
    {
        return <<<ENV
DB_HOST=localhost
DB_NAME=$dbName
DB_USER=root
DB_PASS=

APP_NAME="Mi Aplicación"
APP_ENV=development
ENV;
    }

    private function generateReadme(string $projectName, string $dbName, array $entities, array $options): string
    {
        $entitiesList = implode(', ', array_map('ucfirst', $entities));
        
        return <<<README
# $projectName

Proyecto completo generado automáticamente con **CRUD Generator PHP**.

## 📋 Características

- **Base de datos:** $dbName
- **Módulos:** $entitiesList
- **Arquitectura:** MVC con Composer
- **Total de CRUDs:** 
README . count($entities) . <<<'README'


## 🚀 Instalación

1. **Extraer archivos**
   ```bash
   unzip proyecto.zip
   cd proyecto
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar base de datos**
   - Importar `database.sql` en MySQL
   - Editar `config/config.php` con tus credenciales

4. **Configurar servidor web**
   - Apuntar a la carpeta del proyecto
   - Asegurar que mod_rewrite esté habilitado

5. **Acceder**
   ```
   http://localhost/tu-proyecto/
   ```

## 📁 Estructura del Proyecto

```
proyecto/
├── config/
│   ├── config.php          # Configuración general
│   └── Database.php        # Conexión a BD
├── src/
│   ├── Controllers/        # Controladores funcionales
│   ├── Models/             # Modelos con PDO
│   ├── Interfaces/         # Interfaces de repositorios
│   ├── Core/              # Router y View
│   └── Views/             # Vistas HTML/PHP
├── public/
│   └── css/               # Estilos
├── vendor/                # Dependencias Composer
├── index.php             # Punto de entrada
├── .htaccess            # Configuración Apache
├── composer.json        # Dependencias
└── database.sql        # Script de BD
```

## 🛠️ Tecnologías

- PHP >= 7.4
- MySQL / MariaDB
- Composer
- PDO
- MVC Architecture
- PSR-4 Autoloading

## 📝 Uso

Cada módulo tiene operaciones CRUD completas:
- Listar registros
- Crear nuevo
- Editar existente
- Eliminar

## 🔧 Personalización

Puedes modificar:
- Estilos en `public/css/`
- Vistas en `src/Views/`
- Lógica de negocio en modelos
- Rutas en `index.php`

## 📄 Licencia

Generado con CRUD Generator PHP
Fecha: 
README . date('Y-m-d H:i:s') . <<<'README'

README;
    }
}