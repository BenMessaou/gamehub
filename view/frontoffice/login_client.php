
<!-- frontoffice/login_client.php -->
<?php
session_start();
require_once "../../controller/userController.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password']; // plain text

    $uc = new UserController();
    $user = $uc->getUserByEmail($email);

    // Plain-text comparison (because passwords are stored as plain text right now)
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

        <p style="text-align:center; margin-top:25px; color:#aaa;">
            <a href="verif.php" style="color:#00ff88;">Forgot Password?</a><br><br>
            No account? <a href="signup_client.php" style="color:#00ff88;">Sign up</a>
        </p>
    </div>
</div>

</body>
<?php
session_start();
require_once "../../controller/userController.php";

$error = "";


$uc = new UserController();
$anyPasskeyExists = false;
foreach ($uc->listUsers() as $u) {
    if (!empty($u['passkey_credential'])) {
        $anyPasskeyExists = true;
        break;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['credential'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $user = $uc->getUserByEmail($email);

    if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
        $error = "Account locked for 15 minutes due to too many failed attempts.";
        $uc->logLogin($user['id_user'], $email, false);
    }
    elseif ($user && $user['password'] === $password && strtolower($user['role']) === 'client') {
        $uc->resetFailedAttempts($user['id_user']);
        $uc->logLogin($user['id_user'], $email, true);

        if (!empty($user['totp_secret'])) {
            $_SESSION['pending_2fa_user_id'] = $user['id_user'];
            header("Location: 2fa_verify.php");
            exit;
        }

        $_SESSION['user_id']   = $user['id_user'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = 'client';
        header("Location: profile.php");
        exit;
    } else {
        $error = "Wrong email or password.";
        if ($user) {
            $uc->incrementFailedAttempts($user['id_user']);
            $uc->logLogin($user['id_user'], $email, false);
        } else {
            $uc->logLogin(null, $email, false);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Login - GameHub</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">Gamehub</h1>
        <img src="logo.png" class="logo1" alt="">

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
        <h2 style="color:#00ff88; text-align:center; margin-bottom:20px;">Client Login</h2>

        <?php if ($error): ?>
            <p style="color:#ff4444; text-align:center; margin:15px 0; font-weight:bold;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <div class="input-row">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-row">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="shop-now-btn" style="width:100%; padding:16px; font-size:1.3rem;">
                Login with Password
            </button>
        </form>

        
        <?php if ($anyPasskeyExists): ?>
            <div style="margin:30px 0;:0;">
                <button onclick="loginWithFingerprint()" 
                        style="width:100%; padding:20px; background:#00ff88; color:#000; border:none; border-radius:50px; font-size:1.6rem; font-weight:bold; cursor:pointer; box-shadow:0 0 30px rgba(0,255,136,0.5);">
                    Fingerprint / Face ID Login
                </button>
            </div>
        <?php endif; ?>

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
        if (!resp.ok) throw new Error('Server error');
        const opts = await resp.json();
        opts.challenge = Uint8Array.from(atob(opts.challenge), c => c.charCodeAt(0));
        if (opts.allowCredentials) {
            opts.allowCredentials.forEach(cred => {
                cred.id = Uint8Array.from(atob(cred.id), c => c.charCodeAt(0));
            });
        }

        const credential = await navigator.credentials.get({ publicKey: opts });

        const data = {
            rawId: Array.from(new Uint8Array(credential.rawId)),
            response: {
                authenticatorData: Array.from(new Uint8Array(credential.response.authenticatorData)),
                clientDataJSON: Array.from(new Uint8Array(credential.response.clientDataJSON)),
                signature: Array.from(new Uint8Array(credential.response.signature))
            }
        };

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'passkey_login.php';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'credential';
        input.value = JSON.stringify(data);
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
        

    } catch (err) {
        console.error(err);
        alert("Fingerprint not registered or device not supported.");
    }
}
</script>
<?php include "cookie-consent.php"; ?>
</body>
</html>