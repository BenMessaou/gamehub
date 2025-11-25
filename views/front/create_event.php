<?php
// views/front/create_event.php
require_once __DIR__ . '/../../controllers/EventController.php';
require_once __DIR__ . '/../../models/Event.php';

$eventC = new EventC();

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["create"])) {
        throw new Exception("Invalid request.");
    }

    if (empty($_POST["title"]) || empty($_POST["eventType"]) || empty($_POST["startDate"]) || empty($_POST["endDate"])) {
        throw new Exception("Please fill all required fields.");
    }

    $startDate = date("Y-m-d H:i:s", strtotime($_POST["startDate"]));
    $endDate   = date("Y-m-d H:i:s", strtotime($_POST["endDate"]));

    if (strtotime($endDate) < strtotime($startDate)) {
        throw new Exception("End date must be after start date.");
    }

    $event = new Event(
        null,
        null,
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
        "pending"
    );

    $eventC->ajouterEvent($event);

    header("Location: addevent.php?success=1");
    exit;

} catch (Exception $e) {
    header("Location: addevent.php?error=" . urlencode($e->getMessage()));
    exit;
}