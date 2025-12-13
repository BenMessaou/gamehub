<?php
session_start();
if (!isset($_SESSION['user_id'])) die(json_encode(['error' => 'Unauthorized']));

require_once "../../controller/userController.php";
$uc = new UserController();
$user = $uc->getUserById($_SESSION['user_id']);

$challenge = random_bytes(32);

header('Content-Type: application/json');
echo json_encode([
    'rp' => ['name' => 'GameHub', 'id' => $_SERVER['HTTP_HOST']],
    'user' => [
        'id' => base64_encode('user_'.$user['id_user']),
        'name' => $user['email'],
        'displayName' => $user['name'].' '.$user['lastname']
    ],
    'challenge' => base64_encode($challenge),
    'pubKeyCredParams' => [['type' => 'public-key', 'alg' => -7]],
    'timeout' => 60000,
    'attestation' => 'none',
    'authenticatorSelection' => ['userVerification' => 'preferred']
]);
?>