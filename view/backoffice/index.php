<?php
require_once "../../controller/userController.php";
$uc = new userController();
$users = $uc->listUsers();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Backoffice - Users</title>
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

<div class="container" style="margin-top: 150px;">

    <div class="form-card">
        
        <h2 style="color:#00ff88; margin-bottom:20px;">User List</h2>

        <table border="0" cellpadding="10" cellspacing="0" style="width:100%; color:white; text-align:center;">
            <tr style="color:#00ff88; font-size:1.2rem;">
                <th>ID</th>
                <th>Name</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>CIN</th>
                <th>Tel</th>
                <th>Gender</th>
                <th>Role</th>
                <th>Update</th>
            </tr>

            <?php foreach($users as $u): ?>
            <tr style="padding:20px;">
                <td><?= $u['id_user'] ?></td>
                <td><?= $u['name'] ?></td>
                <td><?= $u['lastname'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['cin'] ?></td>
                <td><?= $u['tel'] ?></td>
                <td><?= $u['gender'] ?></td>
                <td><?= $u['role'] ?></td>
                <td>
                    <a href="update_user.php?id=<?= $u['id_user'] ?>" class="super-button">Update</a>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
        <ul>
        <li><a class="super-button" href="add_user.php">Add User</a></li>
                <li><a class="super-button" href="delete_user.php">Delete User</a></li></ul>
    </div>

</div>

</body>
</html>
