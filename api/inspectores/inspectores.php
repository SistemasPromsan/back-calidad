<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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
