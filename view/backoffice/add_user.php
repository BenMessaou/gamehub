<?php
require_once "../../controller/UserController.php";
require_once "../../model/User.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $uc = new UserController();   // CAPITAL U – this is the fix!

    $user = new User(
        null,
        $_POST['name'],
        $_POST['lastname'],
        $_POST['email'],
        $_POST['password'],
        $_POST['cin'],
        $_POST['tel'],
        $_POST['gender'],
        $_POST['role']
    );

    $uc->addUser($user);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add User</title>
    <link rel="stylesheet" href="../frontoffice/index.css">
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <nav>
            <ul>
                <li><a class="super-button" href="index.php">Dashboard</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:150px;">
    <div class="card">
        <h2>Add New User</h2>

        <form method="POST">

            <div class="input-row">
                <input type="text" name="name" placeholder="Name">
                <input type="text" name="lastname" placeholder="Lastname">
            </div>

            <input type="email" name="email" placeholder="Email"><br><br>
            <input type="password" name="password" placeholder="Password"><br><br>
            <input type="text" name="cin" placeholder="CIN"><br><br>
            <input type="text" name="tel" placeholder="Phone"><br><br>

            <select name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select><br><br>

            <input type="text" name="role" value="client" readonly><br><br>

            <button class="shop-now-btn" type="submit">Add User</button>
        </form>

        <br>
        <a href="index.php" class="shop-now-btn">Back</a>
    </div>
</div>

</body>
</html>