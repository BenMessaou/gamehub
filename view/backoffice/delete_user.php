
<?php
require_once "../../controller/userController.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $uc = new userController();
    $uc->deleteUser($_POST["id_user"]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delete User</title>
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

        <h4 style="color:#ff0088; margin-bottom:20px;">Delete User</h4>

        <form method="POST">
            <input type="text" name="id_user" placeholder="Enter User ID"><br><br>
            <button class="shop-now-btn" type="submit">Delete User</button>
        </form>

        <br>
        <a href="index.php" class="super-button">Back</a>
        <p id="errorBox" style="color:#ff4444; margin-top:10px; min-height:20px;"></p>

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

<?php
require_once "../../controller/userController.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $uc = new userController();
    $uc->deleteUser($_POST["id_user"]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delete User</title>
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

        <h4 style="color:#ff0088; margin-bottom:20px;">Delete User</h4>

        <form method="POST">
            <input type="text" name="id_user" placeholder="Enter User ID"><br><br>
            <button class="shop-now-btn" type="submit">Delete User</button>
        </form>

        <br>
        <a href="index.php" class="super-button">Back</a>
        <p id="errorBox" style="color:#ff4444; margin-top:10px; min-height:20px;"></p>

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
