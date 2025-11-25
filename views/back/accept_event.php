<?php
require_once __DIR__ . '/../../controllers/EventController.php';

$eventC = new EventC();

if (!isset($_GET["id"])) {
    die("Missing event ID");
}

$id = intval($_GET["id"]);

$eventC->mettreAJourStatutEvent($id, "accepted");

header("Location: admindashboard.php?accepted=1");
exit;