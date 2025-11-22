<?php
require_once __DIR__ . '/../../../controller/EventController.php';

$eventC = new EventController();
$event = null;

// Récupération de l'ID
$eventId = $_GET['id'] ?? null;

if ($eventId) {
    $event = $eventC->showEvent($eventId);
}

if (!$event) {
    header('Location: eventList.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Détails de l'Événement - <?= htmlspecialchars($event['title']) ?></title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />

    <style>
        .event-detail-card {
            background: #1b1b29;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
        }
        .event-header {
            border-bottom: 2px solid #7c00ff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .event-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
        }
        .event-banner {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .info-label {
            font-weight: 600;
            color: #7c00ff;
            width: 200px;
        }
        .info-value {
            color: #fff;
            flex: 1;
        }
        .badge-custom {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-online {
            background: linear-gradient(120deg, #00ffea, #7c00ff);
            color: #fff;
        }
        .badge-offline {
            background: #6c757d;
            color: #fff;
        }
        .badge-status {
            background: linear-gradient(120deg, #ff00c7, #7c00ff);
            color: #fff;
        }
    </style>
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
                    <li><a href="eventList.php">Event List</a></li>
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

        <div class="title-wrapper pt-30 d-flex justify-content-between align-items-center">
            <h2>Détails de l'Événement</h2>
            <div>
                <a href="editEvent.php?id=<?= $event['id'] ?>" class="btn btn-warning">✏ Modifier</a>
                <a href="eventList.php" class="btn btn-secondary">← Retour à la liste</a>
            </div>
        </div>

        <div class="event-detail-card">
            
            <!-- Header -->
            <div class="event-header">
                <h1 class="event-title"><?= htmlspecialchars($event['title']) ?></h1>
                <div>
                    <span class="badge badge-custom badge-status"><?= strtoupper($event['status']) ?></span>
                    <?php if ($event['is_online']) { ?>
                        <span class="badge badge-custom badge-online">🌐 En ligne</span>
                    <?php } else { ?>
                        <span class="badge badge-custom badge-offline">📍 Présentiel</span>
                    <?php } ?>
                </div>
            </div>

            <!-- Banner -->
            <?php if (!empty($event['banner'])) { ?>
                <img src="<?= htmlspecialchars($event['banner']) ?>" alt="Banner" class="event-banner">
            <?php } ?>

            <!-- Description -->
            <div class="info-row">
                <div class="info-label">Description</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($event['description'])) ?></div>
            </div>

            <!-- Dates -->
            <div class="info-row">
                <div class="info-label">📅 Date de début</div>
                <div class="info-value">
                    <strong><?= date('d/m/Y à H:i', strtotime($event['start_date'])) ?></strong>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">📅 Date de fin</div>
                <div class="info-value">
                    <strong><?= date('d/m/Y à H:i', strtotime($event['end_date'])) ?></strong>
                </div>
            </div>

            <!-- Location -->
            <div class="info-row">
                <div class="info-label">📍 Lieu / Lien</div>
                <div class="info-value">
                    <?php if ($event['is_online']) { ?>
                        <a href="<?= htmlspecialchars($event['location']) ?>" target="_blank" class="text-primary">
                            <?= htmlspecialchars($event['location']) ?>
                        </a>
                    <?php } else { ?>
                        <strong><?= htmlspecialchars($event['location']) ?></strong>
                    <?php } ?>
                </div>
            </div>

            <!-- Capacity -->
            <div class="info-row">
                <div class="info-label">👥 Capacité</div>
                <div class="info-value">
                    <strong><?= $event['reserved_count'] ?> / <?= $event['capacity'] ?></strong> places réservées
                    <?php 
                    $remaining = $event['capacity'] - $event['reserved_count'];
                    if ($remaining > 0) {
                        echo "<span class='text-success'>($remaining places disponibles)</span>";
                    } else {
                        echo "<span class='text-danger'>(Complet)</span>";
                    }
                    ?>
                </div>
            </div>

            <!-- ID -->
            <div class="info-row">
                <div class="info-label">🆔 ID</div>
                <div class="info-value"><?= $event['id'] ?></div>
            </div>

            <!-- Actions -->
            <div class="mt-4 pt-4 border-top">
                <a href="editEvent.php?id=<?= $event['id'] ?>" class="btn btn-warning me-2">✏ Modifier l'événement</a>
                <a href="deleteEvent.php?id=<?= $event['id'] ?>" 
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')" 
                   class="btn btn-danger me-2">🗑 Supprimer</a>
                <a href="eventList.php" class="btn btn-secondary">← Retour à la liste</a>
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

