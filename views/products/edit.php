<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$productController = new ProductController();

if (!isset($_GET['id'])) {
    header('Location: index.php?page=products');
    exit;
}

$product = $productController->getProductById($_GET['id']);

if (!$product) {
    header('Location: index.php?page=products&error=not_found');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar SKU único (excluyendo el producto actual)
    if ($productController->skuExists($_POST['sku'], $_GET['id'])) {
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
        
        if ($productController->updateProduct($_GET['id'], $data)) {
            header('Location: index.php?page=products&success=updated');
            exit;
        } else {
            $errorMessage = "Error al actualizar el producto";
        }
    }
}

// Obtener información específica del producto
$specificInfo = $product->getSpecificInfo();
?>

<div class="card">
    <div class="card-header">
        <h2>✏️ Editar Producto</h2>
    </div>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <form method="POST" onsubmit="return validateProductForm()">
        <div class="form-group">
            <label for="product_type">Tipo de Producto *</label>
            <select name="product_type" id="product_type" class="form-control" required>
                <option value="physical" <?php echo $product->getProductType() === 'physical' ? 'selected' : ''; ?>>
                    📦 Producto Físico
                </option>
                <option value="digital" <?php echo $product->getProductType() === 'digital' ? 'selected' : ''; ?>>
                    💾 Producto Digital
                </option>
                <option value="service" <?php echo $product->getProductType() === 'service' ? 'selected' : ''; ?>>
                    🛠️ Servicio
                </option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="name">Nombre del Producto *</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="<?php echo htmlspecialchars($product->getName()); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($product->getDescription()); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="sku">SKU (Código Único) *</label>
            <input type="text" name="sku" id="sku" class="form-control" 
                   value="<?php echo htmlspecialchars($product->getSku()); ?>" required>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="cost_price">Precio de Costo ($) *</label>
                <input type="number" name="cost_price" id="cost_price" class="form-control" 
                       step="0.01" min="0" value="<?php echo $product->getCostPrice(); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="sale_price">Precio de Venta ($) *</label>
                <input type="number" name="sale_price" id="sale_price" class="form-control" 
                       step="0.01" min="0" value="<?php echo $product->getSalePrice(); ?>" required>
            </div>
        </div>
        
        <div id="profit_margin" class="profit-margin" style="margin-bottom: 15px;"></div>
        
        <div class="form-group" id="stock_field">
            <label for="stock">Stock</label>
            <input type="number" name="stock" id="stock" class="form-control" 
                   value="<?php echo $product->getStock(); ?>" min="0">
        </div>
        
        <!-- Campos específicos para Producto Físico -->
        <div id="weight_field" style="display: <?php echo $product->getProductType() === 'physical' ? 'block' : 'none'; ?>;">
            <div class="form-group">
                <label for="weight">Peso (kg)</label>
                <input type="number" name="weight" id="weight" class="form-control" 
                       step="0.01" min="0" 
                       value="<?php echo method_exists($product, 'getWeight') ? $product->getWeight() : ''; ?>">
            </div>
        </div>
        
        <!-- Campos específicos para Producto Digital -->
        <div id="download_link_field" style="display: <?php echo $product->getProductType() === 'digital' ? 'block' : 'none'; ?>;">
            <div class="form-group">
                <label for="download_link">Enlace de Descarga</label>
                <input type="url" name="download_link" id="download_link" class="form-control"
                       value="<?php echo method_exists($product, 'getDownloadLink') ? htmlspecialchars($product->getDownloadLink()) : ''; ?>">
            </div>
        </div>
        
        <!-- Campos específicos para Servicio -->
        <div id="duration_field" style="display: <?php echo $product->getProductType() === 'service' ? 'block' : 'none'; ?>;">
            <div class="form-group">
                <label for="duration_hours">Duración (horas)</label>
                <input type="number" name="duration_hours" id="duration_hours" class="form-control" 
                       min="1" 
                       value="<?php echo method_exists($product, 'getDurationHours') ? $product->getDurationHours() : 1; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="image_url">URL de Imagen (opcional)</label>
            <input type="url" name="image_url" id="image_url" class="form-control" 
                   value="<?php echo htmlspecialchars($product->getImageUrl()); ?>">
        </div>
        
        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-success">💾 Actualizar Producto</button>
            <a href="index.php?page=products" class="btn btn-secondary">❌ Cancelar</a>
        </div>
    </form>
</div>