<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

try {
    $stmt = $pdo->query("
        SELECT u.id, u.nombre, u.username, u.email, u.estado, u.id_rol,
               CASE 
                   WHEN u.id_rol = 1 THEN 'Administrador'
                   WHEN u.id_rol = 2 THEN 'Supervisor'
                   WHEN u.id_rol = 3 THEN 'Coordinador'
                   WHEN u.id_rol = 4 THEN 'Cliente'
                   ELSE 'Sin Rol'
               END AS rol
        FROM usuarios u
        ORDER BY u.creado_en ASC
    ");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
