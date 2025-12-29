<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión para modificar el carrito']);
    exit;
}

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['index']) || !isset($data['change'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Verificar si el carrito existe y el índice es válido
if (!isset($_SESSION['carrito']) || !isset($_SESSION['carrito'][$data['index']])) {
    echo json_encode(['success' => false, 'message' => 'Índice inválido']);
    exit;
}

// Actualizar la cantidad
$nueva_cantidad = $_SESSION['carrito'][$data['index']]['cantidad'] + $data['change'];

// Si la cantidad es 0 o menor, eliminar el item
if ($nueva_cantidad <= 0) {
    array_splice($_SESSION['carrito'], $data['index'], 1);
    echo json_encode([
        'success' => true,
        'count' => count($_SESSION['carrito']),
        'removed' => true
    ]);
    exit;
}

// Actualizar la cantidad
$_SESSION['carrito'][$data['index']]['cantidad'] = $nueva_cantidad;

// Calcular el subtotal
$subtotal = $_SESSION['carrito'][$data['index']]['precio'] * $nueva_cantidad;

echo json_encode([
    'success' => true,
    'count' => count($_SESSION['carrito']),
    'quantity' => $nueva_cantidad,
    'subtotal' => number_format($subtotal, 2),
    'total' => number_format(array_sum(array_map(function($item) {
        return $item['precio'] * $item['cantidad'];
    }, $_SESSION['carrito'])), 2)
]); 