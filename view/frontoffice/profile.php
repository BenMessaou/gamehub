<?php
session_start();
require_once __DIR__ . '/../../controller/userController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_client.php');
    exit;
}

$userController = new UserController();
$userId = $_SESSION['user_id'];
$user = $userController->getUserById($userId);

if (!$user) {
    session_destroy();
    header('Location: login_client.php');
    exit;
}


$user['verified']               = $user['verified'] ?? 0;
$user['verification_requested'] = $user['verification_requested'] ?? 0;
$user['address']                = $user['address'] ?? 'Not set yet';

$message = '';
$messageType = '';

// Handle verification request
if (isset($_POST['request_verification'])) {
    try {
        $db = config::getConnexion();

        // Auto-create columns if missing
        $db->exec("ALTER TABLE user ADD COLUMN IF NOT EXISTS verified TINYINT(1) DEFAULT 0");
        $db->exec("ALTER TABLE user ADD COLUMN IF NOT EXISTS verification_requested TINYINT(1) DEFAULT 0");

        $sql = "UPDATE user SET verification_requested = 1 WHERE id_user = :id AND verification_requested = 0";
        $req = $db->prepare($sql);
        $req->execute([':id' => $userId]);

        if ($req->rowCount() > 0) {
            $message = "Verification request sent! Waiting for admin approval.";
            $messageType = "success";
            $user['verification_requested'] = 1;
        } else {
            $message = "You have already requested verification.";
            $messageType = "warning";
        }
    } catch (Exception $e) {
        $message = "Error sending request. Please try again.";
        $messageType = "error";
    }
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - GameHub</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .message {
            padding: 14px 20px;
            border-radius: 12px;
            margin: 20px auto;
            max-width: 600px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 0 20px rgba(0,0,0,0.6);
        }
        .success { background: rgba(0, 255, 136, 0.15); color: #00ff88; border: 1px solid #00ff88; }
        .warning { background: rgba(255, 170, 0, 0.15); color: #ffdd00; border: 1px solid #ffaa00; }
        .error   { background: rgba(255, 0, 0, 0.15); color: #ff4444; border: 1px solid #ff4444; }

        .verified-badge   { color:#00ff88; font-weight:bold; font-size:1.3rem; text-shadow:0 0 15px #00ff88; }
        .pending-badge    { color:#ffdd00; font-weight:bold; font-size:1.1rem; }
        .verify-btn       { margin: 25px auto; display: block; width: fit-content; }
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
                <li><a class="super-button" href="profile.php">Profile</a></li>
                <li><a class="super-button" href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container profile-container">
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="left-column">
            <div class="card profile-card">
                <div class="profile-header">
                    <img id="profile-photo" src="default-avatar.png" alt="Profile Photo" class="profile-photo">
                    <h4><?= e($user['name'] . ' ' . $user['lastname']) ?></h4>
                    <p class="editable job">GameHub Player</p>

                    <div style="text-align:center; margin:25px 0;">
                        <?php if ($user['verified'] == 1): ?>
                            <div class="verified-badge">Verified Account</div>
                        <?php elseif ($user['verification_requested'] == 1): ?>
                            <div class="pending-badge">Verification Pending...</div>
                        <?php else: ?>
                            <form method="POST">
                                <button type="submit" name="request_verification" class="shop-now-btn">
                                    Verify Now
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <h4>About Me</h4>
                <ul class="about-list">
                    <li><strong>Full Name:</strong> <span class="editable"><?= e($user['name'] . ' ' . $user['lastname']) ?></span></li>
                    <li><strong>Email:</strong> <span class="editable"><?= e($user['email']) ?></span></li>
                    <li><strong>Phone:</strong> <span class="editable"><?= e($user['tel']) ?></span></li>
                    <li><strong>CIN:</strong> <span class="editable"><?= e($user['cin']) ?></span></li>
                    <li><strong>Address:</strong> <span class="editable"><?= e($user['address']) ?></span></li>
                </ul>
            </div>
        </div>
        <div class="middle-column">
            <div class="card">
                <h4>Bio</h4>
                <p class="editable">Welcome back, <?= e($user['name']) ?>! Ready to dominate the leaderboard?</p>
            </div>
            <div class="card">
                <h4>Genre Interests</h4>
                <ul class="genre-list">
                    <li class="super-button">Sci-Fi</li>
                    <li class="super-button">Horror</li>
                    <li class="super-button">Action</li>
                    <li class="super-button">Racing</li>
                </ul>
            </div>
        </div>

        <div class="right-column">
            <div class="card">
                <h4>Games Interested In</h4>
                <ul>
                    <li>Cyber Arena 2077</li>
                    <li>Neon Racer X</li>
                    <li>Galaxy Clash</li>
                    <li>Shadow Legacy</li>
                </ul>
            </div>
            <div class="card">
                <h4>Account Info</h4>
                <ul>
                    <li>Logged in as <strong><?= e($user['email']) ?></strong></li>
                    <li>Role: <strong><?= ucfirst(e($user['role'])) ?></strong></li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script src="java.js"></script>
</body>
</html>