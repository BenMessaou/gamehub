<?php
session_start();
if (!isset($_SESSION['pending_2fa_user_id'])) {
    header("Location: login_client.php"); exit;
}

require_once "../../controllers/userController.php";
require_once "googleauth.php";
$ga = new GoogleAuthenticator();
$uc = new UserController();
$error = "";

if ($_POST['code']) {
    $code = $_POST['code'];
    $user = $uc->getUserById($_SESSION['pending_2fa_user_id']);

    if ($ga->verifyCode($user['totp_secret'], $code, 2)) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = 'client';
        unset($_SESSION['pending_2fa_user_id']);
        $uc->resetFailedAttempts($user['id_user']);
        header("Location: profile.php");
        exit;
    } else {
        $error = "Invalid or expired code";
        $uc->incrementFailedAttempts($user['id_user']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>2FA - GameHub</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">Gamehub</h1>
        <img src="logo.png" class="logo1" alt="">
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Home</a></li>
                <li><a href="login_client.php" class="super-button">Login</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:180px;">
    <div class="card" style="max-width:500px;">
        <h2 style="color:#00ff88; text-align:center;">Two-Factor Authentication</h2>
        <p style="text-align:center; color:#ccc;">Enter the code from your authenticator app</p>

        <?php if ($error): ?>
            <p style="color:#ff4444; text-align:center; padding:15px; background:rgba(255,0,0,0.2); border-radius:10px;">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="code" maxlength="6" placeholder="123456" required 
                   style="width:100%; padding:25px; font-size:3rem; text-align:center; letter-spacing:15px; border-radius:50px; border:none;">
            <button type="submit" class="shop-now-btn" style="width:100%; padding:20px; font-size:1.6rem; margin-top:20px;">
                Verify
            </button>
        </form>

        <p style="text-align:center; margin-top:30px;">
            <a href="login_client.php" style="color:#00ff88;">Back to Login</a>
        </p>
    </div>
</div>

<?php include "cookie-consent.php"; ?>
</body>
</html>