<?php
// Redirection vers updateevents.php
$id = $_GET['id'] ?? null;
if ($id) {
    header("Location: updateevents.php?id=" . urlencode($id));
    exit;
} else {
    header("Location: eventList.php");
    exit;
}
?>

