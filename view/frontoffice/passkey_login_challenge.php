<?php
require_once "../../controllers/userController.php";

$uc = new UserController();
$allowCredentials = [];

foreach ($uc->listUsers() as $u) {
    if (!empty($u['passkey_credential'])) {
        $cred = json_decode($u['passkey_credential'], true);
        if (isset($cred['id']) && $cred['id']) {
            $allowCredentials[] = [
                'type' => 'public-key',
                'id'   => base64_decode($cred['id'])   
            ];
        }
    }
}

$challenge = random_bytes(32);

header('Content-Type: application/json');
echo json_encode([
    'challenge'        => base64_encode($challenge),
    'timeout'          => 60000,
    'rpId'             => $_SERVER['HTTP_HOST'],
    'allowCredentials' => $allowCredentials,  
    'userVerification' => 'preferred'
], JSON_UNESCAPED_SLASHES);
?>