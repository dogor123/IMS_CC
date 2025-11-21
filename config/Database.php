<?php
/**
 * Patrón SINGLETON - Conexión robusta para Docker
 */
class Database {
    private static $instance = null;
    private $connection;

    private $host;
    private $database;
    private $username;
    private $password;

    private function __construct() {
        // Variables de entorno desde docker-compose
        $this->host     = getenv("DB_HOST") ?: "db";
        $this->database = getenv("DB_NAME") ?: "inventario_db";
        $this->username = getenv("DB_USER") ?: "ims_user";
        $this->password = getenv("DB_PASS") ?: "ims_pass";

        // ======== RETRY PARA ESPERAR MYSQL ========
        $retries = 20;   // 20 intentos
        $delay   = 1;    // 1 segundo entre intentos

        while ($retries > 0) {
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
                // Conectó -> salimos
                break;

            } catch (PDOException $e) {
                $retries--;
                if ($retries === 0) {
                    die("Error de conexión final: " . $e->getMessage());
                }
                // Esperamos antes del siguiente intento
                sleep($delay);
            }
        }
        // ============================================
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

    public function getConnection() {
        return $this->connection;
    }
}
