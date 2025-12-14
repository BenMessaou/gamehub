
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
        <h1 class="logo">gamehub</h1>
        <img src="logo.png" class="logo1" alt="">
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Projects</a></li>
                <li><a href="#deals" class="super-button">Events
                </a></li>
                <li><a href="../shop.php" class="super-button">Shop </a></li>
                <li><a href="#contact" class="super-button">Article</a></li><li><a class="super-button" href="../index1.php">feedback</a></li>
                <li><a class="super-button" href="profile.php">Profile</a></li><a href="role.html" class="super-button">Back</a></li>
                
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
