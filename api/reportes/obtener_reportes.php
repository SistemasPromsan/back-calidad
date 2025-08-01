<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

error_log("✅ Entró correctamente al archivo obtener_reportes.php");

error_log("🚀 Iniciando obtener_reportes.php");

try {
    // Obtener lista de reportes con información básica
    $stmt = $pdo->query("
        SELECT r.id, r.fecha, r.horas_trabajadas,
               i.inspector AS inspector,
               s.nombre AS supervisor,
               t.nombre AS turno
        FROM reportes r
        LEFT JOIN inspectores i ON r.id_inspector = i.id
        LEFT JOIN supervisores s ON r.id_supervisor = s.id
        LEFT JOIN turnos t ON r.id_turno = t.id
        ORDER BY r.fecha DESC
    ");

    $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reportes);
} catch (Exception $e) {
    error_log("❗ Error en obtener_reportes.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener los reportes."]);
}
