<?php
session_start();
require_once 'config/database.php';

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Procesar el pedido si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        // Crear el pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
        $total = array_sum(array_map(function($item) { return $item['precio'] * $item['cantidad']; }, $_SESSION['carrito']));
        $stmt->execute([$_SESSION['usuario_id'], $total]);
        $pedido_id = $pdo->lastInsertId();

        // Insertar los detalles del pedido
        $stmt = $pdo->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        foreach ($_SESSION['carrito'] as $item) {
            $subtotal = $item['precio'] * $item['cantidad'];
            $stmt->execute([$pedido_id, $item['id'], $item['cantidad'], $item['precio'], $subtotal]);
        }

        $pdo->commit();
        $_SESSION['carrito'] = []; // Limpiar el carrito
        header('Location: confirmacion.php?pedido_id=' . $pedido_id);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Error al procesar el pedido';
    }
}

// Debug: Mostrar contenido del carrito
$debug_carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Pizza Express</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        .mensaje-flotante {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 2000;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
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
                        <span class="cart-count"><?php echo count($_SESSION['carrito']); ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="cart-page">
        <div id="overlay" class="overlay"></div>
        <div id="loading-overlay" class="loading-overlay">
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="loading-text">Procesando tu pedido...</div>
                <div class="loading-steps">
                    <div class="loading-step">
                        <i class="fas fa-spinner fa-spin"></i> Preparando tu pedido
                    </div>
                    <div class="loading-step">
                        <i class="fas fa-clock"></i> Finalizando el proceso
                    </div>
                </div>
            </div>
        </div>
        <section class="cart-summary">
            <h2>Resumen del Pedido</h2>
            <div id="carrito-items">
                <?php if (!empty($_SESSION['carrito'])): ?>
                    <?php foreach ($_SESSION['carrito'] as $index => $item): ?>
                        <div class="cart-item">
                            <div class="item-info">
                                <span class="item-name"><?php echo htmlspecialchars($item['nombre']); ?></span>
                                <div class="quantity-controls">
                                    <button class="quantity-btn" onclick="actualizarCantidad(<?php echo $index; ?>, -1)">-</button>
                                    <span class="item-quantity"><?php echo $item['cantidad']; ?></span>
                                    <button class="quantity-btn" onclick="actualizarCantidad(<?php echo $index; ?>, 1)">+</button>
                                </div>
                                <span class="item-price">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></span>
                                <button class="delete-btn" onclick="eliminarDelCarrito(<?php echo $index; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="cart-total">
                        <strong>Total:</strong>
                        <span>$<?php echo number_format(array_sum(array_map(function($item) { return $item['precio'] * $item['cantidad']; }, $_SESSION['carrito'])), 2); ?></span>
                    </div>
                <?php else: ?>
                    <p>Tu carrito está vacío</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="payment-form">
            <h2>Información de Pago</h2>
            <form id="payment-form" method="POST" action="carrito.php">
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección de entrega</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="metodo-pago">Método de pago</label>
                    <select id="metodo-pago" name="metodo-pago" required onchange="toggleCardFields()">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                    </select>
                </div>
                <div id="card-fields" style="display: none;">
                    <div class="form-group">
                        <label for="numero-tarjeta">Número de tarjeta</label>
                        <input type="text" id="numero-tarjeta" name="numero-tarjeta" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label for="fecha-vencimiento">Fecha de vencimiento</label>
                        <input type="text" id="fecha-vencimiento" name="fecha-vencimiento" placeholder="MM/AA" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" maxlength="3">
                    </div>
                </div>
                <button type="submit" class="btn" id="submit-order">Realizar Pedido</button>
            </form>
        </section>
    </main>

    <!-- Overlay de carga -->
    <div class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Procesando tu pedido...</div>
            <div class="loading-steps">
                <div class="loading-step">
                    <i class="fas fa-spinner fa-spin"></i> Preparando tu pedido
                </div>
                <div class="loading-step">
                    <i class="fas fa-clock"></i> Finalizando el proceso
                </div>
            </div>
        </div>
    </div>

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
            <p>&copy; 2024 Pizzeria. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Función para mostrar mensajes
        function mostrarMensaje(mensaje) {
            const mensajeDiv = document.createElement('div');
            mensajeDiv.className = 'mensaje-flotante';
            mensajeDiv.textContent = mensaje;
            document.body.appendChild(mensajeDiv);
            
            setTimeout(() => {
                mensajeDiv.remove();
            }, 3000);
        }

        // Agregar evento click al botón de realizar pedido
        document.getElementById('submit-order').addEventListener('click', function(e) {
            const carritoVacio = <?php echo empty($_SESSION['carrito']) ? 'true' : 'false'; ?>;
            
            if (carritoVacio) {
                e.preventDefault();
                mostrarMensaje('No puedes realizar un pedido con el carrito vacío');
                return false;
            }
        });

        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Verificar si el carrito está vacío
            const carritoVacio = <?php echo empty($_SESSION['carrito']) ? 'true' : 'false'; ?>;
            
            if (carritoVacio) {
                mostrarMensaje('No puedes realizar un pedido con el carrito vacío');
                return;
            }
            
            // Mostrar el overlay y la animación de carga
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'flex';
            
            // Simular el proceso de preparación
            setTimeout(() => {
                const steps = document.querySelectorAll('.loading-step');
                steps[0].querySelector('i').className = 'fas fa-check-circle';
            }, 1500);
            
            // Simular el proceso de finalización
            setTimeout(() => {
                const steps = document.querySelectorAll('.loading-step');
                steps[1].querySelector('i').className = 'fas fa-check-circle';
                
                // Enviar el formulario después de mostrar todas las animaciones
                setTimeout(() => {
                    this.submit();
                }, 1000);
            }, 3000);
        });
    </script>
</body>
</html> 