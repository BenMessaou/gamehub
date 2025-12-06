<?php
session_start();
require_once "../../controller/userController.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST['passkey_credential'])) {
    header("Location: login_client.php");
    exit;
}

$data = json_decode($_POST['passkey_credential'], true);
if (!$data) {
    header("Location: login_client.php?error=1");
    exit;
}

$uc = new UserController();
$users = $uc->listUsers();

foreach ($users as $u) {
    if (!empty($u['passkey_credential'])) {
        $stored = json_decode($u['passkey_credential'], true);
        if ($stored && $stored['id'] === ($data['rawId'] ?? $data['id'])) {
            $_SESSION['user_id'] = $u['id_user'];
            $_SESSION['user_name'] = $u['name'];
            $_SESSION['role'] = 'client';
            header("Location: profile.php");
            exit;
        }
    }
}

header("Location: login_client.php?error=1");