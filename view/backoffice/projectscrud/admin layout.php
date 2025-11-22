<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? "Admin Panel" ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<div class="sidebar">
    <h2>GAMEHUB PRO</h2>

    <a href="admindashboard.php">🏠 Dashboard</a>
    <a href="projectlist.php">📄 Liste des jeux</a>
    <a href="addProject.php">➕ Ajouter un jeu</a>
    <a href="projectlist.php?status=en_attente">✔️ Vérifications</a>
    <a href="logout.php" style="background:#d50000;">🚪 Logout</a>
</div>

<div class="content">
    <?= $content ?>
</div>

</body>
</html>
