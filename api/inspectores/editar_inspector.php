<?php


require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['inspector']) || !isset($data['descripcion'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$id = $data['id'];
$inspector = trim($data['inspector']);
$descripcion = trim($data['descripcion']);

try {
    $query = "UPDATE inspectores SET inspector = ?, descripcion = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$inspector, $descripcion, $id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en base de datos: ' . $e->getMessage()]);
}
