<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['rawId'])) {
    http_response_code(400);
    exit;
}

// Save the credential ID only (safe and enough)
$credential = [
    'id'   => $data['rawId'],
    'type' => $data['type'] ?? 'public-key'
];

$db = config::getConnexion();
$db->prepare("UPDATE user SET passkey_credential = ? WHERE id_user = ?")
   ->execute([json_encode($credential), $_SESSION['user_id']]);

echo json_encode(['success' => true]);