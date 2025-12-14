<?php
// update_status.php - Change le statut d'un feedback
require_once __DIR__ . '/../models/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$allowed_statuses = ['pending', 'approved', 'rejected'];

if ($id === 0 || !in_array($status, $allowed_statuses)) {
    header('Location: admin.php?error=invalid');
    exit;
}

// Sécurise la requête
$sql = "UPDATE feedback SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
}

// Redirection
header('Location: admin.php?success=status_updated');
exit;
?>
