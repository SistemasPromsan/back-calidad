<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once __DIR__ . '/../../config/bd.php';

try {
    $stmt = $pdo->query("
        SELECT r.*, 
               i.nombre AS inspector_nombre,
               t.nombre AS turno_nombre,
               c.nombre AS cargo_nombre,
               np.num_parte AS num_parte_codigo
        FROM reportes r
        JOIN inspectores i ON r.id_inspector = i.id
        JOIN turnos t ON r.id_turno = t.id
        JOIN cargos c ON r.id_cargo = c.id
        JOIN num_partes np ON r.id_num_parte = np.id
        ORDER BY r.fecha DESC, r.created_at DESC
    ");
    $reportes = $stmt->fetchAll();
    echo json_encode($reportes);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener reportes: " . $e->getMessage()]);
}
