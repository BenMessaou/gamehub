<?php
session_start();
require_once "../../controllers/userController.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST['credential'])) {
    header("Location: login_client.php"); exit;
}

$data = json_decode($_POST['credential'], true);
if (!$data || empty($data['rawId'])) {
    header("Location: login_client.php"); exit;
}

$rawId = '';
foreach ($data['rawId'] as $b) $rawId .= chr($b);
$incomingId = base64_encode($rawId);

$uc = new UserController();
foreach ($uc->listUsers() as $u) {
    if (!empty($u['passkey_credential'])) {
        $stored = json_decode($u['passkey_credential'], true);
        if ($stored['id'] === $incomingId && strtolower($u['role']) === 'client') {
            $_SESSION['user_id'] = $u['id_user'];
            $_SESSION['user_name'] = $u['name'];
            $_SESSION['role'] = 'client';
            $uc->resetFailedAttempts($u['id_user']);
            header("Location: profile.php");
            exit;
        }
    }
}
header("Location: login_client.php?error=1");
?>