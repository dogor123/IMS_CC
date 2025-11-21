<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$productController = new ProductController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar SKU único
    if ($productController->skuExists($_POST['sku'])) {
        $errorMessage = "El SKU ya existe. Por favor usa uno diferente.";
    } else {
        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'product_type' => $_POST['product_type'],
            'cost_price' => $_POST['cost_price'],
            'sale_price' => $_POST['sale_price'],
            'stock' => $_POST['stock'] ?? 0,
            'sku' => $_POST['sku'],
            'image_url' => $_POST['image_url'] ?? null,
            'weight' => $_POST['weight'] ?? null,
            'download_link' => $_POST['download_link'] ?? null,
            'duration_hours' => $_POST['duration_hours'] ?? null
        ];
        
        if ($productController->createProduct($data)) {
            header('Location: index.php?page=products&success=created');
            exit;
        } else {
            $errorMessage = "Error al crear el producto";
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>➕ Agregar Nuevo Producto</h2>
    </div>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <div class="alert alert-info">
        <strong>💡 Patrón Factory Method:</strong> Este formulario crea diferentes tipos de productos 
        (Físicos, Digitales o Servicios) según tu selección.
    </div>
    
    <form method="POST" onsubmit="return validateProductForm()">
        <div class="form-group">
            <label for="product_type">Tipo de Producto *</label>
            <select name="product_type" id="product_type" class="form-control" required>
                <option value="">Seleccione un tipo</option>
                <option value="physical">📦 Producto Físico</option>
                <option value="digital">💾 Producto Digital</option>
                <option value="service">🛠️ Servicio</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="name">Nombre del Producto *</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        
        <div class="form-group">
            <label for="sku">SKU (Código Único) *</label>
            <input type="text" name="sku" id="sku" class="form-control" required 
                   placeholder="Ej: PROD-001">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="cost_price">Precio de Costo ($) *</label>
                <input type="number" name="cost_price" id="cost_price" class="form-control" 
                       step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="sale_price">Precio de Venta ($) *</label>
                <input type="number" name="sale_price" id="sale_price" class="form-control" 
                       step="0.01" min="0" required>
            </div>
        </div>
        
        <div id="profit_margin" class="profit-margin" style="margin-bottom: 15px;"></div>
        
        <div class="form-group" id="stock_field">
            <label for="stock">Stock Inicial</label>
            <input type="number" name="stock" id="stock" class="form-control" 
                   value="0" min="0">
            <small>Solo aplicable para productos físicos</small>
        </div>
        
        <!-- Campos específicos para Producto Físico -->
        <div id="weight_field" style="display: none;">
            <div class="form-group">
                <label for="weight">Peso (kg)</label>
                <input type="number" name="weight" id="weight" class="form-control" 
                       step="0.01" min="0">
            </div>
        </div>
        
        <!-- Campos específicos para Producto Digital -->
        <div id="download_link_field" style="display: none;">
            <div class="form-group">
                <label for="download_link">Enlace de Descarga</label>
                <input type="url" name="download_link" id="download_link" class="form-control" 
                       placeholder="https://example.com/download">
            </div>
        </div>
        
        <!-- Campos específicos para Servicio -->
        <div id="duration_field" style="display: none;">
            <div class="form-group">
                <label for="duration_hours">Duración (horas)</label>
                <input type="number" name="duration_hours" id="duration_hours" class="form-control" 
                       min="1" value="1">
            </div>
        </div>
        
        <div class="form-group">
            <label for="image_url">URL de Imagen (opcional)</label>
            <input type="url" name="image_url" id="image_url" class="form-control" 
                   placeholder="https://example.com/image.jpg">
        </div>
        
        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-success">💾 Guardar Producto</button>
            <a href="index.php?page=products" class="btn btn-secondary">❌ Cancelar</a>
        </div>
    </form>
</div>