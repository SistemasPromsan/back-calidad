<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';
$data = json_decode(file_get_contents("php://input"));

if (isset($data->nombre) && isset($data->descripcion)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO turnos (nombre, descripcion, creado_en, estatus) VALUES (?, ?, NOW(), 'activo')");
        $stmt->execute([$data->nombre, $data->descripcion]);
        echo json_encode(["mensaje" => "Turno creado correctamente."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al insertar: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Faltan campos requeridos."]);
}
