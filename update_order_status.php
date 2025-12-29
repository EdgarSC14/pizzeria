<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar si se proporcionaron los datos necesarios
if (!isset($_POST['order_id']) || !isset($_POST['new_status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$order_id = filter_var($_POST['order_id'], FILTER_SANITIZE_NUMBER_INT);
$new_status = filter_var($_POST['new_status'], FILTER_SANITIZE_STRING);
$user_id = $_SESSION['usuario_id'];

// Definir los estados válidos
$estados_validos = ['Pendiente', 'En preparación', 'En camino', 'Entregado'];

// Verificar si el nuevo estado es válido
if (!in_array($new_status, $estados_validos)) {
    http_response_code(400);
    echo json_encode(['error' => 'Estado no válido']);
    exit;
}

try {
    // Verificar que el pedido existe y pertenece al usuario
    $stmt = $pdo->prepare("SELECT estado FROM pedidos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado']);
        exit;
    }

    // Actualizar el estado del pedido
    $update_stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ? AND usuario_id = ?");
    $update_stmt->execute([$new_status, $order_id, $user_id]);

    echo json_encode(['success' => true, 'status' => $new_status]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar el estado del pedido: ' . $e->getMessage()]);
}
?> 