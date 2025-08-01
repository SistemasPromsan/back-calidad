<?php

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->num_parte) && isset($data->descripcion) && isset($data->id_plataforma)) {
    try {
        // Insertar en num_partes
        $stmt = $pdo->prepare("
            INSERT INTO num_partes (num_parte, descripcion, creado_en, estatus, id_plataforma)
            VALUES (?, ?, NOW(), 'activo', ?)
        ");
        $stmt->execute([$data->num_parte, $data->descripcion, $data->id_plataforma]);

        // Obtener ID insertado
        $idNumParte = $pdo->lastInsertId();

        // Insertar proveedores asociados si existen
        if (!empty($data->proveedores) && is_array($data->proveedores)) {
            $stmtProv = $pdo->prepare("
                INSERT INTO num_parte_proveedor (id_num_parte, id_proveedor) VALUES (?, ?)
            ");
            foreach ($data->proveedores as $idProveedor) {
                $stmtProv->execute([$idNumParte, $idProveedor]);
            }
        }

        echo json_encode(["mensaje" => "Número de parte creado exitosamente."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al insertar: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Faltan campos obligatorios."]);
}
