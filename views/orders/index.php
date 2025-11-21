<?php
require_once __DIR__ . '/../../controllers/OrderController.php';

$orderController = new OrderController();

// Manejar cambio de estado
if (isset($_GET['update_status']) && isset($_GET['status'])) {
    if ($orderController->updateOrderStatus($_GET['update_status'], $_GET['status'])) {
        $successMessage = "Estado del pedido actualizado exitosamente";
    }
}

// Manejar eliminación
if (isset($_GET['delete'])) {
    if ($orderController->deleteOrder($_GET['delete'])) {
        $successMessage = "Pedido eliminado exitosamente";
    } else {
        $errorMessage = "Error al eliminar el pedido";
    }
}

$orders = $orderController->getAllOrders();
?>

<div class="card">
    <div class="card-header">
        <h2>🛒 Lista de Pedidos</h2>
    </div>
    
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <div style="margin-bottom: 20px;">
        <a href="index.php?page=order_create" class="btn btn-primary">➕ Crear Nuevo Pedido</a>
        <input type="text" id="searchInput" class="form-control" 
               style="max-width: 300px; display: inline-block; margin-left: 15px;" 
               placeholder="Buscar pedidos..." onkeyup="filterTable('searchInput', 'ordersTable')">
    </div>
    
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            No hay pedidos registrados. ¡Comienza creando tu primer pedido!
        </div>
    <?php else: ?>
        <div class="table-container">
            <table id="ordersTable">
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Total</th>
                        <th>Items</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                            <td class="price">$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo $order['item_count']; ?></td>
                            <td>
                                <?php
                                $statusNames = [
                                    'pending' => '⏳ Pendiente',
                                    'processing' => '⚙️ Procesando',
                                    'completed' => '✅ Completado',
                                    'cancelled' => '❌ Cancelado'
                                ];
                                $badgeClass = 'badge-' . $order['status'];
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $statusNames[$order['status']] ?? $order['status']; ?>
                                </span>
                                
                                <!-- Mini formulario para cambiar estado -->
                                <select onchange="location = '?page=orders&update_status=<?php echo $order['id']; ?>&status=' + this.value" 
                                        style="margin-top: 5px; padding: 5px; border-radius: 5px; border: 1px solid #ddd;">
                                    <option value="">Cambiar...</option>
                                    <?php foreach (['pending', 'processing', 'completed', 'cancelled'] as $status): ?>
                                        <?php if ($status !== $order['status']): ?>
                                            <option value="<?php echo $status; ?>">
                                                <?php echo $statusNames[$status]; ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="?page=orders&delete=<?php echo $order['id']; ?>" 
                                   class="btn btn-danger" style="padding: 8px 12px;"
                                   onclick="return confirmDelete('¿Eliminar este pedido?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            <?php
            $totalAmount = array_sum(array_column($orders, 'total_amount'));
            $completedOrders = count(array_filter($orders, function($o) { return $o['status'] === 'completed'; }));
            ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo count($orders); ?></h3>
                    <p>Total Pedidos</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $completedOrders; ?></h3>
                    <p>Completados</p>
                </div>
                <div class="stat-card">
                    <h3>$<?php echo number_format($totalAmount, 2); ?></h3>
                    <p>Valor Total</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>