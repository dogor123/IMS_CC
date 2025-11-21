<?php
/**
 * Clase Order - Representa un pedido complejo
 * Utilizada con el patrón Builder
 */
class Order {
    private $id;
    private $orderNumber;
    private $customerName;
    private $customerEmail;
    private $totalAmount;
    private $status;
    private $notes;
    private $items = [];
    private $createdAt;
    
    public function __construct() {
        $this->orderNumber = $this->generateOrderNumber();
        $this->status = 'pending';
        $this->totalAmount = 0;
    }
    
    private function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
    
    public function addItem($product, $quantity) {
        $this->items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'unit_price' => $product->getSalePrice(),
            'subtotal' => $product->getSalePrice() * $quantity
        ];
        $this->calculateTotal();
    }
    
    private function calculateTotal() {
        $this->totalAmount = 0;
        foreach ($this->items as $item) {
            $this->totalAmount += $item['subtotal'];
        }
    }
    
    // Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getOrderNumber() { return $this->orderNumber; }
    
    public function getCustomerName() { return $this->customerName; }
    public function setCustomerName($name) { $this->customerName = $name; }
    
    public function getCustomerEmail() { return $this->customerEmail; }
    public function setCustomerEmail($email) { $this->customerEmail = $email; }
    
    public function getTotalAmount() { return $this->totalAmount; }
    
    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }
    
    public function getNotes() { return $this->notes; }
    public function setNotes($notes) { $this->notes = $notes; }
    
    public function getItems() { return $this->items; }
    
    public function getCreatedAt() { return $this->createdAt; }
    public function setCreatedAt($date) { $this->createdAt = $date; }
}