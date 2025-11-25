<?php
require_once __DIR__ . '/../../controllers/EventController.php';
require_once __DIR__ . '/../../models/Event.php';

$eventC = new EventC();

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["update"])) {
        throw new Exception("Invalid request.");
    }

    $id = intval($_POST["id"]);

    $startDate = date("Y-m-d H:i:s", strtotime($_POST["startDate"]));
    $endDate   = date("Y-m-d H:i:s", strtotime($_POST["endDate"]));

    if (strtotime($endDate) < strtotime($startDate)) {
        throw new Exception("End date must be after start date.");
    }

    $event = new Event(
        $id,
        null, // keep user_id as is (or set from session later)
        trim($_POST["title"]),
        trim($_POST["description"] ?? ""),
        $_POST["eventType"],
        $_POST["platform"] ?? null,
        trim($_POST["location"] ?? ""),
        $startDate,
        $endDate,
        floatval($_POST["ticketPrice"] ?? 0),
        !empty($_POST["availability"]) ? intval($_POST["availability"]) : null,
        floatval($_POST["prizePool"] ?? 0),
        !empty($_POST["imageURL"]) ? $_POST["imageURL"] : null,
        "pending" // after edit, keep pending (admin re-approve)
    );

    $eventC->modifierEvent($event, $id);

    header("Location: events.php?updated=1");
    exit;

} catch (Exception $e) {
    header("Location: edit_event.php?id=" . $_POST["id"] . "&error=" . urlencode($e->getMessage()));
    exit;
}