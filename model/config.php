<?php
// config.php - configuration MySQL (améliorée)
$host = "localhost";
$user = "root";      // change si besoin
$pass = "";          // change si besoin
$db   = "bdgamehub";
$port = 3306;        // change si besoin

// Optionnel : activer les erreurs mysqli en dev
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    // définir charset
    if (!$conn->set_charset("utf8mb4")) {
        // fallback, mais pas fatal
        $conn->query("SET NAMES 'utf8mb4'");
    }
} catch (Exception $e) {
    // en prod, remplacer par un message simple et logger l'erreur
    die("Erreur connexion MySQL: " . htmlspecialchars($e->getMessage()));
}
?>
