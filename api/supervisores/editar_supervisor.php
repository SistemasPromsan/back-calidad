<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->nombre) || empty(trim($data->nombre))) {
    http_response_code(400);
    echo json_encode(["error" => "ID y nombre son obligatorios"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE supervisores SET nombre = ? WHERE id = ?");
    $stmt->execute([$data->nombre, $data->id]);

    echo json_encode(["mensaje" => "Supervisor actualizado correctamente"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar supervisor: " . $e->getMessage()]);
}
