    <?php
    require_once __DIR__ . '/../../config/cors.php';
    require_once __DIR__ . "/../../config/bd.php";

    try {
        $data = json_decode(file_get_contents("php://input"));

        if (!$data || !isset($data->id)) {
            throw new Exception("ID del reporte no proporcionado.");
        }

        $pdo->beginTransaction();

        // Actualizar tabla principal
        $stmt_reporte = $pdo->prepare("UPDATE reportes SET fecha = ?, id_turno = ?, id_inspector = ?, id_supervisor = ?, id_usuario = ?, horas_trabajadas = ?, horas_extras = ? WHERE id = ?");
        $stmt_reporte->execute([
            $data->fecha,
            $data->id_turno,
            $data->id_inspector,
            $data->id_supervisor,
            $data->id_usuario,
            $data->horas_trabajadas,
            $data->horas_extras,
            $data->id
        ]);

        // Eliminar inspecciones previas (para evitar problemas de integridad)
        $stmt_old_insps = $pdo->prepare("SELECT id FROM reportes_inspecciones WHERE id_reporte = ?");
        $stmt_old_insps->execute([$data->id]);
        $old_insps = $stmt_old_insps->fetchAll(PDO::FETCH_ASSOC);

        foreach ($old_insps as $ins) {
            $id_insp = $ins["id"];
            $pdo->prepare("DELETE FROM reportes_inspeccion_rechazos WHERE id_inspeccion = ?")->execute([$id_insp]);
            $pdo->prepare("DELETE FROM reportes_inspeccion_retrabajos WHERE id_inspeccion = ?")->execute([$id_insp]);
        }
        $pdo->prepare("DELETE FROM reportes_inspecciones WHERE id_reporte = ?")->execute([$data->id]);

        // Insertar nuevas inspecciones
        $stmt_inspeccion = $pdo->prepare("INSERT INTO reportes_inspecciones (id_reporte, id_num_parte, proveedor, cargo, lpn, lote, total_retrabajos, hora_inicio, hora_fin, piezas_inspeccionadas, piezas_ok, piezas_no_ok, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt_retrabajo = $pdo->prepare("INSERT INTO reportes_inspeccion_retrabajos (id_inspeccion, id_retrabajo, cantidad) VALUES (?, ?, ?)");
        $stmt_rechazo = $pdo->prepare("INSERT INTO reportes_inspeccion_rechazos (id_inspeccion, id_defecto, cantidad) VALUES (?, ?, ?)");

        foreach ($data->inspecciones as $insp) {
            $stmt_inspeccion->execute([
                $data->id,
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

            foreach ($insp->retrabajos as $r) {
                if ($r->id_retrabajo && $r->cantidad > 0) {
                    $stmt_retrabajo->execute([$id_inspeccion, $r->id_retrabajo, $r->cantidad]);
                }
            }

            foreach ($insp->rechazos as $d) {
                if ($d->id_defecto && $d->cantidad > 0) {
                    $stmt_rechazo->execute([$id_inspeccion, $d->id_defecto, $d->cantidad]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(["status" => "ok"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
