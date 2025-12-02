<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportar Proyecto Completo</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
    <style>
        .highlight-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        .highlight-box h2 {
            margin: 0 0 10px 0;
            font-size: 2em;
        }
        .highlight-box p {
            margin: 0;
            opacity: 0.9;
        }
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 2px solid transparent;
            transition: all 0.3s;
        }
        .checkbox-item:hover {
            background: #e9ecef;
        }
        .checkbox-item input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        .radio-item {
            display: flex;
            align-items: center;
        }
        .radio-item input {
            margin-right: 8px;
        }
        .library-desc {
            font-size: 11px;
            color: #666;
            display: block;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>📦 Exportar Proyecto Completo</h1>
            <a href="<?= BASE_PATH ?>/" class="btn btn-secondary">← Volver</a>
        </header>

        <div class="highlight-box">
            <h2>🚀 Exportación Total</h2>
            <p>Descarga un proyecto completo con TODOS los CRUDs de una base de datos</p>
            <p style="margin-top: 10px;">✅ Listo para usar • ✅ Con composer.json • ✅ Documentación incluida</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_PATH ?>/export/full-project/download">
            
            <!-- Selección de Base de Datos -->
            <div class="card">
                <h2>🗄️ Seleccionar Base de Datos</h2>
                
                <div class="form-group">
                    <label>Base de Datos a Exportar:</label>
                    <select name="database_name" id="database_name" required onchange="updateProjectName()">
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($databases as $db): ?>
                            <option value="<?= htmlspecialchars($db) ?>"><?= htmlspecialchars($db) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small>Todas las tablas con CRUDs serán exportadas</small>
                </div>
            </div>

            <!-- Configuración del Proyecto -->
            <div class="card">
                <h2>⚙️ Configuración del Proyecto</h2>
                
                <div class="form-group">
                    <label>Nombre del Proyecto:</label>
                    <input type="text" name="project_name" id="project_name" placeholder="mi_proyecto" required>
                    <small>Nombre de la carpeta del proyecto exportado</small>
                </div>

                <div class="form-group">
                    <label>Ruta Base (BASE_PATH):</label>
                    <input type="text" name="base_path" id="base_path" placeholder="/mi_proyecto" required>
                    <small>La ruta donde estará instalado el proyecto</small>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="include_styles" value="1" checked>
                        Incluir archivos de estilos CSS
                    </label>
                </div>
            </div>

            <!-- Framework de Estilos -->
            <div class="card">
                <h2>🎨 Framework de Estilos</h2>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" name="style_framework" value="custom" id="style_custom" checked>
                        <label for="style_custom">CSS Personalizado</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="style_framework" value="bootstrap5" id="style_bootstrap">
                        <label for="style_bootstrap">Bootstrap 5</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="style_framework" value="none" id="style_none">
                        <label for="style_none">Sin estilos</label>
                    </div>
                </div>
            </div>

            <!-- Librerías de Composer -->
            <div class="card">
                <h2>📚 Librerías de Composer</h2>
                <p class="text-muted">Selecciona las librerías que deseas incluir</p>
                
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="tcpdf" id="lib_tcpdf">
                        <label for="lib_tcpdf">
                            TCPDF
                            <span class="library-desc">Generación de PDFs</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="phpmailer" id="lib_phpmailer">
                        <label for="lib_phpmailer">
                            PHPMailer
                            <span class="library-desc">Envío de correos</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="phpspreadsheet" id="lib_phpspreadsheet">
                        <label for="lib_phpspreadsheet">
                            PhpSpreadsheet
                            <span class="library-desc">Excel/CSV</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="guzzle" id="lib_guzzle">
                        <label for="lib_guzzle">
                            Guzzle
                            <span class="library-desc">Cliente HTTP</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="monolog" id="lib_monolog">
                        <label for="lib_monolog">
                            Monolog
                            <span class="library-desc">Sistema de logs</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="dotenv" id="lib_dotenv">
                        <label for="lib_dotenv">
                            PHP dotenv
                            <span class="library-desc">Variables de entorno</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="intervention" id="lib_intervention">
                        <label for="lib_intervention">
                            Intervention Image
                            <span class="library-desc">Manipulación de imágenes</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="carbon" id="lib_carbon">
                        <label for="lib_carbon">
                            Carbon
                            <span class="library-desc">Manejo de fechas</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="jwt" id="lib_jwt">
                        <label for="lib_jwt">
                            Firebase JWT
                            <span class="library-desc">Autenticación JWT</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="faker" id="lib_faker">
                        <label for="lib_faker">
                            Faker
                            <span class="library-desc">Datos de prueba</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="respect-validation" id="lib_validation">
                        <label for="lib_validation">
                            Respect Validation
                            <span class="library-desc">Validación de datos</span>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="libraries[]" value="mpdf" id="lib_mpdf">
                        <label for="lib_mpdf">
                            mPDF
                            <span class="library-desc">Generación de PDFs</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botón de Exportación -->
            <div class="card" style="text-align: center;">
                <button type="submit" class="btn btn-primary btn-lg" style="font-size: 1.2em; padding: 15px 40px;">
                    📥 Descargar Proyecto Completo
                </button>
                <p style="margin-top: 15px; color: #666;">
                    Se incluirán todos los CRUDs, configuración, base de datos SQL y documentación
                </p>
            </div>
        </form>
    </div>

    <script>
        // Agrega este script al final de full_export_options.php para debug

console.log('=== DEBUG EXPORTACIÓN ===');
console.log('BASE_PATH:', '<?= BASE_PATH ?>');
console.log('Formulario encontrado:', document.querySelector('form'));

document.querySelector('form').addEventListener('submit', function(e) {
    console.log('=== FORMULARIO ENVIADO ===');
    
    const dbName = document.getElementById('database_name').value;
    const projectName = document.getElementById('project_name').value;
    const basePath = document.getElementById('base_path').value;
    
    console.log('Base de datos:', dbName);
    console.log('Nombre proyecto:', projectName);
    console.log('Base path:', basePath);
    
    if (!dbName) {
        e.preventDefault();
        alert('❌ Debes seleccionar una base de datos');
        console.error('ERROR: No se seleccionó base de datos');
        return false;
    }
    
    if (!projectName) {
        e.preventDefault();
        alert('❌ Debes ingresar un nombre de proyecto');
        console.error('ERROR: No hay nombre de proyecto');
        return false;
    }
    
    console.log('✅ Validación OK, enviando formulario...');
    console.log('Action:', this.action);
    console.log('Method:', this.method);
});
        function updateProjectName() {
            const dbSelect = document.getElementById('database_name');
            const projectInput = document.getElementById('project_name');
            const basePathInput = document.getElementById('base_path');
            
            if (dbSelect.value && !projectInput.value) {
                const projectName = dbSelect.value + '_project';
                projectInput.value = projectName;
                basePathInput.value = '/' + projectName;
            }
        }

        // Auto-actualizar cuando se cambie el nombre del proyecto
        document.getElementById('project_name').addEventListener('input', function() {
            const basePath = document.getElementById('base_path');
            if (this.value) {
                basePath.value = '/' + this.value;
            }
        });
        
    </script>
</body>
</html>