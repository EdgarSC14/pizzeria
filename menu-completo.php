<?php
session_start();
require_once 'config/database.php';

// Obtener todas las categorías
$stmt = $pdo->query("SELECT DISTINCT categoria FROM productos WHERE disponible = 1");
$categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Obtener productos por categoría
$productos_por_categoria = [];
foreach ($categorias as $categoria) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE categoria = ? AND disponible = 1");
    $stmt->execute([$categoria]);
    $productos_por_categoria[$categoria] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Completo - Pizza Express</title>
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

    <main>
        <section class="menu-completo">
            <h1>Nuestro Menú Completo</h1>
            
            <?php foreach ($categorias as $categoria): ?>
            <div class="menu-category">
                <h2><?php echo htmlspecialchars($categoria); ?></h2>
                <div class="menu-grid">
                    <?php foreach ($productos_por_categoria[$categoria] as $producto): ?>
                    <div class="menu-item">
                        <img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <p class="price">$<?php echo number_format($producto['precio'], 2); ?> MXN</p>
                        <button onclick="agregarAlCarrito(<?php echo $producto['id']; ?>, '<?php echo htmlspecialchars($producto['nombre']); ?>', <?php echo $producto['precio']; ?>)">Agregar al carrito</button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
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
            <p>&copy; 2024 Pizza Express. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html> 