<?php
require_once "../../controller/userController.php";
require_once "../../model/User.php";

// Instantiate the controller (case-sensitive)
$uc = new UserController();

// Get user by ID from query string
if (!isset($_GET['id'])) {
    die("User ID is required.");
}
$user = $uc->getUserById($_GET['id']);
if (!$user) {
    die("User not found.");
}

// Handle POST submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userData = $uc->getUserById($_POST["id_user"]);
    $password = $userData["password"];

    // If new password is entered, hash it
    if (!empty($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    }

    $updatedUser = new User(
        $_POST["id_user"],
        $_POST["name"],
        $_POST["lastname"],
        $_POST["email"],
        $password,
        $_POST["cin"],
        $_POST["tel"],
        $_POST["gender"],
        $_POST["role"]
    );

    // Pass BOTH arguments: User object and ID
    $uc->updateUser($updatedUser, $_POST["id_user"]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update User</title>
    <link rel="stylesheet" href="../frontoffice/index.css">
</head>
<body>
<header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Dashboard</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:150px;">
    <div class="card">
        <h2 style="color:#00ff88;">Update User</h2>

        <form method="POST">
            <input type="hidden" name="id_user" value="<?= htmlspecialchars($user['id_user']) ?>">

            <div class="input-row">
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>
            </div>

            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

            <input type="password" name="password" placeholder="New Password (optional)"><br><br>

            <div class="input-row">
                <input type="text" name="cin" value="<?= htmlspecialchars($user['cin']) ?>" required>
                <input type="text" name="tel" value="<?= htmlspecialchars($user['tel']) ?>" required>
            </div>

            <p>Gender</p>
            <label><input type="radio" name="gender" value="M" <?= $user['gender'] === 'M' ? 'checked' : '' ?>> Male</label>
            <label><input type="radio" name="gender" value="F" <?= $user['gender'] === 'F' ? 'checked' : '' ?>> Female</label><br><br>

            <p>Role</p>
            <select name="role" required>
                <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Client</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select><br><br>

            <button class="shop-now-btn" type="submit">Save Changes</button>
        </form>
        <p id="errorBox" style="color:#ff4444; margin-top:10px; min-height:20px;"></p>

        <br>
        <a href="index.php" class="shop-now-btn">Back</a>
    </div>
</div>
<script src="script.js"></script>
<script>
    // Attach validation to the form without using HTML5 required
    const form = document.querySelector("form");
    if (form) {
        form.onsubmit = function() {
            return validateBackofficeForm();
        };
    }
</script>
</body>
</html>
