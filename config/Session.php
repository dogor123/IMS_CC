<?php
/**
 * Patrón SINGLETON para manejo de sesiones - Garantiza una única instancia del gestor de sesiones
 */
class Session {
    private static $instance = null;
    private $sessionStarted = false;
    
    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->sessionStarted = true;
        }
    }
    
    private function __clone() {}
    
    public function __wakeup() {
        throw new Exception("No se puede deserializar un Singleton");
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public function destroy() {
        if ($this->sessionStarted) {
            session_destroy();
            $_SESSION = [];
        }
    }
    
    public function isLoggedIn() {
        return $this->has('user_id') && $this->has('username');
    }
    
    public function getUserId() {
        return $this->get('user_id');
    }
    
    public function getUsername() {
        return $this->get('username');
    }
    
    public function getUserRole() {
        return $this->get('user_role');
    }
    
    public function getFullName() {
        return $this->get('full_name');
    }
    
    public function setFlash($key, $message) {
        $this->set('flash_' . $key, $message);
    }
    
    public function getFlash($key) {
        $message = $this->get('flash_' . $key);
        $this->remove('flash_' . $key);
        return $message;
    }
}