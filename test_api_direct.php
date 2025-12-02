<?php
/**
 * PRUEBA DIRECTA DE LA API
 * Guarda este archivo como: test_api_direct.php
 * Ubicación: raíz del proyecto (mismo nivel que index.php)
 * Acceder a: http://localhost/crud-generator/test_api_direct.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\DatabaseGeneratorController;
use App\Services\DatabaseSchemaBuilder;
use App\Services\EnhancedCrudGenerator;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba Directa API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #667eea; margin-bottom: 20px; }
        .box { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .ok { border-left: 4px solid #28a745; background: #d4edda; }
        .error { border-left: 4px solid #dc3545; background: #f8d7da; }
        pre { background: #1e1e1e; color: #fff; padding: 15px; border-radius: 5px; overflow-x: auto; }
        button { background: #667eea; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #5568d3; }
        .method { display: inline-block; padding: 4px 8px; border-radius: 4px; font-weight: bold; margin-right: 10px; }
        .post { background: #28a745; color: white; }
        .get { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Prueba Directa de API</h1>
        
        <div class="box">
            <h2>📝 Explicación del Problema</h2>
            <p>La ruta <code>/api/generate-full-database</code> está configurada así:</p>
            <p><span class="method post">POST</span> <code>/api/generate-full-database</code></p>
            <p style="margin-top: 10px;">Cuando abres una URL directamente en el navegador, se hace una petición <span class="method get">GET</span>, por eso obtienes error 404.</p>
            <p style="margin-top: 10px;"><strong>Solución:</strong> Usar este archivo que hace la petición POST correctamente, o usar el constructor visual.</p>
        </div>

        <div class="box">
            <h2>🚀 Opciones de Prueba</h2>
            
            <h3 style="margin-top: 20px;">Opción 1: Llamar Directamente al Controlador</h3>
            <button onclick="testDirect()">🎯 Ejecutar Directamente (sin Router)</button>
            <p style="margin-top: 10px; color: #666; font-size: 14px;">Esto ejecuta el método del controlador sin pasar por el Router</p>

            <h3 style="margin-top: 20px;">Opción 2: Simular Petición POST</h3>
            <button onclick="testViaFetch()">📡 Hacer Petición POST via Fetch</button>
            <p style="margin-top: 10px; color: #666; font-size: 14px;">Esto simula una petición POST real desde JavaScript</p>

            <h3 style="margin-top: 20px;">Opción 3: Usar el Constructor Visual</h3>
            <a href="/crud-generator/generator/database-builder" style="display: inline-block; background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">🏗️ Ir al Constructor de BD</a>
            <p style="margin-top: 10px; color: #666; font-size: 14px;">La forma recomendada de usar el sistema</p>
        </div>

        <div id="result"></div>

        <?php
        // ============================================
        // PRUEBA DIRECTA DEL CONTROLADOR
        // ============================================
        if (isset($_GET['test_direct']) && $_GET['test_direct'] === '1') {
            echo '<div class="box">';
            echo '<h2>🔬 Resultado de Prueba Directa</h2>';
            
            try {
                // Crear esquema de prueba
                $testSchema = [
                    'database_name' => 'test_direct_' . time(),
                    'description' => 'Base de datos de prueba directa',
                    'insert_sample_data' => false,
                    'tables' => [
                        [
                            'id' => 'table_test_1',
                            'name' => 'test_items',
                            'timestamps' => true,
                            'soft_deletes' => false,
                            'fields' => [
                                [
                                    'name' => 'title',
                                    'type' => 'string',
                                    'length' => 200,
                                    'nullable' => false,
                                    'unsigned' => false
                                ],
                                [
                                    'name' => 'description',
                                    'type' => 'text',
                                    'length' => null,
                                    'nullable' => true,
                                    'unsigned' => false
                                ],
                                [
                                    'name' => 'price',
                                    'type' => 'decimal',
                                    'length' => '10,2',
                                    'nullable' => false,
                                    'unsigned' => true
                                ]
                            ]
                        ]
                    ],
                    'relationships' => []
                ];

                echo '<h3>📊 Esquema a Generar:</h3>';
                echo '<pre>' . json_encode($testSchema, JSON_PRETTY_PRINT) . '</pre>';

                // Simular el contenido del request
                $_SERVER['REQUEST_METHOD'] = 'POST';
                $_SERVER['CONTENT_TYPE'] = 'application/json';
                
                // Capturar el output
                ob_start();
                
                // Simular el input JSON
                $json = json_encode($testSchema);
                
                // Crear instancia del controlador
                $controller = new DatabaseGeneratorController();
                
                // Simular el método generateFullDatabase
                $schemaBuilder = new DatabaseSchemaBuilder();
                $crudGenerator = new EnhancedCrudGenerator();
                
                // Validar esquema
                $errors = $schemaBuilder->validateSchema($testSchema);
                
                if (!empty($errors)) {
                    echo '<div class="box error">';
                    echo '<h3>❌ Errores de Validación</h3>';
                    foreach ($errors as $error) {
                        echo '<p>• ' . htmlspecialchars($error) . '</p>';
                    }
                    echo '</div>';
                } else {
                    // Crear base de datos
                    $dbResult = $schemaBuilder->createFullDatabase($testSchema);
                    
                    if (!$dbResult['success']) {
                        echo '<div class="box error">';
                        echo '<h3>❌ Error al Crear Base de Datos</h3>';
                        echo '<p>' . htmlspecialchars($dbResult['error'] ?? 'Error desconocido') . '</p>';
                        echo '</div>';
                    } else {
                        // Generar CRUDs
                        $crudResult = $crudGenerator->generateAllCruds(
                            $testSchema['database_name'],
                            ['include_relations' => true, 'generate_api' => false]
                        );
                        
                        if (!$crudResult['success']) {
                            echo '<div class="box error">';
                            echo '<h3>❌ Error al Generar CRUDs</h3>';
                            echo '<p>' . htmlspecialchars($crudResult['error'] ?? 'Error desconocido') . '</p>';
                            echo '</div>';
                        } else {
                            echo '<div class="box ok">';
                            echo '<h3>✅ ¡Prueba Exitosa!</h3>';
                            echo '<p><strong>Base de datos:</strong> ' . htmlspecialchars($testSchema['database_name']) . '</p>';
                            echo '<p><strong>Tablas creadas:</strong> ' . count($testSchema['tables']) . '</p>';
                            echo '<h4>Resultados Detallados:</h4>';
                            echo '<pre>' . json_encode([
                                'database' => $dbResult['results'],
                                'cruds' => $crudResult['results']
                            ], JSON_PRETTY_PRINT) . '</pre>';
                            echo '</div>';
                        }
                    }
                }
                
                $output = ob_get_clean();
                echo $output;
                
            } catch (Exception $e) {
                echo '<div class="box error">';
                echo '<h3>❌ Error de Ejecución</h3>';
                echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p><strong>Archivo:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
                echo '<p><strong>Línea:</strong> ' . $e->getLine() . '</p>';
                echo '<h4>Stack Trace:</h4>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        ?>
    </div>

    <script>
        const BASE_PATH = '/crud-generator';

        function testDirect() {
            window.location.href = window.location.pathname + '?test_direct=1';
        }

        async function testViaFetch() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<div class="box">⏳ Enviando petición POST...</div>';

            const testSchema = {
                database_name: "test_fetch_" + Date.now(),
                description: "Base de datos de prueba via Fetch",
                insert_sample_data: false,
                tables: [
                    {
                        id: "table_test_1",
                        name: "test_products",
                        timestamps: true,
                        soft_deletes: false,
                        fields: [
                            {
                                name: "name",
                                type: "string",
                                length: 150,
                                nullable: false,
                                unsigned: false
                            },
                            {
                                name: "price",
                                type: "decimal",
                                length: "10,2",
                                nullable: false,
                                unsigned: true
                            },
                            {
                                name: "stock",
                                type: "integer",
                                length: null,
                                nullable: false,
                                unsigned: true
                            }
                        ]
                    }
                ],
                relationships: []
            };

            const url = window.location.origin + BASE_PATH + '/api/generate-full-database';
            
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

                console.log('Status:', response.status);
                
                const text = await response.text();
                console.log('Response:', text);

                if (response.status === 404) {
                    resultDiv.innerHTML = `
                        <div class="box error">
                            <h3>❌ Error 404 - Ruta No Encontrada</h3>
                            <p>La ruta <code>${url}</code> no existe.</p>
                            <p><strong>Esto NO debería pasar</strong> según el diagnóstico.</p>
                            <p>Revisa el archivo <code>index.php</code></p>
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
                            <pre>${text.substring(0, 1000)}</pre>
                        </div>
                    `;
                    return;
                }

                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="box ok">
                            <h3>✅ ¡Petición POST Exitosa!</h3>
                            <p><strong>Base de datos:</strong> ${testSchema.database_name}</p>
                            <p><strong>Tablas:</strong> ${data.tables_count || 0}</p>
                            <p><strong>Relaciones:</strong> ${data.relations_count || 0}</p>
                            <h4>Respuesta Completa:</h4>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="box error">
                            <h3>⚠️ Error en la Respuesta</h3>
                            <p><strong>Error:</strong> ${data.error || 'Error desconocido'}</p>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                }

            } catch (error) {
                console.error('Error:', error);
                resultDiv.innerHTML = `
                    <div class="box error">
                        <h3>❌ Error de Conexión</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }
    </script>
</body>
</html>