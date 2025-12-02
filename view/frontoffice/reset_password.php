
<?php
session_start();
require_once "../../controller/userController.php";
require_once "../../model/User.php";

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: verif.php");
    exit;
}

$userController = new UserController();
$userId = $_SESSION['reset_user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password2'];

    if ($pass1 !== $pass2) {
        $message = "Passwords do not match.";
    } elseif (strlen($pass1) < 8) {
        $message = "Password must be at least 8 characters.";
    } elseif (!preg_match("/^[A-Za-z0-9]+$/", $pass1)) {
        $message = "Password can only contain letters and numbers.";
    } else {
        
$password = $pass1;   
        
      $userData = $userController->getUserById($userId);


$newPassword = $_POST['password'];  

$updatedUser = new User(
    $userId,                    
    $userData['name'],          
    $userData['lastname'],     
    $userData['email'],         
    $newPassword,               
    $userData['cin'],           
    $userData['tel'],           
    $userData['gender'],       
    $userData['role']           
);

$userController->updateUser($updatedUser, $userId);
        unset($_SESSION['reset_user_id']);
        $_SESSION['success'] = "Password updated successfully! Please login.";
        header("Location: login_client.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - GameHub</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .success { color: #00ff88; text-align:center; font-weight:bold; }
        .error { color: #ff4444; text-align:center; font-weight:bold; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Home</a></li>
                <li><a href="role.html" class="super-button">Login</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top: 180px; max-width: 500px;">
    <div class="card">
        <h2 style="color:#00ff88; text-align:center;">Create New Password</h2>

        <?php if ($message): ?>
            <div class="<?= strpos($message, 'successfully') ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required><br><br>
            <input type="password" name="password2" placeholder="Confirm New Password" required><br><br>

            <button type="submit" class="shop-now-btn" style="width:100%;">Update Password</button>
        </form>

        <p style="text-align:center; margin-top:20px;">
            <a href="login_client.php" style="color:#00ff88;">← Back to Login</a>
        </p>
    </div>
</div>

</body>
</html>