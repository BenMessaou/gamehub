<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';

if (!isset($_SESSION['user_id'])) {
    // Not logged in → go to login
    header('Location: login_client.php');
    exit;
}

$userController = new UserController();
$userId = $_SESSION['user_id'];

// Use your controller method
$user = $userController->getUserById($userId);
if (!$user) {
    // User not found (deleted?) → clear session and go to login
    session_destroy();
    header('Location: login_client.php');
    exit;
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Gamehub</title>
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
                <li><a class="super-button" href="index.php">Dashboard</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container profile-container">
        <!-- LEFT COLUMN -->
        <div class="left-column">
            <div class="card profile-card">
                <div class="profile-header">
                    <img id="profile-photo" src="" alt="Profile Photo" class="profile-photo">
                    <!-- 👇 name from DB -->
                    <h4><?= e($user['name'] . ' ' . $user['lastname']) ?></h4>
                    <p class="editable job">Gamehub Player</p>
                </div>
            </div>

            <div class="card">
                <h4>About Me</h4>
                <ul class="about-list">
                    <li><strong>Full Name:</strong>
                        <span class="editable">
                            <?= e($user['name'] . ' ' . $user['lastname']) ?>
                        </span>
                    </li>
                    <li><strong>Email:</strong>
                        <span class="editable"><?= e($user['email']) ?></span>
                    </li>
                    <li><strong>Phone:</strong>
                        <span class="editable"><?= e($user['tel']) ?></span>
                    </li>
                    <li><strong>CIN:</strong>
                        <span class="editable"><?= e($user['cin']) ?></span>
                    </li>
                    <li><strong>Address:</strong>
                        <span class="editable">
                            <?php
                            // ⚠️ Your current table doesn't have "address" column.
                            // If you create it later, fill it from $user['address'].
                            echo isset($user['address']) ? e($user['address']) : 'Not set yet';
                            ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- MIDDLE COLUMN -->
        <div class="middle-column">
            <div class="card">
                <h4>Bio</h4>
                <p class="editable">
                    Welcome back, <?= e($user['name']) ?>! This is your bio area.
                </p>
            </div>

            <div class="card">
                <h4>Genre Interests</h4>
                <ul class="genre-list">
                    <li class="super-button">Sci-Fi</li>
                    <li class="super-button">Horror</li>
                    <li class="super-button">Action</li>
                    <li class="super-button">Drama</li>
                </ul>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-column">
            <div class="card">
                <h4>Games Interested In</h4>
                <ul>
                    <li>Cyber Arena</li>
                    <li>Neon Racer</li>
                    <li>Galaxy Clash</li>
                </ul>
            </div>

            <div class="card">
                <h4>Recent Activity</h4>
                <ul>
                    <li>Logged in as <?= e($user['email']) ?></li>
                    <li>Role: <?= e($user['role']) ?></li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script src="java.js"></script>
</body>
</html>
