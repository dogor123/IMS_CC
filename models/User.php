<?php
/**
 * Modelo de Usuario
 */
class User {
    private $id;
    private $username;
    private $email;
    private $fullName;
    private $role;
    private $isActive;
    private $lastLogin;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->fullName = $data['full_name'] ?? '';
        $this->role = $data['role'] ?? 'employee';
        $this->isActive = $data['is_active'] ?? true;
        $this->lastLogin = $data['last_login'] ?? null;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getFullName() { return $this->fullName; }
    public function getRole() { return $this->role; }
    public function isActive() { return $this->isActive; }
    public function getLastLogin() { return $this->lastLogin; }
    
    public function isAdmin() {
        return $this->role === 'admin';
    }
    
    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUsername($username) { $this->username = $username; }
    public function setEmail($email) { $this->email = $email; }
    public function setFullName($fullName) { $this->fullName = $fullName; }
    public function setRole($role) { $this->role = $role; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
    public function setLastLogin($lastLogin) { $this->lastLogin = $lastLogin; }
}