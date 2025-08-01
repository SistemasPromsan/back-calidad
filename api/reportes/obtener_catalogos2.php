<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


try {
    // Inspectores (usando 'inspector' como nombre visible)
    $inspectores = $pdo->query("
        SELECT id, inspector AS nombre 
        FROM inspectores 
        WHERE estatus = 'activo' 
        ORDER BY inspector
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Supervisores (columna 'nombre' está bien)
    $supervisores = $pdo->query("
        SELECT id, nombre 
        FROM supervisores 
        WHERE estatus = '1' 
        ORDER BY nombre
    ")->fetchAll(PDO::FETCH_ASSOC);


    // Turnos (columna 'nombre' está bien)
    $turnos = $pdo->query("
        SELECT id, nombre 
        FROM turnos 
        WHERE estatus = 'activo' 
        ORDER BY id
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Números de parte con plataforma
    $stmt = $pdo->query("
        SELECT np.id, np.num_parte, np.descripcion, p.nombre AS plataforma
        FROM num_partes np
        LEFT JOIN plataformas p ON np.id_plataforma = p.id
        WHERE np.estatus = 'activo'
        ORDER BY np.num_parte
    ");
    $num_partes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Proveedores por número de parte
    $stmt = $pdo->query("
        SELECT 
            npp.id_num_parte, 
            pr.id AS id_proveedor, 
            pr.nombre AS proveedor 
        FROM num_parte_proveedor npp
        JOIN proveedores pr ON npp.id_proveedor = pr.id
    ");
    $proveedores_por_parte = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $id_parte = $row['id_num_parte'];
        if (!isset($proveedores_por_parte[$id_parte])) {
            $proveedores_por_parte[$id_parte] = [];
        }
        $proveedores_por_parte[$id_parte][] = [
            'id' => $row['id_proveedor'],
            'nombre' => $row['proveedor']
        ];
    }

    // Retrabajos
    $retrabajos = $pdo->query("
        SELECT id, nombre 
        FROM retrabajos 
        WHERE estatus = 'activo' 
        ORDER BY nombre
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Defectos
    $defectos = $pdo->query("
        SELECT id, nombre AS defecto 
        FROM defectos 
        WHERE estatus = 'activo' 
        ORDER BY nombre
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'inspectores' => $inspectores,
        'supervisores' => $supervisores,
        'turnos' => $turnos,
        'num_partes' => $num_partes,
        'proveedores_por_parte' => $proveedores_por_parte,
        'retrabajos' => $retrabajos,
        'defectos' => $defectos
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
