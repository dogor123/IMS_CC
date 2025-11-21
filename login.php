<?php
require_once __DIR__ . '/controllers/AuthController.php';

$authController = new AuthController();

// Si ya esta autenticado, redirigir al dashboard
if ($authController->isAuthenticated()) {
    header('Location: index.php');
    exit;
}

$errorMessage = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $errorMessage = 'Por favor completa todos los campos';
    } else {
        $result = $authController->login($username, $password);
        
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Inventarios</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
            max-width: 450px;
            width: 100%;
            animation: fadeIn 0.5s ease-in;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #667eea;
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
        }
        
        .logo {
            font-size: 4em;
            margin-bottom: 10px;
        }
        
        .credentials-info {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .credentials-info h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1em;
        }
        
        .credentials-info p {
            margin: 5px 0;
            font-size: 0.9em;
        }
        
        .credentials-info code {
            background: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box fade-in">
            <div class="login-header">
                <div class="logo">🏪</div>
                <h1>MOAL - Sistema de Inventarios</h1>
                <p>Inicia sesión para continuar</p>
            </div>
            
            <?php if ($errorMessage): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            
            <div class="credentials-info">
                <h3>👤 Credenciales de Prueba</h3>
                <p><strong>Administrador:</strong></p>
                <p>Usuario: <code>admin</code> | Contraseña: <code>password</code></p>
                <p style="margin-top: 10px;"><strong>Empleado:</strong></p>
                <p>Usuario: <code>empleado</code> | Contraseña: <code>password</code></p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" 
                           name="username" 
                           id="username" 
                           class="form-control" 
                           required 
                           autofocus
                           placeholder="Ingresa tu usuario"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="form-control" 
                           required
                           placeholder="Ingresa tu contraseña">
                </div>
                
                <div class="form-group" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1em;">
                        🔐 Iniciar Sesión
                    </button>
                </div>
            </form>

        </div>
    </div>
    
    <script>
        // Auto-focus en el campo de usuario
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>