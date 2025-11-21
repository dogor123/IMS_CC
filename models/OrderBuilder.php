<?php
require_once __DIR__ . '/Order.php';

/**
 * Patrón BUILDER
 * Construye objetos Order complejos paso a paso
 * Permite crear pedidos de forma fluida y legible
 */
class OrderBuilder {
    private $order;
    
    public function __construct() {
        $this->reset();
    }
    
    /**
     * Reinicia el builder con un nuevo pedido
     */
    public function reset() {
        $this->order = new Order();
        return $this;
    }
    
    /**
     * Establece la información del cliente
     * @param string $name Nombre del cliente
     * @param string $email Email del cliente
     * @return OrderBuilder
     */
    public function setCustomer($name, $email) {
        $this->order->setCustomerName($name);
        $this->order->setCustomerEmail($email);
        return $this;
    }
    
    /**
     * Agrega un producto al pedido
     * @param Product $product Producto a agregar
     * @param int $quantity Cantidad
     * @return OrderBuilder
     */
    public function addProduct($product, $quantity = 1) {
        $this->order->addItem($product, $quantity);
        return $this;
    }
    
    /**
     * Agrega múltiples productos al pedido
     * @param array $products Array de ['product' => Product, 'quantity' => int]
     * @return OrderBuilder
     */
    public function addProducts($products) {
        foreach ($products as $item) {
            $this->order->addItem($item['product'], $item['quantity']);
        }
        return $this;
    }
    
    /**
     * Establece el estado del pedido
     * @param string $status Estado (pending, processing, completed, cancelled)
     * @return OrderBuilder
     */
    public function setStatus($status) {
        $this->order->setStatus($status);
        return $this;
    }
    
    /**
     * Agrega notas al pedido
     * @param string $notes Notas adicionales
     * @return OrderBuilder
     */
    public function setNotes($notes) {
        $this->order->setNotes($notes);
        return $this;
    }
    
    /**
     * Construye y devuelve el pedido final
     * @return Order Pedido construido
     */
    public function build() {
        $order = $this->order;
        $this->reset(); // Prepara el builder para un nuevo pedido
        return $order;
    }
    
    /**
     * Obtiene el pedido actual sin resetear el builder
     * @return Order
     */
    public function getOrder() {
        return $this->order;
    }
}