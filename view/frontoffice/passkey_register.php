<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized']));
}

require_once "../../controller/userController.php";

$uc = new UserController();
$user = $uc->getUserById($_SESSION['user_id']);

if (!$user) {
    http_response_code(403);
    die(json_encode(['error' => 'User not found']));
}

// Generate challenge
$challenge = random_bytes(32);
$_SESSION['passkey_challenge'] = $challenge;

$rpId = $_SERVER['HTTP_HOST']; // works on localhost, 127.0.0.1, gamehub.local, etc.

header('Content-Type: application/json');

echo json_encode([
    'rp' => [
        'name' => 'GameHub',
        'id'   => $rpId
    ],
    'user' => [
        'id'          => base64_encode('user_' . $user['id_user']),
        'name'        => $user['email'],
        'displayName' => $user['name'] . ' ' . $user['lastname']
    ],
    'challenge' => base64_encode($challenge),   // ← MUST BE BASE64
    'pubKeyCredParams' => [
        ['type' => 'public-key', 'alg' => -7],
        ['type' => 'public-key', 'alg' => -257]
    ],
    'timeout' => 60000,
    'attestation' => 'none',
    'excludeCredentials' => [],
    'authenticatorSelection' => [
        'userVerification' => 'preferred',
        'residentKey'     => 'preferred',
        'requireResidentKey' => false
    ]
], JSON_UNESCAPED_SLASHES);