<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

$nombre = $data->nombre ?? '';
$email = $data->email ?? '';
$username = $data->username ?? '';
$password = $data->password ?? '';
$id_rol = $data->id_rol ?? 2; // Por defecto 2 (cliente)

if (!$nombre || !$email || !$username || !$password) {
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, username, password, id_rol) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $username, $hash, $id_rol]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al registrar: ' . $e->getMessage()]);
}
