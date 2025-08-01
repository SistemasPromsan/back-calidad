<?php


require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    $stmt = $pdo->query("SELECT * FROM turnos ORDER BY creado_en DESC");
    $turnos = $stmt->fetchAll();
    echo json_encode($turnos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener datos: " . $e->getMessage()]);
}
