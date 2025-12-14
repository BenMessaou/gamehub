<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controller/OrderController.php';

$controller = new OrderController($pdo);

// Récupérer JSON envoyé
$data = json_decode(file_get_contents("php://input"), true);

$total = $data["total"];
$items = $data["items"];

// Créer la commande + lignes
$orderId = $controller->create($total, $items);

// Réponse
echo json_encode([
    "success" => true,
    "order_id" => $orderId
]);
