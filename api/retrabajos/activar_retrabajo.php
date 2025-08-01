<?php


require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    try {
        $stmt = $pdo->prepare("UPDATE retrabajos SET estatus = 'activo' WHERE id = ?");
        $stmt->execute([$data->id]);
        echo json_encode(["mensaje" => "Retrabajo activado."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al activar: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "ID no proporcionado."]);
}
