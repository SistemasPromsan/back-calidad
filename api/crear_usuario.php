<?php

header("Access-Control-Allow-Origin: *"); // Puedes restringirlo a tu dominio si lo deseas
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Si es preflight OPTIONS, finaliza la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !$data['nombre'] || !$data['username'] || !$data['email'] || !$data['password'] || !$data['id_rol']) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, username, email, password, id_rol, estado, creado_en) VALUES (?, ?, ?, ?, ?, 'activo', NOW())");
    $hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt->execute([
        $data['nombre'],
        $data['username'],
        $data['email'],
        $hash,
        $data['id_rol']
    ]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
