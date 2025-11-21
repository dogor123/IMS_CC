// Funciones JavaScript

// Confirmación de eliminación
function confirmDelete(message) {
    return confirm(message || '¿Está seguro de que desea eliminar este elemento?');
}

// Mostrar/ocultar campos específicos según el tipo de producto
function toggleProductFields() {
    const productType = document.getElementById('product_type');
    const weightField = document.getElementById('weight_field');
    const downloadLinkField = document.getElementById('download_link_field');
    const durationField = document.getElementById('duration_field');
    const stockField = document.getElementById('stock_field');
    
    if (!productType) return;
    
    const type = productType.value;
    
    // Ocultar todos
    if (weightField) weightField.style.display = 'none';
    if (downloadLinkField) downloadLinkField.style.display = 'none';
    if (durationField) durationField.style.display = 'none';
    
    // Mostrar campos específicos
    if (type === 'physical') {
        if (weightField) weightField.style.display = 'block';
        if (stockField) {
            stockField.querySelector('input').required = true;
        }
    } else if (type === 'digital') {
        if (downloadLinkField) downloadLinkField.style.display = 'block';
        if (stockField) {
            stockField.querySelector('input').required = false;
            stockField.querySelector('input').value = 999;
        }
    } else if (type === 'service') {
        if (durationField) durationField.style.display = 'block';
        if (stockField) {
            stockField.querySelector('input').required = false;
            stockField.querySelector('input').value = 999;
        }
    }
}

// Calcular margen de ganancia
function calculateMargin() {
    const costPrice = parseFloat(document.getElementById('cost_price')?.value) || 0;
    const salePrice = parseFloat(document.getElementById('sale_price')?.value) || 0;
    const marginDisplay = document.getElementById('profit_margin');
    
    if (marginDisplay && costPrice > 0) {
        const margin = ((salePrice - costPrice) / costPrice) * 100;
        marginDisplay.textContent = `Margen de ganancia: ${margin.toFixed(2)}%`;
        marginDisplay.style.color = margin > 0 ? '#11998e' : '#eb3349';
    }
}

// Agregar producto al pedido
let orderItems = [];

function addProductToOrder() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    
    if (!productSelect || !quantityInput) return;
    
    const productId = productSelect.value;
    const productName = productSelect.options[productSelect.selectedIndex].text;
    const quantity = parseInt(quantityInput.value);
    const price = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price);
    
    if (!productId || quantity <= 0) {
        alert('Por favor seleccione un producto y cantidad válida');
        return;
    }
    
    // Verificar si el producto ya está en el pedido
    const existingIndex = orderItems.findIndex(item => item.product_id === productId);
    
    if (existingIndex >= 0) {
        orderItems[existingIndex].quantity += quantity;
    } else {
        orderItems.push({
            product_id: productId,
            product_name: productName,
            quantity: quantity,
            price: price
        });
    }
    
    updateOrderItemsDisplay();
    productSelect.value = '';
    quantityInput.value = '1';
}

function updateOrderItemsDisplay() {
    const container = document.getElementById('order_items_display');
    const hiddenInput = document.getElementById('order_items_data');
    
    if (!container) return;
    
    let html = '<h3>Productos en el pedido:</h3><table class="table"><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th><th>Acción</th></tr></thead><tbody>';
    
    let total = 0;
    orderItems.forEach((item, index) => {
        const subtotal = item.quantity * item.price;
        total += subtotal;
        
        html += `
            <tr>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>$${item.price.toFixed(2)}</td>
                <td>$${subtotal.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger" onclick="removeOrderItem(${index})">Eliminar</button></td>
            </tr>
        `;
    });
    
    html += `</tbody></table><h3>Total: $${total.toFixed(2)}</h3>`;
    container.innerHTML = html;
    
    // Actualizar input oculto con los datos del pedido
    if (hiddenInput) {
        hiddenInput.value = JSON.stringify(orderItems);
    }
    
    // Habilitar/deshabilitar botón de crear pedido
    const submitBtn = document.getElementById('submit_order_btn');
    if (submitBtn) {
        submitBtn.disabled = orderItems.length === 0;
    }
}

function removeOrderItem(index) {
    orderItems.splice(index, 1);
    updateOrderItemsDisplay();
}

// Validación de formularios
function validateProductForm() {
    const name = document.getElementById('name')?.value.trim();
    const costPrice = parseFloat(document.getElementById('cost_price')?.value);
    const salePrice = parseFloat(document.getElementById('sale_price')?.value);
    const sku = document.getElementById('sku')?.value.trim();
    
    if (!name) {
        alert('El nombre del producto es obligatorio');
        return false;
    }
    
    if (!sku) {
        alert('El SKU es obligatorio');
        return false;
    }
    
    if (isNaN(costPrice) || costPrice < 0) {
        alert('El precio de costo debe ser un número válido');
        return false;
    }
    
    if (isNaN(salePrice) || salePrice < 0) {
        alert('El precio de venta debe ser un número válido');
        return false;
    }
    
    if (salePrice < costPrice) {
        return confirm('El precio de venta es menor que el precio de costo. ¿Desea continuar?');
    }
    
    return true;
}

function validateOrderForm() {
    const customerName = document.getElementById('customer_name')?.value.trim();
    const customerEmail = document.getElementById('customer_email')?.value.trim();
    
    if (!customerName) {
        alert('El nombre del cliente es obligatorio');
        return false;
    }
    
    if (!customerEmail || !validateEmail(customerEmail)) {
        alert('El email del cliente es obligatorio y debe ser válido');
        return false;
    }
    
    if (orderItems.length === 0) {
        alert('Debe agregar al menos un producto al pedido');
        return false;
    }
    
    return true;
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Filtrado de tabla
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    const filter = input.value.toUpperCase();
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell) {
                const textValue = cell.textContent || cell.innerText;
                if (textValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
}

// Auto-ocultar alertas
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
    
    // Inicializar campos de producto si existe el select
    const productTypeSelect = document.getElementById('product_type');
    if (productTypeSelect) {
        toggleProductFields();
        productTypeSelect.addEventListener('change', toggleProductFields);
    }
    
    // Calcular margen al cambiar precios
    const costPriceInput = document.getElementById('cost_price');
    const salePriceInput = document.getElementById('sale_price');
    if (costPriceInput && salePriceInput) {
        costPriceInput.addEventListener('input', calculateMargin);
        salePriceInput.addEventListener('input', calculateMargin);
        calculateMargin();
    }
});