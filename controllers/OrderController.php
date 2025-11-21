<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/OrderBuilder.php';
require_once __DIR__ . '/ProductController.php';

class OrderController {
    private $db;
    private $productController;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->productController = new ProductController();
    }
    
    /**
     * Crea un nuevo pedido usando el patrón Builder
     */
    public function createOrder($customerName, $customerEmail, $products, $notes = '') {
        try {
            $this->db->beginTransaction();
            
            // Usa el patrón Builder para construir el pedido
            $orderBuilder = new OrderBuilder();
            $orderBuilder->setCustomer($customerName, $customerEmail)
                        ->setNotes($notes);
            
            // Agrega productos al pedido
            foreach ($products as $item) {
                $product = $this->productController->getProductById($item['product_id']);
                if ($product) {
                    $orderBuilder->addProduct($product, $item['quantity']);
                }
            }
            
            $order = $orderBuilder->build();
            
            // Guarda el pedido en la base de datos
            $stmt = $this->db->prepare(
                "INSERT INTO orders (order_number, customer_name, customer_email, total_amount, status, notes) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            $stmt->execute([
                $order->getOrderNumber(),
                $order->getCustomerName(),
                $order->getCustomerEmail(),
                $order->getTotalAmount(),
                $order->getStatus(),
                $order->getNotes()
            ]);
            
            $orderId = $this->db->lastInsertId();
            
            // Guarda los items del pedido
            foreach ($order->getItems() as $item) {
                $stmt = $this->db->prepare(
                    "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) 
                     VALUES (?, ?, ?, ?, ?)"
                );
                
                $stmt->execute([
                    $orderId,
                    $item['product']->getId(),
                    $item['quantity'],
                    $item['unit_price'],
                    $item['subtotal']
                ]);
                
                // Actualiza el stock para productos físicos
                if ($item['product']->getProductType() === 'physical') {
                    $newStock = $item['product']->getStock() - $item['quantity'];
                    $this->db->prepare("UPDATE products SET stock = ? WHERE id = ?")
                             ->execute([$newStock, $item['product']->getId()]);
                }
            }
            
            $this->db->commit();
            return ['success' => true, 'order_id' => $orderId, 'order_number' => $order->getOrderNumber()];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Obtiene todos los pedidos
     */
    public function getAllOrders() {
        $stmt = $this->db->query(
            "SELECT o.*, COUNT(oi.id) as item_count 
             FROM orders o 
             LEFT JOIN order_items oi ON o.id = oi.order_id 
             GROUP BY o.id 
             ORDER BY o.created_at DESC"
        );
        return $stmt->fetchAll();
    }
    
    /**
     * Obtiene un pedido por ID con sus items
     */
    public function getOrderById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        
        if ($order) {
            $stmt = $this->db->prepare(
                "SELECT oi.*, p.name as product_name, p.product_type 
                 FROM order_items oi 
                 JOIN products p ON oi.product_id = p.id 
                 WHERE oi.order_id = ?"
            );
            $stmt->execute([$id]);
            $order['items'] = $stmt->fetchAll();
        }
        
        return $order;
    }
    
    /**
     * Actualiza el estado de un pedido
     */
    public function updateOrderStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
    
    /**
     * Elimina un pedido
     */
    public function deleteOrder($id) {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$id]);
    }
}