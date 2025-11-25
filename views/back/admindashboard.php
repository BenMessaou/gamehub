<?php
// views/back/admindashboard.php

require_once __DIR__ . '/../../controllers/EventController.php';
require_once __DIR__ . '/../../models/ReservationM.php';

$eventC = new EventC();

$acceptedEvents = $eventC->afficherEventsParStatut("accepted");
$pendingEvents = $eventC->afficherEventsParStatut("pending");
$rejectedEvents = $eventC->afficherEventsParStatut("rejected");

$reservations = Reservation::getAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CyberDeals</title>
    <link rel="stylesheet" href="css/admindashboard.css">

    <style>
        .badge {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
        }

        .badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge.accepted {
            background: #d4edda;
            color: #155724;
        }

        .badge.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .admin-table th,
        .admin-table td {
            padding: 10px;
            border-bottom: 1px solid #333;
            text-align: left;
            color: #e5e7eb;
            font-size: 14px;
        }

        .admin-table th {
            color: white;
            font-weight: bold;
        }

        .action-btn {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            margin-right: 6px;
        }

        .btn-edit {
            background: #2563eb;
            color: white;
        }

        .btn-delete {
            background: #dc2626;
            color: white;
        }

        .btn-accept {
            background: #16a34a;
            color: white;
        }

        .btn-reject {
            background: #dc2626;
            color: white;
        }

        /* ============================
       NEW: Scroll containers
       max ~5 rows visible per block
       ============================ */
        .table-scroll {
            max-height: 260px;
            /* about 5 rows */
            overflow-y: auto;
            border-radius: 8px;
        }

        /* Sticky headers inside scroll */
        .table-scroll thead th {
            position: sticky;
            top: 0;
            background: #0f172a;
            /* dark theme background */
            z-index: 2;
        }

        /* Optional nicer scrollbar */
        .table-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <img src="logo.png" alt="CyberDeals Admin" class="logo">
            <nav>
                <ul>
                    <li><a href="#dashboard" class="super-button">Dashboard</a></li>
                    <li><a href="#users" class="super-button">Users</a></li>
                    <li><a href="#analytics" class="super-button">Analytics</a></li>
                    <li><a href="#settings" class="super-button">Settings</a></li>
                </ul>
            </nav>
            <button id="sidebar-toggle" class="sidebar-toggle">☰</button>
        </div>
    </header>

    <aside id="sidebar" class="sidebar">
        <nav>
            <ul>
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="#users">Users</a></li>
                <li><a href="#analytics">Analytics</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </nav>
    </aside>

    <main id="main-content" class="main-content">
        <section id="dashboard" class="dashboard">
            <div class="container">
                <div class="dashboard-header">
                    <h2>Dashboard Overview</h2>
                    <div class="actions">
                        <!-- if admin add-event is in back, point to it -->
                        <a href="admin_addevent.php?admin=1" class="super-button">Create Event by Admin</a>
                    </div>
                </div>

                <!-- KEEP YOUR STATS -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <h3>Total Users</h3>
                        <p class="stat-number">12,345</p>
                    </div>
                    <div class="stat-card">
                        <h3>Revenue</h3>
                        <p class="stat-number">$45,678</p>
                    </div>
                    <div class="stat-card">
                        <h3>Orders</h3>
                        <p class="stat-number">1,234</p>
                    </div>
                    <div class="stat-card">
                        <h3>Growth</h3>
                        <p class="stat-number">+15%</p>
                    </div>
                </div>

                <div class="dashboard-widgets">

                    <!-- ✅ WIDGET 1: PENDING EVENTS -->
                    <div class="widget">
                        <h3>Pending Event Approvals</h3>

                        <?php if (!empty($pendingEvents)) { ?>
                            <div class="table-scroll">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingEvents as $e) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e["title"]) ?></td>
                                                <td><span class="badge pending">pending</span></td>
                                                <td>
                                                    <a class="action-btn btn-accept" href="accept_event.php?id=<?= $e['id'] ?>"
                                                        onclick="return confirm('Accept this event?');">
                                                        ✅ Accept
                                                    </a>

                                                    <a class="action-btn btn-reject" href="reject_event.php?id=<?= $e['id'] ?>"
                                                        onclick="return confirm('Reject this event?');">
                                                        ❌ Reject
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p>No pending events.</p>
                        <?php } ?>
                    </div>

                    <!-- ✅ WIDGET 2: ACCEPTED EVENTS (EDIT/DELETE) -->
                    <div class="widget">
                        <h3>Accepted Events</h3>

                        <?php if (!empty($acceptedEvents)) { ?>
                            <div class="table-scroll">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($acceptedEvents as $e) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e["title"]) ?></td>
                                                <td><span class="badge accepted">accepted</span></td>
                                                <td>
                                                    <a class="action-btn btn-edit" href="edit_event.php?id=<?= $e['id'] ?>">
                                                        ✏ Edit
                                                    </a>

                                                    <a class="action-btn btn-delete" href="delete_event.php?id=<?= $e['id'] ?>"
                                                        onclick="return confirm('Delete this event?');">
                                                        🗑 Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p>No accepted events.</p>
                        <?php } ?>
                    </div>

                    <!-- ✅ WIDGET 3: REJECTED EVENTS (VIEW ONLY) -->
                    <div class="widget">
                        <h3>Rejected Events</h3>

                        <?php if (!empty($rejectedEvents)) { ?>
                            <div class="table-scroll">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rejectedEvents as $e) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($e["title"]) ?></td>
                                                <td><span class="badge rejected">rejected</span></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p>No rejected events.</p>
                        <?php } ?>
                    </div>

                    <!-- KEEP YOUR OTHER WIDGETS -->
                    <div class="widget">
                        <h3>Recent Users</h3>
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>John Doe</td>
                                    <td>john@example.com</td>
                                    <td>Active</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Jane Smith</td>
                                    <td>jane@example.com</td>
                                    <td>Active</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Bob Johnson</td>
                                    <td>bob@example.com</td>
                                    <td>Inactive</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget">
                        <?php if (!empty($reservations)) { ?>
                            <div class="table-scroll">
                                <table class="admin-table">
                                    <thead>
                                        <tr>

                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Seats</th>
                                            <th>Reservation Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reservations as $r) { ?>
                                            <tr>
                                                
                                                <td><?= htmlspecialchars($r['fullName']) ?></td>
                                                <td><?= htmlspecialchars($r['email']) ?></td>
                                                <td><?= htmlspecialchars($r['phone']) ?></td>
                                                <td><?= htmlspecialchars($r['seats'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($r['reservationDate'] ?? 'N/A') ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p>No reservations found.</p>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <script src="js/admindashboard.js"></script>
</body>

</html>