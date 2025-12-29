<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Obtener pedidos del usuario
$stmt = $pdo->prepare("
    SELECT p.*, 
           COUNT(dp.id) as total_items,
           GROUP_CONCAT(CONCAT(pr.nombre, ' x', dp.cantidad) SEPARATOR ', ') as items
    FROM pedidos p
    JOIN detalles_pedido dp ON p.id = dp.pedido_id
    JOIN productos pr ON dp.producto_id = pr.id
    WHERE p.usuario_id = ?
    GROUP BY p.id
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute([$_SESSION['usuario_id']]);
$pedidos = $stmt->fetchAll();

// Procesar actualización de perfil
$mensaje = '';
$error = '';
$mensaje_password = '';
$error_password = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar actualización de datos del perfil
    if (isset($_POST['action']) && $_POST['action'] == 'update_profile') {
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
        $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
        
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $telefono, $direccion, $_SESSION['usuario_id']]);
            $mensaje = 'Perfil actualizado correctamente';
            
            // Actualizar información del usuario
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['usuario_id']]);
            $usuario = $stmt->fetch();
            
            // Actualizar el nombre en la sesión
            $_SESSION['usuario_nombre'] = $nombre;
        } catch (PDOException $e) {
            $error = 'Error al actualizar el perfil';
        }
    }
    
    // Procesar actualización de contraseña
    if (isset($_POST['action']) && $_POST['action'] == 'update_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_password = 'Por favor complete todos los campos de contraseña';
        } elseif ($new_password !== $confirm_password) {
            $error_password = 'Las nuevas contraseñas no coinciden';
        } elseif ($current_password !== $usuario['password']) {
            $error_password = 'La contraseña actual es incorrecta';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->execute([$new_password, $_SESSION['usuario_id']]);
                $mensaje_password = 'Contraseña actualizada correctamente';
                
                // Limpiar los campos del formulario
                $current_password = '';
                $new_password = '';
                $confirm_password = '';
            } catch (PDOException $e) {
                $error_password = 'Error al actualizar la contraseña';
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
    <title>Mi Perfil - Pizza Express</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <i class="fas fa-pizza-slice"></i>
                Pizza Express
            </div>
            <ul>
                <li><a href="index.php">Inicio</a></li>
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

    <main class="profile-page">
        <div class="profile-container">
            <div class="profile-section">
                <h2>Mi Perfil</h2>
                <?php if ($mensaje): ?>
                    <div class="success-message"><?php echo htmlspecialchars($mensaje); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="perfil.php" class="profile-form">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                    </div>
                    <button type="submit" class="btn">Actualizar Perfil</button>
                </form>
                
                <hr class="profile-divider">
                
                <h3>Cambiar Contraseña</h3>
                <?php if ($mensaje_password): ?>
                    <div class="success-message"><?php echo htmlspecialchars($mensaje_password); ?></div>
                <?php endif; ?>
                <?php if ($error_password): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_password); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="perfil.php" class="password-form">
                    <input type="hidden" name="action" value="update_password">
                    <div class="form-group">
                        <label for="current_password">Contraseña Actual</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Nueva Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn">Cambiar Contraseña</button>
                </form>
            </div>

            <div class="orders-section">
                <h2>Mis Pedidos</h2>
                <?php if (empty($pedidos)): ?>
                    <p>No tienes pedidos realizados</p>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($pedidos as $pedido): ?>
                            <div class="order-card" data-order-id="<?php echo $pedido['id']; ?>">
                                <div class="order-header">
                                    <h3>Pedido #<?php echo $pedido['id']; ?></h3>
                                    <span class="order-date"><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></span>
                                </div>
                                <div class="order-details">
                                    <p><strong>Estado:</strong> <?php echo $pedido['estado']; ?></p>
                                    <p><strong>Total:</strong> $<?php echo number_format($pedido['total'], 2); ?></p>
                                    <p><strong>Items:</strong> <?php echo htmlspecialchars($pedido['items']); ?></p>
                                </div>
                                <a href="confirmacion.php?pedido_id=<?php echo $pedido['id']; ?>" class="btn">Ver Detalles</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
    </footer>
</body>
</html> 