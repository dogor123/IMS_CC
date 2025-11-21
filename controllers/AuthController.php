<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $session;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->session = Session::getInstance();
    }
    
    /**
     * Intenta autenticar al usuario
     */
    public function login($username, $password) {
        // Buscar usuario
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $userData = $stmt->fetch();
        
        if (!$userData) {
            // Verificar si el usuario existe pero está inactivo
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $inactiveUser = $stmt->fetch();
            
            if ($inactiveUser) {
                return ['success' => false, 'message' => 'Usuario inactivo. Contacta al administrador.'];
            }
            
            return ['success' => false, 'message' => 'Usuario no encontrado. Verifica que escribiste correctamente: ' . $username];
        }
        
        // Debug: Log del intento de login
        error_log("Login attempt - Username: $username");
        error_log("Password from DB (first 20 chars): " . substr($userData['password'], 0, 20));
        error_log("Password verify result: " . (password_verify($password, $userData['password']) ? 'TRUE' : 'FALSE'));
        
        // Verificar contraseña
        if (!password_verify($password, $userData['password'])) {
            return ['success' => false, 'message' => 'Contraseña incorrecta. Asegúrate de escribir: admin123'];
        }
        
        // Crear usuario
        $user = new User($userData);
        
        // Guardar en sesión
        $this->session->set('user_id', $user->getId());
        $this->session->set('username', $user->getUsername());
        $this->session->set('full_name', $user->getFullName());
        $this->session->set('user_role', $user->getRole());
        $this->session->set('user_email', $user->getEmail());
        
        // Actualizar último login
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user->getId()]);
        
        // Crear sesión en BD
        $this->createSessionToken($user->getId());
        
        return ['success' => true, 'user' => $user];
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        $userId = $this->session->getUserId();
        
        if ($userId) {
            // Eliminar tokens de sesión
            $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE user_id = ?");
            $stmt->execute([$userId]);
        }
        
        $this->session->destroy();
        return true;
    }
    
    /**
     * Verifica si el usuario está autenticado
     */
    public function isAuthenticated() {
        return $this->session->isLoggedIn();
    }
    
    /**
     * Obtiene el usuario actual
     */
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$this->session->getUserId()]);
        $userData = $stmt->fetch();
        
        return $userData ? new User($userData) : null;
    }
    
    /**
     * Registra un nuevo usuario
     */
    public function register($username, $password, $email, $fullName, $role = 'employee') {
        // Verificar si el usuario ya existe
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'El usuario o email ya existe'];
        }
        
        // Hash de la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar usuario
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)"
        );
        
        if ($stmt->execute([$username, $hashedPassword, $email, $fullName, $role])) {
            return ['success' => true, 'message' => 'Usuario creado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al crear el usuario'];
    }
    
    /**
     * Crea un token de sesión
     */
    private function createSessionToken($userId) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = $this->db->prepare(
            "INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        $stmt->execute([
            $userId,
            $token,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            $expiresAt
        ]);
        
        $this->session->set('session_token', $token);
        return $token;
    }
    
    /**
     * Verifica si el usuario tiene un rol específico
     */
    public function hasRole($role) {
        return $this->session->getUserRole() === $role;
    }
    
    /**
     * Verifica si el usuario es administrador
     */
    public function isAdmin() {
        return $this->hasRole('admin');
    }
    
    /**
     * Requiere autenticación - redirige al login si no está autenticado
     */
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Requiere rol de administrador
     */
    public function requireAdmin() {
        $this->requireAuth();
        
        if (!$this->isAdmin()) {
            $this->session->setFlash('error', 'No tienes permisos para acceder a esta sección');
            header('Location: index.php');
            exit;
        }
    }
}