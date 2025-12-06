<?php
session_start();
require_once "../../controller/userController.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['passkey_login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $uc = new UserController();
    $user = $uc->getUserByEmail($email);

    if ($user && $user['password'] === $password && strtolower($user['role']) === 'client') {
        $_SESSION['user_id']   = $user['id_user'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = 'client';
        header("Location: profile.php");
        exit;
    } else {
        $error = "Wrong email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Login</title>
    <link rel="stylesheet" href="index.css">
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
    <div class="card">
        <h2 style="color:#00ff88; text-align:center;">Client Login</h2><br>

        <?php if($error): ?>
            <div style="color:#ff4444; background:rgba(255,0,0,0.15); padding:12px; border-radius:8px; text-align:center; margin-bottom:20px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required style="width:100%; padding:12px; margin-bottom:15px;"><br>
            <input type="password" name="password" placeholder="Password" required style="width:100%; padding:12px;"><br><br>

            <button type="submit" class="shop-now-btn" style="width:100%;">Login</button>
        </form>

        <!-- FINGERPRINT BUTTON — CLEAN, BEAUTIFUL, WORKS -->
        <?php 
        if (!empty($_POST['email'])) {
            $uc = new UserController();
            $u = $uc->getUserByEmail($_POST['email']);
            if ($u && !empty($u['passkey_credential'])) {
                echo '
                <div style="margin-top:35px; text-align:center;">
                    <p style="color:#00ff88; margin-bottom:15px; font-size:1.1rem;">Or login instantly:</p>
                    <button onclick="loginWithFingerprint()" 
                            style="background:linear-gradient(45deg,#0066ff,#00ff88); color:white; border:none; padding:18px 50px; border-radius:50px; font-size:1.4rem; font-weight:bold; cursor:pointer; box-shadow:0 0 30px rgba(0,255,136,0.5);">
                        Fingerprint / Face ID
                    </button>
                </div>';
            }
        }
        ?>

        <p style="text-align:center; margin-top:25px; color:#aaa;">
            <a href="verif.php" style="color:#00ff88;">Forgot Password?</a><br><br>
            No account? <a href="signup_client.php" style="color:#00ff88;">Sign up</a>
        </p>
    </div>
</div>

<script>
async function loginWithFingerprint() {
    try {
        const resp = await fetch('passkey_login_challenge.php');
        const opts = await resp.json();

        opts.challenge = Uint8Array.from(atob(opts.challenge), c => c.charCodeAt(0));
        opts.allowCredentials.forEach(c => c.id = Uint8Array.from(atob(c.id), b => b.charCodeAt(0)));

        const cred = await navigator.credentials.get({ publicKey: opts });

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'passkey_login.php';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'credential';
        input.value = JSON.stringify(cred);
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    } catch (e) {
        alert("No fingerprint registered or not supported");
    }
}
</script>

<?php include "cookie-consent.php"; ?>
</body>
</html>