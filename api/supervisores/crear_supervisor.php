<?php


require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->nombre) || empty(trim($data->nombre))) {
    http_response_code(400);
    echo json_encode(["error" => "El nombre es obligatorio"]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO supervisores (nombre) VALUES (?)");
    $stmt->execute([$data->nombre]);

    echo json_encode(["mensaje" => "Supervisor creado correctamente"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al crear supervisor: " . $e->getMessage()]);
}
