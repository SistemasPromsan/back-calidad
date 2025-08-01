<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

error_log("✅ Entró correctamente al archivo obtener_reporte_detalle.php");
error_log("🚀 Iniciando obtener_reporte_detalle.php");

try {
    // Recibir ID por GET
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID de reporte requerido.");
    }

    $id = $_GET['id'];
    error_log("🔍 Consultando detalle para ID: $id");

    // Obtener datos del reporte principal
    $stmt = $pdo->prepare("
        SELECT r.*, i.inspector, s.nombre AS supervisor, t.nombre AS turno
        FROM reportes r
        LEFT JOIN inspectores i ON r.id_inspector = i.id
        LEFT JOIN supervisores s ON r.id_supervisor = s.id
        LEFT JOIN turnos t ON r.id_turno = t.id
        WHERE r.id = ?
    ");
    $stmt->execute([$id]);
    $reporte = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reporte) {
        throw new Exception("Reporte no encontrado.");
    }

    // Obtener inspecciones
    $stmt = $pdo->prepare("
        SELECT ri.*
        FROM reportes_inspecciones ri
        WHERE ri.id_reporte = ?
    ");
    $stmt->execute([$id]);
    $inspecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($inspecciones as &$insp) {

        // Formatear hora_inicio y hora_fin
        $insp['hora_inicio'] = (new DateTime($insp['hora_inicio']))->format('H:i');
        $insp['hora_fin'] = (new DateTime($insp['hora_fin']))->format('H:i');

        // Obtener retrabajos
        $stmtR = $pdo->prepare("
        SELECT r.*, rt.nombre AS motivo
        FROM reportes_inspeccion_retrabajos r
        LEFT JOIN retrabajos rt ON r.id_retrabajo = rt.id
        WHERE r.id_inspeccion = ?
    ");
        $stmtR->execute([$insp['id']]);
        $insp['retrabajos'] = $stmtR->fetchAll(PDO::FETCH_ASSOC);

        // Obtener rechazos
        $stmtD = $pdo->prepare("
        SELECT d.*, df.nombre AS motivo
        FROM reportes_inspeccion_rechazos d
        LEFT JOIN defectos df ON d.id_defecto = df.id
        WHERE d.id_inspeccion = ?
    ");
        $stmtD->execute([$insp['id']]);
        $insp['rechazos'] = $stmtD->fetchAll(PDO::FETCH_ASSOC);
    }


    $reporte['inspecciones'] = $inspecciones;

    echo json_encode($reporte);
} catch (Exception $e) {
    error_log("❗ Error en obtener_reporte_detalle.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
