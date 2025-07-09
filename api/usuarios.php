<?php
header("Access-Control-Allow-Origin: *"); // Puedes restringirlo a tu dominio si lo deseas
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/bd.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT u.id, u.nombre, u.email, u.username, u.estado, r.nombre AS rol
                     FROM usuarios u
                     JOIN roles r ON u.id_rol = r.id
                     ORDER BY u.id DESC");


    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(is_array($usuarios) ? $usuarios : []);
} catch (PDOException $e) {
    echo json_encode([]); // <-- respuesta segura
}
