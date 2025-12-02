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
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .table-card {
            background: #f8f9fa;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .table-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
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
            padding: 10px;
            background: white;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .field-row:hover {
            background: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .field-row input,
        .field-row select {
            padding: 8px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .relation-card {
            background: #fff9e6;
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
            transition: all 0.2s;
        }
        
        .btn-remove:hover {
            background: #c53030;
            transform: scale(1.05);
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
            line-height: 1.5;
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
            font-weight: 600;
        }
        
        .checkbox-inline {
            display: flex;
            gap: 20px;
            margin: 15px 0;
            padding: 15px;
            background: white;
            border-radius: 5px;
        }
        
        .checkbox-inline label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .checkbox-inline input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .success-badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .info-box {
            background: #e0e7ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .info-box strong {
            color: #667eea;
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
                <h2>⚙️ Configuración General</h2>
                
                <div class="info-box">
                    <strong>📝 Información:</strong> Define los datos básicos de tu base de datos. 
                    Esta información se usará para crear la estructura completa con todas las tablas y relaciones.
                </div>
                
                <div class="form-group">
                    <label>Nombre de la Base de Datos: <span style="color: red;">*</span></label>
                    <input type="text" id="dbName" placeholder="blog_system, ecommerce_db, school_management" required>
                    <small>Ejemplo: blog_system, ecommerce_db, school_management</small>
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea id="dbDescription" rows="3" placeholder="Describe el propósito de esta base de datos..."></textarea>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" id="insertSampleData">
                        <span>Insertar datos de ejemplo para pruebas</span>
                    </label>
                    <small>Esto creará registros de muestra en cada tabla</small>
                </div>

                <button class="btn btn-primary btn-lg" onclick="nextTab('tables')">
                    Siguiente: Crear Tablas →
                </button>
            </div>

            <!-- TAB 2: Tablas -->
            <div class="tab-content" id="tables">
                <h2>📋 Definir Tablas</h2>
                <p class="text-muted">Define las tablas que tendrá tu base de datos. Cada tabla tendrá un ID auto-incremental automático.</p>

                <div class="info-box">
                    <strong>💡 Consejo:</strong> Usa nombres en plural para las tablas (users, products, posts). 
                    Los campos timestamp y soft delete se agregan automáticamente si los activas.
                </div>

                <div id="tablesContainer"></div>

                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="btn btn-success" onclick="addTable()">
                        ➕ Agregar Tabla
                    </button>
                    <button class="btn btn-primary" onclick="nextTab('relations')">
                        Siguiente: Relaciones →
                    </button>
                </div>
            </div>

            <!-- TAB 3: Relaciones -->
            <div class="tab-content" id="relations">
                <h2>🔗 Definir Relaciones (Foreign Keys)</h2>
                <p class="text-muted">Conecta las tablas mediante claves foráneas. Esto es opcional pero recomendado.</p>

                <div class="info-box">
                    <strong>📌 Nota:</strong> Las relaciones son opcionales. Si no necesitas conectar tablas, 
                    puedes omitir este paso.
                </div>

                <div id="relationsContainer"></div>

                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="btn btn-secondary" onclick="addRelation()">
                        ➕ Agregar Relación
                    </button>
                    <button class="btn btn-primary" onclick="nextTab('preview')">
                        Ver Vista Previa →
                    </button>
                </div>
            </div>

            <!-- TAB 4: Vista Previa -->
            <div class="tab-content" id="preview">
                <h2>👁️ Vista Previa del Esquema JSON</h2>
                <p class="text-muted">Revisa el esquema completo antes de generar la base de datos</p>

                <div class="json-preview" id="jsonPreview"></div>

                <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
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
                <h2>📚 Cargar Ejemplo Predefinido</h2>
                <p class="text-muted">Selecciona un ejemplo para comenzar rápidamente</p>

                <div class="examples-grid">
                    <div class="example-card" onclick="loadExample('blog')">
                        <h4>📝 Sistema de Blog</h4>
                        <span class="success-badge">COMPLETO</span>
                        <p style="margin: 10px 0;"><strong>Tablas:</strong> users, posts, categories, comments</p>
                        <p><strong>Características:</strong></p>
                        <ul style="font-size: 13px; color: #64748b;">
                            <li>✅ Usuarios con autenticación</li>
                            <li>✅ Posts con soft deletes</li>
                            <li>✅ Sistema de comentarios</li>
                            <li>✅ Categorías</li>
                        </ul>
                    </div>

                    <div class="example-card" onclick="loadExample('ecommerce')">
                        <h4>🛒 E-commerce</h4>
                        <span class="success-badge">COMPLETO</span>
                        <p style="margin: 10px 0;"><strong>Tablas:</strong> customers, products, orders</p>
                        <p><strong>Características:</strong></p>
                        <ul style="font-size: 13px; color: #64748b;">
                            <li>✅ Gestión de clientes</li>
                            <li>✅ Catálogo de productos</li>
                            <li>✅ Sistema de órdenes</li>
                            <li>✅ Control de inventario</li>
                        </ul>
                    </div>

                    <div class="example-card" onclick="loadExample('school')">
                        <h4>🎓 Gestión Escolar</h4>
                        <span class="success-badge">COMPLETO</span>
                        <p style="margin: 10px 0;"><strong>Tablas:</strong> students, teachers, courses</p>
                        <p><strong>Características:</strong></p>
                        <ul style="font-size: 13px; color: #64748b;">
                            <li>✅ Estudiantes y profesores</li>
                            <li>✅ Catálogo de cursos</li>
                            <li>✅ Sistema de inscripciones</li>
                            <li>✅ Gestión de calificaciones</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
       // ============================================
// CONSTRUCTOR DE BASE DE DATOS - MEJORADO
// ============================================




const BASE_PATH = '<?= BASE_PATH ?>' || '/crud-generator';
console.log('BASE_PATH definido:', BASE_PATH);
let tables = [];
let relations = [];
let tableCounter = 0;
// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Database Builder inicializado');
    
    initializeTabs();
    initializeEventListeners();
    
    // Renderizar containers vacíos
    renderTables();
    renderRelations();
    
    console.log('✅ Sistema listo');
});

// ============================================
// SISTEMA DE TABS
// ============================================
function initializeTabs() {
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.dataset.tab;
            switchTab(tabName);
        });
    });
}

function switchTab(tabName) {
    // Remover active de todos
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    // Activar el seleccionado
    const selectedTab = document.querySelector(`[data-tab="${tabName}"]`);
    const selectedContent = document.getElementById(tabName);
    
    if (selectedTab && selectedContent) {
        selectedTab.classList.add('active');
        selectedContent.classList.add('active');
        
        // Si es preview, actualizar
        if (tabName === 'preview') {
            updatePreview();
        }
    }
}

function nextTab(tabName) {
    switchTab(tabName);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================
// EVENT LISTENERS
// ============================================
function initializeEventListeners() {
    // Botón agregar tabla
    const addTableBtn = document.querySelector('[onclick*="addTable"]');
    if (addTableBtn) {
        addTableBtn.onclick = addTable;
    }
    
    // Botón agregar relación
    const addRelationBtn = document.querySelector('[onclick*="addRelation"]');
    if (addRelationBtn) {
        addRelationBtn.onclick = addRelation;
    }
    
    // Auto-completar nombre del proyecto
    const dbNameInput = document.getElementById('dbName');
    if (dbNameInput) {
        dbNameInput.addEventListener('input', function() {
            const projectNameInput = document.getElementById('project_name');
            if (projectNameInput && !projectNameInput.value) {
                projectNameInput.value = this.value + '_project';
            }
        });
    }
}

// ============================================
// GESTIÓN DE TABLAS
// ============================================
function addTable() {
    console.log('➕ Agregando nueva tabla...');
    
    tableCounter++;
    const tableId = 'table_' + Date.now();
    
    const newTable = {
        id: tableId,
        name: '',
        timestamps: true,
        soft_deletes: false,
        fields: []
    };
    
    tables.push(newTable);
    console.log('✅ Tabla agregada:', newTable);
    console.log('📊 Total tablas:', tables.length);
    
    renderTables();
}

function removeTable(index) {
    if (!confirm('¿Eliminar esta tabla?')) {
        return;
    }
    
    console.log('🗑️ Eliminando tabla index:', index);
    tables.splice(index, 1);
    renderTables();
}

function renderTables() {
    const container = document.getElementById('tablesContainer');
    
    if (!container) {
        console.error('❌ No se encontró tablesContainer');
        return;
    }
    
    container.innerHTML = '';
    
    if (tables.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px; border: 2px dashed #cbd5e0;">
                <p style="color: #64748b; font-size: 1.1em; margin: 0;">
                    📋 No hay tablas agregadas
                </p>
                <p style="color: #94a3b8; margin: 10px 0 0 0;">
                    Haz clic en "+ Agregar Tabla" para comenzar
                </p>
            </div>
        `;
        return;
    }
    
    tables.forEach((table, index) => {
        const tableCard = createTableCard(table, index);
        container.appendChild(tableCard);
    });
    
    console.log('✅ Tablas renderizadas:', tables.length);
}

function createTableCard(table, index) {
    const card = document.createElement('div');
    card.className = 'table-card';
    card.innerHTML = `
        <h3>
            📋 Tabla #${index + 1}
            <button type="button" class="btn-remove" onclick="removeTable(${index})">🗑️ Eliminar</button>
        </h3>
        
        <div class="form-group">
            <label>Nombre de la tabla:</label>
            <input type="text" 
                   placeholder="usuarios, productos, posts..." 
                   value="${table.name || ''}"
                   onchange="updateTableName(${index}, this.value)"
                   style="font-weight: 600;">
            <small>Nombre de la tabla en la base de datos (plural recomendado)</small>
        </div>

        <div class="checkbox-inline">
            <label>
                <input type="checkbox" 
                       ${table.timestamps ? 'checked' : ''}
                       onchange="updateTableTimestamps(${index}, this.checked)">
                <strong>Timestamps</strong> (created_at, updated_at)
            </label>
            <label>
                <input type="checkbox" 
                       ${table.soft_deletes ? 'checked' : ''}
                       onchange="updateTableSoftDeletes(${index}, this.checked)">
                <strong>Soft Deletes</strong> (deleted_at)
            </label>
        </div>

        <hr style="margin: 20px 0;">

        <h4>📝 Campos de la tabla</h4>
        <div class="label-small" style="display: grid; grid-template-columns: 2fr 1.5fr 1fr 0.8fr 0.8fr 80px; gap: 10px; margin-bottom: 8px; font-weight: 600; color: #64748b;">
            <div>Nombre</div>
            <div>Tipo</div>
            <div>Longitud</div>
            <div>Nullable</div>
            <div>Unsigned</div>
            <div>Acción</div>
        </div>
        
        <div id="fields_${table.id}"></div>
        
        <button type="button" class="btn btn-sm btn-secondary" onclick="addField(${index})" style="margin-top: 10px;">
            ➕ Agregar Campo
        </button>
    `;
    
    // Después de crear el card, renderizar los campos
    setTimeout(() => renderFields(index), 0);
    
    return card;
}

// ============================================
// FUNCIONES DE ACTUALIZACIÓN DE TABLA
// ============================================
function updateTableName(index, value) {
    tables[index].name = value;
    console.log('✏️ Tabla actualizada:', tables[index]);
}

function updateTableTimestamps(index, checked) {
    tables[index].timestamps = checked;
}

function updateTableSoftDeletes(index, checked) {
    tables[index].soft_deletes = checked;
}

// ============================================
// GESTIÓN DE CAMPOS
// ============================================
function addField(tableIndex) {
    console.log('➕ Agregando campo a tabla:', tableIndex);
    
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

function removeField(tableIndex, fieldIndex) {
    console.log('🗑️ Eliminando campo:', tableIndex, fieldIndex);
    tables[tableIndex].fields.splice(fieldIndex, 1);
    renderFields(tableIndex);
}

function renderFields(tableIndex) {
    const table = tables[tableIndex];
    const container = document.getElementById(`fields_${table.id}`);
    
    if (!container) {
        console.error('❌ Container de campos no encontrado:', table.id);
        return;
    }
    
    container.innerHTML = '';
    
    if (!table.fields || table.fields.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 20px; background: #fff; border: 2px dashed #cbd5e0; border-radius: 5px;">
                <p style="color: #94a3b8; margin: 0;">
                    ➕ No hay campos. Haz clic en "Agregar Campo"
                </p>
            </div>
        `;
        return;
    }
    
    table.fields.forEach((field, fieldIndex) => {
        const fieldRow = createFieldRow(tableIndex, fieldIndex, field);
        container.appendChild(fieldRow);
    });
}

function createFieldRow(tableIndex, fieldIndex, field) {
    const row = document.createElement('div');
    row.className = 'field-row';
    
    const lengthDisabled = ['text', 'longtext', 'date', 'datetime', 'boolean', 'json', 'timestamp'].includes(field.type);
    const unsignedDisabled = !['integer', 'bigint', 'float', 'decimal', 'tinyint', 'smallint', 'mediumint'].includes(field.type);
    
    row.innerHTML = `
        <input type="text" 
               placeholder="nombre_campo" 
               value="${field.name || ''}"
               onchange="updateFieldName(${tableIndex}, ${fieldIndex}, this.value)">
        
        <select onchange="updateFieldType(${tableIndex}, ${fieldIndex}, this.value)">
            ${getTypeOptions(field.type)}
        </select>
        
        <input type="text" 
               placeholder="255" 
               value="${field.length || ''}"
               onchange="updateFieldLength(${tableIndex}, ${fieldIndex}, this.value)"
               ${lengthDisabled ? 'disabled' : ''}>
        
        <input type="checkbox" 
               ${field.nullable ? 'checked' : ''}
               onchange="updateFieldNullable(${tableIndex}, ${fieldIndex}, this.checked)">
        
        <input type="checkbox" 
               ${field.unsigned ? 'checked' : ''}
               onchange="updateFieldUnsigned(${tableIndex}, ${fieldIndex}, this.checked)"
               ${unsignedDisabled ? 'disabled' : ''}>
        
        <button type="button" class="btn-remove" onclick="removeField(${tableIndex}, ${fieldIndex})">🗑️</button>
    `;
    
    return row;
}

function getTypeOptions(selected) {
    const types = [
        'string', 'text', 'longtext', 'mediumtext',
        'integer', 'bigint', 'tinyint', 'smallint', 'mediumint',
        'float', 'double', 'decimal',
        'boolean', 'date', 'datetime', 'timestamp', 'time', 'year',
        'json', 'enum', 'set'
    ];
    
    return types.map(type => 
        `<option value="${type}" ${type === selected ? 'selected' : ''}>${type.toUpperCase()}</option>`
    ).join('');
}

// ============================================
// FUNCIONES DE ACTUALIZACIÓN DE CAMPOS
// ============================================
function updateFieldName(tableIndex, fieldIndex, value) {
    tables[tableIndex].fields[fieldIndex].name = value;
}

function updateFieldType(tableIndex, fieldIndex, value) {
    tables[tableIndex].fields[fieldIndex].type = value;
    renderFields(tableIndex); // Re-renderizar para actualizar disabled
}

function updateFieldLength(tableIndex, fieldIndex, value) {
    tables[tableIndex].fields[fieldIndex].length = value;
}

function updateFieldNullable(tableIndex, fieldIndex, checked) {
    tables[tableIndex].fields[fieldIndex].nullable = checked;
}

function updateFieldUnsigned(tableIndex, fieldIndex, checked) {
    tables[tableIndex].fields[fieldIndex].unsigned = checked;
}

// ============================================
// GESTIÓN DE RELACIONES
// ============================================
function addRelation() {
    console.log('➕ Agregando relación');
    
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

function removeRelation(index) {
    console.log('🗑️ Eliminando relación:', index);
    relations.splice(index, 1);
    renderRelations();
}

function renderRelations() {
    const container = document.getElementById('relationsContainer');
    
    if (!container) {
        console.error('❌ No se encontró relationsContainer');
        return;
    }
    
    container.innerHTML = '';
    
    if (relations.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px; background: #fff3cd; border-radius: 8px; border: 2px dashed #ffc107;">
                <p style="color: #856404; margin: 0;">
                    🔗 No hay relaciones definidas (opcional)
                </p>
            </div>
        `;
        return;
    }
    
    relations.forEach((relation, index) => {
        const relationCard = createRelationCard(relation, index);
        container.appendChild(relationCard);
    });
}

function createRelationCard(relation, index) {
    const card = document.createElement('div');
    card.className = 'relation-card';
    card.innerHTML = `
        <h4>🔗 Relación #${index + 1}</h4>
        <div class="relation-row">
            <div>
                <div class="label-small">Tabla Origen</div>
                <select onchange="updateRelationFromTable(${index}, this.value)">
                    <option value="">Seleccionar...</option>
                    ${tables.map(t => `<option value="${t.name}" ${relation.from_table === t.name ? 'selected' : ''}>${t.name}</option>`).join('')}
                </select>
            </div>
            
            <div>
                <div class="label-small">Campo Origen</div>
                <input type="text" 
                       placeholder="user_id" 
                       value="${relation.from_column}"
                       onchange="updateRelationFromColumn(${index}, this.value)">
            </div>
            
            <div>
                <div class="label-small">Tabla Destino</div>
                <select onchange="updateRelationToTable(${index}, this.value)">
                    <option value="">Seleccionar...</option>
                    ${tables.map(t => `<option value="${t.name}" ${relation.to_table === t.name ? 'selected' : ''}>${t.name}</option>`).join('')}
                </select>
            </div>
            
            <div>
                <div class="label-small">Campo Destino</div>
                <input type="text" 
                       value="${relation.to_column}"
                       onchange="updateRelationToColumn(${index}, this.value)">
            </div>
            
            <div>
                <div class="label-small">ON DELETE</div>
                <select onchange="updateRelationOnDelete(${index}, this.value)">
                    <option value="CASCADE" ${relation.on_delete === 'CASCADE' ? 'selected' : ''}>CASCADE</option>
                    <option value="SET NULL" ${relation.on_delete === 'SET NULL' ? 'selected' : ''}>SET NULL</option>
                    <option value="RESTRICT" ${relation.on_delete === 'RESTRICT' ? 'selected' : ''}>RESTRICT</option>
                </select>
            </div>
            
            <div>
                <div class="label-small">ON UPDATE</div>
                <select onchange="updateRelationOnUpdate(${index}, this.value)">
                    <option value="CASCADE" ${relation.on_update === 'CASCADE' ? 'selected' : ''}>CASCADE</option>
                    <option value="RESTRICT" ${relation.on_update === 'RESTRICT' ? 'selected' : ''}>RESTRICT</option>
                </select>
            </div>
            
            <button type="button" class="btn-remove" onclick="removeRelation(${index})">🗑️</button>
        </div>
    `;
    
    return card;
}

// Funciones de actualización de relaciones
function updateRelationFromTable(index, value) { relations[index].from_table = value; }
function updateRelationFromColumn(index, value) { relations[index].from_column = value; }
function updateRelationToTable(index, value) { relations[index].to_table = value; }
function updateRelationToColumn(index, value) { relations[index].to_column = value; }
function updateRelationOnDelete(index, value) { relations[index].on_delete = value; }
function updateRelationOnUpdate(index, value) { relations[index].on_update = value; }

// ============================================
// VISTA PREVIA Y GENERACIÓN
// ============================================
function updatePreview() {
    const schema = buildSchema();
    const preview = document.getElementById('jsonPreview');
    
    if (preview) {
        preview.textContent = JSON.stringify(schema, null, 2);
    }
}


// También mejorar la función buildSchema para validar mejor
function buildSchema() {
    const schema = {
        database_name: document.getElementById('dbName')?.value.trim() || 'mi_base_datos',
        description: document.getElementById('dbDescription')?.value.trim() || '',
        insert_sample_data: document.getElementById('insertSampleData')?.checked || false,
        tables: [],
        relationships: []
    };
    
    // Limpiar y validar tablas
    schema.tables = tables.map(table => {
        return {
            id: table.id,
            name: table.name.trim(),
            timestamps: table.timestamps || false,
            soft_deletes: table.soft_deletes || false,
            fields: (table.fields || []).map(field => {
                return {
                    name: field.name.trim(),
                    type: field.type,
                    length: field.length === '' ? null : field.length,
                    nullable: field.nullable || false,
                    unsigned: field.unsigned || false
                };
            }).filter(f => f.name !== '') // Filtrar campos sin nombre
        };
    }).filter(t => t.name !== ''); // Filtrar tablas sin nombre
    
    // Limpiar y validar relaciones
    schema.relationships = relations.map(rel => {
        return {
            from_table: rel.from_table.trim(),
            from_column: rel.from_column.trim(),
            to_table: rel.to_table.trim(),
            to_column: rel.to_column.trim(),
            on_delete: rel.on_delete || 'CASCADE',
            on_update: rel.on_update || 'CASCADE'
        };
    }).filter(r => r.from_table && r.from_column && r.to_table && r.to_column);
    
    return schema;
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

// Reemplazar la función generateDatabase() en database_builder.php con esta versión mejorada

function generateDatabase() {
    console.log('=== GENERATE DATABASE INICIADO ===');
    
    const schema = buildSchema();
    
    console.log('Schema construido:', schema);
    
    // Validaciones
    if (!schema.database_name || schema.database_name === 'mi_base_datos') {
        alert('❌ Debes especificar un nombre válido para la base de datos');
        switchTab('config');
        document.getElementById('dbName')?.focus();
        return;
    }

    if (tables.length === 0) {
        alert('❌ Debes agregar al menos una tabla');
        switchTab('tables');
        return;
    }

    // Validar tablas
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
        
        // Validar que los campos tengan nombres
        for (let j = 0; j < tables[i].fields.length; j++) {
            if (!tables[i].fields[j].name) {
                alert(`❌ Campo #${j + 1} de la tabla "${tables[i].name}" no tiene nombre`);
                switchTab('tables');
                return;
            }
        }
    }

    console.log('✅ Validaciones pasadas');

    // IMPORTANTE: Prevenir el comportamiento por defecto del botón
    if (event && event.preventDefault) {
        event.preventDefault();
    }

    const button = event ? event.target : document.querySelector('button[onclick*="generateDatabase"]');
    if (!button) {
        console.error('❌ No se encontró el botón');
        return;
    }
    
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '⏳ Generando...';

    // CORRECCIÓN: Asegurarse de que BASE_PATH esté definido
    const basePath = typeof BASE_PATH !== 'undefined' ? BASE_PATH : '/crud-generator';
    
    // CORRECCIÓN: Construir la URL completa correctamente
    const baseUrl = window.location.origin;
    const fullUrl = baseUrl + basePath + '/api/generate-full-database';
    
    console.log('BASE_PATH:', basePath);
    console.log('URL completa:', fullUrl);
    console.log('Enviando request...');

    fetch(fullUrl, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(schema)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Obtener el texto primero para ver qué respondió
        return response.text().then(text => {
            console.log('Response text:', text.substring(0, 500));
            
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Error parseando JSON:', e);
                console.error('Respuesta recibida:', text);
                throw new Error('La respuesta no es JSON válido: ' + text.substring(0, 100));
            }
        });
    })
    .then(data => {
        console.log('Data recibida:', data);
        button.disabled = false;
        button.innerHTML = originalText;

        if (data.success) {
            alert('✅ ¡Base de datos y CRUDs generados correctamente!\n\n' +
                  `Base de datos: ${schema.database_name}\n` +
                  `Tablas: ${data.tables_count}\n` +
                  `Relaciones: ${data.relations_count}`);
            
            // Redirigir al inicio
            window.location.href = basePath + '/';
        } else {
            alert('❌ Error: ' + (data.error || 'Error desconocido'));
            if (data.trace) {
                console.error('Stack trace:', data.trace);
            }
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        button.disabled = false;
        button.innerHTML = originalText;
        
        let errorMsg = '❌ Error: ' + error.message;
        
        if (error.message.includes('404')) {
            errorMsg += '\n\n🔍 Posibles causas:\n' +
                       '1. La ruta /api/generate-full-database no está registrada en index.php\n' +
                       '2. El BASE_PATH no coincide con la configuración\n' +
                       '3. Hay un problema con el .htaccess\n\n' +
                       'URL intentada: ' + fullUrl;
        }
        
        alert(errorMsg);
    });
}
// ============================================
// EJEMPLOS PREDEFINIDOS
// ============================================
function loadExample(example) {
    const examples = {
        blog: {
            database_name: "blog_system",
            description: "Sistema de blog completo",
            insert_sample_data: false,
            tables: [
                {
                    id: "table_users",
                    name: "users",
                    timestamps: true,
                    soft_deletes: false,
                    fields: [
                        { name: "name", type: "string", length: 100, nullable: false, unsigned: false },
                        { name: "email", type: "string", length: 150, nullable: false, unsigned: false },
                        { name: "password", type: "string", length: 255, nullable: false, unsigned: false }
                    ]
                },
                {
                    id: "table_posts",
                    name: "posts",
                    timestamps: true,
                    soft_deletes: true,
                    fields: [
                        { name: "user_id", type: "integer", length: null, nullable: false, unsigned: true },
                        { name: "title", type: "string", length: 255, nullable: false, unsigned: false },
                        { name: "content", type: "longtext", length: null, nullable: false, unsigned: false }
                    ]
                }
            ],
            relationships: [
                { from_table: "posts", from_column: "user_id", to_table: "users", to_column: "id", on_delete: "CASCADE", on_update: "CASCADE" }
            ]
        },
        ecommerce: {
            database_name: "ecommerce_db",
            description: "Sistema de tienda online",
            insert_sample_data: false,
            tables: [
                {
                    id: "table_products",
                    name: "products",
                    timestamps: true,
                    soft_deletes: false,
                    fields: [
                        { name: "name", type: "string", length: 200, nullable: false, unsigned: false },
                        { name: "price", type: "decimal", length: "10,2", nullable: false, unsigned: false },
                        { name: "stock", type: "integer", length: null, nullable: false, unsigned: true }
                    ]
                }
            ],
            relationships: []
        },
        school: {
            database_name: "school_management",
            description: "Sistema escolar",
            insert_sample_data: false,
            tables: [
                {
                    id: "table_students",
                    name: "students",
                    timestamps: true,
                    soft_deletes: false,
                    fields: [
                        { name: "first_name", type: "string", length: 50, nullable: false, unsigned: false },
                        { name: "last_name", type: "string", length: 50, nullable: false, unsigned: false }
                    ]
                }
            ],
            relationships: []
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
    
    alert('✅ Ejemplo cargado correctamente');
    switchTab('config');
}

// Exponer funciones globales necesarias

window.addTable = addTable;
window.removeTable = removeTable;
window.addField = addField;
window.removeField = removeField;
window.addRelation = addRelation;
window.removeRelation = removeRelation;
window.nextTab = nextTab;
window.generateDatabase = generateDatabase;
window.downloadJSON = downloadJSON;
window.loadExample = loadExample;
window.updateTableName = updateTableName;
window.updateTableTimestamps = updateTableTimestamps;
window.updateTableSoftDeletes = updateTableSoftDeletes;
window.updateFieldName = updateFieldName;
window.updateFieldType = updateFieldType;
window.updateFieldLength = updateFieldLength;
window.updateFieldNullable = updateFieldNullable;
window.updateFieldUnsigned = updateFieldUnsigned;
window.updateRelationFromTable = updateRelationFromTable;
window.updateRelationFromColumn = updateRelationFromColumn;
window.updateRelationToTable = updateRelationToTable;
window.updateRelationToColumn = updateRelationToColumn;
window.updateRelationOnDelete = updateRelationOnDelete;
window.updateRelationOnUpdate = updateRelationOnUpdate;
    </script>
</body>
</html>