<?php
session_start();
require_once "../../controller/userController.php";

$error = "";
$show_2fa_section = false;
$uc = new UserController();

// Auto-bypass reCAPTCHA on localhost
function verifyRecaptcha($token) {
    if (in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) return true;
    $secret = 'YOUR_REAL_SECRET_KEY'; // put real one when live
    $response = @file_get_contents("https://www.google.com/recaptcha/api.siteverify?secret={$secret}&response={$token}");
    $result = json_decode($response);
    return $result && $result->success && ($result->score ?? 0) >= 0.5;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $code = $_POST['code'] ?? '';
    $credential = $_POST['credential'] ?? ''; // from passkey

    if (!verifyRecaptcha($_POST['g-recaptcha-response'] ?? '')) {
        $error = "Security check failed.";
    } else {
        $user = $uc->getUserByEmail($email);

        if ($user && $user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) {
            $error = "Account locked.";
        }
        elseif ($user && $user['password'] === $password && strtolower($user['role']) === 'client') {

            // 2FA or Passkey enabled?
            if (!empty($user['totp_secret']) || !empty($user['passkey_credential'])) {
                $show_2fa_section = true;
                $_SESSION['pending_2fa_user_id'] = $user['id_user'];
            } else {
                // No 2FA → login
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = 'client';
                $uc->resetFailedAttempts($user['id_user']);
                header("Location: profile.php");
                exit;
            }
        } else {
            $error = "Wrong email or password.";
            if ($user) $uc->incrementFailedAttempts($user['id_user']);
        }
    }

    // HANDLE PASKEY LOGIN (fingerprint/face)
    if ($show_2fa_section && $credential) {
        $stored = json_decode($user['passkey_credential'], true);
        if ($stored && hash_equals($stored['id'], $credential)) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = 'client';
            unset($_SESSION['pending_2fa_user_id']);
            $uc->resetFailedAttempts($user['id_user']);
            header("Location: profile.php");
            exit;
        }
    }

    // HANDLE TOTP CODE
    if ($show_2fa_section && $code) {
        require_once "googleauth.php";
        $ga = new GoogleAuthenticator();
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
            $error = "Invalid code";
            $uc->incrementFailedAttempts($user['id_user']);
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
    <script src="https://www.google.com/recaptcha/api.js?render=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></script>
</head>
<body>

<!-- Your header -->
<header>...</header>

<div class="container" style="margin-top:180px;">
    <div class="card" style="max-width:480px;margin:0 auto;padding:40px;">
        <h2 style="color:#00ff88;text-align:center;">Client Login</h2>

        <?php if($error): ?>
            <div style="color:#ff4444;background:rgba(255,0,0,0.2);padding:15px;border-radius:10px;text-align:center;margin:20px 0;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email']??'') ?>" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>

            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            <input type="hidden" name="credential" id="credential">

            <button type="submit" class="shop-now-btn" style="width:100%;padding:16px;">
                <?= $show_2fa_section ? 'Continue' : 'Login' ?>
            </button>
        </form>

        <!-- FINGERPRINT + TOTP SECTION -->
        <div id="securityBox" style="margin-top:40px;text-align:center;display:<?= $show_2fa_section?'block':'none' ?>;">
            <p style="color:#00ff88;font-size:1.4rem;">Verify Identity</p>

            <!-- FINGERPRINT BUTTON -->
            <button onclick="loginWithFingerprint()" class="shop-now-btn" 
                    style="background:#0066ff;margin:20px auto;display:block;padding:18px 40px;font-size:1.3rem;">
                Fingerprint / Face ID
            </button>

            <p style="color:#888;margin:20px 0;">OR</p>

            <!-- TOTP CODE -->
            <form method="POST">
                <input type="text" name="code" maxlength="6" placeholder="------" required autofocus
                       style="font-size:3rem;width:300px;letter-spacing:25px;text-align:center;padding:20px;background:rgba(0,255,136,0.1);border:3px solid #00ff88;border-radius:15px;">
                <input type="hidden" name="email" value="<?= htmlspecialchars($_POST['email']??'') ?>">
                <input type="hidden" name="password" value="<?= htmlspecialchars($_POST['password']??'') ?>">
                <br><br>
                <button type="submit" class="shop-now-btn" style="width:300px;padding:18px;">Enter Code</button>
            </form>
        </div>

        <div style="text-align:center;margin-top:40px;color:#888;">
            <a href="verif.php" style="color:#00ff88;">Forgot Password?</a><br><br>
            <a href="signup_client.php" style="color:#00ff88;">Sign up</a>
        </div>
    </div>
</div>

<script>
// reCAPTCHA
grecaptcha.ready(() => {
    grecaptcha.execute('6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI', {action: 'login'}).then(token => {
        document.getElementById('g-recaptcha-response').value = token;
    });
});

// FINGERPRINT LOGIN
async function loginWithFingerprint() {
    try {
        const resp = await fetch('passkey_challenge.php');
        const challenge = await resp.json();

        const credential = await navigator.credentials.get({
            publicKey: {
                challenge: Uint8Array.from(atob(challenge.challenge), c => c.charCodeAt(0)),
                allowCredentials: challenge.allowCredentials.map(c => ({
                    id: Uint8Array.from(atob(c.id), c => c.charCodeAt(0)),
                    type: 'public-key'
                })),
                timeout: 60000,
            }
        });

        document.getElementById('credential').value = btoa(
            String.fromCharCode(...new Uint8Array(credential.rawId))
        );
        document.getElementById('loginForm').submit();
    } catch (e) {
        alert('No fingerprint registered or not supported');
    );
    }
}
</script>

<?php include "cookie-consent.php"; ?>
</body>
</html>