<?php


require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    $stmt = $pdo->query("SELECT * FROM cargos ORDER BY creado_en DESC");
    $cargos = $stmt->fetchAll();
    echo json_encode($cargos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener cargos: " . $e->getMessage()]);
}
