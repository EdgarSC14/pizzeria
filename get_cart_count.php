<?php
session_start();
header('Content-Type: application/json');

$count = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;
echo json_encode(['count' => $count]); 