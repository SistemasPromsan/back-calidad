<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT u.*, r.nombre AS rol FROM usuarios u
                           JOIN roles r ON u.id_rol = r.id
                           WHERE u.username = ?");
    $stmt->execute([$username]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    if ($usuario['estado'] !== 'activo') {
        echo json_encode(['error' => 'Usuario inactivo']);
        exit;
    }

    if (!password_verify($password, $usuario['password'])) {
        echo json_encode(['error' => 'Contraseña incorrecta']);
        exit;
    }

    // Login válido
    unset($usuario['password']); // No devolver la contraseña
    echo json_encode([
        'success' => true,
        'usuario' => $usuario
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor']);
}
