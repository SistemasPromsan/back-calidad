<?php

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

// Leer los datos recibidos
$data = json_decode(file_get_contents("php://input"), true);

// Validar que se recibieron los datos necesarios
if (!$data || !isset($data['nombre']) || !isset($data['descripcion'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$inspector = $data['nombre']; // Se usa 'nombre' para mantener consistencia
$descripcion = $data['descripcion'];
$estatus = 'activo';

try {
    // Insertar en la base de datos
    $query = $pdo->prepare("INSERT INTO inspectores (inspector, descripcion, estatus, creado_en) VALUES (?, ?, ?, NOW())");
    $query->execute([$inspector, $descripcion, $estatus]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
