<?php
session_start();
if (!isset($_SESSION['pending_2fa_user_id'])) {
    header("Location: login_client.php"); exit;
}

require_once "../../controller/userController.php";
require_once "googleauth.php";           // ← our file
$ga = new GoogleAuthenticator();         // ← create the object

$uc = new UserController();
$ga = new PHPGangsta_GoogleAuthenticator();
$error = "";

if ($_POST['code']) {
    $code = $_POST['code'];
    $user = $uc->getUserById($_SESSION['pending_2fa_user_id']);

    if ($ga->verifyCode($user['totp_secret'], $code, 2)) { // 2 = 2*30sec windows
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = 'client';
        unset($_SESSION['pending_2fa_user_id']);
        $uc->resetFailedAttempts($user['id_user']);
        $uc->logLogin($user['id_user'], $user['email'], true);
        header("Location: profile.php");
        exit;
    } else {
        $error = "Invalid or expired code";
        $uc->incrementFailedAttempts($user['id_user']);
    }
}
?>

<!DOCTYPE html>
<html><head><title>2FA</title><link rel="stylesheet" href="index.css"></head><body>
<div class="container" style="margin-top:150px; max-width:400px;">
    <div class="card">
        <h2 style="color:#00ff88; text-align:center;">Two-Factor Authentication</h2>
        <p style="text-align:center;">Enter the 6-digit code from your authenticator app</p>

        <?php if($error): ?><p style="color:#ff4444;text-align:center;"><?= $error ?></p><?php endif; ?>

        <form method="POST">
            <input type="text" name="code" maxlength="6" placeholder="123456" required style="width:100%;padding:15px;font-size:1.5rem;text-align:center;letter-spacing:8px;">
            <br><br>
            <button type="submit" class="shop-now-btn" style="width:100%;">Verify</button>
        </form>
    </div>
</div>
</body></html>