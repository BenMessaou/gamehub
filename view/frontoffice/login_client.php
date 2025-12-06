<?php
session_start();
require_once "../../controller/userController.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password']; // plain text

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

            <button type="submit" class="shop-now-btn" style="width:100%;">Login</button>
        </form>

        <!-- FINGERPRINT LOGIN BUTTON — OPTIONAL & SAFE -->
        <div style="margin-top:40px; text-align:center;">
            <p style="color:#aaa; margin-bottom:15px;">Login faster next time with:</p>
            <button onclick="tryFingerprintLogin()" 
                style="background:#0066ff; color:white; border:none; padding:16px 40px; border-radius:50px; font-size:1.2rem; cursor:pointer; font-weight:bold;">
                Fingerprint / Face ID
            </button>
        </div>

        <p style="text-align:center; margin-top:25px; color:#aaa;">
            <a href="verif.php" style="color:#00ff88;">Forgot Password?</a><br><br>
            No account? <a href="signup_client.php" style="color:#00ff88;">Sign up</a>
        </p>
    </div>
</div>

<!-- FINGERPRINT LOGIN SCRIPT — DOES NOTHING IF USER HAS NO PASSKEY -->
<script>
async function tryFingerprintLogin() {
    if (!window.PublicKeyCredential) {
        alert("Your device doesn't support fingerprint login yet");
        return;
    }

    try {
        const resp = await fetch('passkey_login_challenge.php');
        if (!resp.ok) throw new Error();
        const options = await resp.json();

        const credential = await navigator.credentials.get({ publicKey: options });

        const form = document.createElement('form');
        form.method = "POST";
        form.action = "";

        const input = document.createElement('input');
        input.type = "hidden";
        input.name = "passkey_credential";
        input.value = JSON.stringify(credential);
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();

    } catch (e) {
        alert("No fingerprint registered yet. Register it first in your profile!");
    }
}
</script>

<?php include "cookie-consent.php"; ?>
</body>
</html>