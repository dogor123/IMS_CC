<?php
require_once __DIR__ . '/../../config/Database.php';

$db = Database::getInstance()->getConnection();

// Verificar que el ID existe
if (!isset($_GET['id'])) {
    header('Location: index.php?page=users');
    exit;
}

$userId = $_GET['id'];

// Verificar permisos: solo admin o el propio usuario
$canEdit = $currentUser->isAdmin() || $currentUser->getId() == $userId;

if (!$canEdit) {
    echo '<div class="card">';
    echo '<div class="alert alert-error">⛔ No tienes permisos para editar este usuario.</div>';
    echo '<a href="index.php" class="btn btn-primary">← Volver al Inicio</a>';
    echo '</div>';
    return;
}

// Obtener datos del usuario
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: index.php?page=users&error=not_found');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $fullName = trim($_POST['full_name']);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Solo admins pueden cambiar el rol
    $role = $currentUser->isAdmin() ? $_POST['role'] : $user['role'];
    
    // Validaciones
    if (empty($email) || empty($fullName)) {
        $errorMessage = "Email y nombre completo son obligatorios";
    } elseif (!empty($password) && $password !== $confirmPassword) {
        $errorMessage = "Las contraseñas no coinciden";
    } elseif (!empty($password) && strlen($password) < 6) {
        $errorMessage = "La contraseña debe tener al menos 6 caracteres";
    } else {
        // Verificar si el email ya existe (excluyendo el usuario actual)
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        
        if ($stmt->fetchColumn() > 0) {
            $errorMessage = "El email ya está en uso por otro usuario";
        } else {
            // Preparar actualización
            if (!empty($password)) {
                // Actualizar con nueva contraseña
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare(
                    "UPDATE users SET email = ?, full_name = ?, password = ?, role = ? WHERE id = ?"
                );
                $result = $stmt->execute([$email, $fullName, $hashedPassword, $role, $userId]);
            } else {
                // Actualizar sin cambiar contraseña
                $stmt = $db->prepare(
                    "UPDATE users SET email = ?, full_name = ?, role = ? WHERE id = ?"
                );
                $result = $stmt->execute([$email, $fullName, $role, $userId]);
            }
            
            if ($result) {
                header('Location: index.php?page=users&success=updated');
                exit;
            } else {
                $errorMessage = "Error al actualizar el usuario";
            }
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>✏️ Editar Usuario</h2>
    </div>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <div class="alert alert-info">
        <strong>ℹ️ Nota:</strong> El nombre de usuario no se puede cambiar. 
        Deja el campo de contraseña vacío si no deseas cambiarla.
    </div>
    
    <form method="POST">
        <div class="form-group">
            <label>Nombre de Usuario</label>
            <input type="text" class="form-control" 
                   value="<?php echo htmlspecialchars($user['username']); ?>" 
                   disabled style="background: #f0f0f0;">
            <small>El nombre de usuario no se puede modificar</small>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" class="form-control" 
                       required value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="full_name">Nombre Completo *</label>
                <input type="text" name="full_name" id="full_name" class="form-control" 
                       required value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" 
                       minlength="6" placeholder="Dejar vacío para no cambiar">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Nueva Contraseña</label>
                <input type="password" name="confirm_password" id="confirm_password" 
                       class="form-control" minlength="6" placeholder="Repetir nueva contraseña">
            </div>
        </div>
        
        <?php if ($currentUser->isAdmin()): ?>
            <div class="form-group">
                <label for="role">Rol del Usuario *</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="employee" <?php echo ($user['role'] === 'employee') ? 'selected' : ''; ?>>
                        👔 Empleado
                    </option>
                    <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>
                        👑 Administrador
                    </option>
                </select>
            </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label>Información Adicional</label>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <p><strong>Fecha de creación:</strong> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
                <p><strong>Último acceso:</strong> 
                    <?php 
                    if ($user['last_login']) {
                        echo date('d/m/Y H:i', strtotime($user['last_login']));
                    } else {
                        echo 'Nunca';
                    }
                    ?>
                </p>
                <p><strong>Estado:</strong> 
                    <?php if ($user['is_active']): ?>
                        <span class="badge badge-completed">✅ Activo</span>
                    <?php else: ?>
                        <span class="badge badge-cancelled">❌ Inactivo</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-success">💾 Actualizar Usuario</button>
            <a href="index.php?page=users" class="btn btn-secondary">❌ Cancelar</a>
        </div>
    </form>
</div>

<script>
// Validar que las contraseñas coincidan
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && confirmPassword && password !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});

// Si se escribe en confirmar contraseña, requerir contraseña
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password');
    if (this.value) {
        password.required = true;
    }
});
</script>