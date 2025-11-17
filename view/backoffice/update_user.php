<?php
require_once "../../controller/userController.php";
require_once "../../model/User.php";

$uc = new userController();
$user = $uc->getUserById($_GET["id"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $userData = $uc->getUserById($_POST["id_user"]);
    $password = $userData["password"];

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

    $uc->updateUser($updatedUser);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Update User</title>
    <link rel="stylesheet" href="../frontoffice/index.css">
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

<div class="container" style="margin-top:150px;">
    <div class="card">

        <h2 style="color:#00ff88;">Update User</h2>

        <form method="POST">

            <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

            <div class="input-row">
                <input type="text" name="name" value="<?= $user['name'] ?>">
                <input type="text" name="lastname" value="<?= $user['lastname'] ?>">
            </div>

            <input type="email" name="email" value="<?= $user['email'] ?>"><br><br>

            <input type="password" name="password" placeholder="New Password (optional)"><br><br>

            <div class="input-row">
                <input type="text" name="cin" value="<?= $user['cin'] ?>">
                <input type="text" name="tel" value="<?= $user['tel'] ?>">
            </div>

            <input type="text" name="gender" value="<?= $user['gender'] ?>"><br><br>

            <input type="text" name="role" value="<?= $user['role'] ?>"><br><br>

            <button class="shop-now-btn" type="submit">Save Changes</button>

        </form>

        <br>
        <a href="index.php" class="shop-now-btn">Back</a>

    </div>
</div>

</body>
</html>
