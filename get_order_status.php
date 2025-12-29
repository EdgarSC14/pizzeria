<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar si se proporcionó un ID de pedido
if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de pedido no proporcionado']);
    exit;
}

$order_id = filter_var($_GET['order_id'], FILTER_SANITIZE_NUMBER_INT);
$user_id = $_SESSION['usuario_id'];

try {
    // Obtener el estado actual del pedido
    $stmt = $pdo->prepare("SELECT estado FROM pedidos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pedido) {
        // Definir los estados posibles en orden
        $estados = ['Pendiente', 'En preparación', 'En camino', 'Entregado'];
        $estado_actual = $pedido['estado'];
        
        // Verificar si el estado actual es válido
        if (!in_array($estado_actual, $estados)) {
            // Si el estado no es válido, establecer como Pendiente
            $estado_actual = 'Pendiente';
            $update_stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
            $update_stmt->execute([$estado_actual, $order_id]);
        }
        
        echo json_encode(['status' => $estado_actual]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar el estado del pedido: ' . $e->getMessage()]);
}
?> 