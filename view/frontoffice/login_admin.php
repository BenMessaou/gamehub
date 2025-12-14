
<?php
session_start();
require_once __DIR__ . '/../../controller/userController.php';

$userController = new UserController();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $user = $userController->getUserByEmail($email);
    if ($user && $user['password'] === $password && $user['role'] === 'admin') {
        $_SESSION['admin_id'] = $user['id_user'];
        header('Location: ../backoffice/index.php');
        exit;
    } else {
        $error = 'Invalid credentials or you are not an admin.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Gamehub</title>
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
                    
                
            </ul>
        </nav>
    </div>
</header>


<div class="container" style="margin-top:150px;">
    <div class="card">
        <h4>Admin Login</h4>

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <div class="input-row">
                <input type="email" placeholder="admin email" id="email" name="email" >
            </div>

            <div class="input-row">
                <input type="password" placeholder="password" id="password" name="password" >
            </div>

            <button type="submit" class="shop-now-btn">Login as Admin</button>
        </form>
    </div>
</div>

<script src="java.js"></script>
</body>
</html>
