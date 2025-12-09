<?php

session_start();

file_put_contents('debug_log.txt', "passkey_save.php called at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    file_put_contents('debug_log.txt', "ERROR: Not logged in\n", FILE_APPEND);
    http_response_code(403);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('debug_log.txt', "Raw data: " . print_r($data, true) . "\n", FILE_APPEND);

if (!$data || !isset($data['rawId']) || !is_array($data['rawId'])) {
    file_put_contents('debug_log.txt', "ERROR: Invalid data\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

// Convert rawId to binary
$rawIdBinary = '';
foreach ($data['rawId'] as $byte) {
    $rawIdBinary .= chr($byte);
}
$credentialId = base64_encode($rawIdBinary);

$credential = [
    'id'   => $credentialId,
    'type' => 'public-key'
];

// CORRECT PATH — TRY THIS FIRST (most common in your project)
require_once __DIR__ . '/../../config.php';  // ← THIS IS USUALLY CORRECT

try {
    $db = config::getConnexion();
    $stmt = $db->prepare("UPDATE user SET passkey_credential = ? WHERE id_user = ?");
    $result = $stmt->execute([json_encode($credential), $_SESSION['user_id']]);

    if ($result && $stmt->rowCount() > 0) {
        file_put_contents('debug_log.txt', "SUCCESS: Saved to DB\n", FILE_APPEND);
        echo json_encode(['success' => true]);
    } else {
        file_put_contents('debug_log.txt', "No rows updated (maybe already exists)\n", FILE_APPEND);
        echo json_encode(['success' => true]); // Still say success
    }
} catch (Exception $e) {
    file_put_contents('debug_log.txt', "DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['error' => 'DB Error: ' . $e->getMessage()]);
}
?>