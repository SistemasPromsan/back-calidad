<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['estado' => 'desconocido']);
    exit;
}

$stmt = $pdo->prepare("SELECT estado FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['estado' => $user['estado']]);
} else {
    echo json_encode(['estado' => 'desconocido']);
}
