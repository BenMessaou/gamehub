<?php
require_once __DIR__ . '/../../../controller/EventController.php';
require_once __DIR__ . '/../../../model/Event.php';

$eventC = new EventController();
$eventsResult = $eventC->listEvents();
$events = $eventsResult->fetchAll(PDO::FETCH_ASSOC); // Convertir en tableau
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Event List</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
</head>

<body>

<div id="preloader"><div class="spinner"></div></div>

<!-- SIDEBAR -->
<aside class="sidebar-nav-wrapper">
    <div class="navbar-logo">
        <a href="index.html">
            <img src="images/logo.png" alt="logo" width="40%" height="70%" />
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item nav-item-has-children">
                <a href="#0" data-bs-toggle="collapse" data-bs-target="#menu_events" class="active">
                    <span class="icon">📅</span>
                    <span class="text">Event Management</span>
                </a>
                <ul id="menu_events" class="collapse show dropdown-nav">
                    <li><a href="eventList.php" class="active">Event List</a></li>
                    <li><a href="addevent.php">Add Event</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>

<!-- MAIN -->
<main class="main-wrapper">

<header class="header">
    <div class="container-fluid">
        <div class="header-right">
            <div class="profile-box">
                <button class="dropdown-toggle bg-transparent border-0">
                    <div class="profile-info">
                        <div class="image"><img src="assets/images/profile/profile-image.png" alt="" /></div>
                        <div><h6>Admin</h6><p>Dashboard</p></div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</header>

<section class="section">
    <div class="container-fluid">

        <div class="title-wrapper pt-30 d-flex justify-content-between">
            <h2>Event List</h2>
            <a href="addevent.php" class="btn btn-primary">➕ Add Event</a>
        </div>

        <div class="card mt-4">
            <div class="card-body">

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Location</th>
                        <th>Online?</th>
                        <th>Capacity</th>
                        <th>Reserved</th>
                        <th>Banner</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($events as $event) { ?>
                        <tr class="text-center">
                            <td><?= $event['id'] ?></td>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= $event['start_date'] ?></td>
                            <td><?= $event['end_date'] ?></td>
                            <td><?= htmlspecialchars($event['location']) ?></td>
                            <td><?= $event['is_online'] ? "Yes" : "No" ?></td>
                            <td><?= $event['capacity'] ?></td>
                            <td><?= $event['reserved_count'] ?></td>
                            <td>
                                <?php if (!empty($event['banner'])) { ?>
                                    <img src="<?= $event['banner'] ?>" width="70" height="50" style="border-radius:6px">
                                <?php } else { echo "-"; } ?>
                            </td>
                            <td>
                                <span class="badge bg-success"><?= $event['status'] ?></span>
                            </td>

                            <td>
                                <a href="editEvent.php?id=<?= $event['id'] ?>" class="btn btn-warning btn-sm">✏ Edit</a>
                                <a href="deleteEvent.php?id=<?= $event['id'] ?>" 
                                   onclick="return confirm('Are you sure?')" 
                                   class="btn btn-danger btn-sm">🗑 Delete</a>
                                <a href="viewEvent.php?id=<?= $event['id'] ?>" class="btn btn-info btn-sm text-white">👁 View</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                </table>

            </div>
        </div>
    </div>
</section>

<footer class="footer text-center">
    <p>Designed by Esprit Student</p>
</footer>

</main>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>
