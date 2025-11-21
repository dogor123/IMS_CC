<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistema de Gestión de Inventarios'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css">
</head>
<body>
    <div class="main-container fade-in">
        <header class="header">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h1>🏪 MOAL - Sistema de Gestión de Inventarios</h1>
                    <p>Gestión inteligente de tienda para la tienda MOAL</p>
                </div>
                <?php if (isset($currentUser) && $currentUser): ?>
                <div style="text-align: right;">
                    <p style="margin-bottom: 5px;">
                        👤 <strong><?php echo htmlspecialchars($currentUser->getFullName()); ?></strong>
                    </p>
                    <p style="font-size: 0.9em; opacity: 0.9; margin-bottom: 10px;">
                        <?php echo $currentUser->isAdmin() ? '👑 Administrador' : '👔 Empleado'; ?>
                    </p>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-danger" style="padding: 8px 16px;">
                        🚪 Cerrar Sesión
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </header>
        
        <nav class="nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($_GET['page'])) ? 'active' : ''; ?>">📊 Inicio</a></li>
                <li><a href="<?php echo BASE_URL; ?>index.php?page=products" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'products') ? 'active' : ''; ?>">📦 Productos</a></li>
                <li><a href="<?php echo BASE_URL; ?>index.php?page=orders" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'orders') ? 'active' : ''; ?>">🛒 Pedidos</a></li>
                 <?php if (isset($currentUser) && $currentUser && $currentUser->isAdmin()): ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php?page=users" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'users') ? 'active' : ''; ?>">👥 Usuarios</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <main>