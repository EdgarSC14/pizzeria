<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión para agregar productos al carrito']);
    exit;
}

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['nombre']) || !isset($data['precio'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verificar si el producto ya está en el carrito
$producto_existente = false;
foreach ($_SESSION['carrito'] as &$item) {
    if ($item['id'] == $data['id']) {
        $item['cantidad']++;
        $producto_existente = true;
        break;
    }
}

// Si el producto no existe, agregarlo
if (!$producto_existente) {
    $_SESSION['carrito'][] = [
        'id' => $data['id'],
        'nombre' => $data['nombre'],
        'precio' => $data['precio'],
        'cantidad' => 1
    ];
}

echo json_encode(['success' => true, 'count' => count($_SESSION['carrito'])]); 