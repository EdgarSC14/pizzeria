-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 30, 2025 at 02:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pizzeria`
--

-- --------------------------------------------------------

--
-- Table structure for table `detalles_pedido`
--

CREATE TABLE `detalles_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detalles_pedido`
--

INSERT INTO `detalles_pedido` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 11, 1, 35.00, 35.00),
(2, 1, 12, 1, 35.00, 35.00),
(3, 2, 5, 1, 200.00, 200.00),
(4, 2, 6, 1, 280.00, 280.00),
(5, 2, 4, 1, 260.00, 260.00),
(6, 2, 3, 1, 240.00, 240.00),
(7, 2, 2, 1, 220.00, 220.00),
(8, 2, 1, 1, 180.00, 180.00),
(9, 2, 7, 1, 80.00, 80.00),
(10, 2, 8, 1, 120.00, 120.00),
(11, 2, 9, 1, 140.00, 140.00),
(12, 2, 10, 1, 160.00, 160.00),
(13, 2, 16, 1, 45.00, 45.00),
(14, 2, 15, 1, 40.00, 40.00),
(15, 2, 11, 1, 35.00, 35.00),
(16, 2, 12, 1, 35.00, 35.00),
(17, 2, 13, 1, 35.00, 35.00),
(18, 2, 14, 1, 25.00, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente',
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `total`, `estado`, `fecha_pedido`) VALUES
(1, 2, 70.00, 'Pendiente', '2025-06-30 11:49:13'),
(2, 2, 2095.00, 'Pendiente', '2025-06-30 12:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `categoria`, `imagen_url`, `disponible`, `created_at`) VALUES
(1, 'Pizza Margherita', 'Pizza clásica con tomate, mozzarella y albahaca', 180.00, 'Pizza', 'https://assets.tmecosys.com/image/upload/t_web_rdp_recipe_584x480/img/recipe/ras/Assets/4F1526F0-0A46-4C87-A3D5-E80AD76C0D70/Derivates/df9a8be7-6ab2-4d5a-8c4d-6cbe8aceda72.jpg', 1, '2025-06-30 11:39:56'),
(2, 'Pizza Pepperoni', 'Pizza con pepperoni, mozzarella y salsa de tomate', 220.00, 'Pizza', 'https://www.sortirambnens.com/wp-content/uploads/2019/02/pizza-de-peperoni.jpg', 1, '2025-06-30 11:39:56'),
(3, 'Pizza Hawaiana', 'Pizza con jamón, piña y mozzarella', 240.00, 'Pizza', 'https://cloudfront-us-east-1.images.arcpublishing.com/infobae/BZWVMJ2EA5HGPH7IY2S2AJ3NEI.jpg', 1, '2025-06-30 11:39:56'),
(4, 'Pizza Cuatro Quesos', 'Pizza con mozzarella, gorgonzola, parmesano y provolone', 260.00, 'Pizza', 'https://www.hola.com/horizon/landscape/e8bb41b65869-pizzacuatroquesos-adob-t.jpg', 1, '2025-06-30 11:39:56'),
(5, 'Pizza Vegetariana', 'Pizza con champiñones, pimientos, cebolla y aceitunas', 200.00, 'Pizza', 'https://www.revistapancaliente.co/wp-content/uploads/2024/09/Pizza_vegetariana.jpg', 1, '2025-06-30 11:39:56'),
(6, 'Pizza BBQ Chicken', 'Pizza con pollo, salsa BBQ, cebolla y mozzarella', 280.00, 'Pizza', 'https://www.allrecipes.com/thmb/qZ7LKGV1_RYDCgYGSgfMn40nmks=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/AR-24878-bbq-chicken-pizza-beauty-4x3-39cd80585ad04941914dca4bd82eae3d.jpg', 1, '2025-06-30 11:39:56'),
(7, 'Breadsticks', 'Palitos de pan con ajo y parmesano', 80.00, 'Entradas', 'https://preppykitchen.com/wp-content/uploads/2023/01/Breadsticks-Recipe-Card.jpg', 1, '2025-06-30 11:39:56'),
(8, 'Mozzarella Sticks', 'Bastones de mozzarella empanizados', 120.00, 'Entradas', 'https://easyweeknightrecipes.com/wp-content/uploads/2024/04/Mozzarella-Sticks_0013-500x375.jpg', 1, '2025-06-30 11:39:56'),
(9, 'Ensalada César', 'Lechuga romana, crutones, parmesano y aderezo César', 140.00, 'Entradas', 'https://www.gourmet.cl/wp-content/uploads/2016/09/EnsaladaCesar2.webp', 1, '2025-06-30 11:39:56'),
(10, 'Alitas BBQ', 'Alitas de pollo con salsa BBQ', 160.00, 'Entradas', 'https://www.unileverfoodsolutions.com.co/dam/global-ufs/mcos/NOLA/calcmenu/recipes/col-recipies/recetas-fruco-bbq/Alitas-BBQ-1200x709.jpg', 1, '2025-06-30 11:39:56'),
(11, 'Coca Cola', 'Refresco Coca Cola 600ml', 35.00, 'Bebidas', 'https://res.cloudinary.com/riqra/image/upload/v1678811253/sellers/2/a6okdnxxfo6oeslfs6sn.jpg', 1, '2025-06-30 11:39:56'),
(12, 'Sprite', 'Refresco Sprite 600ml', 35.00, 'Bebidas', 'https://www.superaki.mx/cdn/shop/files/7501055305629_100924_cc2b3040-470b-4d24-95ac-044c11176e2d.png?v=1726061339', 1, '2025-06-30 11:39:56'),
(13, 'Fanta', 'Refresco Fanta 600ml', 35.00, 'Bebidas', 'https://150927483.v2.pressablecdn.com/wp-content/uploads/2025/01/wp-image-5034.jpg', 1, '2025-06-30 11:39:56'),
(14, 'Agua Mineral', 'Agua mineral con gas 500ml', 25.00, 'Bebidas', 'https://www.alambique251.com/cdn/shop/products/AguaMineralTopoChico1.5L.jpg?v=1640740335', 1, '2025-06-30 11:39:56'),
(15, 'Limonada', 'Limonada natural 500ml', 40.00, 'Bebidas', 'https://www.recetasnestle.com.mx/sites/default/files/srh_recipes/dab36729433d8afdc15a8b239f030d97.jpg', 1, '2025-06-30 11:39:56'),
(16, 'Cerveza', 'Cerveza nacional 355ml', 45.00, 'Bebidas', 'https://topbeer.mx/wp-content/uploads/2022/08/tipos-de-cerveza-en-beer-flight.jpg', 1, '2025-06-30 11:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `telefono`, `direccion`, `created_at`) VALUES
(1, 'Usuario Demo', 'demo@pizzeria.com', 'password123', '555-123-4567', 'Calle Principal #123, Ciudad', '2025-06-30 11:39:56'),
(2, 'EdgarSC', 'itsfreestyle043@gmail.coma', '87654321', '5568632197', 'Santa Cruz Ayotuxco', '2025-06-30 11:46:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_detalles_pedido` (`pedido_id`),
  ADD KEY `idx_detalles_producto` (`producto_id`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedidos_usuario` (`usuario_id`),
  ADD KEY `idx_pedidos_fecha` (`fecha_pedido`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_productos_categoria` (`categoria`),
  ADD KEY `idx_productos_disponible` (`disponible`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD CONSTRAINT `detalles_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalles_pedido_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
