<?php



require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

$data = json_decode(file_get_contents("php://input"), true);

// Validar
if (
    !$data ||
    empty($data['id']) ||
    empty($data['nombre']) ||
    empty($data['username']) ||
    empty($data['email']) ||
    empty($data['id_rol'])
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

try {
    if (!empty($data['password'])) {
        // Con contraseña nueva
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nombre = ?, username = ?, email = ?, password = ?, id_rol = ? 
            WHERE id = ?
        ");
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->execute([
            $data['nombre'],
            $data['username'],
            $data['email'],
            $hash,
            $data['id_rol'],
            $data['id']
        ]);
    } else {
        // Sin cambiar contraseña
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nombre = ?, username = ?, email = ?, id_rol = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $data['nombre'],
            $data['username'],
            $data['email'],
            $data['id_rol'],
            $data['id']
        ]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $e->getMessage()]);
}
