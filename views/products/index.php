<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$productController = new ProductController();

// Manejar eliminacion
if (isset($_GET['delete'])) {
    if ($productController->deleteProduct($_GET['delete'])) {
        $successMessage = "Producto eliminado exitosamente";
    } else {
        $errorMessage = "Error al eliminar el producto";
    }
}

// Manejar clonacion (Patron Prototype)
if (isset($_GET['clone'])) {
    $result = $productController->cloneProduct($_GET['clone']);
    if ($result['success']) {
        $successMessage = "
            <strong>✅ Producto clonado exitosamente usando el Patrón Prototype</strong><br><br>
            📋 El producto ha sido duplicado con las siguientes características:<br>
            • <strong>Nuevo SKU:</strong> " . htmlspecialchars($result['sku']) . "<br>
            • <strong>Nombre:</strong> Se agregó el sufijo '(Copia)'<br>
            • <strong>ID único:</strong> Generado automáticamente<br>
            • <strong>Contenido:</strong> Idéntico al producto original<br><br>
            <small>💡 Ahora puedes editar el producto clonado para ajustar los detalles necesarios.</small>
        ";
    } else {
        $errorMessage = $result['message'];
    }
}

$products = $productController->getAllProducts();
?>

<div class="card">
    <div class="card-header">
        <h2>📦 Lista de Productos</h2>
    </div>
    
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <div style="margin-bottom: 20px;">
        <a href="index.php?page=product_create" class="btn btn-primary">➕ Agregar Nuevo Producto</a>
        <input type="text" id="searchInput" class="form-control" style="max-width: 300px; display: inline-block; margin-left: 15px;" 
               placeholder="Buscar productos..." onkeyup="filterTable('searchInput', 'productsTable')">
    </div>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            No hay productos registrados. ¡Comienza agregando tu primer producto!
        </div>
    <?php else: ?>
        
        <div class="table-container">
            <table id="productsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>SKU</th>
                        <th>Tipo</th>
                        <th>Precio Costo</th>
                        <th>Precio Venta</th>
                        <th>Margen</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product->getId(); ?></td>
                            <td><strong><?php echo htmlspecialchars($product->getName()); ?></strong></td>
                            <td><?php echo htmlspecialchars($product->getSku()); ?></td>
                            <td>
                                <?php
                                $type = $product->getProductType();
                                $badgeClass = 'badge-' . $type;
                                $typeNames = [
                                    'physical' => '📦 Físico',
                                    'digital' => '💾 Digital',
                                    'service' => '🛠️ Servicio'
                                ];
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $typeNames[$type] ?? $type; ?>
                                </span>
                            </td>
                            <td>$<?php echo number_format($product->getCostPrice(), 2); ?></td>
                            <td class="price">$<?php echo number_format($product->getSalePrice(), 2); ?></td>
                            <td class="profit-margin">
                                <?php echo number_format($product->getProfitMargin(), 1); ?>%
                            </td>
                            <td>
                                <?php 
                                $stock = $product->getStock();
                                if ($product->getProductType() === 'physical') {
                                    echo $stock;
                                    if ($stock < 10) {
                                        echo ' <span style="color: #eb3349;">⚠️</span>';
                                    }
                                } else {
                                    echo '<span style="color: #11998e;">∞</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="index.php?page=product_edit&id=<?php echo $product->getId(); ?>" 
                                   class="btn btn-primary" style="padding: 8px 12px; margin-right: 5px;">
                                   ✏️ Editar
                                </a>
                                <a href="?page=products&clone=<?php echo $product->getId(); ?>" 
                                   class="btn btn-success" style="padding: 8px 12px; margin-right: 5px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);"
                                   title="Clonar producto usando Patrón Prototype"
                                   onclick="return confirm('¿Deseas clonar este producto?\n\nSe creará una copia con:\n• Mismo contenido\n• Nuevo SKU automático\n• Nombre con sufijo (Copia)')">
                                   <strong>📋 Clonar</strong>
                                </a>
                                <a href="?page=products&delete=<?php echo $product->getId(); ?>" 
                                   class="btn btn-danger" style="padding: 8px 12px;"
                                   onclick="return confirmDelete('¿Eliminar este producto?')">
                                   🗑️ Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; color: #666;">
            <p><strong>Total de productos:</strong> <?php echo count($products); ?></p>
        </div>
    <?php endif; ?>
</div>