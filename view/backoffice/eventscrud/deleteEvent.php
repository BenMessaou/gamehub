<?php
// Redirection vers deleteevents.php
$id = $_GET['id'] ?? null;
if ($id) {
    header("Location: deleteevents.php?id=" . urlencode($id));
    exit;
} else {
    header("Location: eventList.php");
    exit;
}
?>

