

<?php
session_start();
require_once "../../controller/userController.php";

$error = "";
$userController = new UserController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $tel = trim($_POST['tel']);

    if (empty($email) || empty($tel)) {
        $error = "Please fill in both fields.";
    } else {
        $user = $userController->getUserByEmail($email);
        if ($user && $user['tel'] == $tel) {
            $_SESSION['reset_user_id'] = $user['id_user'];
            header("Location: reset_password.php");
            exit;
        } else {
            $error = "Email or phone number does not match our records.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Identity - GameHub</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .error-msg {
            color: #ff4444;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            min-height: 24px;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <img src="logo.png" class="logo1" alt="">
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Home</a></li>
                <li><a href="#deals" class="super-button">Deals</a></li>
                <li><a href="#deals" class="super-button">Shop Now</a></li>
                <li><a href="#contact" class="super-button">Contact</a></li>
                <li><a href="role.html" class="super-button">Login</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top: 180px; max-width: 500px;">
    <div class="card">
        <h2 style="color:#00ff88; text-align:center;">Verify Your Identity</h2>
        <p style="text-align:center; color:#aaa;">Enter your email and phone number to reset password</p>

        <form method="POST" onsubmit="return validateVerifForm()"><div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <input type="email" name="email" placeholder="Email Address" ><br><br>
            <input type="text" name="tel" placeholder="Phone Number (8 digits)" maxlength="8" ><br><br>

            <button type="submit" class="shop-now-btn" style="width:100%;">Continue</button>
        </form>

        <p style="text-align:center; margin-top:20px;">
            <a href="login_client.php" style="color:#00ff88;">← Back to Login</a>
        </p>
    </div>
</div>

<script src="java.js"></script>
<script>
function validateVerifForm() {
    const email = document.querySelector("input[name='email']").value.trim();
    const tel = document.querySelector("input[name='tel']").value.trim();
    const errorBox = document.querySelector(".error-msg");

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const telRegex = /^\d{8}$/;

    if (!emailRegex.test(email)) {
        errorBox.textContent = "Please enter a valid email.";
        return false;
    }
    if (!telRegex.test(tel)) {
        errorBox.textContent = "Phone must be exactly 8 digits.";
        return false;
    }
    return true;
}
</script>

</body>

<?php
session_start();
require_once "../../controller/userController.php";
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    $uc = new UserController();
    $user = $uc->getUserByEmail($email);

    if (!$user) {
        $error = "No account found with this email.";
    } else {
        $code = sprintf("%06d", mt_rand(0, 999999));
        $expires = date("Y-m-d H:i:s", strtotime('+10 minutes'));

        $db = config::getConnexion();
        $stmt = $db->prepare("UPDATE user SET reset_code = ?, reset_expires = ? WHERE id_user = ?");
        $stmt->execute([$code, $expires, $user['id_user']]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nourkahlaoui1234@gmail.com';           
            $mail->Password   = 'fdof mbwg esxu tzma';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('yourgmail@gmail.com', 'GameHub');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your GameHub Reset Code';
            $mail->Body    = "
                <div style='text-align:center; font-family:Arial; padding:30px; background:#111; color:#fff;'>
                    <h1 style='color:#00ff88;'>GameHub</h1>
                    <h2>Your verification code:</h2>
                    <h1 style='font-size:50px; letter-spacing:10px; color:#00ff88;'>$code</h1>
                    <p>Valid for 10 minutes</p>
                </div>
            ";

            $mail->send();
            $success = "Check your email for the 6-digit code!";
            $_SESSION['reset_email'] = $email;
        } catch (Exception $e) {
            $error = "Email failed. Try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - GameHub</title>
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
                <li><a href="login_client.php" class="super-button">Login</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:180px;">
    <div class="card" style="max-width:500px;">
        <h2 style="color:#00ff88; text-align:center;">Forgot Password</h2>

        <?php if ($error): ?>
            <p style="color:#ff4444; text-align:center; padding:15px; background:rgba(255,0,0,0.2); border-radius:10px; margin:15px 0;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color:#00ff88; text-align:center; font-weight:bold; padding:20px;">
                <?= $success ?>
            </p>
            <div style="text-align:center;">
                <a href="verify_code.php" class="shop-now-btn" style="padding:20px 50px; font-size:1.6rem;">
                    Enter Code
                </a>
            </div>
        <?php else: ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Your Email" required 
                       style="width:100%; padding:18px; margin:15px 0; border-radius:50px; border:none; font-size:1.3rem;">
                <button type="submit" class="shop-now-btn" style="width:100%; padding:20px; font-size:1.6rem;">
                    Send Code
                </button>
            </form>
        <?php endif; ?>

        <p style="text-align:center; margin-top:30px;">
            <a href="login_client.php" style="color:#00ff88;">Back to Login</a>
        </p>
    </div>
</div>
</body>

</html>