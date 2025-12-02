<?php
// Crear este archivo como: src/Views/generator/diagnostic.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico del Sistema</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
    <style>
        .diagnostic-box {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .success { border-color: #28a745; background: #d4edda; }
        .error { border-color: #dc3545; background: #f8d7da; }
        .warning { border-color: #ffc107; background: #fff3cd; }
        pre { background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .test-btn { margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🔍 Diagnóstico del Sistema</h1>
            <a href="<?= BASE_PATH ?>/" class="btn btn-secondary">← Volver</a>
        </header>

        <!-- TEST 1: Conexión a Base de Datos -->
        <div class="card">
            <h2>1️⃣ Conexión a Base de Datos</h2>
            <?php
            try {
                $db = \App\Config\Database::getInstance()->getConnection();
                echo '<div class="diagnostic-box success">';
                echo '✅ <strong>Conexión exitosa</strong><br>';
                echo 'Driver: ' . $db->getAttribute(PDO::ATTR_DRIVER_NAME) . '<br>';
                echo 'Versión: ' . $db->getAttribute(PDO::ATTR_SERVER_VERSION);
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="diagnostic-box error">';
                echo '❌ <strong>Error de conexión:</strong><br>';
                echo htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            ?>
        </div>

        <!-- TEST 2: Permisos de Escritura -->
        <div class="card">
            <h2>2️⃣ Permisos de Escritura</h2>
            <?php
            $paths = [
                'generated/controllers' => __DIR__ . '/../../../generated/controllers',
                'generated/models' => __DIR__ . '/../../../generated/models',
                'generated/views' => __DIR__ . '/../../../generated/views',
                'generated/interfaces' => __DIR__ . '/../../../generated/interfaces',
                'exports' => __DIR__ . '/../../../exports'
            ];

            foreach ($paths as $name => $path) {
                if (!is_dir($path)) {
                    @mkdir($path, 0777, true);
                }
                
                $writable = is_writable($path);
                $class = $writable ? 'success' : 'error';
                $icon = $writable ? '✅' : '❌';
                
                echo "<div class='diagnostic-box $class'>";
                echo "$icon <strong>$name:</strong> ";
                echo $writable ? 'Escribible' : 'No escribible';
                echo "<br><small>$path</small>";
                echo "</div>";
            }
            ?>
        </div>

        <!-- TEST 3: Clases Requeridas -->
        <div class="card">
            <h2>3️⃣ Clases Requeridas</h2>
            <?php
            $classes = [
                'App\Config\Database',
                'App\Services\DatabaseSchemaBuilder',
                'App\Services\EnhancedCrudGenerator',
                'App\Controllers\DatabaseGeneratorController'
            ];

            foreach ($classes as $class) {
                $exists = class_exists($class);
                $icon = $exists ? '✅' : '❌';
                $status = $exists ? 'Existe' : 'No encontrada';
                $boxClass = $exists ? 'success' : 'error';
                
                echo "<div class='diagnostic-box $boxClass'>";
                echo "$icon <strong>$class:</strong> $status";
                echo "</div>";
            }
            ?>
        </div>

        <!-- TEST 4: Rutas Registradas -->
        <div class="card">
            <h2>4️⃣ Rutas Registradas</h2>
            <div class="diagnostic-box">
                <p>Verifica que estas rutas estén en <code>index.php</code>:</p>
                <pre>
// Debe estar en index.php:
$router->add('GET', '/generator/database-builder', [DatabaseGeneratorController::class, 'showBuilder']);
$router->add('POST', '/api/generate-full-database', [DatabaseGeneratorController::class, 'generateFullDatabase']);
                </pre>
            </div>
        </div>

        <!-- TEST 5: Test de Creación de Tabla -->
        <div class="card">
            <h2>5️⃣ Test de Creación de Tabla</h2>
            <button class="btn btn-primary test-btn" onclick="testCreateTable()">🧪 Probar Crear Tabla de Prueba</button>
            <div id="testResult"></div>
        </div>

        <!-- TEST 6: Test de API -->
        <div class="card">
            <h2>6️⃣ Test de API</h2>
            <button class="btn btn-primary test-btn" onclick="testAPI()">🧪 Probar Endpoint de API</button>
            <div id="apiTestResult"></div>
        </div>

        <!-- TEST 7: Información de PHP -->
        <div class="card">
            <h2>7️⃣ Configuración PHP</h2>
            <div class="diagnostic-box">
                <strong>PHP Version:</strong> <?= phpversion() ?><br>
                <strong>PDO MySQL:</strong> <?= extension_loaded('pdo_mysql') ? '✅ Instalado' : '❌ No instalado' ?><br>
                <strong>JSON:</strong> <?= extension_loaded('json') ? '✅ Instalado' : '❌ No instalado' ?><br>
                <strong>Memory Limit:</strong> <?= ini_get('memory_limit') ?><br>
                <strong>Max Execution Time:</strong> <?= ini_get('max_execution_time') ?>s<br>
            </div>
        </div>

        <!-- TEST 8: Ver Errores de PHP -->
        <div class="card">
            <h2>8️⃣ Log de Errores</h2>
            <div class="diagnostic-box warning">
                <p><strong>Revisa errores en:</strong></p>
                <ul>
                    <li>Error Log PHP: <?= ini_get('error_log') ?: 'No configurado' ?></li>
                    <li>Apache/Nginx Error Log</li>
                    <li>Consola del navegador (F12)</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const BASE_PATH = '<?= BASE_PATH ?>';

        function testCreateTable() {
            const resultDiv = document.getElementById('testResult');
            resultDiv.innerHTML = '<div class="diagnostic-box">⏳ Probando...</div>';

            const testSchema = {
                database_name: 'test_diagnostic_db',
                description: 'Base de datos de prueba',
                insert_sample_data: false,
                tables: [
                    {
                        id: 'table_test',
                        name: 'test_table',
                        timestamps: true,
                        soft_deletes: false,
                        fields: [
                            {
                                name: 'nombre',
                                type: 'string',
                                length: 100,
                                nullable: false,
                                unsigned: false
                            },
                            {
                                name: 'edad',
                                type: 'integer',
                                length: null,
                                nullable: true,
                                unsigned: true
                            }
                        ]
                    }
                ],
                relationships: []
            };

            fetch(BASE_PATH + '/api/generate-full-database', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(testSchema)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="diagnostic-box success">
                            ✅ <strong>¡Prueba exitosa!</strong><br>
                            Base de datos creada: test_diagnostic_db<br>
                            Tablas: ${data.tables_count}<br>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="diagnostic-box error">
                            ❌ <strong>Error en la prueba:</strong><br>
                            ${data.error}<br>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="diagnostic-box error">
                        ❌ <strong>Error de conexión:</strong><br>
                        ${error.message}<br>
                        <strong>Posibles causas:</strong>
                        <ul>
                            <li>La ruta /api/generate-full-database no está registrada</li>
                            <li>El servidor no responde</li>
                            <li>Hay un error en el controlador</li>
                        </ul>
                    </div>
                `;
                console.error('Error completo:', error);
            });
        }

        function testAPI() {
            const resultDiv = document.getElementById('apiTestResult');
            resultDiv.innerHTML = '<div class="diagnostic-box">⏳ Probando endpoint...</div>';

            fetch(BASE_PATH + '/api/generate-full-database', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                return response.text().then(text => {
                    try {
                        const data = JSON.parse(text);
                        resultDiv.innerHTML = `
                            <div class="diagnostic-box ${data.success ? 'success' : 'warning'}">
                                ${data.success ? '✅' : '⚠️'} <strong>API responde:</strong><br>
                                <pre>${JSON.stringify(data, null, 2)}</pre>
                            </div>
                        `;
                    } catch (e) {
                        resultDiv.innerHTML = `
                            <div class="diagnostic-box error">
                                ❌ <strong>Respuesta no es JSON válido:</strong><br>
                                <pre>${text}</pre>
                            </div>
                        `;
                    }
                });
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="diagnostic-box error">
                        ❌ <strong>Error de conexión:</strong><br>
                        ${error.message}<br>
                        La ruta probablemente no existe.
                    </div>
                `;
            });
        }

        // Auto-ejecutar pruebas al cargar
        console.log('Página de diagnóstico cargada');
        console.log('BASE_PATH:', BASE_PATH);
    </script>
</body>
</html>