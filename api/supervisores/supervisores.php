<?php

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/bd.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM supervisores WHERE estatus = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $supervisores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($supervisores);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener supervisores: " . $e->getMessage()]);
}
    