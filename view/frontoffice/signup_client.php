<?php
// user_project/view/frontoffice/signup_client.php
session_start();

require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../model/User.php';

$userController = new UserController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $cin      = $_POST['cin'] ?? '';
    $tel      = $_POST['tel'] ?? '';
    $gender   = $_POST['gender'] ?? ''; // if present in form
    $role     = 'client';

    // You already have JS validation (saisie()), but we can still rely on it

    // Create a User object (id_user = null for insert)
    $user = new User(
        null,
        $name,
        $lastname,
        $email,
        $password, // later: password_hash(...)
        $cin,
        $tel,
        $gender,
        $role
    );

    try {
        // Insert into DB
        $userController->addUser($user);

        // Retrieve the inserted user (by email)
        $insertedUser = $userController->getUserByEmail($email);

        if ($insertedUser) {
            // Store ID in session
            $_SESSION['user_id'] = $insertedUser['id_user'];

            // Redirect to profile
            header('Location: profile.php');
            exit;
        } else {
            $error = 'Signup succeeded but user could not be loaded.';
        }
    } catch (Exception $e) {
        $error = 'Error during signup: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Sign Up</title>
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

<div class="container" style="margin-top:150px; justify-content:center;">
    <div class="card">
        <h4>Client Sign Up</h4>

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="" onsubmit="return saisie();">
            <div id="errorBox" style="color:red;"></div>

            <div class="input-row">
                
                <input type="text" placeholder="name" id="name" name="name" required>
            </div>

            <div class="input-row">
                <input type="text" placeholder="last name" id="lastname" name="lastname" required>
            </div>

            <div class="input-row">
               
                <input type="email" placeholder="Email" id="email" name="email" required>
            </div>

            <div class="input-row">
                <input type="password" placeholder="password" id="password" name="password" required>
            </div>

            <div class="input-row">
                
                <input type="password" placeholder="retype password" id="password2" name="password2" required>
            </div>

            <div class="input-row">
                <input type="text" placeholder="cin" id="cin" name="cin" required>
            </div>

            <div class="input-row">
                <input type="text"placeholder="tel" id="tel" name="tel" required>
            </div>

            <!-- Optional gender radios (only if you want) -->
            <div class="input-row">
                <p>Gender</p>
                <label><input type="radio" name="gender" value="M"> Male</label>
                <label><input type="radio" name="gender" value="F"> Female</label>
            </div>

            <button type="submit" class="super-button">Sign Up</button>
        </form>
    </div>
</div>

<script src="java.js"></script>
</body>
</html>
