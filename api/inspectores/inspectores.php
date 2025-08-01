<?php


require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    // Renombramos 'inspector' como 'nombre' para que sea compatible con el frontend
    $stmt = $pdo->query("
        SELECT 
            id, 
            inspector AS nombre, 
            descripcion, 
            estatus, 
            creado_en 
        FROM inspectores 
        WHERE estatus = 'activo'
        ORDER BY creado_en ASC
    ");
    $inspectores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(is_array($inspectores) ? $inspectores : []);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
