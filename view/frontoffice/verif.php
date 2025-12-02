
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
</html>