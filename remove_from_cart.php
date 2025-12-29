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

if (!isset($data['index'])) {
    echo json_encode(['success' => false, 'message' => 'Índice no especificado']);
    exit;
}

// Verificar si el carrito existe y el índice es válido
if (!isset($_SESSION['carrito']) || !isset($_SESSION['carrito'][$data['index']])) {
    echo json_encode(['success' => false, 'message' => 'Índice inválido']);
    exit;
}

// Eliminar el item del carrito
array_splice($_SESSION['carrito'], $data['index'], 1);

echo json_encode(['success' => true, 'count' => count($_SESSION['carrito'])]); 