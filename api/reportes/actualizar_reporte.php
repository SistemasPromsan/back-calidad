<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->id) &&
    isset($data->fecha) &&
    isset($data->id_turno) &&
    isset($data->id_inspector) &&
    isset($data->id_num_parte) &&
    isset($data->descripcion) &&
    isset($data->plataforma) &&
    isset($data->proveedor) &&
    isset($data->id_cargo) &&
    isset($data->lpn) &&
    isset($data->lote_proveedor) &&
    isset($data->cantidad_inspeccionada) &&
    isset($data->cantidad_correcta)
) {
    try {
        $stmt = $pdo->prepare("UPDATE reportes SET
            fecha = ?, id_turno = ?, id_inspector = ?, id_num_parte = ?, descripcion = ?,
            plataforma = ?, proveedor = ?, id_cargo = ?, lpn = ?, lote_proveedor = ?,
            cantidad_inspeccionada = ?, cantidad_correcta = ?, cantidad_retrabajo = ?,
            cantidad_rechazada = ?, horas = ?, minutos = ?, total_horas = ?,
            motivo_inspeccion = ?, defectos = ?, observaciones = ?
            WHERE id = ?");

        $stmt->execute([
            $data->fecha,
            $data->id_turno,
            $data->id_inspector,
            $data->id_num_parte,
            $data->descripcion,
            $data->plataforma,
            $data->proveedor,
            $data->id_cargo,
            $data->lpn,
            $data->lote_proveedor,
            $data->cantidad_inspeccionada,
            $data->cantidad_correcta,
            $data->cantidad_retrabajo ?? 0,
            $data->cantidad_rechazada ?? 0,
            $data->horas ?? 0,
            $data->minutos ?? 0,
            $data->total_horas ?? 0,
            $data->motivo_inspeccion ?? '',
            $data->defectos ?? '',
            $data->observaciones ?? '',
            $data->id
        ]);

        echo json_encode(["mensaje" => "Reporte actualizado correctamente"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar reporte: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Faltan campos requeridos"]);
}
