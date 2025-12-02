<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones de Exportación</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
    <style>
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
        .checkbox-item input[type="checkbox"]:checked + label {
            font-weight: 600;
            color: #667eea;
        }
        .checkbox-item input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .checkbox-item label {
            cursor: pointer;
            margin: 0;
            flex: 1;
        }
        .library-desc {
            font-size: 11px;
            color: #666;
            display: block;
            margin-top: 3px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
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
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>📦 Opciones de Exportación</h1>
            <p>Configurar proyecto para: <strong><?= ucfirst($entity) ?></strong></p>
        </header>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_PATH ?>/export/<?= $entity ?>/download">
            
            <!-- Configuración del Proyecto -->
            <div class="card">
                <h2>⚙️ Configuración del Proyecto</h2>
                
                <div class="form-group">
                    <label>Nombre del Proyecto:</label>
                    <input type="text" name="project_name" value="crud-<?= $entity ?>" required>
                    <small>Este será el nombre de la carpeta del proyecto</small>
                </div>

                <div class="form-group">
                    <label>Ruta Base (BASE_PATH):</label>
                    <input type="text" name="base_path" value="/crud-<?= $entity ?>" required>
                    <small>La ruta donde estará instalado (ej: /mi-proyecto)</small>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="include_base_files" value="1" checked>
                        Incluir archivos base (index.php, .htaccess, Router, etc.)
                    </label>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="include_styles" value="1" checked>
                        Incluir archivos de estilos CSS y JavaScript
                    </label>
                </div>
            </div>

            <!-- Framework de Estilos -->
            <div class="card">
                <h2>🎨 Framework de Estilos</h2>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" name="style_framework" value="custom" id="style_custom" checked>
                        <label for="style_custom">CSS Personalizado (Incluido)</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="style_framework" value="bootstrap5" id="style_bootstrap">
                        <label for="style_bootstrap">Bootstrap 5</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="style_framework" value="tailwind" id="style_tailwind">
                        <label for="style_tailwind">Tailwind CSS</label>
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
                <p class="text-muted">Selecciona las librerías que deseas incluir en composer.json</p>
                
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

            <!-- Botones -->
            <div class="card" style="text-align: center;">
                <button type="submit" class="btn btn-primary btn-lg">
                    📥 Descargar Proyecto Completo
                </button>
                <a href="<?= BASE_PATH ?>/" class="btn btn-secondary btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>