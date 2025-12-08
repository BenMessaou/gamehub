<?php
session_start();
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
<div class="container" style="margin-top:180px;">
    <div class="card" style="max-width:500px;">
        <h2 style="color:#00ff88; text-align:center;">Enter Verification Code</h2>
        <p style="text-align:center; color:#ccc;">Check your email for the 6-digit code</p>

        <?php if ($error): ?>
            <p style="color:#ff4444; text-align:center;"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="code" maxlength="6" placeholder="123456" required 
                   style="width:100%; padding:20px; font-size:2rem; text-align:center; letter-spacing:10px;">
            <button type="submit" class="shop-now-btn" style="width:100%; margin-top:20px; padding:16px;">
                Verify & Reset Password
            </button>
        </form>

        <p style="text-align:center; margin-top:20px;">
            <a href="verif.php" style="color:#00ff88;">← Back</a>
        </p>
    </div>
</div>
</body>
</html>