<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["error" => "ID del inspector es obligatorio."]);
        exit;
    }

    $id = intval($data['id']);

    // Corrige esta línea:
    $stmt = $pdo->prepare("UPDATE inspectores SET estatus = 'inactivo' WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["success" => true, "message" => "Inspector desactivado correctamente."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al desactivar el inspector: " . $e->getMessage()]);
}
