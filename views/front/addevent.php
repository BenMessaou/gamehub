<?php
// views/front/addevent.php
// FORM ONLY — NO INSERT LOGIC HERE

$success = "";
$error = "";

if (isset($_GET["success"])) {
    $success = "✅ Event added successfully! Waiting for admin approval.";
}

if (isset($_GET["error"])) {
    $error = "❌ " . htmlspecialchars($_GET["error"]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link rel="stylesheet" href="css/addevent.css">
</head>

<body>

    <header>
        <div class="container">
            <img src="logo.png" alt="EventHub" class="logo">
            <nav>
                <ul>
                    <li><a href="#hero">Home</a></li>
                    <li><a href="#add-event">Add Event</a></li>
                    <li><a href="./events.php">Manage Events</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="hero" class="hero">
        <div class="container">
            <h2>Add Your Big Event</h2>
            <p>Create and manage well-known events with all the details you need.</p>
            <div class="countdown">
                <div class="time">
                    <span id="days">00</span>
                    <small>Days</small>
                </div>
                <div class="time">
                    <span id="hours">00</span>
                    <small>Hours</small>
                </div>
                <div class="time">
                    <span id="minutes">00</span>
                    <small>Minutes</small>
                </div>
                <div class="time">
                    <span id="seconds">00</span>
                    <small>Seconds</small>
                </div>
            </div>
        </div>
    </section>

    <section id="add-event" class="add-event">
        <div class="container">
            <h3>Add New Event</h3>

            <!-- Messages -->
            <?php if (!empty($success)) { ?>
            <div style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:12px;">
                <?= $success ?>
            </div>
            <?php } ?>

            <?php if (!empty($error)) { ?>
            <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin-bottom:12px;">
                <?= $error ?>
            </div>
            <?php } ?>

            <div class="form-card">
                <!-- Goes to create_event.php -->
                <form method="post" action="create_event.php" id="event-form">

                    <div class="form-group">
                        <label for="title">Event Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter event title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Enter event description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="eventType">Event Type</label>
                        <select id="eventType" name="eventType" required>
                            <option value="">Select Type</option>
                            <option value="competition">Competition</option>
                            <option value="tournament">Tournament</option>
                            <option value="game launch">Game Launch</option>
                            <option value="conference">Conference</option>
                            <option value="meetup">Meetup</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="platform">Platform</label>
                        <select id="platform" name="platform">
                            <option value="">Select Platform</option>
                            <option value="PC">PC</option>
                            <option value="Console">Console</option>
                            <option value="Mobile">Mobile</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="Enter location">
                    </div>

                    <div class="form-group">
                        <label for="startDate">Start Date</label>
                        <input type="datetime-local" id="startDate" name="startDate" required>
                    </div>

                    <div class="form-group">
                        <label for="endDate">End Date</label>
                        <input type="datetime-local" id="endDate" name="endDate" required>
                    </div>

                    <div class="form-group">
                        <label for="ticketPrice">Ticket Price</label>
                        <input type="number" step="0.01" id="ticketPrice" name="ticketPrice" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="availability">Availability (Places)</label>
                        <input type="number" id="availability" name="availability" placeholder="Number of places">
                    </div>

                    <div class="form-group">
                        <label for="prizePool">Prize Pool</label>
                        <input type="number" step="0.01" id="prizePool" name="prizePool" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="imageURL">Image URL</label>
                        <input type="url" id="imageURL" name="imageURL" placeholder="Enter image URL">
                    </div>

                    <button type="submit" name="create" class="submit-btn">Create Event</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2023 EventHub. All rights reserved.</p>
        </div>
    </footer>

    <!--<script src="js/addevent.js">-->
    </script>
</body>

</html>