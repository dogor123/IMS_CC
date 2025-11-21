<?php
/**
 * Patrón STATE
 * Permite que un objeto altere su comportamiento cuando su estado interno cambia
 */

// Contexto: Pedido con estados
class OrderContext {
    private $state;
    private $orderId;
    private $orderNumber;
    
    public function __construct($orderId, $orderNumber, $currentState = 'pending') {
        $this->orderId = $orderId;
        $this->orderNumber = $orderNumber;
        $this->setState($currentState);
    }
    
    public function setState($stateName) {
        switch($stateName) {
            case 'pending':
                $this->state = new PendingState();
                break;
            case 'processing':
                $this->state = new ProcessingState();
                break;
            case 'completed':
                $this->state = new CompletedState();
                break;
            case 'cancelled':
                $this->state = new CancelledState();
                break;
            default:
                $this->state = new PendingState();
        }
    }
    
    public function getState() {
        return $this->state;
    }
    
    public function getOrderId() {
        return $this->orderId;
    }
    
    public function getOrderNumber() {
        return $this->orderNumber;
    }
    
    // Delegamos las acciones al estado actual
    public function process() {
        return $this->state->process($this);
    }
    
    public function complete() {
        return $this->state->complete($this);
    }
    
    public function cancel() {
        return $this->state->cancel($this);
    }
    
    public function getAvailableActions() {
        return $this->state->getAvailableActions();
    }
    
    public function getStatusInfo() {
        return $this->state->getStatusInfo();
    }
}

// Interfaz de estado
interface OrderState {
    public function process(OrderContext $context);
    public function complete(OrderContext $context);
    public function cancel(OrderContext $context);
    public function getAvailableActions();
    public function getStatusInfo();
    public function getStateName();
}

// Estado: Pendiente
class PendingState implements OrderState {
    public function process(OrderContext $context) {
        $context->setState('processing');
        return ['success' => true, 'message' => 'Pedido en procesamiento'];
    }
    
    public function complete(OrderContext $context) {
        return ['success' => false, 'message' => 'No se puede completar un pedido pendiente. Primero debe procesarse.'];
    }
    
    public function cancel(OrderContext $context) {
        $context->setState('cancelled');
        return ['success' => true, 'message' => 'Pedido cancelado'];
    }
    
    public function getAvailableActions() {
        return ['process', 'cancel'];
    }
    
    public function getStatusInfo() {
        return [
            'name' => 'Pendiente',
            'icon' => '⏳',
            'color' => '#f093fb',
            'description' => 'El pedido está esperando ser procesado'
        ];
    }
    
    public function getStateName() {
        return 'pending';
    }
}

// Estado: Procesando
class ProcessingState implements OrderState {
    public function process(OrderContext $context) {
        return ['success' => false, 'message' => 'El pedido ya está en procesamiento'];
    }
    
    public function complete(OrderContext $context) {
        $context->setState('completed');
        return ['success' => true, 'message' => 'Pedido completado exitosamente'];
    }
    
    public function cancel(OrderContext $context) {
        $context->setState('cancelled');
        return ['success' => true, 'message' => 'Pedido cancelado'];
    }
    
    public function getAvailableActions() {
        return ['complete', 'cancel'];
    }
    
    public function getStatusInfo() {
        return [
            'name' => 'Procesando',
            'icon' => '⚙️',
            'color' => '#fa709a',
            'description' => 'El pedido está siendo preparado'
        ];
    }
    
    public function getStateName() {
        return 'processing';
    }
}

// Estado: Completado
class CompletedState implements OrderState {
    public function process(OrderContext $context) {
        return ['success' => false, 'message' => 'El pedido ya está completado'];
    }
    
    public function complete(OrderContext $context) {
        return ['success' => false, 'message' => 'El pedido ya está completado'];
    }
    
    public function cancel(OrderContext $context) {
        return ['success' => false, 'message' => 'No se puede cancelar un pedido completado'];
    }
    
    public function getAvailableActions() {
        return [];
    }
    
    public function getStatusInfo() {
        return [
            'name' => 'Completado',
            'icon' => '✅',
            'color' => '#11998e',
            'description' => 'El pedido ha sido entregado exitosamente'
        ];
    }
    
    public function getStateName() {
        return 'completed';
    }
}

// Estado: Cancelado
class CancelledState implements OrderState {
    public function process(OrderContext $context) {
        return ['success' => false, 'message' => 'No se puede procesar un pedido cancelado'];
    }
    
    public function complete(OrderContext $context) {
        return ['success' => false, 'message' => 'No se puede completar un pedido cancelado'];
    }
    
    public function cancel(OrderContext $context) {
        return ['success' => false, 'message' => 'El pedido ya está cancelado'];
    }
    
    public function getAvailableActions() {
        return [];
    }
    
    public function getStatusInfo() {
        return [
            'name' => 'Cancelado',
            'icon' => '❌',
            'color' => '#eb3349',
            'description' => 'El pedido ha sido cancelado'
        ];
    }
    
    public function getStateName() {
        return 'cancelled';
    }
}