<?php
/**
 * Patrón SINGLETON - Garantiza una única instancia de conexión a la base de datos
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $database = 'inventario_db';
    private $username = 'root';
    private $password = '';
    
    // Constructor privado para evitar instanciación directa
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    // Evitar clonación del objeto
    private function __clone() {}
    
    // Evitar deserialización
    public function __wakeup() {
        throw new Exception("No se puede deserializar un Singleton");
    }
    
    // Método estático para obtener la única instancia
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Obtener la conexión PDO
    public function getConnection() {
        return $this->connection;
    }
}