<?php
session_start();
require_once 'config/database.php';

// Obtener productos destacados
$stmt = $pdo->query("SELECT * FROM productos WHERE categoria = 'Pizza' AND disponible = 1 LIMIT 3");
$productos_destacados = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza Express - La mejor pizza de la ciudad</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <i class="fas fa-pizza-slice"></i>
                Pizza Express
            </div>
            <ul>
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#nosotros">Nosotros</a></li>
                <li><a href="#menu">Menú</a></li>
                <li><a href="#organigrama">Organigrama</a></li>
                <li><a href="#pagos">Pagos</a></li>
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
        <section id="inicio" class="hero" style="background-image: url('https://images.unsplash.com/photo-1513104890138-7c749659a591?q=80&w=2070&auto=format&fit=crop'); background-size: cover; background-position: center;">
            <div class="hero-overlay">
                <h1>Bienvenidos a Pizza Express</h1>
                <p>Las mejores pizzas artesanales de la ciudad</p>
            </div>
        </section>

        <section id="nosotros" class="about">
            <h2>Sobre Nosotros</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>Pizza Express es una pizzería familiar fundada en 2010, comprometida con la calidad y el sabor auténtico italiano. Nuestras pizzas están hechas con ingredientes frescos y masa artesanal preparada diariamente.</p>
                    <ul class="features">
                        <li><i class="fas fa-check"></i> Ingredientes frescos y de calidad</li>
                        <li><i class="fas fa-check"></i> Masa artesanal preparada diariamente</li>
                        <li><i class="fas fa-check"></i> Horno de leña tradicional</li>
                        <li><i class="fas fa-check"></i> Servicio rápido y eficiente</li>
                    </ul>
                </div>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3762.888481674835!2d-99.16726492345677!3d19.427020981872!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff35f5bd1563%3A0x6c366f0e2de02ff7!2sEl%20Moral%2C%20Ciudad%20de%20M%C3%A9xico%2C%20CDMX!5e0!3m2!1ses-419!2smx!4v1709850000000!5m2!1ses-419!2smx" 
                        width="100%" 
                        height="300" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>

        <section id="menu" class="menu">
            <h2>Nuestro Menú</h2>
            <div class="menu-grid">
                <?php foreach($productos_destacados as $producto): ?>
                <div class="menu-item">
                    <img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p class="price">$<?php echo number_format($producto['precio'], 2); ?> MXN</p>
                    <button onclick="agregarAlCarrito(<?php echo $producto['id']; ?>, '<?php echo htmlspecialchars($producto['nombre']); ?>', <?php echo $producto['precio']; ?>)">Agregar al carrito</button>
                </div>
                <?php endforeach; ?>
            </div>
            <a href="menu-completo.php" class="ver-mas-btn">Ver más productos</a>
        </section>

        <section id="organigrama" class="org-chart">
            <h2>Organigrama</h2>
            <div class="org-container">
                <div class="org-level">
                    <div class="org-box">Gerente General</div>
                </div>
                <div class="org-level">
                    <div class="org-box">Chef Principal</div>
                    <div class="org-box">Supervisor de Servicio</div>
                </div>
                <div class="org-level">
                    <div class="org-box">Pizzaiolos</div>
                    <div class="org-box">Meseros</div>
                    <div class="org-box">Cajeros</div>
                </div>
            </div>
        </section>

        <section id="pagos" class="payment">
            <h2>Métodos de Pago</h2>
            <div class="payment-methods">
                <div class="payment-method">
                    <i class="fas fa-credit-card"></i>
                    <h3>Tarjetas de Crédito/Débito</h3>
                    <p>Visa, MasterCard, American Express</p>
                </div>
                <div class="payment-method">
                    <i class="fas fa-money-bill-wave"></i>
                    <h3>Efectivo</h3>
                    <p>Aceptamos efectivo en todas nuestras sucursales</p>
                </div>
            </div>
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
            <p>&copy; 2025 Pizza Express. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html> 