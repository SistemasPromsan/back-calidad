<?php

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    $stmt = $pdo->query("SELECT * FROM proveedores ORDER BY creado_en DESC");
    $proveedores = $stmt->fetchAll();
    echo json_encode($proveedores);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener proveedores: " . $e->getMessage()]);
}
