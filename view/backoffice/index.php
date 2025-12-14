
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

<?php
session_start();
require_once "../../controller/userController.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../frontoffice/login_admin.php");
    exit;
}

$uc = new UserController();
$users = $uc->listUsers()->fetchAll();

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

$db = config::getConnexion();
$loginLogs = $db->query("
    SELECT ll.*, u.name, u.email 
    FROM login_log ll 
    LEFT JOIN user u ON ll.user_id = u.id_user 
    ORDER BY ll.created_at DESC 
    LIMIT 50
")->fetchAll();

$totalLogins = $db->query("SELECT COUNT(*) FROM login_log")->fetchColumn();
$successfulLogins = $db->query("SELECT COUNT(*) FROM login_log WHERE success = 1")->fetchColumn();
$failedLogins = $db->query("SELECT COUNT(*) FROM login_log WHERE success = 0")->fetchColumn();
$todayLogins = $db->query("SELECT COUNT(*) FROM login_log WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$uniqueUsersToday = $db->query("SELECT COUNT(DISTINCT user_id) FROM login_log WHERE DATE(created_at) = CURDATE()")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Backoffice - GameHub Admin</title>
    <link rel="stylesheet" href="../frontoffice/index.css">
    <style>
        table { width:100%; border-collapse:collapse; color:white; margin-top:30px; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid rgba(0,255,136,0.3); }
        th { background:rgba(0,255,136,0.15); color:#00ff88; cursor:pointer; user-select:none; }
        th:hover { background:rgba(0,255,136,0.3); }
        .action-btn { padding:8px 14px; margin:4px; border:none; border-radius:8px; cursor:pointer; }
        .search-bar { padding:12px; width:300px; border-radius:8px; border:1px solid #00ff88; background:rgba(255,255,255,0.1); color:white; margin-bottom:20px; }
        .filters { margin-bottom:20px; text-align:center; }
        .filters select, .filters input { padding:10px; margin:5px; background:#111; color:#00ff88; border:1px solid #00ff88; border-radius:8px; }

       
        .stats-grid { 
            display:grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); 
            gap:20px; 
            margin:40px 0; 
        }
        .stat-card { 
            background:rgba(0,255,136,0.1); 
            padding:25px; 
            border-radius:15px; 
            text-align:center; 
            border:2px solid #00ff88; 
            box-shadow:0 0 20px rgba(0,255,136,0.2);
        }
        .stat-number { 
            font-size:3rem; 
            font-weight:bold; 
            color:#00ff88; 
            margin:10px 0; 
            text-shadow:0 0 15px #00ff88;
        }
        .stat-label { 
            color:#aaa; 
            font-size:1.2rem; 
            letter-spacing:1px;
        }
        .login-table { width:100%; border-collapse:collapse; margin-top:30px; }
        .login-table th { background:rgba(0,255,136,0.2); color:#00ff88; }
        .login-table td { padding:12px; }
        .success { color:#00ff88; font-weight:bold; }
        .failed { color:#ff4444; font-weight:bold; }
        .section-title { color:#00ff88; font-size:2rem; text-align:center; margin:50px 0 30px; text-shadow:0 0 10px #00ff88; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">Gamehub Admin</h1>
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Dashboard</a></li>
                <li><a href="../frontoffice/role.html" class="super-button">View Site</a></li>
                <li><a href="logout.php" class="super-button">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top:100px;">

    <h2 class="section-title">Admin Dashboard</h2>
    <h3 class="section-title">Login Activity Overview</h3>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $totalLogins ?></div>
            <div class="stat-label">Total Attempts</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $successfulLogins ?></div>
            <div class="stat-label">Successful Logins</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $failedLogins ?></div>
            <div class="stat-label">Failed Attempts</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $todayLogins ?></div>
            <div class="stat-label">Logins Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $uniqueUsersToday ?></div>
            <div class="stat-label">Active Users Today</div>
        </div>
    </div>
    <h3 class="section-title">Recent Login Activity</h3>
    <div class="card">
        <table class="login-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>IP Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($loginLogs as $log): ?>
                <tr>
                    <td><?= date('M d, Y - H:i', strtotime($log['created_at'])) ?></td>
                    <td><?= htmlspecialchars($log['name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($log['email'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($log['ip']) ?></td>
                    <td class="<?= $log['success'] ? 'success' : 'failed' ?>">
                        <?= $log['success'] ? 'Success' : 'Failed' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($loginLogs)): ?>
                <tr><td colspan="5" style="text-align:center; color:#aaa; padding:30px;">No login activity recorded yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <h3 class="section-title">User Management</h3>
    <div class="filters">
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

    <table id="usersTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">Name</th>
                <th onclick="sortTable(2)">Lastname</th>
                <th onclick="sortTable(3)">Email</th>
                <th onclick="sortTable(4)">Role</th>
                <th onclick="sortTable(5)">Gender</th>
                <th onclick="sortTable(6)">Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
            <tr 
                data-role="<?= strtolower($u['role']) ?>" 
                data-gender="<?= strtolower($u['gender'] ?? '') ?>"
                data-status="<?= $u['verified']==1 ? 'verified' : ($u['verification_requested']==1 ? 'pending' : 'none') ?>">
                
                <td><?= $u['id_user'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['lastname']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td><?= ucfirst($u['gender'] ?? 'N/A') ?></td>
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

    <div style="text-align:center;margin-top:40px;">
        <a href="add_user.php" class="shop-now-btn" style="padding:18px 50px; font-size:1.6rem;">+ Add New User</a>
    </div>
</div>

<script>
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

let sortDirection = {};
function sortTable(colIndex) {
    const table = document.getElementById("usersTable");
    const rows = Array.from(table.tBodies[0].rows);

    sortDirection[colIndex] = sortDirection[colIndex] === 'asc' ? 'desc' : 'asc';

    rows.sort((a, b) => {
        let aVal = a.cells[colIndex].textContent.trim();
        let bVal = b.cells[colIndex].textContent.trim();

        if (!isNaN(aVal) && !isNaN(bVal)) {
            return sortDirection[colIndex] === 'asc' ? aVal - bVal : bVal - aVal;
        }
        return sortDirection[colIndex] === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
    });

    rows.forEach(row => table.tBodies[0].appendChild(row));
}
</script>

</body>

</html>