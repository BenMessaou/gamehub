<?php
require_once __DIR__ . '/../../controllers/EventController.php';

$eventC = new EventC();

if (!isset($_GET["id"])) {
    die("Event ID missing.");
}

$id = intval($_GET["id"]);

$ok = $eventC->supprimerEvent($id);

if ($ok) {
    header("Location: admindashboard.php?deleted=1");
} else {
    header("Location: admindahsboard.php?error=delete_failed");
}
exit;