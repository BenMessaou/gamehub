<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
    exit;
}

require_once "../../controller/userController.php";
$uc = new UserController();
$user = $uc->getUserById($_SESSION['user_id']);

if (!$user) {
    http_response_code(403);
    exit;
}

// Generate a random challenge
$challenge = random_bytes(32);
$_SESSION['passkey_challenge'] = $challenge;

header('Content-Type: application/json');
echo json_encode([
    "rp" => [
        "name" => "GameHub",
        "id"   => parse_url("http://" . $_SERVER['HTTP_HOST'], PHP_URL_HOST)
    ],
    "user" => [
        "id"    => base64_encode("user_" . $user['id_user']),
        "name"  => $user['email'],
        "displayName" => $user['name'] . " " . $user['lastname']
    ],
    "challenge" => base64_encode($challenge),
    "pubKeyCredParams" => [
        ["type" => "public-key", "alg" => -7],
        ["type" => "public-key", "alg" => -257]
    ],
    "timeout" => 60000,
    "attestation" => "none",
    "authenticatorSelection" => [
        "userVerification" => "preferred",
        "residentKey" => "preferred"
    ],
    "excludeCredentials" => []
]);