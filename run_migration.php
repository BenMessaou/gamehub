<?php
/**
 * Migration script to add statut, date_soumission, and date_publication columns
 * Run this file once in your browser: http://localhost/gamehubprjt/run_migration.php
 */

require_once __DIR__ . '/config/config.php';

$db = config::getConnexion();

try {
    echo "<h2>Running Database Migration...</h2>";
    echo "<pre>";
    
    // Check if statut column already exists
    $checkColumn = $db->query("SHOW COLUMNS FROM projects LIKE 'statut'");
    if ($checkColumn->rowCount() > 0) {
        echo "✓ Column 'statut' already exists.\n";
    } else {
        // Add statut column
        $db->exec("ALTER TABLE projects ADD COLUMN statut VARCHAR(20) DEFAULT 'en_attente' AFTER screenshots");
        echo "✓ Added 'statut' column.\n";
    }
    
    // Check if date_soumission column already exists
    $checkColumn = $db->query("SHOW COLUMNS FROM projects LIKE 'date_soumission'");
    if ($checkColumn->rowCount() > 0) {
        echo "✓ Column 'date_soumission' already exists.\n";
    } else {
        // Add date_soumission column
        $db->exec("ALTER TABLE projects ADD COLUMN date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP AFTER statut");
        echo "✓ Added 'date_soumission' column.\n";
    }
    
    // Check if date_publication column already exists
    $checkColumn = $db->query("SHOW COLUMNS FROM projects LIKE 'date_publication'");
    if ($checkColumn->rowCount() > 0) {
        echo "✓ Column 'date_publication' already exists.\n";
    } else {
        // Add date_publication column
        $db->exec("ALTER TABLE projects ADD COLUMN date_publication DATETIME NULL AFTER date_soumission");
        echo "✓ Added 'date_publication' column.\n";
    }
    
    // Update existing records to have 'publie' status if they don't have one
    $db->exec("UPDATE projects SET statut = 'publie' WHERE statut IS NULL OR statut = ''");
    echo "✓ Updated existing records with 'publie' status.\n";
    
    // Set date_soumission for existing records if they don't have one
    $db->exec("UPDATE projects SET date_soumission = NOW() WHERE date_soumission IS NULL");
    echo "✓ Set date_soumission for existing records.\n";
    
    // Set date_publication for existing published records
    $db->exec("UPDATE projects SET date_publication = NOW() WHERE statut = 'publie' AND date_publication IS NULL");
    echo "✓ Set date_publication for published records.\n";
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\nYou can now:\n";
    echo "1. Submit games as a user (they will have status 'en_attente')\n";
    echo "2. View pending games in the admin dashboard\n";
    echo "3. Approve games to make them appear on the home page\n";
    
    echo "</pre>";
    echo "<p><a href='view/frontoffice/index.php'>Go to Home Page</a> | <a href='view/backoffice/projectscrud/projectlist.php'>Go to Admin Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<pre>";
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "</pre>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>

