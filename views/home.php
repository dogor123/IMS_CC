<?php
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/OrderController.php';

$productController = new ProductController();
$orderController = new OrderController();

$products = $productController->getAllProducts();
$orders = $orderController->getAllOrders();

// Calcular estadisticas
$totalProducts = count($products);
$totalOrders = count($orders);
$totalRevenue = array_sum(array_column($orders, 'total_amount'));
$lowStockProducts = array_filter($products, function($p) {
    return $p->getProductType() === 'physical' && $p->getStock() < 10;
});

// Contar por tipo de producto
$physicalCount = count(array_filter($products, fn($p) => $p->getProductType() === 'physical'));
$digitalCount = count(array_filter($products, fn($p) => $p->getProductType() === 'digital'));
$serviceCount = count(array_filter($products, fn($p) => $p->getProductType() === 'service'));
?>

<div class="card">
    <h2>🏠 Panel de Control</h2>
    <p>Bienvenido al sistema de gestión de inventarios</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $totalProducts; ?></h3>
        <p>📦 Total Productos</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $totalOrders; ?></h3>
        <p>🛒 Total Pedidos</p>
    </div>
    <div class="stat-card">
        <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
        <p>💰 Ingresos Totales</p>
    </div>
    <div class="stat-card">
        <h3><?php echo count($lowStockProducts); ?></h3>
        <p>⚠️ Stock Bajo</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <div class="card">
        <h3>📊 Distribución de Productos</h3>
        <div style="margin-top: 20px;">
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>📦 Productos Físicos</span>
                    <strong><?php echo $physicalCount; ?></strong>
                </div>
                <div style="background: #e0e0e0; height: 10px; border-radius: 5px;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                height: 100%; width: <?php echo $totalProducts > 0 ? ($physicalCount / $totalProducts * 100) : 0; ?>%; 
                                border-radius: 5px;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>💾 Productos Digitales</span>
                    <strong><?php echo $digitalCount; ?></strong>
                </div>
                <div style="background: #e0e0e0; height: 10px; border-radius: 5px;">
                    <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); 
                                height: 100%; width: <?php echo $totalProducts > 0 ? ($digitalCount / $totalProducts * 100) : 0; ?>%; 
                                border-radius: 5px;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>🛠️ Servicios</span>
                    <strong><?php echo $serviceCount; ?></strong>
                </div>
                <div style="background: #e0e0e0; height: 10px; border-radius: 5px;">
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); 
                                height: 100%; width: <?php echo $totalProducts > 0 ? ($serviceCount / $totalProducts * 100) : 0; ?>%; 
                                border-radius: 5px;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3>📈 Acciones Rápidas</h3>
        <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
            <a href="index.php?page=product_create" class="btn btn-primary" style="text-align: center;">
                ➕ Agregar Nuevo Producto
            </a>
            <a href="index.php?page=order_create" class="btn btn-success" style="text-align: center;">
                🛒 Crear Nuevo Pedido
            </a>
            <a href="index.php?page=products" class="btn btn-warning" style="text-align: center;">
                📦 Ver Todos los Productos
            </a>
            <a href="index.php?page=orders" class="btn btn-secondary" style="text-align: center;">
                📋 Ver Todos los Pedidos
            </a>
        </div>
    </div>
</div>

<?php if (count($lowStockProducts) > 0): ?>
<div class="card">
    <h3>⚠️ Productos con Stock Bajo</h3>
    <div class="alert alert-error">
        Los siguientes productos tienen menos de 10 unidades en stock:
    </div>
    <ul style="margin-top: 15px;">
        <?php foreach ($lowStockProducts as $product): ?>
            <li style="margin-bottom: 10px;">
                <strong><?php echo htmlspecialchars($product->getName()); ?></strong> 
                - Stock actual: <span style="color: #eb3349; font-weight: bold;"><?php echo $product->getStock(); ?></span>
                <a href="index.php?page=product_edit&id=<?php echo $product->getId(); ?>" 
                   style="margin-left: 10px; color: #667eea;">Editar</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>