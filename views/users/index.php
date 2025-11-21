<?php
// Verificar que sea administrador
if (!$currentUser->isAdmin()) {
    echo '<div class="card">';
    echo '<div class="alert alert-error">⛔ No tienes permisos para acceder a esta sección. Solo los administradores pueden gestionar usuarios.</div>';
    echo '<a href="index.php" class="btn btn-primary">← Volver al Inicio</a>';
    echo '</div>';
    return;
}

require_once __DIR__ . '/../../config/Database.php';

$db = Database::getInstance()->getConnection();

// Manejar eliminación
if (isset($_GET['delete']) && $_GET['delete'] != $currentUser->getId()) {
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$_GET['delete']])) {
        $successMessage = "Usuario eliminado exitosamente";
    } else {
        $errorMessage = "Error al eliminar el usuario";
    }
}

// Manejar cambio de estado
if (isset($_GET['toggle_status']) && $_GET['toggle_status'] != $currentUser->getId()) {
    $stmt = $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
    if ($stmt->execute([$_GET['toggle_status']])) {
        $successMessage = "Estado del usuario actualizado";
    }
}

// Obtener todos los usuarios
$stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h2>👥 Gestión de Usuarios</h2>
    </div>
    
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php 
            switch($_GET['success']) {
                case 'created':
                    echo '✅ Usuario creado exitosamente';
                    break;
                case 'updated':
                    echo '✅ Usuario actualizado exitosamente';
                    break;
                default:
                    echo '✅ Operación realizada exitosamente';
            }
            ?>
        </div>
    <?php endif; ?>
    
    <div style="margin-bottom: 20px;">
        <a href="index.php?page=user_create" class="btn btn-primary">➕ Agregar Nuevo Usuario</a>
        <input type="text" id="searchInput" class="form-control" style="max-width: 300px; display: inline-block; margin-left: 15px;" 
               placeholder="Buscar usuarios..." onkeyup="filterTable('searchInput', 'usersTable')">
    </div>
    
    <?php if (empty($users)): ?>
        <div class="alert alert-info">
            No hay usuarios registrados.
        </div>
    <?php else: ?>
        <div class="table-container">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Nombre Completo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge badge-physical">👑 Administrador</span>
                                <?php else: ?>
                                    <span class="badge badge-digital">👔 Empleado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="badge badge-completed">✅ Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-cancelled">❌ Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if ($user['last_login']) {
                                    echo date('d/m/Y H:i', strtotime($user['last_login']));
                                } else {
                                    echo '<span style="color: #999;">Nunca</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($user['id'] != $currentUser->getId()): ?>
                                    <a href="index.php?page=user_edit&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-warning" style="padding: 8px 12px; margin-right: 5px;">
                                        ✏️ Editar
                                    </a>
                                    
                                    <a href="?page=users&toggle_status=<?php echo $user['id']; ?>" 
                                       class="btn <?php echo $user['is_active'] ? 'btn-secondary' : 'btn-success'; ?>" 
                                       style="padding: 8px 12px; margin-right: 5px;"
                                       onclick="return confirm('¿Cambiar el estado de este usuario?')">
                                        <?php echo $user['is_active'] ? '🔒 Desactivar' : '🔓 Activar'; ?>
                                    </a>
                                    
                                    <a href="?page=users&delete=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger" style="padding: 8px 12px;"
                                       onclick="return confirmDelete('¿Eliminar este usuario? Esta acción no se puede deshacer.')">
                                        🗑️ Eliminar
                                    </a>
                                <?php else: ?>
                                    <span class="badge badge-processing">👤 Tú</span>
                                    <a href="index.php?page=user_edit&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-warning" style="padding: 8px 12px;">
                                        ✏️ Editar Perfil
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo count($users); ?></h3>
                    <p>Total Usuarios</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($users, fn($u) => $u['role'] === 'admin')); ?></h3>
                    <p>Administradores</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($users, fn($u) => $u['role'] === 'employee')); ?></h3>
                    <p>Empleados</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($users, fn($u) => $u['is_active'])); ?></h3>
                    <p>Usuarios Activos</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>