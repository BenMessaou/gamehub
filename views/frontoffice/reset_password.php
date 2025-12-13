<?php
session_start();
require_once "../../controllers/userController.php";

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: verif.php");
    exit;
}

$uc = new UserController();
$userId = $_SESSION['reset_user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password2'];

    if ($pass1 !== $pass2) {
        $message = "Passwords do not match.";
    } elseif (strlen($pass1) < 8) {
        $message = "Password must be at least 8 characters.";
    } else {
        $uc->resetUserPassword($userId, $pass1);
        $uc->clearResetCode($userId);
        unset($_SESSION['reset_user_id']);
        $message = "Password reset successful! You can now log in.";
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - GameHub</title>
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
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:180px;">
    <div class="card" style="max-width:500px;">
        <h2 style="color:#00ff88; text-align:center;">Reset Your Password</h2>

        <?php if (isset($success)): ?>
            <p style="color:#00ff88; text-align:center; font-weight:bold; padding:20px;">
                <?= $message ?>
            </p>
            <div style="text-align:center;">
                <a href="login_client.php" class="shop-now-btn" style="padding:20px 60px; font-size:1.6rem;">
                    Back to Login
                </a>
            </div>
        <?php else: ?>
            <?php if ($message): ?>
                <p style="color:#ff4444; text-align:center; padding:15px; background:rgba(255,0,0,0.2); border-radius:10px;">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <input type="password" name="password" placeholder="New Password" required 
                       style="width:100%; padding:18px; margin:15px 0; border-radius:50px; border:none;">
                <input type="password" name="password2" placeholder="Confirm Password" required 
                       style="width:100%; padding:18px; margin:15px 0; border-radius:50px; border:none;">
                <button type="submit" class="shop-now-btn" style="width:100%; padding:18px; font-size:1.6rem;">
                    Reset Password
                </button>
            </form>
        <?php endif; ?>

        <p style="text-align:center; margin-top:30px;">
            <a href="login_client.php" style="color:#00ff88;">Back to Login</a>
        </p>
    </div>
</div>

<?php include "cookie-consent.php"; ?>
</body>
</html>