<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['pedido_id'])) {
    header('Location: index.php');
    exit;
}

$pedido_id = $_GET['pedido_id'];

// Obtener detalles del pedido
$stmt = $pdo->prepare("
    SELECT p.*, u.nombre as nombre_usuario, u.email, u.telefono, u.direccion,
           dp.cantidad, dp.precio_unitario, dp.subtotal,
           pr.nombre as nombre_producto
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN detalles_pedido dp ON p.id = dp.pedido_id
    JOIN productos pr ON dp.producto_id = pr.id
    WHERE p.id = ? AND p.usuario_id = ?
");
$stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
$detalles = $stmt->fetchAll();

if (empty($detalles)) {
    header('Location: index.php');
    exit;
}

$pedido = $detalles[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido - Pizza Express</title>
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
            </ul>
        </nav>
    </header>

    <main class="confirmation-page">
        <div class="confirmation-box">
            <h2><i class="fas fa-check-circle"></i> ¡Pedido Confirmado!</h2>
            <p>Gracias por tu compra. Tu pedido ha sido procesado correctamente.</p>
            
            <div class="order-details">
                <h3>Detalles del Pedido #<?php echo $pedido_id; ?></h3>
                <div class="customer-info">
                    <h4>Información del Cliente</h4>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['nombre_usuario']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['email']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['telefono']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion']); ?></p>
                </div>

                <div class="order-items">
                    <h4>Productos</h4>
                    <?php foreach ($detalles as $item): ?>
                        <div class="order-item">
                            <span class="item-name"><?php echo htmlspecialchars($item['nombre_producto']); ?></span>
                            <span class="item-quantity">x<?php echo $item['cantidad']; ?></span>
                            <span class="item-price">$<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="order-total">
                        <strong>Total:</strong>
                        <span>$<?php echo number_format($pedido['total'], 2); ?></span>
                    </div>
                </div>

                <div class="order-status">
                    <h4>Estado del Pedido</h4>
                    <p><strong>Estado actual:</strong> <?php echo $pedido['estado']; ?></p>
                    <p><strong>Fecha del pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                </div>
            </div>

            <div class="confirmation-actions">
                <button onclick="window.print()" class="btn"><i class="fas fa-print"></i> Imprimir Recibo</button>
                <a href="index.php" class="btn"><i class="fas fa-home"></i> Volver al Inicio</a>
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
    <script>
        function actualizarEstadoPedido() {
            const orderId = <?php echo $pedido_id; ?>;
            
            fetch(`get_order_status.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const estadoElement = document.querySelector('.order-status p strong').nextSibling;
                        estadoElement.textContent = ' ' + data.status;
                        
                        // Si el pedido no está entregado, continuar actualizando
                        if (data.status !== 'Entregado') {
                            setTimeout(actualizarEstadoPedido, 30000); // Actualizar cada 30 segundos
                        }
                    }
                })
                .catch(error => console.error('Error al actualizar el estado:', error));
        }

        function actualizarEstado(nuevoEstado) {
            const orderId = <?php echo $pedido_id; ?>;
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('new_status', nuevoEstado);

            fetch('update_order_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const estadoElement = document.querySelector('.order-status p strong').nextSibling;
                    estadoElement.textContent = ' ' + data.status;
                } else {
                    console.error('Error al actualizar el estado:', data.error);
                }
            })
            .catch(error => console.error('Error en la petición:', error));
        }

        // Iniciar la actualización automática cuando se carga la página
        document.addEventListener('DOMContentLoaded', () => {
            actualizarEstadoPedido();
        });
    </script>
</body>
</html> 