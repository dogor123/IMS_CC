<?php
// Verificar que sea administrador
if (!$currentUser->isAdmin()) {
    echo '<div class="card">';
    echo '<div class="alert alert-error">⛔ No tienes permisos para acceder a esta sección.</div>';
    echo '<a href="index.php" class="btn btn-primary">← Volver al Inicio</a>';
    echo '</div>';
    return;
}

require_once __DIR__ . '/../../controllers/AuthController.php';

$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    $fullName = trim($_POST['full_name']);
    $role = $_POST['role'];
    
    // Validaciones
    if (empty($username) || empty($password) || empty($email) || empty($fullName)) {
        $errorMessage = "Todos los campos son obligatorios";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "Las contraseñas no coinciden";
    } elseif (strlen($password) < 6) {
        $errorMessage = "La contraseña debe tener al menos 6 caracteres";
    } else {
        $result = $authController->register($username, $password, $email, $fullName, $role);
        
        if ($result['success']) {
            header('Location: index.php?page=users&success=created');
            exit;
        } else {
            $errorMessage = $result['message'];
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>➕ Agregar Nuevo Usuario</h2>
    </div>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="username">Nombre de Usuario *</label>
                <input type="text" name="username" id="username" class="form-control" 
                       required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                       placeholder="usuario123">
                <small>Solo letras, números y guiones bajos</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" class="form-control" 
                       required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="usuario@ejemplo.com">
            </div>
        </div>
        
        <div class="form-group">
            <label for="full_name">Nombre Completo *</label>
            <input type="text" name="full_name" id="full_name" class="form-control" 
                   required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                   placeholder="Juan Pérez García">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="password">Contraseña *</label>
                <input type="password" name="password" id="password" class="form-control" 
                       required minlength="6" placeholder="Mínimo 6 caracteres">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña *</label>
                <input type="password" name="confirm_password" id="confirm_password" 
                       class="form-control" required minlength="6" placeholder="Repite la contraseña">
            </div>
        </div>
        
        <div class="form-group">
            <label for="role">Rol del Usuario *</label>
            <select name="role" id="role" class="form-control" required>
                <option value="employee" <?php echo (($_POST['role'] ?? '') === 'employee') ? 'selected' : ''; ?>>
                    👔 Empleado - Acceso básico al sistema
                </option>
                <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>
                    👑 Administrador - Acceso completo
                </option>
            </select>
            <small>Los empleados pueden gestionar productos y pedidos. Los administradores tienen acceso completo.</small>
        </div>
        
        <div style="margin-top: 25px;">
            <button type="submit" class="btn btn-success">💾 Crear Usuario</button>
            <a href="index.php?page=users" class="btn btn-secondary">❌ Cancelar</a>
        </div>
    </form>
</div>

<script>
// Validar que las contraseñas coincidan
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});
</script>