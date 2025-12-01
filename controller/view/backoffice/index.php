<?php
session_start();
require_once "../../controller/userController.php";

$uc = new UserController();
$users = $uc->listUsers();

$fixedUsers = [];
foreach ($users as $user) {
    $user['verified']               = $user['verified'] ?? 0;
    $user['verification_requested'] = $user['verification_requested'] ?? 0;
    $fixedUsers[] = $user;
}
$users = $fixedUsers;
if (isset($_POST['approve_verify'])) {
    $id = (int)$_POST['approve_id'];
    $sql = "UPDATE user SET verified = 1 WHERE id_user = :id";
    $req = config::getConnexion()->prepare($sql);
    $req->execute([':id' => $id]);
    echo '<script>alert("User verified successfully!"); location.reload();</script>';
}

if (isset($_POST['delete_user'])) {
    $id = (int)$_POST['delete_id'];
    $uc->deleteUser($id);
    echo '<script>alert("User deleted successfully!"); location.reload();</script>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Backoffice - Users</title>
    <link rel="stylesheet" href="../frontoffice/index.css">
    <style>
        .status-verified { color:#00ff88; font-weight:bold; }
        .status-pending  { color:#ffdd00; font-weight:bold; }
        .status-none     { color:#ff4444; font-weight:bold; }
        .action-btn { margin: 4px; padding: 8px 14px; font-size: 0.9rem; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Dashboard</a></li>
                <li><a href="../frontoffice/index.php" class="super-button">View Site</a></li>
                <li><a href="logout.php" class="super-button">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top: 120px;">
    <div class="form-card">
        <h2 style="color:#00ff88; text-align:center; margin-bottom:30px;">User Management</h2>

        <table style="width:100%; border-collapse:collapse; color:white;">
            <tr style="color:#00ff88; font-size:1.2rem;">
                <th>ID</th>
                <th>Name</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>CIN</th>
                <th>Tel</th>
                <th>Gender</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php foreach($users as $u): ?>
            <tr style="border-bottom:1px solid #333; text-align:center;">
                <td><?= $u['id_user'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['lastname']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['cin']) ?></td>
                <td><?= htmlspecialchars($u['tel']) ?></td>
                <td><?= ucfirst($u['gender']) ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td>
                    <?php if ($u['verified'] == 1): ?>
                        <span class="status-verified">Verified</span>
                    <?php elseif ($u['verification_requested'] == 1): ?>
                        <span class="status-pending">Pending</span>
                    <?php else: ?>
                        <span class="status-none">Not Verified</span>
                    <?php endif; ?>
                </td>
                <td style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                    <a href="update_user.php?id=<?= $u['id_user'] ?>" class="super-button action-btn">Update</a>

                    <?php if ($u['verification_requested'] == 1 && $u['verified'] == 0): ?>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="approve_id" value="<?= $u['id_user'] ?>">
                            <button type="submit" name="approve_verify" class="action-btn"
                                    style="background:#00ff88; color:black; border:none; border-radius:8px; font-weight:bold;">
                                Approve
                            </button>
                        </form>
                    <?php endif; ?>

                    <form method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="delete_id" value="<?= $u['id_user'] ?>">
                        <button type="submit" name="delete_user" class="shop-now-btn">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <br><br>
        <a href="add_user.php" class="shop-now-btn">Add New User</a>
    </div>
</div>

</body>
</html>