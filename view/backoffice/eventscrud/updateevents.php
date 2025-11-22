<?php
require_once __DIR__ . '/../../../controller/EventController.php';
require_once __DIR__ . '/../../../model/Event.php';

$error = '';
$event = null;
$eventC = new EventController();

// Récupération de l'ID
$eventId = $_GET['id'] ?? $_POST['id'] ?? null;

// Charger l'événement existant
if ($eventId) {
    $event = $eventC->showEvent($eventId);
}

// Traitement de mise à jour
if (
    isset($_POST['id'], $_POST['title'], $_POST['description'], $_POST['start_date'], 
          $_POST['end_date'], $_POST['location'], $_POST['capacity'])
) {
    if (
        !empty($_POST['title']) &&
        !empty($_POST['description']) &&
        !empty($_POST['start_date']) &&
        !empty($_POST['end_date']) &&
        !empty($_POST['location']) &&
        !empty($_POST['capacity'])
    ) {
        // Créer un nouvel objet Event mis à jour
        $updatedEvent = new Event(
            $_POST['id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['location'],
            isset($_POST['is_online']) ? (bool)$_POST['is_online'] : false,
            (int)$_POST['capacity'],
            $event['reserved_count'] ?? 0,
            $_POST['banner'] ?? null,
            $event['status'] ?? "active"
        );

        // Mettre à jour dans la BD
        $eventC->updateEvent($updatedEvent, $_POST['id']);

        header('Location: eventList.php');
        exit();
    } else {
        $error = '⚠ Please fill all required fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub | Update Event</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="styles.css" />

    <style>
        .layout-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .update-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 28px;
            box-shadow: var(--shadow);
        }
        .form-label {
            font-weight: 600;
            color: var(--text);
        }
        .neon-field {
            background: rgba(5, 0, 20, 0.75);
            border: 1px solid rgba(255, 0, 199, 0.2);
            color: #fff;
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            transition: 0.2s;
        }
        .neon-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 18px rgba(255,0,199,0.35);
            background: rgba(0, 0, 0, 0.65);
        }
        textarea.neon-field {
            min-height: 140px;
        }
        .section-title {
            font-family: Orbitron, sans-serif;
            font-size: 1rem;
            text-transform: uppercase;
            color: var(--cyan);
            margin-bottom: 16px;
        }
        .cta-zone {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .btn-gradient {
            padding: 12px 36px;
            border-radius: 999px;
            border: none;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(120deg, #ff00c7, #7c00ff, #00ffea);
            background-size: 200% 200%;
            transition: 0.3s;
        }
        .btn-gradient:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
        }
        .btn-ghost {
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 12px 24px;
            color: var(--text);
            text-decoration: none;
        }
    </style>

</head>

<body class="admin-body">

<header class="admin-header">
    <div class="container">
        <div class="admin-logo">
            <img src="../frontoffice/assests/logo.png" alt="Logo">
            GameHub Admin
        </div>
        <nav class="admin-nav">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="eventList.php" class="nav-link">Events</a>
            <a href="addevent.php" class="nav-link">Add Event</a>
        </nav>
    </div>
</header>

<main class="admin-main">
    <div class="container">

        <section class="admin-section">
            <h2>Modifier un événement</h2>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>

            <?php if (!$event) { ?>
                <div class="alert alert-warning">Event not found. <a href="eventList.php">Retour</a></div>
            <?php } else { ?>

            <form method="POST" class="form-neon">
                <input type="hidden" name="id" value="<?= $event['id'] ?>">

                <div class="layout-grid">

                    <!-- CARD 1 : Infos principales -->
                    <div class="update-card">
                        <p class="section-title">Informations principales</p>

                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control neon-field" name="title" required 
                                   value="<?= $event['title'] ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control neon-field" name="description" required><?= $event['description'] ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date Début</label>
                            <input type="datetime-local" class="form-control neon-field" 
                                   name="start_date" required value="<?= date('Y-m-d\TH:i', strtotime($event['start_date'])) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date Fin</label>
                            <input type="datetime-local" class="form-control neon-field" 
                                   name="end_date" required value="<?= date('Y-m-d\TH:i', strtotime($event['end_date'])) ?>">
                        </div>
                    </div>

                    <!-- CARD 2 : Lieu + capacité -->
                    <div class="update-card">
                        <p class="section-title">Organisation</p>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control neon-field" name="location" required 
                                   value="<?= $event['location'] ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Événement en ligne ?</label><br>
                            <label><input type="radio" name="is_online" value="1" <?= $event['is_online'] ? "checked" : "" ?>> Oui</label>
                            <label class="ms-3"><input type="radio" name="is_online" value="0" <?= !$event['is_online'] ? "checked" : "" ?>> Non</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Capacité</label>
                            <input type="number" class="form-control neon-field" name="capacity" required 
                                   value="<?= $event['capacity'] ?>">
                        </div>
                    </div>

                    <!-- CARD 3 : Media -->
                    <div class="update-card">
                        <p class="section-title">Media</p>

                        <div class="mb-3">
                            <label class="form-label">Banner URL</label>
                            <input type="text" class="form-control neon-field" name="banner" 
                                   value="<?= $event['banner'] ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Places réservées (readonly)</label>
                            <input type="number" class="form-control neon-field" readonly 
                                   value="<?= $event['reserved_count'] ?>">
                        </div>

                    </div>
                </div>

                <div class="cta-zone">
                    <button type="submit" class="btn-gradient">Mettre à jour l'événement</button>
                    <a href="eventList.php" class="btn-ghost">Annuler</a>
                </div>

            </form>

            <?php } ?>
        </section>

    </div>
</main>

<footer class="admin-footer">
    GameHub Admin • Powered by XR Labs
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
