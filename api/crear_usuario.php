<?php


require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validar datos requeridos
if (
    !$data ||
    empty($data['nombre']) ||
    empty($data['username']) ||
    empty($data['email']) ||
    empty($data['password']) ||
    empty($data['id_rol'])
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

try {
    // Validar si el username o email ya existen
    $check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? OR email = ?");
    $check->execute([$data['username'], $data['email']]);
    if ($check->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'error' => 'El usuario o correo ya existe']);
        exit;
    }

    // Insertar usuario
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nombre, username, email, password, id_rol, estado, creado_en) 
        VALUES (?, ?, ?, ?, ?, 'activo', NOW())
    ");
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
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al guardar: ' . $e->getMessage()]);
}
