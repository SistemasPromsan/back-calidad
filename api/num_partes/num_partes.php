<?php

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    // Obtener todos los num_partes con nombre de plataforma
    $stmt = $pdo->query("
        SELECT np.*, p.nombre AS plataforma
        FROM num_partes np
        LEFT JOIN plataformas p ON np.id_plataforma = p.id
        WHERE np.estatus = 'activo'
        ORDER BY np.creado_en DESC
    ");
    $numPartes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($numPartes as &$parte) {
        // Obtener proveedores asociados como array de objetos con id y nombre
        $stmtProv = $pdo->prepare("
            SELECT pr.id, pr.nombre 
            FROM num_parte_proveedor npp
            JOIN proveedores pr ON npp.id_proveedor = pr.id
            WHERE npp.id_num_parte = ?
        ");
        $stmtProv->execute([$parte['id']]);
        $proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

        // Asignar arreglo de proveedores completo
        $parte['proveedores'] = $proveedores;
    }

    echo json_encode($numPartes);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
}
