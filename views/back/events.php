<?php
// views/back/events.php

require_once __DIR__ . '/../../controllers/EventController.php';

$eventC = new EventC();

// fetch all events
$events = $eventC->afficherEvents(); 
// afficherEvents() returns PDOStatement in your controller style
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - CyberDeals</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
    /* Simple status badges */
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

    .event-card {
        background: #111827;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 14px;
        color: white;
        display: flex;
        gap: 16px;
        align-items: flex-start;
    }

    .event-card img {
        width: 160px;
        height: 110px;
        object-fit: cover;
        border-radius: 10px;
    }

    .event-info h4 {
        margin: 0 0 6px;
        font-size: 18px;
    }

    .event-info p {
        margin: 4px 0;
        opacity: 0.9;
    }

    .event-meta {
        font-size: 13px;
        opacity: 0.8;
    }
    </style>
</head>

<body>

    <header>
        <div class="container">
            <img src="logo.png" alt="CyberDeals" class="logo">
            <nav>
                <ul>
                    <li><a href="index.html" class="super-button">Home</a></li>
                    <li><a href="#events" class="super-button">Events</a></li>
                    <li><a href="#deals" class="super-button">Deals</a></li>
                    <li><a href="#contact" class="super-button">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="events" class="events">
        <div class="container">
            <h3>Upcoming Events</h3>

            <div class="event-controls">
                <input type="text" id="search-bar" placeholder="Search events..." class="search-input">
                <select id="sort-bar" class="sort-select">
                    <option value="date">Sort by Date Released</option>
                    <option value="popular">Sort by Popularity</option>
                    <option value="name">Sort by Name</option>
                </select>

                <!-- update to php -->
                <a href="addevent.php" class="create-event-btn">Create Event</a>
            </div>

            <div id="event-list" class="event-list">

                <?php if ($events && $events->rowCount() > 0) { ?>
                <?php foreach ($events as $event) { ?>

                <div class="event-card">

                    <!-- Image -->
                    <img src="<?= !empty($event['imageURL']) ? $event['imageURL'] : 'https://via.placeholder.com/160x110' ?>"
                        alt="event image">

                    <!-- Info -->
                    <div class="event-info">

                        <h4><?= htmlspecialchars($event['title']) ?></h4>

                        <!-- Status Badge -->
                        <span class="badge <?= $event['status'] ?>">
                            <?= htmlspecialchars($event['status']) ?>
                        </span>
                        <div style="margin-top:8px;">
                            <a href="edit_event.php?id=<?= $event['id'] ?>"
                                style="padding:6px 10px;background:#2563eb;color:white;border-radius:6px;text-decoration:none;">
                                ✏ Edit
                            </a>

                            <a href="delete_event.php?id=<?= $event['id'] ?>"
                                onclick="return confirm('Are you sure you want to delete this event?');"
                                style="padding:6px 10px;background:#dc2626;color:white;border-radius:6px;text-decoration:none;margin-left:6px;">
                                🗑 Delete
                            </a>
                        </div>

                        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>

                        <p class="event-meta">
                            <strong>Type:</strong> <?= htmlspecialchars($event['eventType']) ?> |
                            <strong>Platform:</strong> <?= htmlspecialchars($event['platform'] ?? '-') ?> |
                            <strong>Location:</strong> <?= htmlspecialchars($event['location'] ?? '-') ?>
                        </p>

                        <p class="event-meta">
                            <strong>Start:</strong> <?= htmlspecialchars($event['startDate']) ?> |
                            <strong>End:</strong> <?= htmlspecialchars($event['endDate']) ?>
                        </p>

                        <p class="event-meta">
                            <strong>Ticket:</strong> <?= htmlspecialchars($event['ticketPrice']) ?> DT |
                            <strong>Places:</strong> <?= htmlspecialchars($event['availability'] ?? 'Unlimited') ?> |
                            <strong>Prize Pool:</strong> <?= htmlspecialchars($event['prizePool']) ?> DT
                        </p>

                    </div>
                </div>

                <?php } ?>
                <?php } else { ?>
                <p style="color:white;">No events found.</p>
                <?php } ?>

            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <p>&copy; 2025 CyberDeals. All rights reserved.</p>
            <p>Contact: info@cyberdeals.com</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>

</html>