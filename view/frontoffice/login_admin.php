<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/UserController.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminC = new getUserById($id);
    $admin = $adminC->login($_POST['admin_id']);

    if ($admin) {
        header("Location: /../backoffice/index.php");
        exit;
    } else {
        $error = "Admin not found. Incorrect credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>

<header>
    <div class="container">
        <h1 class="logo">gamehub</h1>

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

<div class="profile-container" style="margin-top:150px; justify-content:center;">
    <div class="card" style="width:450px;">
        
        <h2 style="color:#00ff88; margin-bottom:20px;">Admin Login</h2>

        <?php if ($error): ?>
            <div style="color:red; margin-bottom:20px; font-size:1.1rem;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input type="text"name="admin_id" placeholder="Admin ID" 
               style="width:100%; padding:12px; margin-bottom:15px;">

            <input type="text" name="email" placeholder="Email" 
               style="width:100%; padding:12px; margin-bottom:15px;">

            <input type="password" name="password" placeholder="Password" 
               style="width:100%; padding:12px; margin-bottom:30px;">

            <button class="shop-now-btn" style="width:100%;">Login</button>
        </form>

    </div>
</div>

</body>
</html>
