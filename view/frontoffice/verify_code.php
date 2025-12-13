<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once "../../controllers/userController.php";

if (!isset($_SESSION['reset_email'])) {
    header("Location: verif.php");
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = trim($_POST['code']);
    $db = config::getConnexion();
    $stmt = $db->prepare("SELECT * FROM user WHERE email = ? AND reset_code = ? AND reset_expires > NOW()");
    $stmt->execute([$_SESSION['reset_email'], $code]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['reset_user_id'] = $user['id_user'];
        unset($_SESSION['reset_email']);
        header("Location: reset_password.php");
        exit;
    } else {
        $error = "Invalid or expired code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Code - GameHub</title>
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
                <li><a href="#deals" class="super-button">Deals</a></li>
                <li><a href="#deals" class="super-button">Shop Now</a></li>
                <li><a href="#contact" class="super-button">Contact</a></li>
                <li><a href="login_client.php" class="super-button">Login</a></li>
                <li><a href="verif.php" class="super-button">Forgot Password</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:180px;">
    <div class="card" style="max-width:500px;">
        <h2 style="color:#00ff88; text-align:center;">Enter Verification Code</h2>
        <p style="text-align:center; color:#ccc;">Check your email for the 6-digit code</p>

        <?php if ($error): ?>
            <p style="color:#ff4444; text-align:center; padding:15px; background:rgba(255,0,0,0.2); border-radius:10px;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="code" maxlength="6" placeholder="123456" required 
                   style="width:100%; padding:20px; font-size:2.5rem; text-align:center; letter-spacing:15px; border-radius:50px; border:none;">
            <button type="submit" class="shop-now-btn" style="width:100%; padding:18px; font-size:1.6rem;">
                Verify Code
            </button>
        </form>

        <p style="text-align:center; margin-top:30px;">
            <a href="verif.php" style="color:#00ff88;">← Back</a>
        </p>
    </div>
</div>

<?php include "cookie-consent.php"; ?>
</body>
</html>