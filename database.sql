-- Base de datos para Sistema de Gestión de Inventarios
CREATE DATABASE IF NOT EXISTS inventario_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario_db;

-- Tabla de productos
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    product_type ENUM('physical', 'digital', 'service') NOT NULL,
    cost_price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    sku VARCHAR(100) UNIQUE,
    image_url VARCHAR(255),
    weight DECIMAL(8, 2) NULL COMMENT 'Para productos físicos',
    download_link VARCHAR(255) NULL COMMENT 'Para productos digitales',
    duration_hours INT NULL COMMENT 'Para servicios',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de pedidos
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de items de pedidos
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Datos de ejemplo
INSERT INTO products (name, description, product_type, cost_price, sale_price, stock, sku, weight) VALUES
('Laptop Dell XPS 13', 'Laptop ultradelgada de alto rendimiento', 'physical', 800.00, 1200.00, 15, 'LAP-DELL-001', 1.2),
('Mouse Inalámbrico Logitech', 'Mouse ergonómico con conectividad Bluetooth', 'physical', 15.00, 35.00, 50, 'MOU-LOG-001', 0.1),
('Teclado Mecánico RGB', 'Teclado gaming con switches mecánicos', 'physical', 45.00, 89.99, 30, 'KEY-MEC-001', 0.8);

INSERT INTO products (name, description, product_type, cost_price, sale_price, stock, sku, download_link) VALUES
('Curso de PHP Avanzado', 'Curso completo de desarrollo web con PHP', 'digital', 20.00, 99.00, 999, 'CURSO-PHP-001', 'https://example.com/curso-php'),
('eBook: Patrones de Diseño', 'Libro digital sobre patrones de software', 'digital', 5.00, 29.99, 999, 'EBOOK-PAT-001', 'https://example.com/ebook-patrones');

INSERT INTO products (name, description, product_type, cost_price, sale_price, stock, sku, duration_hours) VALUES
('Consultoría de Software', 'Asesoría personalizada en desarrollo de software', 'service', 30.00, 100.00, 999, 'SERV-CONS-001', 2),
('Soporte Técnico Premium', 'Soporte técnico prioritario 24/7', 'service', 50.00, 150.00, 999, 'SERV-SUPP-001', 1);

-- Tabla de usuarios para autenticación
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') DEFAULT 'employee',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Usuario administrador por defecto (password: admin123)
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@inventario.com', 'Administrador del Sistema', 'admin'),
('empleado', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'empleado@inventario.com', 'Empleado de Ventas', 'employee');

-- Tabla de sesiones activas
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla para el patrón Observer (notificaciones)
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);