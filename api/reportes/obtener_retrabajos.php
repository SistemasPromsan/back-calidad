<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once __DIR__ . '/../../config/bd.php';

$id_reporte = $_GET['id_reporte'] ?? null;

if ($id_reporte) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM reportes_retrabajo WHERE id_reporte = ?");
        $stmt->execute([$id_reporte]);
        $datos = $stmt->fetchAll();
        echo json_encode($datos);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Parámetro id_reporte requerido"]);
}
