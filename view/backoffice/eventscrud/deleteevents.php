<?php
require_once __DIR__ . '/../../../controller/EventController.php';

$eventC = new EventController();

// Vérifier si l'ID existe
if (isset($_GET["id"]) && !empty($_GET["id"])) {

    $eventC->deleteEvent($_GET["id"]);

    // Redirection vers la liste des événements
    header('Location: eventList.php');
    exit;

} else {
    echo "Error: missing event ID.";
}
?>
