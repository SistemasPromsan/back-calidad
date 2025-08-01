<?php



require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !$data['id']) {
    http_response_code(400);
    echo json_encode(['error' => 'ID requerido']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'inactivo' WHERE id = ?");
    $stmt->execute([$data['id']]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
