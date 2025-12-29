// Carrito de compras
let carrito = [];
let pedidoActual = null;

// Función para actualizar el contador del carrito
function actualizarContadorCarrito() {
    const contador = document.querySelector('.cart-count');
    if (contador) {
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                contador.textContent = data.count;
                // Animación del contador
                contador.style.animation = 'none';
                contador.offsetHeight; // Trigger reflow
                contador.style.animation = 'scaleIn 0.3s ease-out';
            });
    }
}

// Función para agregar productos al carrito
function agregarAlCarrito(id, nombre, precio) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            nombre: nombre,
            precio: precio
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarContadorCarrito();
            mostrarMensaje('Producto agregado al carrito');
        } else {
            mostrarMensaje('Error al agregar el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Error al agregar el producto');
    });
}

// Función para actualizar la cantidad de un producto
function actualizarCantidad(index, change) {
    fetch('update_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            index: index,
            change: change
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarContadorCarrito();
            
            if (data.removed) {
                // Si el producto fue eliminado, recargar la página
                location.reload();
            } else {
                // Actualizar la cantidad y los totales en la página
                const itemElement = document.querySelector(`.cart-item:nth-child(${index + 1})`);
                if (itemElement) {
                    const quantityElement = itemElement.querySelector('.item-quantity');
                    const priceElement = itemElement.querySelector('.item-price');
                    const totalElement = document.querySelector('.cart-total span');
                    
                    if (quantityElement) quantityElement.textContent = data.quantity;
                    if (priceElement) priceElement.textContent = `$${data.subtotal}`;
                    if (totalElement) totalElement.textContent = `$${data.total}`;
                }
            }
        } else {
            mostrarMensaje('Error al actualizar la cantidad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Error al actualizar la cantidad');
    });
}

// Función para actualizar la visualización del carrito
function actualizarCarrito() {
    const carritoItems = document.getElementById('carrito-items');
    const carritoTotal = document.getElementById('carrito-total');
    
    if (!carritoItems) return; // Si no estamos en la página del carrito
    
    carritoItems.innerHTML = '';
    let total = 0;

    carrito.forEach((item, index) => {
        const itemTotal = item.precio * item.cantidad;
        total += itemTotal;

        const itemElement = document.createElement('div');
        itemElement.className = 'carrito-item';
        itemElement.innerHTML = `
            <div class="carrito-item-content">
                <div class="carrito-item-info">
                    <span class="item-nombre">${item.nombre}</span>
                    <span class="item-cantidad">x ${item.cantidad}</span>
                </div>
                <div class="carrito-item-precio">
                    ${formatearPrecio(itemTotal)}
                </div>
            </div>
            <button onclick="eliminarDelCarrito(${index})" class="btn-eliminar">
                <i class="fas fa-trash"></i>
            </button>
        `;
        carritoItems.appendChild(itemElement);
    });

    carritoTotal.innerHTML = `
        <div class="carrito-total-content">
            <span>Total:</span>
            <span>${formatearPrecio(total)}</span>
        </div>
    `;
}

// Función para eliminar items del carrito
function eliminarDelCarrito(index) {
    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            index: index
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarContadorCarrito();
            if (window.location.pathname.includes('carrito.php')) {
                location.reload();
            }
        }
    });
}

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

// Función para mostrar/ocultar campos de tarjeta
function toggleCardFields() {
    const metodoPago = document.getElementById('metodo-pago');
    const cardFields = document.getElementById('card-fields');
    
    if (metodoPago.value === 'tarjeta') {
        cardFields.style.display = 'block';
    } else {
        cardFields.style.display = 'none';
    }
}

// Función para procesar el pago
function procesarPago(event) {
    event.preventDefault();
    
    // Mostrar overlay de carga
    const loadingOverlay = document.querySelector('.loading-overlay');
    const loadingSteps = document.querySelectorAll('.loading-step');
    loadingOverlay.style.display = 'flex';
    
    // Función para actualizar los pasos
    function updateStep(stepIndex) {
        loadingSteps.forEach((step, index) => {
            const icon = step.querySelector('i');
            if (index < stepIndex) {
                icon.className = 'fas fa-check-circle';
            } else if (index === stepIndex) {
                icon.className = 'fas fa-spinner fa-spin';
            } else {
                icon.className = 'fas fa-clock';
            }
        });
    }
    
    // Simular el proceso de pago
    updateStep(0);
    setTimeout(() => {
        updateStep(1);
        setTimeout(() => {
            updateStep(2);
            setTimeout(() => {
                // Enviar el formulario
                document.getElementById('payment-form').submit();
            }, 1000);
        }, 1000);
    }, 1000);
}

// Función para generar PDF del recibo
function generarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Agregar contenido al PDF
    doc.setFontSize(20);
    doc.text('Pizza Express - Recibo', 20, 20);
    
    doc.setFontSize(12);
    doc.text('Fecha: ' + new Date().toLocaleDateString(), 20, 40);
    
    // Obtener los items del carrito desde el DOM
    const items = document.querySelectorAll('.cart-item');
    let y = 60;
    
    items.forEach(item => {
        const nombre = item.querySelector('.item-name').textContent;
        const cantidad = item.querySelector('.item-quantity').textContent;
        const precio = item.querySelector('.item-price').textContent;
        doc.text(`${nombre} ${cantidad} - ${precio}`, 20, y);
        y += 10;
    });
    
    // Agregar total
    const total = document.querySelector('.cart-total span').textContent;
    doc.text(`Total: ${total}`, 20, y + 10);
    
    // Guardar el PDF
    doc.save('recibo-pizza-express.pdf');
}

// Función para manejar las animaciones al hacer scroll
function handleScrollAnimations() {
    const sections = document.querySelectorAll('section');
    const footerSections = document.querySelectorAll('.footer-section');
    
    sections.forEach(section => {
        const sectionTop = section.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (sectionTop < windowHeight * 0.75) {
            section.classList.add('visible');
        }
    });

    footerSections.forEach(section => {
        const sectionTop = section.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (sectionTop < windowHeight * 0.75) {
            section.classList.add('visible');
        }
    });
}

// Scroll Reveal Animation
function revealOnScroll() {
    const elements = document.querySelectorAll('.scroll-reveal');
    
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementTop < windowHeight - 100) {
            element.classList.add('active');
        }
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar contador del carrito
    actualizarContadorCarrito();
    
    // Smooth scroll para los enlaces de navegación
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Observar el scroll para las animaciones
    window.addEventListener('scroll', handleScrollAnimations);
    
    // Trigger inicial para las animaciones
    handleScrollAnimations();

    // Manejar cambio de método de pago
    const metodoPago = document.getElementById('metodo-pago');
    if (metodoPago) {
        metodoPago.addEventListener('change', toggleCardFields);
    }

    // Add scroll-reveal class to sections
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        section.classList.add('scroll-reveal');
    });

    // Add scroll-reveal class to menu items
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.classList.add('scroll-reveal');
    });

    // Add scroll-reveal class to payment methods
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(method => {
        method.classList.add('scroll-reveal');
    });

    // Initial check for elements in view
    revealOnScroll();

    // Add scroll event listener
    window.addEventListener('scroll', revealOnScroll);

    // Actualizar estado inmediatamente al cargar la página
    actualizarEstadoPedido();
});

function mostrarConfirmacion(datos) {
    const orderDetails = document.getElementById('order-details');
    const total = datos.items.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    
    orderDetails.innerHTML = `
        <div class="client-info">
            <p><strong>Nombre:</strong> ${datos.nombre}</p>
            <p><strong>Email:</strong> ${datos.email}</p>
            <p><strong>Teléfono:</strong> ${datos.telefono}</p>
            <p><strong>Dirección:</strong> ${datos.direccion}</p>
        </div>
        <div class="order-items">
            <h3>Detalles del Pedido</h3>
            ${datos.items.map(item => `
                <div class="order-item">
                    <span>${item.nombre} x${item.cantidad}</span>
                    <span>${formatearPrecio(item.precio * item.cantidad)}</span>
                </div>
            `).join('')}
            <div class="order-total">
                <span>Total:</span>
                <span>${formatearPrecio(total)}</span>
            </div>
        </div>
    `;
    
    document.getElementById('order-confirmation').style.display = 'block';
    document.body.classList.add('order-confirmation-active');
}

function cerrarConfirmacion() {
    document.getElementById('order-confirmation').style.display = 'none';
    document.body.classList.remove('order-confirmation-active');
}

function formatearPrecio(precio) {
    return `$${Math.round(precio)} MXN`;
}

// Función para actualizar el estado del pedido
function actualizarEstadoPedido() {
    const orderCards = document.querySelectorAll('.order-card');
    if (orderCards.length === 0) return;

    orderCards.forEach(card => {
        const orderId = card.dataset.orderId;
        if (!orderId) return;

        fetch(`get_order_status.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    const statusElement = card.querySelector('.status');
                    if (statusElement) {
                        statusElement.className = `status ${data.status.toLowerCase()}`;
                        statusElement.textContent = data.status;
                    }
                }
            })
            .catch(error => console.error('Error al actualizar estado:', error));
    });
}

// Iniciar actualización automática cada 2 minutos
setInterval(actualizarEstadoPedido, 120000); 