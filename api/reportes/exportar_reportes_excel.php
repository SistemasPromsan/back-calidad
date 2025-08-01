<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/bd.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reportes_completos.xlsx"');
header('Cache-Control: max-age=0');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Reportes");

$row = 1;
$sheet->setCellValue("A{$row}", "ID Reporte");
$sheet->setCellValue("B{$row}", "Fecha");
$sheet->setCellValue("C{$row}", "Inspector");
$sheet->setCellValue("D{$row}", "Supervisor");
$sheet->setCellValue("E{$row}", "Turno");
$sheet->setCellValue("F{$row}", "Horas Trabajadas");
$sheet->setCellValue("G{$row}", "Horas Extras");
$sheet->setCellValue("H{$row}", "ID Inspección");
$sheet->setCellValue("I{$row}", "Num Parte");
$sheet->setCellValue("J{$row}", "Proveedor");
$sheet->setCellValue("K{$row}", "Cargo");
$sheet->setCellValue("L{$row}", "LPN");
$sheet->setCellValue("M{$row}", "Lote");
$sheet->setCellValue("N{$row}", "Inicio");
$sheet->setCellValue("O{$row}", "Fin");
$sheet->setCellValue("P{$row}", "Pzas Inspeccionadas");
$sheet->setCellValue("Q{$row}", "OK");
$sheet->setCellValue("R{$row}", "NO OK");
$sheet->setCellValue("S{$row}", "Observaciones");
$sheet->setCellValue("T{$row}", "Tipo Motivo");
$sheet->setCellValue("U{$row}", "Motivo");
$sheet->setCellValue("V{$row}", "Cantidad");

$row++;

$stmt = $pdo->query("
    SELECT r.*, i.inspector, s.nombre AS supervisor, t.nombre AS turno
    FROM reportes r
    LEFT JOIN inspectores i ON r.id_inspector = i.id
    LEFT JOIN supervisores s ON r.id_supervisor = s.id
    LEFT JOIN turnos t ON r.id_turno = t.id
    ORDER BY r.id DESC
");

$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reportes as $reporte) {
    $stmtIns = $pdo->prepare("SELECT * FROM reportes_inspecciones WHERE id_reporte = ?");
    $stmtIns->execute([$reporte['id']]);
    $inspecciones = $stmtIns->fetchAll(PDO::FETCH_ASSOC);

    foreach ($inspecciones as $insp) {
        // Rechazos
        $stmtRech = $pdo->prepare("
            SELECT d.cantidad, df.nombre AS motivo 
            FROM reportes_inspeccion_rechazos d
            LEFT JOIN defectos df ON d.id_defecto = df.id
            WHERE d.id_inspeccion = ?
        ");
        $stmtRech->execute([$insp['id']]);
        $rechazos = $stmtRech->fetchAll(PDO::FETCH_ASSOC);

        // Retrabajos
        $stmtRet = $pdo->prepare("
            SELECT r.cantidad, rt.nombre AS motivo 
            FROM reportes_inspeccion_retrabajos r
            LEFT JOIN retrabajos rt ON r.id_retrabajo = rt.id
            WHERE r.id_inspeccion = ?
        ");
        $stmtRet->execute([$insp['id']]);
        $retrabajos = $stmtRet->fetchAll(PDO::FETCH_ASSOC);

        $motivos = array_merge(
            array_map(fn($r) => ['tipo' => 'Rechazo'] + $r, $rechazos),
            array_map(fn($r) => ['tipo' => 'Retrabajo'] + $r, $retrabajos)
        );

        if (empty($motivos)) {
            $sheet->fromArray([
                $reporte['id'],
                $reporte['fecha'],
                $reporte['inspector'],
                $reporte['supervisor'],
                $reporte['turno'],
                $reporte['horas_trabajadas'],
                $reporte['horas_extras'],
                $insp['id'],
                $insp['id_num_parte'],
                $insp['proveedor'],
                $insp['cargo'],
                $insp['lpn'],
                $insp['lote'],
                $insp['hora_inicio'],
                $insp['hora_fin'],
                $insp['piezas_inspeccionadas'],
                $insp['piezas_ok'],
                $insp['piezas_no_ok'],
                $insp['observaciones'],
                '',
                '',
                ''
            ], null, "A{$row}");
            $row++;
        } else {
            foreach ($motivos as $m) {
                $sheet->fromArray([
                    $reporte['id'],
                    $reporte['fecha'],
                    $reporte['inspector'],
                    $reporte['supervisor'],
                    $reporte['turno'],
                    $reporte['horas_trabajadas'],
                    $reporte['horas_extras'],
                    $insp['id'],
                    $insp['id_num_parte'],
                    $insp['proveedor'],
                    $insp['cargo'],
                    $insp['lpn'],
                    $insp['lote'],
                    $insp['hora_inicio'],
                    $insp['hora_fin'],
                    $insp['piezas_inspeccionadas'],
                    $insp['piezas_ok'],
                    $insp['piezas_no_ok'],
                    $insp['observaciones'],
                    $m['tipo'],
                    $m['motivo'],
                    $m['cantidad']
                ], null, "A{$row}");
                $row++;
            }
        }
    }
}

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
