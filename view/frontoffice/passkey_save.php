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

$rawId = '';
foreach ($data['rawId'] as $byte) {
    $rawId .= chr($byte);
}

$credential = [
    'id'   => base64_encode($rawId),
    'type' => 'public-key'
];

$db = config::getConnexion();
$stmt = $db->prepare("UPDATE user SET passkey_credential = ? WHERE id_user = ?");
$stmt->execute([json_encode($credential), $_SESSION['user_id']]);

echo json_encode(['success' => true]);
?>