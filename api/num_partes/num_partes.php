<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejo del método OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/bd.php';

try {
    // Obtener todos los num_partes con nombre de plataforma
    $stmt = $pdo->query("
        SELECT np.*, p.nombre AS plataforma
        FROM num_partes np
        LEFT JOIN plataformas p ON np.id_plataforma = p.id
        ORDER BY np.creado_en DESC
    ");
    $numPartes = $stmt->fetchAll();

    foreach ($numPartes as &$parte) {
        // Obtener proveedores asociados como texto plano
        $stmtProv = $pdo->prepare("
            SELECT pr.nombre 
            FROM num_partes np
            JOIN num_parte_proveedor npp ON np.id = npp.id_num_parte
            JOIN proveedores pr ON npp.id_proveedor = pr.id
            WHERE np.id = ?
        ");
        $stmtProv->execute([$parte['id']]);
        $proveedores = $stmtProv->fetchAll(PDO::FETCH_COLUMN);

        // Agregar el campo plano esperado por el frontend
        $parte['proveedor'] = implode(', ', $proveedores);
    }

    echo json_encode($numPartes);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
}
