<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constructor de Base de Datos</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/css/style.css">
    <style>
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .tab {
            padding: 12px 24px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
            font-weight: 500;
            color: #64748b;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .tab:hover {
            color: #667eea;
            background: #f1f5f9;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .table-card {
            background: #f8f9fa;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .table-card h3 {
            margin-top: 0;
            color: #667eea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .field-row {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr 0.8fr 0.8fr 80px;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .field-row input,
        .field-row select {
            padding: 8px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
        }
        
        .relation-card {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .relation-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 100px 100px 80px;
            gap: 10px;
            align-items: center;
        }
        
        .btn-remove {
            background: #f56565;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-remove:hover {
            background: #c53030;
        }
        
        .json-preview {
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            max-height: 500px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        
        .examples-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .example-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .example-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }
        
        .example-card h4 {
            color: #667eea;
            margin-top: 0;
        }
        
        .label-small {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
        }
        
        .checkbox-inline {
            display: flex;
            gap: 15px;
            margin: 10px 0;
        }
        
        .checkbox-inline label {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🏗️ Constructor Avanzado de Base de Datos</h1>
            <a href="<?= BASE_PATH ?>/" class="btn btn-secondary">← Volver</a>
        </header>

        <div class="card">
            <div class="tabs">
                <button class="tab active" data-tab="config">⚙️ Configuración</button>
                <button class="tab" data-tab="tables">📋 Tablas</button>
                <button class="tab" data-tab="relations">🔗 Relaciones</button>
                <button class="tab" data-tab="preview">👁️ Vista Previa</button>
                <button class="tab" data-tab="examples">📚 Ejemplos</button>
            </div>

            <!-- TAB 1: Configuración -->
            <div class="tab-content active" id="config">
                <h2>Configuración General</h2>
                
                <div class="form-group">
                    <label>Nombre de la Base de Datos:</label>
                    <input type="text" id="dbName" placeholder="mi_base_datos" required>
                    <small>Ejemplo: blog_system, ecommerce_db, school_management</small>
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea id="dbDescription" rows="3" placeholder="Describe brevemente el propósito de esta base de datos"></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" id="insertSampleData">
                        Insertar datos de ejemplo
                    </label>
                    <small>Crea registros de muestra para probar</small>
                </div>

                <button class="btn btn-primary" onclick="nextTab('tables')">Siguiente: Crear Tablas →</button>
            </div>

            <!-- TAB 2: Tablas -->
            <div class="tab-content" id="tables">
                <h2>Definir Tablas</h2>
                <p class="text-muted">Define las tablas que tendrá tu base de datos</p>

                <div id="tablesContainer"></div>

                <button class="btn btn-secondary" onclick="addTable()">+ Agregar Tabla</button>
                <button class="btn btn-primary" onclick="nextTab('relations')">Siguiente: Relaciones →</button>
            </div>

            <!-- TAB 3: Relaciones -->
            <div class="tab-content" id="relations">
                <h2>Definir Relaciones (Foreign Keys)</h2>
                <p class="text-muted">Conecta las tablas mediante claves foráneas</p>

                <div id="relationsContainer"></div>

                <button class="btn btn-secondary" onclick="addRelation()">+ Agregar Relación</button>
                <button class="btn btn-primary" onclick="nextTab('preview')">Ver Vista Previa →</button>
            </div>

            <!-- TAB 4: Vista Previa -->
            <div class="tab-content" id="preview">
                <h2>Vista Previa del Esquema JSON</h2>
                <p class="text-muted">Revisa el esquema antes de generar</p>

                <div class="json-preview" id="jsonPreview"></div>

                <div style="margin-top: 20px;">
                    <button class="btn btn-success btn-lg" onclick="generateDatabase()">
                        🚀 Generar Base de Datos y CRUDs
                    </button>
                    <button class="btn btn-secondary" onclick="downloadJSON()">
                        💾 Descargar JSON
                    </button>
                </div>
            </div>

            <!-- TAB 5: Ejemplos -->
            <div class="tab-content" id="examples">
                <h2>Cargar Ejemplo Predefinido</h2>
                <p class="text-muted">Selecciona un ejemplo para comenzar rápidamente</p>

                <div class="examples-grid">
                    <div class="example-card" onclick="loadExample('blog')">
                        <h4>📝 Sistema de Blog</h4>
                        <p><strong>Tablas:</strong> users, posts, categories, comments</p>
                        <p><strong>Características:</strong> Blog completo con usuarios, categorías y comentarios</p>
                        <ul style="font-size: 13px;">
                            <li>Usuarios con roles</li>
                            <li>Posts con soft deletes</li>
                            <li>Sistema de comentarios</li>
                            <li>Categorías</li>
                        </ul>
                    </div>

                    <div class="example-card" onclick="loadExample('ecommerce')">
                        <h4>🛒 E-commerce</h4>
                        <p><strong>Tablas:</strong> customers, products, orders</p>
                        <p><strong>Características:</strong> Tienda online completa</p>
                        <ul style="font-size: 13px;">
                            <li>Gestión de clientes</li>
                            <li>Catálogo de productos</li>
                            <li>Sistema de órdenes</li>
                            <li>Control de stock</li>
                        </ul>
                    </div>

                    <div class="example-card" onclick="loadExample('school')">
                        <h4>🎓 Gestión Escolar</h4>
                        <p><strong>Tablas:</strong> students, teachers, courses, enrollments</p>
                        <p><strong>Características:</strong> Sistema académico completo</p>
                        <ul style="font-size: 13px;">
                            <li>Estudiantes y profesores</li>
                            <li>Catálogo de cursos</li>
                            <li>Inscripciones</li>
                            <li>Calificaciones</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Agregar este script al final de database_builder.php, reemplazando el <script> existente

const BASE_PATH = '<?= BASE_PATH ?>';
let tables = [];
let relations = [];

// Sistema de Tabs
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const tabName = tab.dataset.tab;
        switchTab(tabName);
    });
});

function switchTab(tabName) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    document.getElementById(tabName).classList.add('active');
    
    if (tabName === 'preview') {
        updatePreview();
    }
}

function nextTab(tabName) {
    switchTab(tabName);
}

// Agregar Tabla
function addTable() {
    const tableId = 'table_' + Date.now();
    tables.push({
        id: tableId,
        name: '',
        timestamps: true,
        soft_deletes: false,
        fields: []
    });
    
    renderTables();
}

function renderTables() {
    const container = document.getElementById('tablesContainer');
    container.innerHTML = '';

    if (tables.length === 0) {
        container.innerHTML = '<p class="text-muted">No hay tablas agregadas. Click en "+ Agregar Tabla" para comenzar.</p>';
        return;
    }

    tables.forEach((table, index) => {
        const tableCard = document.createElement('div');
        tableCard.className = 'table-card';
        tableCard.innerHTML = `
            <h3>
                Tabla #${index + 1}
                <button class="btn-remove" onclick="removeTable(${index})">🗑️ Eliminar</button>
            </h3>
            
            <div class="form-group">
                <label>Nombre de la tabla:</label>
                <input type="text" placeholder="usuarios, productos, etc." 
                       value="${table.name}"
                       onchange="tables[${index}].name = this.value">
            </div>

            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" ${table.timestamps ? 'checked' : ''}
                           onchange="tables[${index}].timestamps = this.checked">
                    Timestamps (created_at, updated_at)
                </label>
                <label>
                    <input type="checkbox" ${table.soft_deletes ? 'checked' : ''}
                           onchange="tables[${index}].soft_deletes = this.checked">
                    Soft Deletes (deleted_at)
                </label>
            </div>

            <h4>Campos:</h4>
            <div class="label-small" style="display: grid; grid-template-columns: 2fr 1.5fr 1fr 0.8fr 0.8fr 80px; gap: 10px; margin-bottom: 8px;">
                <div>Nombre</div>
                <div>Tipo</div>
                <div>Longitud</div>
                <div>Nullable</div>
                <div>Unsigned</div>
                <div>Acción</div>
            </div>
            
            <div id="fields_${tableId}"></div>
            
            <button class="btn btn-sm btn-secondary" onclick="addField(${index})">+ Campo</button>
        `;
        
        container.appendChild(tableCard);
        renderFields(index);
    });
}

function addField(tableIndex) {
    if (!tables[tableIndex].fields) {
        tables[tableIndex].fields = [];
    }
    
    tables[tableIndex].fields.push({
        name: '',
        type: 'string',
        length: 255,
        nullable: false,
        unsigned: false
    });
    renderFields(tableIndex);
}

function renderFields(tableIndex) {
    const table = tables[tableIndex];
    const container = document.getElementById(`fields_${table.id}`);
    
    if (!container) {
        console.error('Container not found for table:', table.id);
        return;
    }
    
    container.innerHTML = '';

    if (!table.fields || table.fields.length === 0) {
        container.innerHTML = '<p class="text-muted">No hay campos. Click en "+ Campo" para agregar.</p>';
        return;
    }

    table.fields.forEach((field, fieldIndex) => {
        const fieldRow = document.createElement('div');
        fieldRow.className = 'field-row';
        fieldRow.innerHTML = `
            <input type="text" placeholder="nombre_campo" 
                   value="${field.name || ''}"
                   onchange="tables[${tableIndex}].fields[${fieldIndex}].name = this.value">
            
            <select onchange="tables[${tableIndex}].fields[${fieldIndex}].type = this.value; renderFields(${tableIndex})">
                ${getTypeOptions(field.type)}
            </select>
            
            <input type="text" placeholder="255" 
                   value="${field.length || ''}"
                   onchange="tables[${tableIndex}].fields[${fieldIndex}].length = this.value"
                   ${['text', 'longtext', 'date', 'datetime', 'boolean'].includes(field.type) ? 'disabled' : ''}>
            
            <input type="checkbox" ${field.nullable ? 'checked' : ''}
                   onchange="tables[${tableIndex}].fields[${fieldIndex}].nullable = this.checked">
            
            <input type="checkbox" ${field.unsigned ? 'checked' : ''}
                   onchange="tables[${tableIndex}].fields[${fieldIndex}].unsigned = this.checked"
                   ${!['integer', 'bigint', 'float', 'decimal'].includes(field.type) ? 'disabled' : ''}>
            
            <button class="btn-remove" onclick="removeField(${tableIndex}, ${fieldIndex})">🗑️</button>
        `;
        container.appendChild(fieldRow);
    });
}

function getTypeOptions(selected) {
    const types = ['string', 'text', 'longtext', 'integer', 'bigint', 'float', 'decimal', 
                  'boolean', 'date', 'datetime', 'timestamp', 'json', 'enum'];
    
    return types.map(type => 
        `<option value="${type}" ${type === selected ? 'selected' : ''}>${type}</option>`
    ).join('');
}

function removeTable(index) {
    if (confirm('¿Eliminar esta tabla?')) {
        tables.splice(index, 1);
        renderTables();
    }
}

function removeField(tableIndex, fieldIndex) {
    tables[tableIndex].fields.splice(fieldIndex, 1);
    renderFields(tableIndex);
}

// Relaciones
function addRelation() {
    relations.push({
        from_table: '',
        from_column: '',
        to_table: '',
        to_column: 'id',
        on_delete: 'CASCADE',
        on_update: 'CASCADE'
    });
    renderRelations();
}

function renderRelations() {
    const container = document.getElementById('relationsContainer');
    container.innerHTML = '';

    if (relations.length === 0) {
        container.innerHTML = '<p class="text-muted">No hay relaciones. Las relaciones son opcionales.</p>';
        return;
    }

    relations.forEach((relation, index) => {
        const relationCard = document.createElement('div');
        relationCard.className = 'relation-card';
        relationCard.innerHTML = `
            <h4>Relación #${index + 1}</h4>
            <div class="relation-row">
                <div>
                    <div class="label-small">Tabla Origen</div>
                    <select onchange="relations[${index}].from_table = this.value">
                        <option value="">Seleccionar...</option>
                        ${tables.map(t => `<option value="${t.name}" ${relation.from_table === t.name ? 'selected' : ''}>${t.name}</option>`).join('')}
                    </select>
                </div>
                
                <div>
                    <div class="label-small">Campo Origen</div>
                    <input type="text" placeholder="campo_id" value="${relation.from_column}"
                           onchange="relations[${index}].from_column = this.value">
                </div>
                
                <div>
                    <div class="label-small">Tabla Destino</div>
                    <select onchange="relations[${index}].to_table = this.value">
                        <option value="">Seleccionar...</option>
                        ${tables.map(t => `<option value="${t.name}" ${relation.to_table === t.name ? 'selected' : ''}>${t.name}</option>`).join('')}
                    </select>
                </div>
                
                <div>
                    <div class="label-small">Campo Destino</div>
                    <input type="text" value="${relation.to_column}"
                           onchange="relations[${index}].to_column = this.value">
                </div>
                
                <div>
                    <div class="label-small">ON DELETE</div>
                    <select onchange="relations[${index}].on_delete = this.value">
                        <option value="CASCADE" ${relation.on_delete === 'CASCADE' ? 'selected' : ''}>CASCADE</option>
                        <option value="SET NULL" ${relation.on_delete === 'SET NULL' ? 'selected' : ''}>SET NULL</option>
                        <option value="RESTRICT" ${relation.on_delete === 'RESTRICT' ? 'selected' : ''}>RESTRICT</option>
                    </select>
                </div>
                
                <div>
                    <div class="label-small">ON UPDATE</div>
                    <select onchange="relations[${index}].on_update = this.value">
                        <option value="CASCADE" ${relation.on_update === 'CASCADE' ? 'selected' : ''}>CASCADE</option>
                        <option value="RESTRICT" ${relation.on_update === 'RESTRICT' ? 'selected' : ''}>RESTRICT</option>
                    </select>
                </div>
                
                <button class="btn-remove" onclick="removeRelation(${index})">🗑️</button>
            </div>
        `;
        container.appendChild(relationCard);
    });
}

function removeRelation(index) {
    relations.splice(index, 1);
    renderRelations();
}

// Vista Previa
function updatePreview() {
    const schema = buildSchema();
    document.getElementById('jsonPreview').textContent = JSON.stringify(schema, null, 2);
}

function buildSchema() {
    return {
        database_name: document.getElementById('dbName').value || 'mi_base_datos',
        description: document.getElementById('dbDescription').value || '',
        insert_sample_data: document.getElementById('insertSampleData').checked,
        tables: tables,
        relationships: relations
    };
}

// Generar Base de Datos
function generateDatabase() {
    const schema = buildSchema();
    
    // Validaciones
    if (!schema.database_name || schema.database_name === 'mi_base_datos') {
        alert('❌ Debes especificar un nombre válido para la base de datos');
        switchTab('config');
        document.getElementById('dbName').focus();
        return;
    }

    if (tables.length === 0) {
        alert('❌ Debes agregar al menos una tabla');
        switchTab('tables');
        return;
    }

    // Validar que las tablas tengan nombre
    for (let i = 0; i < tables.length; i++) {
        if (!tables[i].name) {
            alert(`❌ La tabla #${i + 1} no tiene nombre`);
            switchTab('tables');
            return;
        }
        if (!tables[i].fields || tables[i].fields.length === 0) {
            alert(`❌ La tabla "${tables[i].name}" no tiene campos`);
            switchTab('tables');
            return;
        }
    }

    // Mostrar loading
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '⏳ Generando...';

    // Enviar al servidor
    fetch(BASE_PATH + '/api/generate-full-database', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(schema)
    })
    .then(response => response.json())
    .then(data => {
        button.disabled = false;
        button.innerHTML = originalText;

        if (data.success) {
            alert('✅ ¡Base de datos y CRUDs generados correctamente!\n\n' +
                  'Tablas creadas: ' + data.tables_count + '\n' +
                  'Relaciones: ' + data.relations_count + '\n\n' +
                  'Puedes acceder a los CRUDs desde el menú principal.');
            window.location.href = BASE_PATH + '/';
        } else {
            alert('❌ Error al generar:\n\n' + data.error);
            console.error('Error details:', data);
        }
    })
    .catch(error => {
        button.disabled = false;
        button.innerHTML = originalText;
        alert('❌ Error de conexión: ' + error.message);
        console.error('Fetch error:', error);
    });
}

function downloadJSON() {
    const schema = buildSchema();
    const blob = new Blob([JSON.stringify(schema, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = schema.database_name + '_schema_' + Date.now() + '.json';
    a.click();
    URL.revokeObjectURL(url);
}

// Cargar Ejemplos
function loadExample(example) {
    const examples = {
        blog: {
            "database_name":"blog_system",
            "description":"Sistema de blog completo con usuarios, posts y comentarios",
            "insert_sample_data":false,
            "tables":[
                {
                    "id":"table_users",
                    "name":"users",
                    "timestamps":true,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"name","type":"string","length":100,"nullable":false,"unsigned":false},
                        {"name":"email","type":"string","length":150,"nullable":false,"unsigned":false},
                        {"name":"password","type":"string","length":255,"nullable":false,"unsigned":false}
                    ]
                },
                {
                    "id":"table_posts",
                    "name":"posts",
                    "timestamps":true,
                    "soft_deletes":true,
                    "fields":[
                        {"name":"user_id","type":"integer","length":null,"nullable":false,"unsigned":true},
                        {"name":"title","type":"string","length":255,"nullable":false,"unsigned":false},
                        {"name":"content","type":"longtext","length":null,"nullable":false,"unsigned":false}
                    ]
                },
                {
                    "id":"table_comments",
                    "name":"comments",
                    "timestamps":true,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"post_id","type":"integer","length":null,"nullable":false,"unsigned":true},
                        {"name":"content","type":"text","length":null,"nullable":false,"unsigned":false}
                    ]
                }
            ],
            "relationships":[
                {"from_table":"posts","from_column":"user_id","to_table":"users","to_column":"id","on_delete":"CASCADE","on_update":"CASCADE"},
                {"from_table":"comments","from_column":"post_id","to_table":"posts","to_column":"id","on_delete":"CASCADE","on_update":"CASCADE"}
            ]
        },
        
        ecommerce: {
            "database_name":"ecommerce_db",
            "description":"Sistema de e-commerce con productos y órdenes",
            "insert_sample_data":false,
            "tables":[
                {
                    "id":"table_customers",
                    "name":"customers",
                    "timestamps":true,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"name","type":"string","length":100,"nullable":false,"unsigned":false},
                        {"name":"email","type":"string","length":150,"nullable":false,"unsigned":false}
                    ]
                },
                {
                    "id":"table_products",
                    "name":"products",
                    "timestamps":true,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"name","type":"string","length":200,"nullable":false,"unsigned":false},
                        {"name":"price","type":"decimal","length":"10,2","nullable":false,"unsigned":false},
                        {"name":"stock","type":"integer","length":null,"nullable":false,"unsigned":true}
                    ]
                },
                {
                    "id":"table_orders",
                    "name":"orders",
                    "timestamps":true,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"customer_id","type":"integer","length":null,"nullable":false,"unsigned":true},
                        {"name":"total","type":"decimal","length":"10,2","nullable":false,"unsigned":false}
                    ]
                }
            ],
            "relationships":[
                {"from_table":"orders","from_column":"customer_id","to_table":"customers","to_column":"id","on_delete":"RESTRICT","on_update":"CASCADE"}
            ]
        },
        
        school: {
            "database_name":"school_management",
            "description":"Sistema de gestión escolar",
            "insert_sample_data":false,
            "tables":[
                {
                    "id":"table_students",
                    "name":"students",
                    "timestamps":true,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"first_name","type":"string","length":50,"nullable":false,"unsigned":false},
                        {"name":"last_name","type":"string","length":50,"nullable":false,"unsigned":false},
                        {"name":"email","type":"string","length":100,"nullable":false,"unsigned":false}
                    ]
                },
                {
                    "id":"table_teachers",
                    "name":"teachers",
                    "timestamps":true,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"first_name","type":"string","length":50,"nullable":false,"unsigned":false},
                        {"name":"last_name","type":"string","length":50,"nullable":false,"unsigned":false}
                    ]
                },
                {
                    "id":"table_courses",
                    "name":"courses",
                    "timestamps":false,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"code","type":"string","length":20,"nullable":false,"unsigned":false},
                        {"name":"name","type":"string","length":100,"nullable":false,"unsigned":false}
                    ]
                },
                {
                    "id":"table_enrollments",
                    "name":"enrollments",
                    "timestamps":true,
                    "soft_deletes":false,
                    "fields":[
                        {"name":"student_id","type":"integer","length":null,"nullable":false,"unsigned":true},
                        {"name":"course_id","type":"integer","length":null,"nullable":false,"unsigned":true},
                        {"name":"grade","type":"decimal","length":"5,2","nullable":true,"unsigned":false}
                    ]
                }
            ],
            "relationships":[
                {"from_table":"enrollments","from_column":"student_id","to_table":"students","to_column":"id","on_delete":"CASCADE","on_update":"CASCADE"},
                {"from_table":"enrollments","from_column":"course_id","to_table":"courses","to_column":"id","on_delete":"RESTRICT","on_update":"CASCADE"}
            ]
        }
    };

    const schema = examples[example];
    
    if (!schema) {
        alert('❌ Ejemplo no encontrado');
        return;
    }
    
    document.getElementById('dbName').value = schema.database_name;
    document.getElementById('dbDescription').value = schema.description;
    document.getElementById('insertSampleData').checked = schema.insert_sample_data;
    
    tables = schema.tables;
    relations = schema.relationships;
    
    renderTables();
    renderRelations();
    
    alert('✅ Ejemplo cargado: ' + schema.description + '\n\nRevisa las pestañas de Configuración, Tablas y Relaciones.');
    switchTab('config');
}

// Inicializar con una tabla vacía
window.addEventListener('DOMContentLoaded', () => {
    console.log('Database Builder inicializado');
    renderTables();
    renderRelations();
});
    </script>
</body>
</html>