<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login_client.php"); exit; }

require_once "../../controller/userController.php";
require_once "googleauth.php";           // ← our file
$ga = new GoogleAuthenticator();         // ← create the object


$uc = new UserController();
$user = $uc->getUserById($_SESSION['user_id']);

if (isset($_GET['disable'])) {
    config::getConnexion()->prepare("UPDATE user SET totp_secret = NULL WHERE id_user = ?")
                           ->execute([$_SESSION['user_id']]);
    echo "<script>alert('2FA Disabled'); window.location='profile.php';</script>";
    exit;
}

if (empty($user['totp_secret'])) {
    $secret = $ga->createSecret();
    $qrCodeUrl = $ga->getQRCodeGoogleUrl('GameHub - '.$user['email'], $secret);
    config::getConnexion()->prepare("UPDATE user SET totp_secret = ? WHERE id_user = ?")
                           ->execute([$secret, $_SESSION['user_id']]);
}
?>

<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Setup 2FA</title><link rel="stylesheet" href="index.css"></head><body>
<div class="container" style="margin-top:100px;">
    <div class="card" style="max-width:500px;margin:0 auto;text-align:center;padding:40px;">
        <h2 style="color:#00ff88;">Enable Two-Factor Authentication</h2>
        <p>1. Install <strong>Google Authenticator</strong> or <strong>Authy</strong></p>
        <p>2. Scan the QR code below:</p>
        <br>
        <img src="<?= $qrCodeUrl ?>" alt="QR Code" style="width:250px;height:250px;">
        <br><br>
        <p>Can't scan? Enter manually:<br>
           <code style="background:#222;padding:10px;font-size:1.3rem;letter-spacing:3px;"><?= $secret ?></code>
        </p>
        <br>
        <a href="profile.php" class="shop-now-btn" style="padding:15px 40px;">I scanned it → Continue</a>
    </div>
</div>
</body></html>