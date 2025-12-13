<?php
session_start();

require_once "../../controller/controllercollab/CollabMemberController.php";

if (!isset($_GET['collab_id'])) {
    die("ID Collab manquant.");
}

$collab_id = $_GET['collab_id'];

$memberController = new CollabMemberController();
$members = $memberController->getMembers($collab_id);
?>

<html>
<head><title>Liste des membres</title></head>
<body>

<h2>Liste des membres du projet <?php echo $collab_id; ?></h2>

<table border="1" cellpadding="5">
    <tr>
        <th>ID membre</th>
        <th>ID utilisateur</th>
        <th>Rôle</th>
        <th>Action</th>
    </tr>

<?php foreach ($members as $m) { ?>
    <tr>
        <td><?php echo $m['id']; ?></td>
        <td><?php echo $m['user_id']; ?></td>
        <td><?php echo $m['role']; ?></td>
        <td>
            <form action="delete_member.php" method="POST">
                <input type="hidden" name="member_id" value="<?php echo $m['id']; ?>">
                <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">
                <input type="submit" value="Supprimer">
            </form>

            <a href="update_member.php?member_id=<?php echo $m['id']; ?>&collab_id=<?php echo $collab_id; ?>">Modifier rôle</a>
        </td>
    </tr>
<?php } ?>

</table>

</body>
</html>
