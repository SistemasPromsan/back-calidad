<?php

// Permitir peticiones desde el frontend
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Manejar preflight (pre-solicitud)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}   

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !$data['id']) {
    http_response_code(400);
    echo json_encode(['error' => 'ID requerido']);
    exit;
}

try {
    $sql = "UPDATE usuarios SET nombre = ?, username = ?, email = ?, id_rol = ?" .
        (isset($data['password']) && $data['password'] ? ", password = ?" : "") .
        " WHERE id = ?";
    $params = [$data['nombre'], $data['username'], $data['email'], $data['id_rol']];

    if (isset($data['password']) && $data['password']) {
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    $params[] = $data['id'];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
