<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login_client.php"); 
    exit; 
}

require_once "../../controller/userController.php";
require_once "googleauth.php";          

$ga = new GoogleAuthenticator();
$uc = new UserController();
$user = $uc->getUserById($_SESSION['user_id']);

// ken yheb inahi 2fa
if (isset($_GET['disable'])) {
    config::getConnexion()
           ->prepare("UPDATE user SET totp_secret = NULL WHERE id_user = ?")
           ->execute([$_SESSION['user_id']]);
    echo "<script>alert('2FA Disabled'); window.location='profile.php';</script>";
    exit;
}

// generati el qr code wel seceret code
if (empty($user['totp_secret'])) {
    $secret = $ga->createSecret();                                    
    $qrCodeUrl = $ga->getQRCodeGoogleUrl('GameHub - '.$user['email'], $secret);
    config::getConnexion()
           ->prepare("UPDATE user SET totp_secret = ? WHERE id_user = ?")
           ->execute([$secret, $_SESSION['user_id']]);
} else {
    $secret = $user['totp_secret'];
    $qrCodeUrl = $ga->getQRCodeGoogleUrl('GameHub - '.$user['email'], $secret);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Enable Two-Factor Authentication</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="container" style="margin-top:100px;">
    <div class="card" style="max-width:550px;margin:0 auto;padding:40px;text-align:center;">
        <h2 style="color:#00ff88;">Enable Two-Factor Authentication</h2>
        <p style="color:#ccc;margin:20px 0;">
            1. Install <strong>Google Authenticator</strong> or <strong>Authy</strong><br>
            2. Scan the QR code below:
        </p>

        <img src="<?= $qrCodeUrl ?>" alt="QR Code" style="width:280px;height:280px;border:8px solid #111;border-radius:15px;">

        <p style="margin:30px 0 10px;color:#aaa;">
            Can't scan? Enter manually:
        </p>
        <code style="background:#222;padding:12px 20px;font-size:1.4rem;letter-spacing:4px;border-radius:8px;">
            <?= $secret ?>
        </code>

        <br><br>
        <a href="profile.php" class="shop-now-btn" style="padding:16px 50px;font-size:1.2rem;">
            I scanned it → Go back
        </a>
    </div>
</div>
</body>
</html>