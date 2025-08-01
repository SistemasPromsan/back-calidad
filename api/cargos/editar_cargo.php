<?php


require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';
$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->nombre)) {
    try {
        $stmt = $pdo->prepare("UPDATE cargos SET nombre = ?, descripcion = ? WHERE id = ?");
        $stmt->execute([$data->nombre, $data->descripcion, $data->id]);

        echo json_encode(["mensaje" => "Cargo actualizado correctamente."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Faltan campos."]);
}
