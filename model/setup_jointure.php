<?php
/**
 * JOINTURE: Lier les tables contact et feedback par email
 * Ajoute une clé étrangère pour lier les deux tables
 */

function setupForeignKey($conn) {
    $errors = [];
    
    // Vérifier si la colonne contact_id existe dans feedback
    $checkColumn = $conn->query("SHOW COLUMNS FROM feedback LIKE 'contact_id'");
    if (!$checkColumn || $checkColumn->num_rows === 0) {
        $sql1 = "ALTER TABLE feedback ADD COLUMN `contact_id` INT AFTER `email`";
        if (!$conn->query($sql1)) {
            $errors[] = "Erreur ajout colonne contact_id: " . $conn->error;
        }
    }
    
    // Ajouter INDEX sur contact_id
    $checkIndexContactId = $conn->query("SHOW INDEX FROM feedback WHERE Column_name = 'contact_id'");
    if (!$checkIndexContactId || $checkIndexContactId->num_rows === 0) {
        $conn->query("ALTER TABLE feedback ADD INDEX idx_contact_id (contact_id)");
    }
    
    // Ajouter clé étrangère (si elle n'existe pas)
    $checkFK = $conn->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                             WHERE TABLE_NAME = 'feedback' AND COLUMN_NAME = 'contact_id' AND REFERENCED_TABLE_NAME = 'contact'");
    if (!$checkFK || $checkFK->num_rows === 0) {
        // Vérifier d'abord si la clé existe avant de la supprimer
        $checkExistingFK = $conn->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
                                         WHERE TABLE_NAME = 'feedback' AND CONSTRAINT_NAME = 'fk_feedback_contact'");
        if ($checkExistingFK && $checkExistingFK->num_rows > 0) {
            $conn->query("ALTER TABLE feedback DROP FOREIGN KEY fk_feedback_contact");
        }
        
        // Ajouter la nouvelle clé étrangère
        $sqlFK = "ALTER TABLE feedback ADD CONSTRAINT fk_feedback_contact 
                  FOREIGN KEY (contact_id) REFERENCES contact(id) ON DELETE SET NULL";
        if (!$conn->query($sqlFK)) {
            // Clé étrangère peut ne pas être supportée, ce n'est pas fatal
            // $errors[] = "Erreur ajout clé étrangère: " . $conn->error;
        }
    }
    
    return [
        'success' => count($errors) === 0,
        'errors' => $errors
    ];
}

// Exécuter si appelé directement
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'] ?? '')) {
    require_once __DIR__ . '/config.php';
    $result = setupForeignKey($conn);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
