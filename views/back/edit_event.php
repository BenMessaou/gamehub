<?php
require_once __DIR__ . '/../../controllers/EventController.php';

$eventC = new EventC();

if (!isset($_GET["id"])) {
    die("Event ID missing.");
}

$id = intval($_GET["id"]);
$event = $eventC->recupererEvent($id);

if (!$event) {
    die("Event not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="css/addevent.css">
</head>

<body>

    <header>
        <div class="container">
            <h2>Edit Event</h2>
            <a href="events.php">⬅ Back to Events</a>
        </div>
    </header>

    <section class="add-event">
        <div class="container">
            <h3>Update Event</h3>

            <div class="form-card">
                <form method="post" action="update_event.php">

                    <input type="hidden" name="id" value="<?= $event['id'] ?>">

                    <div class="form-group">
                        <label for="title">Event Title</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($event['title']) ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description"
                            name="description"><?= htmlspecialchars($event['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="eventType">Event Type</label>
                        <select id="eventType" name="eventType" required>
                            <option value="competition" <?= $event['eventType']=="competition"?"selected":"" ?>>
                                Competition</option>
                            <option value="tournament" <?= $event['eventType']=="tournament"?"selected":"" ?>>Tournament
                            </option>
                            <option value="game launch" <?= $event['eventType']=="game launch"?"selected":"" ?>>Game
                                Launch</option>
                            <option value="conference" <?= $event['eventType']=="conference"?"selected":"" ?>>Conference
                            </option>
                            <option value="meetup" <?= $event['eventType']=="meetup"?"selected":"" ?>>Meetup</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="platform">Platform</label>
                        <select id="platform" name="platform">
                            <option value="">Select Platform</option>
                            <option value="PC" <?= $event['platform']=="PC"?"selected":"" ?>>PC</option>
                            <option value="Console" <?= $event['platform']=="Console"?"selected":"" ?>>Console</option>
                            <option value="Mobile" <?= $event['platform']=="Mobile"?"selected":"" ?>>Mobile</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location"
                            value="<?= htmlspecialchars($event['location']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="startDate">Start Date</label>
                        <input type="datetime-local" id="startDate" name="startDate"
                            value="<?= date('Y-m-d\TH:i', strtotime($event['startDate'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="endDate">End Date</label>
                        <input type="datetime-local" id="endDate" name="endDate"
                            value="<?= date('Y-m-d\TH:i', strtotime($event['endDate'])) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="ticketPrice">Ticket Price</label>
                        <input type="number" step="0.01" id="ticketPrice" name="ticketPrice"
                            value="<?= $event['ticketPrice'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="availability">Availability</label>
                        <input type="number" id="availability" name="availability"
                            value="<?= $event['availability'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="prizePool">Prize Pool</label>
                        <input type="number" step="0.01" id="prizePool" name="prizePool"
                            value="<?= $event['prizePool'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="imageURL">Image URL</label>
                        <input type="url" id="imageURL" name="imageURL"
                            value="<?= htmlspecialchars($event['imageURL']) ?>">
                    </div>

                    <button type="submit" name="update" class="submit-btn">Update Event</button>
                </form>
            </div>
        </div>
    </section>

</body>

</html>