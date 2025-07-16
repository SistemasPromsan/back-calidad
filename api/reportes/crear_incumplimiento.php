<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id_reporte) && isset($data->id_usuario) && isset($data->id_motivo)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO reportes_incumplimiento (id_reporte, id_usuario, id_motivo, created_at)
                               VALUES (?, ?, ?, NOW())");
        $stmt->execute([$data->id_reporte, $data->id_usuario, $data->id_motivo]);
        echo json_encode(["mensaje" => "Incumplimiento registrado"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
}
