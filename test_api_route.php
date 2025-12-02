<?php
/**
 * ARCHIVO DE DIAGNÓSTICO - Guardar como: test_api_route.php
 * Ubicación: raíz del proyecto (mismo nivel que index.php)
 * Acceder a: http://localhost/crud-generator/test_api_route.php
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico de Rutas API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #667eea; margin-bottom: 20px; }
        .box { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .ok { border-left: 4px solid #28a745; background: #d4edda; }
        .error { border-left: 4px solid #dc3545; background: #f8d7da; }
        .warning { border-left: 4px solid #ffc107; background: #fff3cd; }
        .info { border-left: 4px solid #17a2b8; background: #d1ecf1; }
        pre { background: #1e1e1e; color: #fff; padding: 15px; border-radius: 5px; overflow-x: auto; margin: 10px 0; }
        button { background: #667eea; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; font-size: 14px; margin: 5px; }
        button:hover { background: #5568d3; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .status-item { padding: 10px; margin: 5px 0; border-radius: 5px; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .section-title { color: #667eea; margin: 15px 0 10px 0; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico de Rutas API - CRUD Generator</h1>

        <?php
        // ====================================
        // 1. VERIFICAR ARCHIVOS CRÍTICOS
        // ====================================
        echo '<div class="box">';
        echo '<h2 class="section-title">1️⃣ Archivos del Sistema</h2>';
        
        $files = [
            'vendor/autoload.php' => 'Composer Autoload',
            'index.php' => 'Archivo Principal',
            'config/Database.php' => 'Configuración BD',
            'src/Controllers/DatabaseGeneratorController.php' => 'Controlador Database',
            'src/Services/DatabaseSchemaBuilder.php' => 'Schema Builder',
            'src/Services/EnhancedCrudGenerator.php' => 'CRUD Generator',
            'src/Core/Router.php' => 'Router',
            '.htaccess' => 'Configuración Apache'
        ];

        $allFilesOk = true;
        foreach ($files as $file => $name) {
            $exists = file_exists(__DIR__ . '/' . $file);
            if (!$exists) $allFilesOk = false;
            $icon = $exists ? '✅' : '❌';
            $class = $exists ? 'ok' : 'error';
            echo "<div class='status-item $class'>$icon <strong>$name:</strong> <code>$file</code></div>";
        }
        echo '</div>';

        if (!$allFilesOk) {
            echo '<div class="box error">';
            echo '<strong>❌ ERROR CRÍTICO:</strong> Faltan archivos necesarios. No se puede continuar.';
            echo '</div>';
            exit;
        }

        // ====================================
        // 2. CARGAR AUTOLOAD Y VERIFICAR CLASES
        // ====================================
        require_once __DIR__ . '/vendor/autoload.php';

        echo '<div class="box">';
        echo '<h2 class="section-title">2️⃣ Clases PHP</h2>';
        
        $classes = [
            'App\\Controllers\\DatabaseGeneratorController' => 'Database Generator Controller',
            'App\\Services\\DatabaseSchemaBuilder' => 'Schema Builder Service',
            'App\\Services\\EnhancedCrudGenerator' => 'Enhanced CRUD Generator',
            'App\\Core\\Router' => 'Router Core',
            'App\\Config\\Database' => 'Database Config'
        ];

        $allClassesOk = true;
        foreach ($classes as $class => $name) {
            $exists = class_exists($class);
            if (!$exists) $allClassesOk = false;
            $icon = $exists ? '✅' : '❌';
            $classShort = basename(str_replace('\\', '/', $class));
            echo "<div class='status-item " . ($exists ? 'ok' : 'error') . "'>$icon <strong>$name:</strong> <code>$class</code></div>";
        }
        echo '</div>';

        if (!$allClassesOk) {
            echo '<div class="box error">';
            echo '<strong>❌ ERROR:</strong> Faltan clases. Ejecuta: <code>composer dump-autoload</code>';
            echo '</div>';
        }

        // ====================================
        // 3. VERIFICAR RUTAS EN INDEX.PHP
        // ====================================
        echo '<div class="box">';
        echo '<h2 class="section-title">3️⃣ Análisis de index.php</h2>';
        
        $indexContent = file_get_contents(__DIR__ . '/index.php');
        
        $routeChecks = [
            'use DatabaseGeneratorController' => strpos($indexContent, 'DatabaseGeneratorController') !== false,
            "Ruta POST /api/generate-full-database" => strpos($indexContent, '/api/generate-full-database') !== false,
            "Método 'generateFullDatabase'" => strpos($indexContent, 'generateFullDatabase') !== false,
            "Método add('POST'" => strpos($indexContent, "add('POST'") !== false
        ];

        $allRoutesOk = true;
        foreach ($routeChecks as $check => $found) {
            if (!$found) $allRoutesOk = false;
            $icon = $found ? '✅' : '❌';
            echo "<div class='status-item " . ($found ? 'ok' : 'error') . "'>$icon $check</div>";
        }

        // Mostrar fragmentos relevantes
        echo '<h3 style="margin-top: 20px;">Fragmento de index.php:</h3>';
        $lines = explode("\n", $indexContent);
        $relevant = [];
        foreach ($lines as $i => $line) {
            if (stripos($line, 'database') !== false || stripos($line, '/api/') !== false || stripos($line, 'use App') !== false) {
                $relevant[] = sprintf("%03d: %s", $i + 1, $line);
            }
        }
        if (!empty($relevant)) {
            echo '<pre>' . htmlspecialchars(implode("\n", array_slice($relevant, 0, 30))) . '</pre>';
        }
        echo '</div>';

        // ====================================
        // 4. VERIFICAR BASE DE DATOS
        // ====================================
        echo '<div class="box">';
        echo '<h2 class="section-title">4️⃣ Conexión a Base de Datos</h2>';
        try {
            $db = App\Config\Database::getInstance()->getConnection();
            echo "<div class='status-item ok'>✅ Conexión exitosa</div>";
            echo "<div class='status-item info'>📊 <strong>Driver:</strong> " . $db->getAttribute(PDO::ATTR_DRIVER_NAME) . "</div>";
            echo "<div class='status-item info'>📊 <strong>Versión:</strong> " . $db->getAttribute(PDO::ATTR_SERVER_VERSION) . "</div>";
        } catch (Exception $e) {
            echo "<div class='status-item error'>❌ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        echo '</div>';

        // ====================================
        // 5. VERIFICAR .HTACCESS
        // ====================================
        echo '<div class="box">';
        echo '<h2 class="section-title">5️⃣ Configuración .htaccess</h2>';
        if (file_exists(__DIR__ . '/.htaccess')) {
            $htaccess = file_get_contents(__DIR__ . '/.htaccess');
            echo "<div class='status-item ok'>✅ Archivo .htaccess existe</div>";
            echo '<h3>Contenido:</h3>';
            echo '<pre>' . htmlspecialchars($htaccess) . '</pre>';
            
            if (strpos($htaccess, 'RewriteEngine On') !== false) {
                echo "<div class='status-item ok'>✅ RewriteEngine está activo</div>";
            } else {
                echo "<div class='status-item error'>❌ RewriteEngine no está configurado</div>";
            }
        } else {
            echo "<div class='status-item error'>❌ Archivo .htaccess NO existe</div>";
        }
        echo '</div>';

        // ====================================
        // 6. INFORMACIÓN DEL SERVIDOR
        // ====================================
        echo '<div class="box info">';
        echo '<h2 class="section-title">6️⃣ Información del Servidor</h2>';
        echo "<div class='status-item info'>🔧 <strong>PHP Version:</strong> " . phpversion() . "</div>";
        echo "<div class='status-item info'>📁 <strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</div>";
        echo "<div class='status-item info'>📄 <strong>Script:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</div>";
        echo "<div class='status-item info'>🌐 <strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</div>";
        echo "<div class='status-item info'>⚙️ <strong>mod_rewrite:</strong> " . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? 'Activo' : 'Desconocido') . "</div>";
        echo '</div>';
        ?>

        <!-- ====================================
             7. PRUEBA INTERACTIVA
             ==================================== -->
        <div class="box">
            <h2 class="section-title">7️⃣ Prueba de la Ruta API</h2>
            <p>Haz clic en el botón para probar la ruta <code>/api/generate-full-database</code></p>
            <button onclick="testAPIRoute()" id="testBtn">🧪 Probar Ruta API</button>
            <button onclick="testManualURL()" id="manualBtn">🔗 Abrir URL Directamente</button>
            <div id="testResult" style="margin-top: 20px;"></div>
        </div>

        <!-- ====================================
             8. SOLUCIONES
             ==================================== -->
        <div class="box warning">
            <h2 class="section-title">🔧 Soluciones al Error 404</h2>
            <ol style="line-height: 1.8;">
                <li><strong>Verificar index.php:</strong> Asegúrate de que contenga:
                    <pre style="font-size: 12px;">use App\Controllers\DatabaseGeneratorController;

$router->add('POST', '/api/generate-full-database', 
    [DatabaseGeneratorController::class, 'generateFullDatabase']);</pre>
                </li>
                <li><strong>Ejecutar en terminal:</strong>
                    <pre>composer dump-autoload</pre>
                </li>
                <li><strong>Limpiar caché del navegador:</strong> Ctrl + Shift + R</li>
                <li><strong>Verificar .htaccess:</strong> Debe tener RewriteEngine On y RewriteBase configurado</li>
                <li><strong>Revisar logs:</strong> Ver errores en error_log de PHP/Apache</li>
            </ol>
        </div>

    </div>

    <script>
        const BASE_PATH = '/crud-generator';

        async function testAPIRoute() {
            const btn = document.getElementById('testBtn');
            const resultDiv = document.getElementById('testResult');
            
            btn.disabled = true;
            btn.textContent = '⏳ Probando...';
            
            resultDiv.innerHTML = '<div class="box info">⏳ Enviando petición a la API...</div>';
            
            const testSchema = {
                database_name: "test_diagnostic_" + Date.now(),
                description: "Base de datos de prueba para diagnóstico",
                insert_sample_data: false,
                tables: [
                    {
                        id: "table_test_1",
                        name: "test_users",
                        timestamps: true,
                        soft_deletes: false,
                        fields: [
                            {
                                name: "name",
                                type: "string",
                                length: 100,
                                nullable: false,
                                unsigned: false
                            },
                            {
                                name: "email",
                                type: "string",
                                length: 150,
                                nullable: false,
                                unsigned: false
                            }
                        ]
                    }
                ],
                relationships: []
            };
            
            const url = window.location.origin + BASE_PATH + '/api/generate-full-database';
            
            console.log('=== TEST API ROUTE ===');
            console.log('URL:', url);
            console.log('Schema:', testSchema);
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(testSchema)
                });
                
                console.log('Response Status:', response.status);
                console.log('Response Headers:', [...response.headers.entries()]);
                
                const text = await response.text();
                console.log('Response Text:', text);
                
                if (response.status === 404) {
                    resultDiv.innerHTML = `
                        <div class="box error">
                            <h3>❌ Error 404 - Ruta No Encontrada</h3>
                            <p><strong>URL intentada:</strong> <code>${url}</code></p>
                            <p><strong>Esto significa que:</strong></p>
                            <ul>
                                <li>La ruta <code>/api/generate-full-database</code> NO está registrada en <code>index.php</code></li>
                                <li>O el archivo <code>index.php</code> no está procesando correctamente las rutas</li>
                                <li>O hay un problema con el <code>.htaccess</code></li>
                            </ul>
                            <p><strong>Solución:</strong></p>
                            <ol>
                                <li>Abre <code>index.php</code></li>
                                <li>Busca la sección de rutas de API</li>
                                <li>Asegúrate de que exista esta línea ANTES de las rutas /crud:</li>
                            </ol>
                            <pre>$router->add('POST', '/api/generate-full-database', [DatabaseGeneratorController::class, 'generateFullDatabase']);</pre>
                        </div>
                    `;
                    return;
                }
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    resultDiv.innerHTML = `
                        <div class="box error">
                            <h3>❌ Respuesta No es JSON</h3>
                            <p><strong>Status:</strong> ${response.status}</p>
                            <p><strong>La respuesta no es JSON válido. Contenido:</strong></p>
                            <pre>${text.substring(0, 1000)}</pre>
                        </div>
                    `;
                    return;
                }
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="box ok">
                            <h3>✅ ¡Ruta Funcionando Correctamente!</h3>
                            <p><strong>Base de datos creada:</strong> ${testSchema.database_name}</p>
                            <p><strong>Tablas generadas:</strong> ${data.tables_count || 0}</p>
                            <p><strong>Relaciones:</strong> ${data.relations_count || 0}</p>
                            <p><strong>Respuesta completa:</strong></p>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="box warning">
                            <h3>⚠️ La Ruta Responde Pero Hay un Error</h3>
                            <p><strong>Error:</strong> ${data.error || 'Error desconocido'}</p>
                            <p><strong>Respuesta completa:</strong></p>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                }
                
            } catch (error) {
                console.error('Error:', error);
                resultDiv.innerHTML = `
                    <div class="box error">
                        <h3>❌ Error de Conexión</h3>
                        <p><strong>Error:</strong> ${error.message}</p>
                        <p><strong>Posibles causas:</strong></p>
                        <ul>
                            <li>La ruta no existe (404)</li>
                            <li>Error de red</li>
                            <li>El servidor no responde</li>
                            <li>CORS bloqueado</li>
                        </ul>
                    </div>
                `;
            } finally {
                btn.disabled = false;
                btn.textContent = '🧪 Probar Ruta API';
            }
        }

        function testManualURL() {
            const url = window.location.origin + BASE_PATH + '/api/generate-full-database';
            alert('Se abrirá la URL en una nueva pestaña. Deberías ver un error JSON o un mensaje de la API.\n\nURL: ' + url);
            window.open(url, '_blank');
        }

        // Auto-ejecutar prueba al cargar
        console.log('Diagnóstico cargado. BASE_PATH:', BASE_PATH);
    </script>
</body>
</html