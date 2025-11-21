<?php
// Configuración base
define('BASE_URL', './');

// Requerir autenticación
require_once __DIR__ . '/controllers/AuthController.php';
$authController = new AuthController();

// Verificar si está autenticado, si no redirigir al login
if (!$authController->isAuthenticated()) {
    header('Location: login.php');
    exit;
}

// Obtener usuario actual
$currentUser = $authController->getCurrentUser();

// Verificar que el usuario existe
if (!$currentUser) {
    // Si por alguna razón no se puede obtener el usuario, cerrar sesión
    $authController->logout();
    header('Location: login.php');
    exit;
}

// Determinar qué página mostrar
$page = $_GET['page'] ?? 'home';

// Título de la página
$pageTitle = 'Sistema de Gestión de Inventarios';

// Incluir header
include __DIR__ . '/views/layout/header.php';

// Rutas de las páginas
switch($page) {
    case 'home':
        include __DIR__ . '/views/home.php';
        break;
    
    case 'products':
        include __DIR__ . '/views/products/index.php';
        break;
    
    case 'product_create':
        include __DIR__ . '/views/products/create.php';
        break;
    
    case 'product_edit':
        include __DIR__ . '/views/products/edit.php';
        break;
    
    case 'orders':
        include __DIR__ . '/views/orders/index.php';
        break;
    
    case 'order_create':
        include __DIR__ . '/views/orders/create.php';
        break;
    
    case 'patterns':
        include __DIR__ . '/views/patterns_demo.php';
        break;
    
    case 'users':
        include __DIR__ . '/views/users/index.php';
        break;
    
    case 'user_create':
        include __DIR__ . '/views/users/create.php';
        break;
    
    case 'user_edit':
        include __DIR__ . '/views/users/edit.php';
        break;
    
    default:
        echo '<div class="card">';
        echo '<h2>Página no encontrada</h2>';
        echo '<p>La página que buscas no existe.</p>';
        echo '<a href="index.php" class="btn btn-primary">Volver al inicio</a>';
        echo '</div>';
        break;
}

// Incluir footer
include __DIR__ . '/views/layout/footer.php';