<?php
require_once __DIR__ . '/../../../controller/EventController.php';
require_once __DIR__ . '/../../../model/Event.php';

$error = "";
$eventC = new EventController();

if (
    isset($_POST["title"]) &&
    isset($_POST["description"]) &&
    isset($_POST["start_date"]) &&
    isset($_POST["end_date"]) &&
    isset($_POST["location"]) &&
    isset($_POST["is_online"]) &&
    isset($_POST["capacity"])
) {
    if (
        !empty($_POST["title"]) &&
        !empty($_POST["description"]) &&
        !empty($_POST["start_date"]) &&
        !empty($_POST["end_date"]) &&
        !empty($_POST["location"]) &&
        !empty($_POST["capacity"])
    ) {

        // Création de l'objet Event
        $event = new Event(
            null,                       
            $_POST['title'],
            $_POST['description'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['location'],
            (bool)$_POST['is_online'],
            (int)$_POST['capacity'],
            0,
            $_POST['banner'] ?? null,
            "active"
        );

        // Enregistrer dans la BD
        $eventC->addEvent($event);

        header('Location: eventList.php');
        exit;
    } else {
        $error = "⚠ Missing information";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Add Event</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />

    <!-- Script validation -->
    <script defer src="assets/js/addEvents.js"></script>
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
                <a href="#0" data-bs-toggle="collapse" data-bs-target="#menu_events">
                    <span class="icon">📅</span>
                    <span class="text">Event Management</span>
                </a>
                <ul id="menu_events" class="collapse show dropdown-nav">
                    <li><a href="eventList.php">Event List</a></li>
                    <li><a href="addevent.php" class="active">Add Event</a></li>
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

        <div class="title-wrapper pt-30">
            <h2>Add New Event</h2>
        </div>

        <div class="content">

            <div class="container mt-4">
                <?php if (!empty($error)) { ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php } ?>

                <form action="" method="POST" id="addEventForm">

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">Event Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <small id="title_error"></small>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="4" name="description" required></textarea>
                        <small id="description_error"></small>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                        <small id="start_date_error"></small>
                    </div>

                    <!-- End Date -->
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                        <small id="end_date_error"></small>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                        <small id="location_error"></small>
                    </div>

                    <!-- Is Online -->
                    <div class="mb-3">
                        <label class="form-label">Is Online</label><br>
                        <input type="radio" name="is_online" value="1"> Yes
                        <input type="radio" name="is_online" value="0" checked> No
                    </div>

                    <!-- Capacity -->
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" min="0" required>
                        <small id="capacity_error"></small>
                    </div>

                    <!-- Banner -->
                    <div class="mb-3">
                        <label class="form-label">Banner URL (optional)</label>
                        <input type="text" class="form-control" id="banner" name="banner">
                    </div>

                    <!-- Submit -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">➕ Add Event</button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</section>

<footer class="footer">
    <div class="container-fluid text-center">
        <p>Designed by Esprit Student</p>
    </div>
</footer>

</main>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>
