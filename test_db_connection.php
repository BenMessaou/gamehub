<?php
/**
 * Script de test pour vérifier la connexion à la base de données
 * Accédez à: http://localhost/gamehubprjt/test_db_connection.php
 */

require_once __DIR__ . '/config.php';

echo "<h2>Test de connexion à la base de données</h2>";
echo "<pre>";

try {
    $db = config::getConnexion();
    echo "✓ Connexion réussie!\n\n";
    
    // Afficher le nom de la base de données
    $result = $db->query("SELECT DATABASE() as db_name");
    $dbName = $result->fetch();
    echo "Base de données actuelle: " . $dbName['db_name'] . "\n\n";
    
    // Lister les tables
    echo "Tables disponibles:\n";
    $tables = $db->query("SHOW TABLES");
    $tableCount = 0;
    while ($table = $tables->fetch(PDO::FETCH_NUM)) {
        $tableCount++;
        echo "  - " . $table[0] . "\n";
    }
    echo "\nTotal: $tableCount tables\n\n";
    
    // Vérifier si la table 'user' existe
    $checkUser = $db->query("SHOW TABLES LIKE 'user'");
    if ($checkUser->rowCount() > 0) {
        echo "✓ La table 'user' existe!\n";
        
        // Compter les utilisateurs
        $userCount = $db->query("SELECT COUNT(*) as count FROM user")->fetch();
        echo "  Nombre d'utilisateurs: " . $userCount['count'] . "\n";
    } else {
        echo "✗ La table 'user' n'existe PAS dans cette base de données!\n";
        echo "\nVérifiez:\n";
        echo "1. Que vous êtes connecté à la bonne base de données\n";
        echo "2. Que la table 'user' existe dans cette base\n";
        echo "3. Le nom de la base dans config.php correspond à celle dans phpMyAdmin\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    echo "\nVérifiez:\n";
    echo "1. Que MySQL/MariaDB est démarré\n";
    echo "2. Que les identifiants dans config.php sont corrects\n";
    echo "3. Que la base de données existe\n";
}

echo "</pre>";
?>

