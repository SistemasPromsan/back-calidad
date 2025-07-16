<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->num_parte) && isset($data->descripcion) && isset($data->id_plataforma)) {
    try {
        // 1. Actualizar el registro principal
        $stmt = $pdo->prepare("UPDATE num_partes SET num_parte = ?, descripcion = ?, id_plataforma = ? WHERE id = ?");
        $stmt->execute([$data->num_parte, $data->descripcion, $data->id_plataforma, $data->id]);

        // 2. Eliminar proveedores anteriores
        $stmtDelete = $pdo->prepare("DELETE FROM num_parte_proveedor WHERE id_num_parte = ?");
        $stmtDelete->execute([$data->id]);

        // 3. Insertar nuevos proveedores si existen
        if (!empty($data->proveedores) && is_array($data->proveedores)) {
            $stmtProv = $pdo->prepare("INSERT INTO num_parte_proveedor (id_num_parte, id_proveedor) VALUES (?, ?)");
            foreach ($data->proveedores as $idProveedor) {
                $stmtProv->execute([$data->id, $idProveedor]);
            }
        }

        echo json_encode(["mensaje" => "Número de parte actualizado."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Faltan campos."]);
}
