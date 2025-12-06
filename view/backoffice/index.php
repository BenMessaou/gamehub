<?php
session_start();
require_once "../../controller/userController.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../frontoffice/login_admin.php");
    exit;
}

$uc = new UserController();
$users = $uc->listUsers()->fetchAll();

// Handle verification approve & delete (your original code)
if (isset($_POST['approve_verify'])) {
    $id = (int)$_POST['approve_id'];
    config::getConnexion()->prepare("UPDATE user SET verified = 1 WHERE id_user = ?")->execute([$id]);
    echo '<script>alert("User verified!"); location.reload();</script>';
}
if (isset($_POST['delete_user'])) {
    $id = (int)$_POST['delete_id'];
    $uc->deleteUser($id);
    echo '<script>alert("User deleted!"); location.reload();</script>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Backoffice - Users</title>
    <link rel="stylesheet" href="../frontoffice/index.css">
    <style>
        table { width:100%; border-collapse:collapse; color:white; margin-top:30px; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid rgba(0,255,136,0.3); }
        th { background:rgba(0,255,136,0.15); color:#00ff88; cursor:pointer; user-select:none; }
        th:hover { background:rgba(0,255,136,0.3); }
        .action-btn { padding:8px 14px; margin:4px; border:none; border-radius:8px; cursor:pointer; }
        .search-bar { padding:12px; width:300px; border-radius:8px; border:1px solid #00ff88; background:rgba(255,255,255,0.1); color:white; margin-bottom:20px; }
        .filters { margin-bottom:20px; }
        .filters select { padding:10px; margin-right:10px; background:#111; color:#00ff88; border:1px solid #00ff88; border-radius:8px; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Dashboard</a></li>
                <li><a href="../frontoffice/role.html" class="super-button">View Site</a></li>
                <li><a href="logout.php" class="super-button">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:140px;">
    <h2 style="color:#00ff88; text-align:center; margin-bottom:30px;">User Management</h2>

    <!-- Search + Filters -->
    <div class="filters" style="text-align:center;">
        <input type="text" id="searchInput" class="search-bar" placeholder="Search by name, email, CIN...">

        <select id="roleFilter">
            <option value="">All Roles</option>
            <option value="client">Client</option>
            <option value="admin">Admin</option>
        </select>

        <select id="genderFilter">
            <option value="">All Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <select id="statusFilter">
            <option value="">All Status</option>
            <option value="verified">Verified</option>
            <option value="pending">Pending</option>
            <option value="none">Not Verified</option>
        </select>
    </div>

    <!-- Users Table -->
    <table id="usersTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">Name</th>
                <th onclick="sortTable(2)">Lastname</th>
                <th onclick="sortTable(3)">Email</th>
                <th onclick="sortTable(4)">Role</th>
                <th onclick="5">Gender</th>
                <th onclick="sortTable(6)">Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
            <tr 
                data-role="<?= strtolower($u['role']) ?>" 
                data-gender="<?= strtolower($u['gender']) ?>"
                data-status="<?= $u['verified']==1 ? 'verified' : ($u['verification_requested']==1 ? 'pending' : 'none') ?>">
                
                <td><?= $u['id_user'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['lastname']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td><?= ucfirst($u['gender']) ?></td>
                <td>
                    <?php if($u['verified']==1): ?>
                        <span style="color:#00ff88;font-weight:bold;">Verified</span>
                    <?php elseif($u['verification_requested']==1): ?>
                        <span style="color:#ffdd00;font-weight:bold;">Pending</span>
                    <?php else: ?>
                        <span style="color:#ff4444;">Not Verified</span>
                    <?php endif; ?>
                </td>
                <td style="text-align:center;">
                    <a href="update_user.php?id=<?= $u['id_user'] ?>" class="super-button action-btn">Edit</a>

                    <?php if($u['verification_requested']==1 && $u['verified']==0): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="approve_id" value="<?= $u['id_user'] ?>">
                            <button type="submit" name="approve_verify" class="action-btn" style="background:#00ff88;color:black;">Approve</button>
                        </form>
                    <?php endif; ?>

                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?')">
                        <input type="hidden" name="delete_id" value="<?= $u['id_user'] ?>">
                        <button type="submit" name="delete_user" style="background:#ff4444;color:white;" class="shop-now-btn">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align:center;margin-top:30px;">
        <a href="add_user.php" class="shop-now-btn">+ Add New User</a>
    </div>
</div>

<script>
// Search + Filter
const searchInput = document.getElementById('searchInput');
const roleFilter = document.getElementById('roleFilter');
const genderFilter = document.getElementById('genderFilter');
const statusFilter = document.getElementById('statusFilter');

function applyFilters() {
    const search = searchInput.value.toLowerCase();
    const role = roleFilter.value;
    const gender = genderFilter.value;
    const status = statusFilter.value;

    document.querySelectorAll('#usersTable tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowRole = row.dataset.role;
        const rowGender = row.dataset.gender;
        const rowStatus = row.dataset.status;

        let show = true;
        if (search && !text.includes(search)) show = false;
        if (role && rowRole !== role) show = false;
        if (gender && rowGender !== gender) show = false;
        if (status && rowStatus !== status) show = false;

        row.style.display = show ? '' : 'none';
    });
}

searchInput.addEventListener('input', applyFilters);
roleFilter.addEventListener('change', applyFilters);
genderFilter.addEventListener('change', applyFilters);
statusFilter.addEventListener('change', applyFilters);

// Sorting when clicking headers
let sortDirection = {};
function sortTable(colIndex) {
    const table = document.getElementById("usersTable");
    const rows = Array.from(table.tBodies[0].rows);

    // Toggle direction
    sortDirection[colIndex] = sortDirection[colIndex] === 'asc' ? 'desc' : 'asc';

    rows.sort((a, b) => {
        let aVal = a.cells[colIndex].textContent.trim();
        let bVal = b.cells[colIndex].textContent.trim();

        if (!isNaN(aVal) && !isNaN(bVal)) {
            return sortDirection[colIndex] === 'asc' 
                ? aVal - bVal 
                : bVal - aVal;
        }

        return sortDirection[colIndex] === 'asc' 
            ? aVal.localeCompare(bVal) 
            : bVal.localeCompare(aVal);
    });

    // Re-append rows
    rows.forEach(row => table.tBodies[0].appendChild(row));
}
</script>

</body>
</html>