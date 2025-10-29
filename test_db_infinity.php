<?php
/**
 * Test de connexion à la base de données InfinityFree
 */

// Configuration directe pour le test
$host = 'sql301.infinityfree.com';
$dbname = 'if0_39987344_asod_academie';
$username = 'if0_39987344';
$password = 'lnGWxSJQ7dwQE3S';
$charset = 'utf8mb4';

echo "<h2>Test de connexion à la base de données InfinityFree</h2>";

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "<p style='color: green;'>✅ Connexion réussie !</p>";
    
    // Tester une requête simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM information_schema.tables WHERE table_schema = '$dbname'");
    $result = $stmt->fetch();
    echo "<p>📊 Nombre de tables dans la base : <strong>" . $result['total'] . "</strong></p>";
    
    // Lister quelques tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>📋 Tables trouvées :</p><ul>";
    foreach (array_slice($tables, 0, 10) as $table) {
        echo "<li>$table</li>";
    }
    if (count($tables) > 10) {
        echo "<li>... et " . (count($tables) - 10) . " autres</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion : " . $e->getMessage() . "</p>";
    echo "<p>Détails techniques :</p>";
    echo "<ul>";
    echo "<li>Host: $host</li>";
    echo "<li>Database: $dbname</li>";
    echo "<li>Username: $username</li>";
    echo "<li>Charset: $charset</li>";
    echo "</ul>";
}
?>
