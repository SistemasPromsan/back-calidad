<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->motivo) && isset($data->cantidad)) {
    try {
        $stmt = $pdo->prepare("UPDATE reportes_rechazo SET motivo = ?, cantidad = ? WHERE id = ?");
        $stmt->execute([$data->motivo, $data->cantidad, $data->id]);
        echo json_encode(["mensaje" => "Rechazo actualizado correctamente"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos"]);
}
