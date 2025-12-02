<?php
echo "<h1>Verificación de Instalación CRUD Generator</h1>";

$checks = [
    'Composer Autoload' => file_exists(__DIR__ . '/vendor/autoload.php'),
    'Config Database' => file_exists(__DIR__ . '/config/Database.php'),
    'Config config.php' => file_exists(__DIR__ . '/config/config.php'),
    'Core Router' => file_exists(__DIR__ . '/src/Core/Router.php'),
    'Core View' => file_exists(__DIR__ . '/src/Core/View.php'),
    'Controllers GeneratorController' => file_exists(__DIR__ . '/src/Controllers/GeneratorController.php'),
    'Controllers CrudController' => file_exists(__DIR__ . '/src/Controllers/CrudController.php'),
    'Services DatabaseGenerator' => file_exists(__DIR__ . '/src/Services/DatabaseGenerator.php'),
    'Services CrudGenerator' => file_exists(__DIR__ . '/src/Services/CrudGenerator.php'),
    'Models Model' => file_exists(__DIR__ . '/src/Models/Model.php'),
    'Views generator/index' => file_exists(__DIR__ . '/src/Views/generator/index.php'),
    'Views generator/table_builder' => file_exists(__DIR__ . '/src/Views/generator/table_builder.php'),
    'Public CSS' => file_exists(__DIR__ . '/public/css/style.css'),
    'Public JS' => file_exists(__DIR__ . '/public/js/app.js'),
    'Generated controllers/' => is_dir(__DIR__ . '/generated/controllers'),
    'Generated models/' => is_dir(__DIR__ . '/generated/models'),
    'Generated views/' => is_dir(__DIR__ . '/generated/views'),
    '.htaccess' => file_exists(__DIR__ . '/.htaccess'),
];

echo "<ul style='list-style: none; font-family: monospace;'>";
foreach ($checks as $name => $status) {
    $icon = $status ? '✅' : '❌';
    echo "<li>$icon $name</li>";
}
echo "</ul>";

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    echo "<h2>Clases Cargadas por PSR-4:</h2>";
    echo "<ul style='font-family: monospace;'>";
    
    $classes = [
        'App\\Config\\Database',
        'App\\Core\\Router',
        'App\\Core\\View',
        'App\\Controllers\\GeneratorController',
        'App\\Controllers\\CrudController',
        'App\\Services\\DatabaseGenerator',
        'App\\Services\\CrudGenerator',
        'App\\Models\\Model',
    ];
    
    foreach ($classes as $class) {
        $exists = class_exists($class);
        $icon = $exists ? '✅' : '❌';
        echo "<li>$icon $class</li>";
    }
    echo "</ul>";
} else {
    echo "<h2 style='color: red;'>❌ Composer no instalado. Ejecuta: composer install</h2>";
}

echo "<hr><p>Si todo está en ✅, accede a: <a href='index.php'>index.php</a></p>";
?>