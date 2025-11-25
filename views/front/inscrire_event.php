<?php
// views/front/inscrire_event.php

require_once __DIR__ . '/../../controllers/EventController.php';

$eventC = new EventC();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    if (!isset($_POST['event_id'])) {
        die("Missing event ID.");
    }
    $eventId = intval($_POST['event_id']);
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $seats = intval($_POST['seats'] ?? 1);

    // Basic server-side validation
    if (empty($fullName) || empty($email) || empty($phone) || $seats < 1) {
        die("Please fill in all required fields correctly.");
    }

    // Get event to check availability
    $event = $eventC->recupererEvent($eventId);
    if (!$event) {
        die("Event not found.");
    }

    try {
        // If availability is limited, block when full or not enough seats
        if ($event["availability"] !== null && intval($event["availability"]) < $seats) {
            throw new Exception("Not enough places left for this event.");
        }

        // Register user (user_id NULL for now), passing full details
        $eventC->inscrireEvent($eventId, null, $fullName, $email, $phone, $seats);

        // Decrease availability if limited
        if ($event["availability"] !== null) {
            $eventC->decrementAvailability($eventId, $seats);
        }

        header("Location: events.php?registered=1");
        exit;

    } catch (Exception $e) {
        header("Location: events.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Display form (GET request)
    if (!isset($_GET["id"])) {
        die("Missing event ID.");
    }
    $eventId = intval($_GET["id"]);

    // Get event to check availability
    $event = $eventC->recupererEvent($eventId);
    if (!$event) {
        die("Event not found.");
    }

    // If availability is limited and zero, do not display form
    if ($event["availability"] !== null && intval($event["availability"]) <= 0) {
        die("No places left for this event.");
    }
    ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription à l'événement</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/addevent.css" />

    <script>
    function validateForm() {
        let fullName = document.forms["reservationForm"]["fullName"].value.trim();
        let email = document.forms["reservationForm"]["email"].value.trim();
        let phone = document.forms["reservationForm"]["phone"].value.trim();
        let seats = document.forms["reservationForm"]["seats"].value.trim();
        if (!fullName || !email || !phone || !seats) {
            alert("Tous les champs sont requis.");
            return false;
        }
        if (isNaN(seats) || parseInt(seats) < 1) {
            alert("Veuillez saisir un nombre valide de places.");
            return false;
        }
        // Basic email format check
        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert("Veuillez saisir une adresse e-mail valide.");
            return false;
        }
        return true;
    }
    </script>
</head>

<body>
    <header>
        <div class="container">
            <img src="logo.png" alt="CyberDeals" class="logo" />
            <nav>
                <ul>
                    <li><a href="index.html" class="super-button">Home</a></li>
                    <li><a href="events.php" class="super-button">Events</a></li>
                    <li><a href="#deals" class="super-button">Deals</a></li>
                    <li><a href="#contact" class="super-button">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="event-registration" class="event-registration">
        <div class="container">
            <h2>Inscription à l'événement: <?= htmlspecialchars($event['name']) ?></h2>
            <div class="form-card">
            <form name="reservationForm" action="/gamehub/index.php?controller=reservation&action=add" method="POST" onsubmit="return validateForm()">
                    <input type="hidden" name="event_id" value="<?= $eventId ?>" />

                    <div class="form-group">
                        <label for="fullName">Nom complet:</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Nom complet" required />
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Email" required />
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone:</label>
                        <input type="text" id="phone" name="phone" placeholder="Téléphone" required />
                    </div>

                    <div class="form-group">
                        <label for="seats">Nombre de places:</label>
                        <input type="number" id="seats" name="seats" value="1" min="1" required />
                    </div>

                    <button type="submit" class="submit-btn">Confirmer</button>
                </form>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <p>&copy; 2025 CyberDeals. All rights reserved.</p>
            <p>Contact: info@cyberdeals.com</p>
        </div>
    </footer>
</body>

</html>
<?php
}
