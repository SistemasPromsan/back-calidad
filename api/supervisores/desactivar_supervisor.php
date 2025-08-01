<?php

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->estatus)) {
    http_response_code(400);
    echo json_encode(["error" => "ID y estatus son obligatorios"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE supervisores SET estatus = ? WHERE id = ?");
    $stmt->execute([$data->estatus, $data->id]);

    $accion = $data->estatus == 1 ? 'activado' : 'desactivado';
    echo json_encode(["mensaje" => "Supervisor $accion correctamente"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al cambiar estatus del supervisor: " . $e->getMessage()]);
}
