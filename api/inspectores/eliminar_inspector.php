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

    if (!isset($data['id']) || !isset($data['rol'])) {
        echo json_encode(["error" => "ID y rol son obligatorios."]);
        exit;
    }

    $id = intval($data['id']);
    $rol = strtolower(trim($data['rol']));

    if ($rol !== 'admin' && $rol !== 'administrador') {
        http_response_code(403);
        echo json_encode(["error" => "No tienes permiso para eliminar este registro."]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM inspectores WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["success" => true, "message" => "Inspector eliminado permanentemente."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al eliminar el inspector: " . $e->getMessage()]);
}
