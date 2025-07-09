<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    try {
        $stmt = $pdo->prepare("UPDATE proveedores SET estatus = 'inactivo' WHERE id = ?");
        $stmt->execute([$data->id]);
        echo json_encode(["mensaje" => "Proveedor desactivado."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al desactivar proveedor: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "ID no proporcionado."]);
}
