<?php
// views/back/pendingevents.php  (ADMIN PAGE)

require_once __DIR__ . '/../../controllers/EventController.php';

$eventC = new EventC();

// Fetch only pending events
$pendingEvents = $eventC->afficherEventsParStatut("pending");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Events - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">

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

    .event-meta {
        font-size: 13px;
        opacity: 0.8;
    }

    .admin-actions a {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        margin-right: 8px;
        font-size: 14px;
    }

    .btn-accept {
        background: #16a34a;
        color: white;
    }

    .btn-reject {
        background: #dc2626;
        color: white;
    }
    </style>
</head>

<body>

    <header>
        <div class="container">
            <h2>Pending Events - Admin Panel</h2>
            <nav>
                <a href="pendingevents.php">Pending Events</a>
                <a href="../front/events.php">View All Events</a>
            </nav>
        </div>
    </header>

    <section class="events">
        <div class="container">

            <h3>Pending Events</h3>

            <?php if (isset($_GET["accepted"])) { ?>
            <div style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:12px;">
                ✅ Event accepted successfully.
            </div>
            <?php } ?>

            <?php if (isset($_GET["rejected"])) { ?>
            <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;">
                ❌ Event rejected successfully.
            </div>
            <?php } ?>

            <?php if (!empty($pendingEvents)) { ?>
            <?php foreach ($pendingEvents as $event) { ?>

            <div class="event-card">

                <img src="<?= !empty($event['imageURL']) ? $event['imageURL'] : 'https://via.placeholder.com/160x110' ?>"
                    alt="event image">

                <div class="event-info">

                    <h4><?= htmlspecialchars($event['title']) ?></h4>

                    <span class="badge pending">pending</span>

                    <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>

                    <p class="event-meta">
                        <strong>Type:</strong> <?= $event['eventType'] ?> |
                        <strong>Platform:</strong> <?= $event['platform'] ?? '-' ?> |
                        <strong>Location:</strong> <?= $event['location'] ?? '-' ?>
                    </p>

                    <p class="event-meta">
                        <strong>Start:</strong> <?= $event['startDate'] ?> |
                        <strong>End:</strong> <?= $event['endDate'] ?>
                    </p>

                    <p class="event-meta">
                        <strong>Ticket:</strong> <?= $event['ticketPrice'] ?> DT |
                        <strong>Places:</strong> <?= $event['availability'] ?? 'Unlimited' ?> |
                        <strong>Prize Pool:</strong> <?= $event['prizePool'] ?> DT
                    </p>

                    <div class="admin-actions" style="margin-top:10px;">
                        <a class="btn-accept" href="accept_event.php?id=<?= $event['id'] ?>"
                            onclick="return confirm('Accept this event?');">
                            ✅ Accept
                        </a>

                        <a class="btn-reject" href="reject_event.php?id=<?= $event['id'] ?>"
                            onclick="return confirm('Reject this event?');">
                            ❌ Reject
                        </a>
                    </div>

                </div>

            </div>

            <?php } ?>
            <?php } else { ?>
            <p style="color:white;">No pending events right now.</p>
            <?php } ?>

        </div>
    </section>

</body>

</html>