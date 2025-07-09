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

if (isset($data->id) && isset($data->nombre) && isset($data->descripcion)) {
    try {
        $stmt = $pdo->prepare("UPDATE retrabajos SET nombre = ?, descripcion = ? WHERE id = ?");
        $stmt->execute([$data->nombre, $data->descripcion, $data->id]);
        echo json_encode(["mensaje" => "Retrabajo actualizado correctamente."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Faltan campos."]);
}
