<?php
require_once __DIR__ . '/Product.php';

/**
 * Producto Físico - Implementación concreta del patrón Factory Method
 */
class PhysicalProduct extends Product {
    private $weight;
    
    public function __construct($data = []) {
        parent::__construct($data);
        $this->weight = $data['weight'] ?? 0;
        $this->productType = 'physical';
    }
    
    public function getSpecificInfo() {
        return [
            'type' => 'Producto Físico',
            'weight' => $this->weight,
            'requires_shipping' => true,
            'shipping_cost' => $this->calculateShippingCost()
        ];
    }
    
    private function calculateShippingCost() {
        // Cálculo simple basado en peso
        return $this->weight * 2.5;
    }
    
    public function getWeight() { return $this->weight; }
    public function setWeight($weight) { $this->weight = $weight; }
}