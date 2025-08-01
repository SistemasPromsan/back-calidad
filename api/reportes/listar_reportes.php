<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    $stmt = $pdo->query("
        SELECT r.id, r.fecha, r.horas_trabajadas,
               i.inspector AS nombre_inspector,
               s.nombre AS nombre_supervisor,
               t.nombre AS nombre_turno
        FROM reportes r
        LEFT JOIN inspectores i ON r.id_inspector = i.id
        LEFT JOIN supervisores s ON r.id_supervisor = s.id
        LEFT JOIN turnos t ON r.id_turno = t.id
        ORDER BY r.fecha DESC, r.id DESC
    ");

    $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reportes);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
