<?php
require_once __DIR__ . "/../../../model/Event.php";
require_once __DIR__ . "/../../../controller/EventController.php";

// Création d’un objet Event pour test
$event1 = new Event(
    1,                         // id
    "Workshop FullStack",      // title
    "Formation pratique JS/PHP",
    "2025-03-10 09:00:00",     // start_date
    "2025-03-10 17:00:00",     // end_date
    "Salle 12 - Bloc A",       // location
    false,                     // is_online
    50,                        // capacity
    12,                        // reserved_count
    "https://example.com/banner.jpg", // banner
    "active",                  // status
    "2025-02-20",              // created_at
    "2025-02-20"               // updated_at
);

// Affichage avec var_dump()
echo "<h2>Affichage avec var_dump :</h2>";
var_dump($event1);

// Utilisation du controller
$controller = new EventController();
$controller->showEvent($event1);
?>
