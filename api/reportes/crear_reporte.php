<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    $data = json_decode(file_get_contents("php://input"));

    // Validación básica
    if (
        !$data ||
        !isset($data->fecha, $data->id_turno, $data->id_inspector, $data->id_supervisor, $data->id_usuario, $data->inspecciones)
    ) {
        throw new Exception("Datos incompletos");
    }

    // 1. Insertar reporte principal
    $stmt = $pdo->prepare("INSERT INTO reportes (fecha, id_turno, id_inspector, id_supervisor, id_usuario, horas_extras) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data->fecha,
        $data->id_turno,
        $data->id_inspector,
        $data->id_supervisor,
        $data->id_usuario,
        $data->horas_extras
    ]);
    $id_reporte = $pdo->lastInsertId();

    $total_min_trabajados = 0;

    // 2. Insertar inspecciones
    $stmt_inspeccion = $pdo->prepare("INSERT INTO reportes_inspecciones (
        id_reporte, id_num_parte, proveedor, cargo, lpn, lote, hora_inicio, hora_fin, total_retrabajos,
        piezas_inspeccionadas, piezas_ok, piezas_no_ok, observaciones
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt_retrabajo = $pdo->prepare("INSERT INTO reportes_inspeccion_retrabajos (id_inspeccion, id_retrabajo, cantidad) VALUES (?, ?, ?)");
    $stmt_rechazo = $pdo->prepare("INSERT INTO reportes_inspeccion_rechazos (id_inspeccion, id_defecto, cantidad) VALUES (?, ?, ?)");

    foreach ($data->inspecciones as $insp) {
        // ✅ Validación: los NO OK deben justificarse solo con rechazos
        $piezas_no_ok = intval($insp->piezas_no_ok);
        $suma_rechazos = 0;

        if (!empty($insp->rechazos)) {
            foreach ($insp->rechazos as $d) {
                $suma_rechazos += intval($d->cantidad);
            }
        }

        if ($piezas_no_ok > 0 && $suma_rechazos === 0) {
            throw new Exception("La inspección tiene $piezas_no_ok piezas NO OK sin rechazos registrados.");
        }

        if ($piezas_no_ok > 0 && $suma_rechazos !== $piezas_no_ok) {
            throw new Exception("La suma de rechazos ($suma_rechazos) no coincide con las piezas NO OK ($piezas_no_ok).");
        }

        $inicio = new DateTime($insp->hora_inicio);
        $fin = new DateTime($insp->hora_fin);

        if ($fin < $inicio) {
            $fin->modify('+1 day');
        }

        $minutos = ($fin->getTimestamp() - $inicio->getTimestamp()) / 60;
        $total_min_trabajados += $minutos;


        // Insertar inspección
        $stmt_inspeccion->execute([
            $id_reporte,
            $insp->id_num_parte,
            $insp->proveedor,
            $insp->cargo,
            $insp->lpn,
            $insp->lote,
            $insp->total_retrabajos,
            $insp->hora_inicio,
            $insp->hora_fin,
            $insp->piezas_inspeccionadas,
            $insp->piezas_ok,
            $insp->piezas_no_ok,
            $insp->observaciones
        ]);

        $id_inspeccion = $pdo->lastInsertId();

        // Insertar retrabajos (ahora independientes)
        if (!empty($insp->retrabajos)) {
            foreach ($insp->retrabajos as $r) {
                $stmt_retrabajo->execute([
                    $id_inspeccion,
                    $r->id_retrabajo,
                    $r->cantidad
                ]);
            }
        }

        // Insertar rechazos
        if (!empty($insp->rechazos)) {
            foreach ($insp->rechazos as $d) {
                $stmt_rechazo->execute([
                    $id_inspeccion,
                    $d->id_defecto,
                    $d->cantidad
                ]);
            }
        }
    }


    // 3. Calcular horas trabajadas y actualizar el reporte
    $horas_trabajadas = round($total_min_trabajados / 60, 2);
    $stmt_update = $pdo->prepare("UPDATE reportes SET horas_trabajadas = ? WHERE id = ?");
    $stmt_update->execute([$horas_trabajadas, $id_reporte]);

    echo json_encode(["success" => true, "mensaje" => "Reporte guardado correctamente"]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["success" => false, "mensaje" => $e->getMessage()]);
}
