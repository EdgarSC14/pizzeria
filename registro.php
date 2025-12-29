<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Por favor complete todos los campos obligatorios';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Este correo electrónico ya está registrado';
        } else {
            // Incluir el password en la inserción
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, telefono, direccion) VALUES (?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$nombre, $email, $password, $telefono, $direccion]);
                $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
            } catch (PDOException $e) {
                $error = 'Error al registrar el usuario';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Pizza Express</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="auth.js" defer></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <i class="fas fa-pizza-slice"></i>
                Pizza Express
            </div>
            <ul>
                <li><a href="index.php#inicio">Inicio</a></li>
                <li><a href="index.php#nosotros">Nosotros</a></li>
                <li><a href="index.php#menu">Menú</a></li>
                <li><a href="index.php#organigrama">Organigrama</a></li>
                <li><a href="index.php#pagos">Pagos</a></li>
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <li><a href="perfil.php"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                <?php endif; ?>
                <li class="cart-icon">
                    <a href="carrito.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="register-container">
            <div class="register-box">
                <h2><i class="fas fa-user-plus"></i> Registro de Usuario</h2>
                <?php if ($error): ?>
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <form method="POST" action="registro.php">
                    <div class="form-group">
                        <label for="nombre"><i class="fas fa-user"></i> Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Correo Electrónico:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono"><i class="fas fa-phone"></i> Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono">
                    </div>
                    <div class="form-group">
                        <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección:</label>
                        <textarea id="direccion" name="direccion"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock"></i> Confirmar Contraseña:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="register-btn">
                        <span>Registrarse</span>
                        <i class="fas fa-user-plus"></i>
                    </button>
                </form>
                <p class="login-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Contacto</h3>
                <p><i class="fas fa-envelope"></i> info@pizzeria.com</p>
                <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                <p><i class="fas fa-map-marker-alt"></i> Calle Principal #123, Ciudad</p>
            </div>
            <div class="footer-section">
                <h3>Horarios</h3>
                <div class="hours">
                    <p><i class="fas fa-clock"></i> Lunes - Viernes: 11:00 AM - 10:00 PM</p>
                    <p><i class="fas fa-clock"></i> Sábado - Domingo: 12:00 PM - 11:00 PM</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Pizza Express. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html> 