<?php
// Crear este archivo como: test_routes.php en la raíz del proyecto

require_once __DIR__ . '/vendor/autoload.php';

echo "<h1>🔍 Diagnóstico de Rutas</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .error{color:red;}</style>";

// 1. Verificar que las clases existen
echo "<h2>1. Verificar Clases</h2>";
$classes = [
    'App\Controllers\FullExportController',
    'App\Services\FullProjectExporter',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "<div class='ok'>✅ $class existe</div>";
    } else {
        echo "<div class='error'>❌ $class NO EXISTE</div>";
    }
}

// 2. Verificar archivos
echo "<h2>2. Verificar Archivos</h2>";
$files = [
    'src/Controllers/FullExportController.php',
    'src/Services/FullProjectExporter.php',
    'src/Views/generator/full_export_options.php',
];

foreach ($files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "<div class='ok'>✅ $file existe</div>";
    } else {
        echo "<div class='error'>❌ $file NO EXISTE</div>";
    }
}

// 3. Verificar rutas en index.php
echo "<h2>3. Contenido de index.php</h2>";
$indexContent = file_get_contents(__DIR__ . '/index.php');

if (strpos($indexContent, 'FullExportController') !== false) {
    echo "<div class='ok'>✅ FullExportController está en index.php</div>";
} else {
    echo "<div class='error'>❌ FullExportController NO está en index.php</div>";
}

if (strpos($indexContent, '/export/full-project') !== false) {
    echo "<div class='ok'>✅ Ruta /export/full-project está registrada</div>";
} else {
    echo "<div class='error'>❌ Ruta /export/full-project NO está registrada</div>";
}

// 4. Probar acceso directo
echo "<h2>4. Pruebas de Acceso</h2>";
echo "<p>Intenta acceder a estas URLs:</p>";
echo "<ul>";
echo "<li><a href='/crud-generator/export/full-project' target='_blank'>GET /export/full-project</a></li>";
echo "</ul>";

// 5. Mostrar rutas registradas en index.php
echo "<h2>5. Código en index.php (últimas líneas)</h2>";
$lines = explode("\n", $indexContent);
$lastLines = array_slice($lines, -30);
echo "<pre>" . htmlspecialchars(implode("\n", $lastLines)) . "</pre>";