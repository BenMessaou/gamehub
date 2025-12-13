<?php
session_start();

require_once "../../controller/controllercollab/CollabMemberController.php";

if (!isset($_GET['member_id'], $_GET['collab_id'])) {
    die("Paramètres manquants.");
}

$member_id = $_GET['member_id'];
$collab_id = $_GET['collab_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $role = $_POST['role'];

    $controller = new CollabMemberController();
    $controller->updateRole($member_id, $role);

    header("Location: list_members.php?collab_id=" . $collab_id);
    exit;
}
?>

<html>
<head><title>Modifier rôle</title></head>
<body>

<h2>Modifier le rôle du membre <?php echo $member_id; ?></h2>

<form method="POST">
    Nouveau rôle :
    <select name="role">
        <option value="membre">membre</option>
        <option value="moderateur">modérateur</option>
        <option value="owner">owner</option>
    </select><br><br>

    <input type="submit" value="Modifier">
</form>

</body>
</html>
