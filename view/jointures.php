<?php
/**
 * REQUÊTES JOIN - Exemples de jointures entre contact et feedback
 * Utiliser ces requêtes dans votre code pour lier les données
 */

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/setup_jointure.php';

// Auto-setup jointure
setupForeignKey($conn);

// ===== REQUÊTES AVEC JOINTURE =====

// 1. Tous les feedbacks avec infos du contact (par email)
echo "<h2>1. Feedbacks avec infos Contact (JOIN sur email)</h2>";
$sql1 = "
    SELECT 
        f.id as feedback_id,
        f.pseudo,
        f.email,
        f.game,
        f.rating,
        f.message,
        f.status,
        f.created_at as feedback_date,
        c.id as contact_id,
        c.name as contact_name,
        c.status as contact_status,
        c.created_at as contact_date
    FROM feedback f
    LEFT JOIN contact c ON f.email = c.email
    ORDER BY f.created_at DESC
    LIMIT 10
";
$result1 = $conn->query($sql1);
if ($result1) {
    while ($row = $result1->fetch_assoc()) {
        echo "Feedback #{$row['feedback_id']} - {$row['pseudo']} ({$row['email']}) <br>";
        if ($row['contact_id']) {
            echo "  → Contact: {$row['contact_name']} (ID: {$row['contact_id']})<br>";
        }
    }
}

echo "<hr>";

// 2. Contacts avec tous leurs feedbacks
echo "<h2>2. Contacts avec leurs Feedbacks</h2>";
$sql2 = "
    SELECT 
        c.id,
        c.name,
        c.email,
        c.status,
        COUNT(f.id) as nombre_avis,
        GROUP_CONCAT(f.game SEPARATOR ', ') as jeux
    FROM contact c
    LEFT JOIN feedback f ON f.email = c.email
    GROUP BY c.id
    ORDER BY nombre_avis DESC
";
$result2 = $conn->query($sql2);
if ($result2) {
    while ($row = $result2->fetch_assoc()) {
        echo "{$row['name']} ({$row['email']}) - {$row['nombre_avis']} avis sur: {$row['jeux']}<br>";
    }
}

echo "<hr>";

// 3. Utilisateurs qui ont fait un contact ET des avis
echo "<h2>3. Utilisateurs avec Contact ET Feedback</h2>";
$sql3 = "
    SELECT DISTINCT
        c.id as contact_id,
        c.name,
        c.email,
        COUNT(DISTINCT f.id) as nombre_avis,
        c.status as contact_status
    FROM contact c
    INNER JOIN feedback f ON f.email = c.email
    GROUP BY c.id
    ORDER BY nombre_avis DESC
";
$result3 = $conn->query($sql3);
if ($result3) {
    while ($row = $result3->fetch_assoc()) {
        echo "{$row['name']} ({$row['email']}) - {$row['nombre_avis']} avis - Status: {$row['contact_status']}<br>";
    }
}

echo "<hr>";

// 4. Feedbacks non encore associés à un contact
echo "<h2>4. Feedbacks sans Contact associé</h2>";
$sql4 = "
    SELECT 
        f.id,
        f.pseudo,
        f.email,
        f.game,
        f.status
    FROM feedback f
    LEFT JOIN contact c ON f.email = c.email
    WHERE c.id IS NULL
    ORDER BY f.created_at DESC
";
$result4 = $conn->query($sql4);
if ($result4) {
    $count = $result4->num_rows;
    echo "Total: $count feedbacks sans contact<br>";
    while ($row = $result4->fetch_assoc()) {
        echo "#{$row['id']} - {$row['pseudo']} ({$row['email']}) - {$row['game']}<br>";
    }
}

echo "<hr>";

// 5. Statistiques combinées
echo "<h2>5. Statistiques Combinées</h2>";
$sql5 = "
    SELECT 
        COUNT(DISTINCT c.id) as nombre_contacts,
        COUNT(DISTINCT f.id) as nombre_avis,
        COUNT(DISTINCT f.email) as emails_uniques,
        AVG(f.rating) as note_moyenne,
        COUNT(CASE WHEN c.id IS NOT NULL THEN 1 END) as avis_avec_contact,
        COUNT(CASE WHEN c.id IS NULL THEN 1 END) as avis_sans_contact
    FROM contact c
    FULL OUTER JOIN feedback f ON f.email = c.email
";
$result5 = $conn->query($sql5);
if ($result5) {
    $stats = $result5->fetch_assoc();
    echo "Contacts: {$stats['nombre_contacts']}<br>";
    echo "Avis: {$stats['nombre_avis']}<br>";
    echo "Emails uniques: {$stats['emails_uniques']}<br>";
    echo "Note moyenne: " . round($stats['note_moyenne'], 2) . "<br>";
    echo "Avis avec contact: {$stats['avis_avec_contact']}<br>";
    echo "Avis sans contact: {$stats['avis_sans_contact']}<br>";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jointures - Contact & Feedback</title>
    <link rel="stylesheet" href="/feeeed_backkkkkkkkk/public/assets/style.css">
    <style>
        body { font-family: Arial; background: #0a0e27; color: #e0e0e0; padding: 20px; }
        h2 { color: #00ff88; }
        hr { border-color: #00ff88; }
        code { background: #1a1f3a; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔗 Jointures: Contact ↔ Feedback</h1>
    <p>Exemples de requêtes avec JOIN</p>
</body>
</html>
