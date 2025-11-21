<?php
require_once __DIR__ . '/../../controllers/OrderController.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

$orderController = new OrderController();
$productController = new ProductController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderItemsData = json_decode($_POST['order_items_data'], true);
    
    if (empty($orderItemsData)) {
        $errorMessage = "Debe agregar al menos un producto al pedido";
    } else {
        $result = $orderController->createOrder(
            $_POST['customer_name'],
            $_POST['customer_email'],
            $orderItemsData,
            $_POST['notes'] ?? ''
        );
        
        if ($result['success']) {
            header('Location: index.php?page=orders&success=created&order=' . $result['order_number']);
            exit;
        } else {
            $errorMessage = "Error al crear el pedido: " . $result['error'];
        }
    }
}

$products = $productController->getAllProducts();
?>

<div class="card">
    <div class="card-header">
        <h2>➕ Crear Nuevo Pedido</h2>
    </div>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <div class="alert alert-info">
        <strong>🏗️ Patrón Builder:</strong> Este formulario utiliza el patrón Builder para construir 
        pedidos complejos paso a paso de forma fluida y estructurada.
    </div>
    
    <form method="POST" onsubmit="return validateOrderForm()">
        <div class="card" style="background: #f8f9fa;">
            <h3>📋 Información del Cliente</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="customer_name">Nombre del Cliente *</label>
                    <input type="text" name="customer_name" id="customer_name" 
                           class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="customer_email">Email del Cliente *</label>
                    <input type="email" name="customer_email" id="customer_email" 
                           class="form-control" required>
                </div>
            </div>
        </div>
        
        <div class="card" style="background: #f8f9fa; margin-top: 20px;">
            <h3>🛍️ Agregar Productos</h3>
            
            <?php if (empty($products)): ?>
                <div class="alert alert-error">
                    No hay productos disponibles. <a href="index.php?page=product_create">Crear producto</a>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; align-items: end;">
                    <div class="form-group" style="margin: 0;">
                        <label for="product_id">Producto</label>
                        <select id="product_id" class="form-control">
                            <option value="">Seleccione un producto</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product->getId(); ?>" 
                                        data-price="<?php echo $product->getSalePrice(); ?>"
                                        data-stock="<?php echo $product->getStock(); ?>"
                                        data-type="<?php echo $product->getProductType(); ?>">
                                    <?php echo htmlspecialchars($product->getName()); ?> 
                                    - $<?php echo number_format($product->getSalePrice(), 2); ?>
                                    <?php if ($product->getProductType() === 'physical'): ?>
                                        (Stock: <?php echo $product->getStock(); ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin: 0;">
                        <label for="quantity">Cantidad</label>
                        <input type="number" id="quantity" class="form-control" 
                               value="1" min="1">
                    </div>
                    
                    <button type="button" class="btn btn-success" 
                            onclick="addProductToOrder()" style="margin-bottom: 0;">
                        ➕ Agregar
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="order_items_display" style="margin-top: 20px;">
            <!-- Los productos agregados aparecerán aquí -->
        </div>
        
        <input type="hidden" name="order_items_data" id="order_items_data" value="[]">
        
        <div class="card" style="background: #f8f9fa; margin-top: 20px;">
            <div class="form-group">
                <label for="notes">Notas del Pedido (opcional)</label>
                <textarea name="notes" id="notes" class="form-control" 
                          rows="3" placeholder="Instrucciones especiales, comentarios, etc."></textarea>
            </div>
        </div>
        
        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-primary" id="submit_order_btn" disabled>
                💾 Crear Pedido
            </button>
            <a href="index.php?page=orders" class="btn btn-secondary">❌ Cancelar</a>
        </div>
    </form>
</div>

<style>
/* Estilos adicionales para la tabla de items del pedido */
#order_items_display table {
    margin-top: 15px;
}
#order_items_display h3 {
    color: #667eea;
    margin-bottom: 10px;
}
</style>