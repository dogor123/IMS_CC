<?php
/**
 * Patrón DECORATOR
 * Permite agregar funcionalidades adicionales a objetos dinámicamente
 */

// Interfaz base para componentes de pedido
interface OrderComponent {
    public function getTotal();
    public function getDescription();
}

// Componente concreto base
class BasicOrder implements OrderComponent {
    private $subtotal;
    private $description;
    
    public function __construct($subtotal, $description = "Pedido básico") {
        $this->subtotal = $subtotal;
        $this->description = $description;
    }
    
    public function getTotal() {
        return $this->subtotal;
    }
    
    public function getDescription() {
        return $this->description;
    }
}

// Decorador base abstracto
abstract class OrderDecorator implements OrderComponent {
    protected $order;
    
    public function __construct(OrderComponent $order) {
        $this->order = $order;
    }
    
    public function getTotal() {
        return $this->order->getTotal();
    }
    
    public function getDescription() {
        return $this->order->getDescription();
    }
}

// Decorador: Agregar impuestos
class TaxDecorator extends OrderDecorator {
    private $taxRate;
    
    public function __construct(OrderComponent $order, $taxRate = 0.16) {
        parent::__construct($order);
        $this->taxRate = $taxRate;
    }
    
    public function getTotal() {
        $baseTotal = $this->order->getTotal();
        return $baseTotal + ($baseTotal * $this->taxRate);
    }
    
    public function getDescription() {
        $taxPercentage = $this->taxRate * 100;
        return $this->order->getDescription() . " + Impuesto ({$taxPercentage}%)";
    }
    
    public function getTaxAmount() {
        return $this->order->getTotal() * $this->taxRate;
    }
}

// Decorador: Agregar seguro
class InsuranceDecorator extends OrderDecorator {
    private $insuranceCost;
    
    public function __construct(OrderComponent $order, $insuranceCost = 10.0) {
        parent::__construct($order);
        $this->insuranceCost = $insuranceCost;
    }
    
    public function getTotal() {
        return $this->order->getTotal() + $this->insuranceCost;
    }
    
    public function getDescription() {
        return $this->order->getDescription() . " + Seguro (\${$this->insuranceCost})";
    }
    
    public function getInsuranceCost() {
        return $this->insuranceCost;
    }
}

// Decorador: Agregar embalaje especial
class GiftWrapDecorator extends OrderDecorator {
    private $wrapCost;
    
    public function __construct(OrderComponent $order, $wrapCost = 5.0) {
        parent::__construct($order);
        $this->wrapCost = $wrapCost;
    }
    
    public function getTotal() {
        return $this->order->getTotal() + $this->wrapCost;
    }
    
    public function getDescription() {
        return $this->order->getDescription() . " + Embalaje de regalo (\${$this->wrapCost})";
    }
    
    public function getWrapCost() {
        return $this->wrapCost;
    }
}

// Decorador: Agregar envío urgente
class UrgentShippingDecorator extends OrderDecorator {
    private $urgentFee;
    
    public function __construct(OrderComponent $order, $urgentFee = 25.0) {
        parent::__construct($order);
        $this->urgentFee = $urgentFee;
    }
    
    public function getTotal() {
        return $this->order->getTotal() + $this->urgentFee;
    }
    
    public function getDescription() {
        return $this->order->getDescription() . " + Envío urgente (\${$this->urgentFee})";
    }
    
    public function getUrgentFee() {
        return $this->urgentFee;
    }
}

// Decorador: Agregar propina
class TipDecorator extends OrderDecorator {
    private $tipPercentage;
    
    public function __construct(OrderComponent $order, $tipPercentage = 10) {
        parent::__construct($order);
        $this->tipPercentage = $tipPercentage;
    }
    
    public function getTotal() {
        $baseTotal = $this->order->getTotal();
        return $baseTotal + ($baseTotal * ($this->tipPercentage / 100));
    }
    
    public function getDescription() {
        return $this->order->getDescription() . " + Propina ({$this->tipPercentage}%)";
    }
    
    public function getTipAmount() {
        return $this->order->getTotal() * ($this->tipPercentage / 100);
    }
}