<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Solo para desarrollo local
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/bd.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['inspector']) || !isset($data['descripcion'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$inspector = $data['inspector'];
$descripcion = $data['descripcion'];
$estatus = 'activo';

try {
    $query = $pdo->prepare("INSERT INTO inspectores (inspector, descripcion, estatus, creado_en) VALUES (?, ?, ?, NOW())");
    $query->execute([$inspector, $descripcion, $estatus]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
