<?php
session_start();
require_once "../../controller/userController.php";

$uc = new UserController();
$users = $uc->listUsers();

$allow = [];
foreach ($users as $u) {
    if (!empty($u['passkey_credential'])) {
        $cred = json_decode($u['passkey_credential'], true);
        if ($cred) {
            $allow[] = [
                "type" => "public-key",
                "id" => $cred['id']
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    "challenge" => base64_encode(random_bytes(32)),
    "allowCredentials" => $allow,
    "timeout" => 60000,
    "userVerification" => "preferred"
]);