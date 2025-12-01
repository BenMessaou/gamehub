<?php
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
    $gender   = $_POST['gender'] ?? '';
    $role     = 'client';
    $user = new User(
        null,
        $name,
        $lastname,
        $email,
        $password,
        $cin,
        $tel,
        $gender,
        $role
    );

    try {
        $userController->addUser($user);
        $insertedUser = $userController->getUserByEmail($email);

        if ($insertedUser) {
            $_SESSION['user_id'] = $insertedUser['id_user'];
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
    <style>
.password-strength {
    margin: 12px 0 20px;
}

.strength-bar {
    width:  : 100%;
    height   : 10px;
    background: rgba(255,255,255,0.08);
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #00ff8830;
}

.password-strength .fill {
    height: 100%;
    width : 0%;
    transition: width 0.4s ease, background 0.4s ease;
    border-radius: 8px;
}

/* Colors */
.weak   { background: linear-gradient(90deg, #ff3366, #ff6666); width: 33% !important; }
.medium { background: linear-gradient(90deg, #ffaa00, #ffdd33); width: 66% !important; }
.strong { background: linear-gradient(90deg, #00ff88, #00cc66); width: 100% !important; }

#strengthLabel {
    margin-top: 8px;
    text-align: center;
    font-weight: bold;
    font-size: 0.95rem;
    color: #aaa;
}
.weak-text   { color: #ff6666 !important; }
.medium-text { color: #ffdd33 !important; }
.strong-text { color: #00ff88 !important; text-shadow: 0 0 10px #00ff88; }
</style>
<header>
    <div class="container">
        
        <h1 class="logo">gamehub</h1>
        <img src="logo.png" class="logo1" alt="" >

        <nav>
            <ul>
                <li><a href="#home" class="super-button">Home</a></li>
                <li><a href="#deals" class="super-button">Deals</a></li>
                <li><a href="#shop" class="super-button">Shop Now</a></li>
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

        <form method="post" action="" onsubmit="return saisie();" >
            <div id="errorBox" style="color:red;"></div>

            <div class="input-row">
                <input type="text" placeholder="name" id="name" name="name" >
            </div>

            <div class="input-row">
                <input type="text" placeholder="last name" id="lastname" name="lastname" >
            </div>

            <div class="input-row">
                <input type="email" placeholder="Email" id="email" name="email" >
            </div>

            <div class="input-row">
<input type="password" name="password" id="passwordField" placeholder="Create Password" required autocomplete="new-password">
        </div>
<!-- Password Strength Meter (this will appear) -->
<div id="passwordStrengthMeter" class="password-strength">
    <div class="strength-bar">
        <div id="strengthBar" class="fill"></div>
    </div>
    <div id="strengthLabel">Enter a password</div>
</div>

            <div class="input-row">
                <input type="password" placeholder="retype password" id="password2" name="password2" >
            </div>

            <div class="input-row">
                <input type="text" placeholder="cin" id="cin" name="cin" >
            </div>

            <div class="input-row">
                <input type="text" placeholder="tel" id="tel" name="tel" >
            </div>

            <div class="input-row">
                <p>Gender</p>
                <label><input type="radio" name="gender" value="M"> Male</label>
                <label><input type="radio" name="gender" value="F"> Female</label>
            </div>

            <button type="submit" class="super-button" >Sign Up</button>
        </form>
    </div>
</div>
<script>
document.getElementById('passwordField').addEventListener('input', function() {
    const pwd = this.value;
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');

    // Reset classes
    bar.className = 'fill';
    label.classList.remove('weak-text', 'medium-text', 'strong-text');

    if (pwd.length === 0) {
        bar.style.width = '0%';
        label.textContent = 'Enter a password';
        return;
    }

    let score = 0;

    if (pwd.length >= 8)               score += 1;
    else if (pwd.length >= 6)          score += 0.5;

    if (/[A-Z]/.test(pwd))             score += 1;
    if ((pwd.match(/\d/g) || []).length >= 2) score += 1;
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pwd)) score += 1;

    if (score <= 1.5) {
        bar.classList.add('weak');
        label.textContent = 'Weak';
        label.classList.add('weak-text');
    }
    else if (score <= 3) {
        bar.classList.add('medium');
        label.textContent = 'Medium';
        label.classList.add('medium-text');
    }
    else {
        bar.classList.add('strong');
        label.textContent = 'Strong';
        label.classList.add('strong-text');
    }
});
</script>
<script src="java.js"></script>
</body>
</html>
