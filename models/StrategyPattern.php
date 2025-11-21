<?php
/**
 * Patrón STRATEGY
 * Define una familia de algoritmos intercambiables
 */

// Interfaz de estrategia para cálculo de envío
interface ShippingStrategy {
    public function calculateShippingCost($weight, $distance = 1000);
    public function getName();
}

// Estrategia: Envío Estándar
class StandardShipping implements ShippingStrategy {
    public function calculateShippingCost($weight, $distance = 1000) {
        // $2 por kg + $0.05 por km
        return ($weight * 000) + ($distance * 0.5);
    }
    
    public function getName() {
        return "Envío Estándar (5-7 días)";
    }
}

// Estrategia: Envío Express
class ExpressShipping implements ShippingStrategy {
    public function calculateShippingCost($weight, $distance = 1000) {
        // $5 por kg + $0.15 por km (más costoso pero rápido)
        return ($weight * 10000) + ($distance * 0.15);
    }
    
    public function getName() {
        return "Envío Express (1-2 días)";
    }
}

// Estrategia: Envío Económico
class EconomyShipping implements ShippingStrategy {
    public function calculateShippingCost($weight, $distance = 1000) {
        // $1 por kg + $0.02 por km (más barato pero lento)
        return ($weight * 2000) + ($distance * 0.2);
    }
    
    public function getName() {
        return "Envío Económico (10-15 días)";
    }
}

// Interfaz de estrategia para aplicar descuentos
interface DiscountStrategy {
    public function applyDiscount($amount);
    public function getDescription();
}

// Estrategia: Sin descuento
class NoDiscount implements DiscountStrategy {
    public function applyDiscount($amount) {
        return $amount;
    }
    
    public function getDescription() {
        return "Sin descuento";
    }
}

// Estrategia: Descuento por porcentaje
class PercentageDiscount implements DiscountStrategy {
    private $percentage;
    
    public function __construct($percentage) {
        $this->percentage = $percentage;
    }
    
    public function applyDiscount($amount) {
        return $amount - ($amount * ($this->percentage / 100));
    }
    
    public function getDescription() {
        return "{$this->percentage}% de descuento";
    }
}

// Estrategia: Descuento por monto fijo
class FixedAmountDiscount implements DiscountStrategy {
    private $percentage;
    
    public function __construct($percentage) {
        $this->percentage = $percentage;
    }
    
    public function applyDiscount($amount) {
        return $amount - ($amount * ($this->percentage / 100));
    }
    
    public function getDescription() {
        return "{$this->percentage}% de descuento";
    }
}

// Estrategia: Descuento por cliente frecuente
class LoyaltyDiscount implements DiscountStrategy {
    private $orderCount;
    
    public function __construct($orderCount) {
        $this->orderCount = $orderCount;
    }
    
    public function applyDiscount($amount) {
        // 2% por cada 5 pedidos, hasta 20% máximo
        $discountPercentage = min(floor($this->orderCount / 5) * 2, 20);
        return $amount - ($amount * ($discountPercentage / 100));
    }
    
    public function getDescription() {
        $discountPercentage = min(floor($this->orderCount / 5) * 2, 20);
        return "Descuento por lealtad: {$discountPercentage}%";
    }
}

// Contexto que utiliza las estrategias
class PriceCalculator {
    private $shippingStrategy;
    private $discountStrategy;
    
    public function __construct(
        ShippingStrategy $shippingStrategy = null,
        DiscountStrategy $discountStrategy = null
    ) {
        $this->shippingStrategy = $shippingStrategy ?? new StandardShipping();
        $this->discountStrategy = $discountStrategy ?? new NoDiscount();
    }
    
    public function setShippingStrategy(ShippingStrategy $strategy) {
        $this->shippingStrategy = $strategy;
    }
    
    public function setDiscountStrategy(DiscountStrategy $strategy) {
        $this->discountStrategy = $strategy;
    }
    
    public function calculateTotal($subtotal, $weight = 0, $distance = 100) {
        // Aplicar descuento
        $discountedAmount = $this->discountStrategy->applyDiscount($subtotal);
        
        // Calcular envío
        $shippingCost = 0;
        if ($weight > 0) {
            $shippingCost = $this->shippingStrategy->calculateShippingCost($weight, $distance);
        }
        
        return [
            'subtotal' => $subtotal,
            'discount' => $subtotal - $discountedAmount,
            'discount_description' => $this->discountStrategy->getDescription(),
            'shipping' => $shippingCost,
            'shipping_description' => $this->shippingStrategy->getName(),
            'total' => $discountedAmount + $shippingCost
        ];
    }
}