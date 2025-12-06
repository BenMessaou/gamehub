<?php
session_start();
require_once "../../controller/userController.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST['credential'])) {
    header("Location: login_client.php");
    exit;
}

$data = json_decode($_POST['credential'], true);
if (!$data || empty($data['rawId'])) {
    header("Location: login_client.php?error=1");
    exit;
}

$uc = new UserController();
$users = $uc->listUsers()->fetchAll();

foreach ($users as $u) {
    if (!empty($u['passkey_credential'])) {
        $stored = json_decode($u['passkey_credential'], true);
        $incomingId = base64_encode(implode('', array_map('chr', $data['rawId'])));

        if ($stored['id'] === $incomingId && strtolower($u['role']) === 'client') {
            $uc->resetFailedAttempts($u['id_user']);
            $uc->logLogin($u['id_user'], $u['email'], true);

            // Optional: Force 2FA even with fingerprint (remove // to enable)
            // if (!empty($u['totp_secret'])) {
            //     $_SESSION['pending_2fa_user_id'] = $u['id_user'];
            //     header("Location: 2fa_verify.php"); exit;
            // }

            $_SESSION['user_id'] = $u['id_user'];
            $_SESSION['user_name'] = $u['name'];
            $_SESSION['role'] = 'client';
            header("Location: profile.php");
            exit;
        }
    }
}

header("Location: login_client.php?error=1");
?>