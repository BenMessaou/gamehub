<?php
/**
 * AUTO-MIGRATION: Ajoute les colonnes manquantes à la BDD
 * Ce fichier est appelé automatiquement par admin.php si les colonnes n'existent pas
 */

function runAutoMigration($conn) {
    $errors = [];
    
    // Vérifier et ajouter colonne 'status' à 'feedback'
    $checkStatus = $conn->query("SHOW COLUMNS FROM feedback LIKE 'status'");
    if (!$checkStatus || $checkStatus->num_rows === 0) {
        $sql1 = "ALTER TABLE feedback ADD COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER `message`";
        if (!$conn->query($sql1)) {
            $errors[] = "Erreur ajout colonne status: " . $conn->error;
        }
    }
    
    // Vérifier et ajouter colonne 'email' à 'feedback'
    $checkEmail = $conn->query("SHOW COLUMNS FROM feedback LIKE 'email'");
    if (!$checkEmail || $checkEmail->num_rows === 0) {
        $sql2 = "ALTER TABLE feedback ADD COLUMN `email` VARCHAR(255) AFTER `pseudo`";
        if (!$conn->query($sql2)) {
            $errors[] = "Erreur ajout colonne email: " . $conn->error;
        }
    }
    
    // Vérifier et ajouter colonne 'recurrence_count' à 'feedback'
    $checkRecurrence = $conn->query("SHOW COLUMNS FROM feedback LIKE 'recurrence_count'");
    if (!$checkRecurrence || $checkRecurrence->num_rows === 0) {
        $sql3 = "ALTER TABLE feedback ADD COLUMN `recurrence_count` INT DEFAULT 1 AFTER `status`";
        if (!$conn->query($sql3)) {
            $errors[] = "Erreur ajout colonne recurrence_count: " . $conn->error;
        }
    }
    
    // Ajouter INDEX sur status
    $checkIndexStatus = $conn->query("SHOW INDEX FROM feedback WHERE Column_name = 'status'");
    if (!$checkIndexStatus || $checkIndexStatus->num_rows === 0) {
        $conn->query("ALTER TABLE feedback ADD INDEX idx_status (status)");
    }
    
    // Ajouter INDEX sur email
    $checkIndexEmail = $conn->query("SHOW INDEX FROM feedback WHERE Column_name = 'email'");
    if (!$checkIndexEmail || $checkIndexEmail->num_rows === 0) {
        $conn->query("ALTER TABLE feedback ADD INDEX idx_email (email)");
    }
    
    // Ajouter INDEX sur pseudo
    $checkIndexPseudo = $conn->query("SHOW INDEX FROM feedback WHERE Column_name = 'pseudo'");
    if (!$checkIndexPseudo || $checkIndexPseudo->num_rows === 0) {
        $conn->query("ALTER TABLE feedback ADD INDEX idx_pseudo (pseudo)");
    }
    
    // Ajouter INDEX sur created_at
    $checkIndexCreated = $conn->query("SHOW INDEX FROM feedback WHERE Column_name = 'created_at'");
    if (!$checkIndexCreated || $checkIndexCreated->num_rows === 0) {
        $conn->query("ALTER TABLE feedback ADD INDEX idx_created_at (created_at)");
    }
    
    // Vérifier et ajouter colonne 'status' à 'contact'
    $checkContactStatus = $conn->query("SHOW COLUMNS FROM contact LIKE 'status'");
    if (!$checkContactStatus || $checkContactStatus->num_rows === 0) {
        $sql4 = "ALTER TABLE contact ADD COLUMN `status` ENUM('pending', 'read', 'resolved') DEFAULT 'pending' AFTER `message`";
        if (!$conn->query($sql4)) {
            $errors[] = "Erreur ajout colonne status contact: " . $conn->error;
        }
    }
    
    // ===== JOINTURE =====
    // Vérifier et ajouter colonne 'contact_id' à 'feedback'
    $checkContactId = $conn->query("SHOW COLUMNS FROM feedback LIKE 'contact_id'");
    if (!$checkContactId || $checkContactId->num_rows === 0) {
        $sql5 = "ALTER TABLE feedback ADD COLUMN `contact_id` INT AFTER `email`";
        if (!$conn->query($sql5)) {
            $errors[] = "Erreur ajout colonne contact_id: " . $conn->error;
        }
    }
    
    // Ajouter INDEX sur contact_id
    $checkIndexContactId = $conn->query("SHOW INDEX FROM feedback WHERE Column_name = 'contact_id'");
    if (!$checkIndexContactId || $checkIndexContactId->num_rows === 0) {
        $conn->query("ALTER TABLE feedback ADD INDEX idx_contact_id (contact_id)");
    }
    
    return [
        'success' => count($errors) === 0,
        'errors' => $errors
    ];
}

// Exécuter si appelé directement
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'] ?? '')) {
    require_once __DIR__ . '/../models/config.php';
    $result = runAutoMigration($conn);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
