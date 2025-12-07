<?php
session_start();

if (!isset($_SESSION['pending_2fa_user_id'])) {
    header("Location: login_client.php");
    exit;
}

require_once "../../controller/userController.php";
require_once "googleauth.php";  // ← This is YOUR working file

$ga = new GoogleAuthenticator();  // ← Correct class name (not PHPGangsta_...)
$uc = new UserController();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim($_POST['code']);
    $user = $uc->getUserById($_SESSION['pending_2fa_user_id']);

    if ($ga->verifyCode($user['totp_secret'], $code, 2)) {
        $uc->resetFailedAttempts($user['id_user']);
        $uc->logLogin($user['id_user'], $user['email'], true);

        $_SESSION['user_id']   = $user['id_user'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = 'client';
        unset($_SESSION['pending_2fa_user_id']);

        header("Location: profile.php");
        exit;
    } else {
        $error = "Invalid or expired code";
        $uc->incrementFailedAttempts($user['id_user']);
        $uc->logLogin($user['id_user'], $user['email'], false);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>2FA Verification</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<div class="container" style="margin-top:150px; max-width:400px;">
    <div class="card">
        <h2 style="color:#00ff88; text-align:center;">Two-Factor Authentication</h2>
        <p style="text-align:center; color:#ccc;">Enter the 6-digit code from Google Authenticator</p>

        <?php if ($error): ?>
            <p style="color:#ff4444; text-align:center;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="code" maxlength="6" placeholder="123456" required 
                   style="width:100%; padding:20px; font-size:2rem; text-align:center; letter-spacing:10px; margin:20px 0;">
            <button type="submit" class="shop-now-btn" style="width:100%; padding:15px;">Verify</button>
        </form>

        <p style="text-align:center; margin-top:20px;">
            <a href="login_client.php" style="color:#00ff88;">← Back to Login</a>
        </p>
    </div>
</div>

<?php include "cookie-consent.php"; ?>
</body>
</html>