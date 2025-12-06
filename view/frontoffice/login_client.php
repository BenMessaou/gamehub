<!-- view/frontoffice/login_client.php -->
<?php
session_start();
require_once "../../controller/userController.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password']; // plain text

    // ==================== NEW: reCAPTCHA v3 ====================
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $recaptcha_secret   = 'YOUR_RECAPTCHA_SECRET_KEY_HERE'; // Change this!
    $recaptcha_verify   = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $recaptcha_result   = json_decode($recaptcha_verify);

    if (!$recaptcha_result->success || ($recaptcha_result->score ?? 0) < 0.5) {
        $error = "Security check failed. Please try again.";
    } else {
        // ==================== YOUR ORIGINAL CODE STARTS HERE ====================
        $uc = new UserController();
        $user = $uc->getUserByEmail($email);

        // NEW: Check if account is locked (3 failed attempts)
        if ($user && $user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) {
            $error = "Account locked due to too many attempts. Try again later.";
        }
        // Original plain-text comparison + role check
        elseif ($user && $user['password'] === $password && strtolower($user['role']) === 'client') {
            // NEW: If user has 2FA enabled → go to verification page
            if (!empty($user['totp_secret'])) {
                $_SESSION['pending_2fa_user_id'] = $user['id_user'];
                header("Location: 2fa_verify.php");
                exit;
            }

            // YOUR ORIGINAL SUCCESSFUL LOGIN
            $_SESSION['user_id']   = $user['id_user'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role']      = 'client';

            // NEW: Reset failed attempts on successful login
            $uc->resetFailedAttempts($user['id_user']);

            header("Location: profile.php");
            exit;
        } else {
            $error = "Wrong email or password.";

            // NEW: Count failed attempts
            if ($user) {
                $uc->incrementFailedAttempts($user['id_user']);
            }
        }
        // ==================== YOUR ORIGINAL CODE ENDS HERE ====================
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Login</title>
    <link rel="stylesheet" href="index.css">

    <!-- reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=YOUR_SITE_KEY_HERE"></script> <!-- Change this! -->
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">Gamehub</h1>
        <img src="logo.png" class="logo1" alt="" >

        <nav>
            <ul>
                <li><a href="#home" class="super-button">Home</a></li>
                <li><a href="#deals" class="super-button">Deals</a></li>
                <li><a href="#deals" class="super-button">Shop Now</a></li>
                <li><a href="#contact" class="super-button">Contact</a></li>
                <li><a href="role.html" class="super-button">Back</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:180px;">
    <div class="card" >
        <h2 style="color:#00ff88; text-align:center;">Client Login</h2><br>

        <?php if($error): ?>
            <div style="color:#ff4444; background:rgba(255,0,0,0.15); padding:12px; border-radius:8px; text-align:center; margin-bottom:20px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required style="width:100%; padding:12px; margin-bottom:15px;"><br>
            <input type="password" name="password" placeholder="Password" required style="width:100%; padding:12px;"><br><br>

            <!-- Hidden reCAPTCHA token -->
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

            <button type="submit" class="shop-now-btn" style="width:100%;">Login</button>
        </form>

        <p style="text-align:center; margin-top:25px; color:#aaa;">
            <a href="verif.php" style="color:#00ff88;">Forgot Password?</a><br><br>
            No account? <a href="signup_client.php" style="color:#00ff88;">Sign up</a>
        </p>
    </div>
</div>

<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('YOUR_SITE_KEY_HERE', {action: 'login'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
    });
</script>

<?php include "cookie-consent.php"; // if you want the cookie banner ?>
</body>
</html>