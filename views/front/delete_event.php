<?php
require_once __DIR__ . '/../../controllers/EventController.php';

$eventC = new EventC();

if (!isset($_GET["id"])) {
    die("Event ID missing.");
}

$id = intval($_GET["id"]);

$ok = $eventC->supprimerEvent($id);

if ($ok) {
    header("Location: events.php?deleted=1");
} else {
    header("Location: events.php?error=delete_failed");
}
exit;