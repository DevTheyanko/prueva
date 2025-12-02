document.addEventListener('DOMContentLoaded', function() {
    const fieldsContainer = document.getElementById('fieldsContainer');
    const addFieldBtn = document.getElementById('addFieldBtn');
    const form = document.getElementById('tableBuilderForm');
    let fieldCount = 0;

    // Agregar primer campo automáticamente
    addField();

    addFieldBtn.addEventListener('click', addField);

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fields = collectFields();
        
        if (fields.length === 0) {
            alert('Debes agregar al menos un campo');
            return;
        }

        document.getElementById('fieldsData').value = JSON.stringify(fields);
        form.submit();
    });

    function addField() {
        fieldCount++;
        
        const fieldRow = document.createElement('div');
        fieldRow.className = 'field-row';
        fieldRow.id = `field-${fieldCount}`;
        
        fieldRow.innerHTML = `
            <div>
                <label>Nombre del Campo:</label>
                <input type="text" class="field-name" placeholder="nombre, precio, email..." required>
            </div>
            <div>
                <label>Tipo de Dato:</label>
                <select class="field-type">
                    <option value="string">Texto (VARCHAR)</option>
                    <option value="text">Texto Largo (TEXT)</option>
                    <option value="integer">Número Entero (INT)</option>
                    <option value="bigint">Entero Grande (BIGINT)</option>
                    <option value="float">Decimal (FLOAT)</option>
                    <option value="decimal">Decimal Preciso (DECIMAL)</option>
                    <option value="boolean">Booleano (TRUE/FALSE)</option>
                    <option value="date">Fecha</option>
                    <option value="datetime">Fecha y Hora</option>
                    <option value="timestamp">Timestamp</option>
                </select>
            </div>
            <div>
                <label>Longitud:</label>
                <input type="number" class="field-length" placeholder="255" value="255">
            </div>
            <div>
                <label>¿Nulo?</label>
                <input type="checkbox" class="field-nullable">
            </div>
            <div>
                <button type="button" class="btn btn-secondary" onclick="removeField(${fieldCount})">Eliminar</button>
            </div>
        `;
        
        fieldsContainer.appendChild(fieldRow);
    }

    window.removeField = function(id) {
        const field = document.getElementById(`field-${id}`);
        if (field) {
            field.remove();
        }
    };

    function collectFields() {
        const fields = [];
        const fieldRows = document.querySelectorAll('.field-row');
        
        fieldRows.forEach(row => {
            const name = row.querySelector('.field-name').value.trim();
            const type = row.querySelector('.field-type').value;
            const length = parseInt(row.querySelector('.field-length').value) || null;
            const nullable = row.querySelector('.field-nullable').checked;
            
            if (name) {
                fields.push({ name, type, length, nullable });
            }
        });
        
        return fields;
    }
});
