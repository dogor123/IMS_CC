<?php
/**
 * Patrón OBSERVER
 * Permite notificar a múltiples observadores cuando ocurre un evento
 */

// Interfaz Observer
interface Observer {
    public function update($subject, $event, $data);
}

// Interfaz Subject (Observable)
interface Subject {
    public function attach(Observer $observer);
    public function detach(Observer $observer);
    public function notify($event, $data);
}

// Implementación del Subject para el sistema de inventario
class InventorySubject implements Subject {
    private $observers = [];
    
    public function attach(Observer $observer) {
        $className = get_class($observer);
        if (!isset($this->observers[$className])) {
            $this->observers[$className] = $observer;
        }
    }
    
    public function detach(Observer $observer) {
        $className = get_class($observer);
        if (isset($this->observers[$className])) {
            unset($this->observers[$className]);
        }
    }
    
    public function notify($event, $data) {
        foreach ($this->observers as $observer) {
            $observer->update($this, $event, $data);
        }
    }
}

// Observer para notificaciones de stock bajo
class LowStockObserver implements Observer {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function update($subject, $event, $data) {
        if ($event === 'low_stock') {
            $this->createNotification($data);
        }
    }
    
    private function createNotification($data) {
        $message = "⚠️ Stock bajo: El producto '{$data['product_name']}' tiene solo {$data['stock']} unidades disponibles.";
        
        // Obtener todos los usuarios administradores
        $stmt = $this->db->query("SELECT id FROM users WHERE role = 'admin'");
        $admins = $stmt->fetchAll();
        
        foreach ($admins as $admin) {
            $stmt = $this->db->prepare(
                "INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)"
            );
            $stmt->execute([$admin['id'], 'low_stock', $message]);
        }
    }
}

// Observer para registro de auditoría
class AuditLogObserver implements Observer {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function update($subject, $event, $data) {
        $this->log($event, $data);
    }
    
    private function log($event, $data) {
        // En un sistema real, esto guardaría en una tabla de auditoría
        error_log("AUDIT: Event '{$event}' - " . json_encode($data));
    }
}

// Observer para notificaciones de pedidos
class OrderNotificationObserver implements Observer {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function update($subject, $event, $data) {
        if ($event === 'order_created') {
            $this->notifyOrderCreated($data);
        } elseif ($event === 'order_completed') {
            $this->notifyOrderCompleted($data);
        }
    }
    
    private function notifyOrderCreated($data) {
        $message = "🛒 Nuevo pedido #{$data['order_number']} creado por {$data['customer_name']} - Total: \${$data['total']}";
        
        $stmt = $this->db->query("SELECT id FROM users WHERE role = 'admin'");
        $admins = $stmt->fetchAll();
        
        foreach ($admins as $admin) {
            $stmt = $this->db->prepare(
                "INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)"
            );
            $stmt->execute([$admin['id'], 'order_created', $message]);
        }
    }
    
    private function notifyOrderCompleted($data) {
        $message = "✅ Pedido #{$data['order_number']} completado exitosamente.";
        
        $stmt = $this->db->query("SELECT id FROM users");
        $users = $stmt->fetchAll();
        
        foreach ($users as $user) {
            $stmt = $this->db->prepare(
                "INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)"
            );
            $stmt->execute([$user['id'], 'order_completed', $message]);
        }
    }
}