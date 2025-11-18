<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>

<h2>Connexion Admin</h2>

<?php 
if (!empty($_SESSION['error'])) {
    echo "<p style='color:red'>".$_SESSION['error']."</p>";
    unset($_SESSION['error']);
}
?>

<form method="POST" action="/feedback-games/admin/index.php?action=login">
    <label>Nom d'utilisateur :</label><br>
    <input type="text" name="username" required><br><br>

    <label>Mot de passe :</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Se connecter</button>
</form>

</body>
</html>
