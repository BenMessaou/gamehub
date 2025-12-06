<?php
session_start();
require_once "../../controller/userController.php";

$uc = new UserController();
$users = $uc->listUsers()->fetchAll();

$allowCredentials = [];

foreach ($users as $u) {
    if (!empty($u['passkey_credential'])) {
        $cred = json_decode($u['passkey_credential'], true);
        if ($cred && !empty($cred['id'])) {
            $allowCredentials[] = [
                'type' => 'public-key',
                'id'   => base64_decode($cred['id'])  // ← decode back to binary
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    'challenge'        => random_bytes(32),
    'timeout'          => 60000,
    'rpId'             => $_SERVER['HTTP_HOST'],
    'allowCredentials'          => $allowCredentials,
    'userVerification' => 'preferred'
]);