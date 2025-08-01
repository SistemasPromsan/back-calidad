<?php


require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta el ID']);
    exit;
}

$id = $data['id'];

try {
    $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'activo' WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor']);
}
