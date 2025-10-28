<?php
/**
 * Script d'export de la base de données ASOD ACADEMIE
 * Exporte la base locale de manière simple et robuste (compatibles InfinityFree)
 */

// Configuration de la base de données locale
$host = 'localhost';
$dbname = 'asod_fc';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>📤 Export de la base de données ASOD ACADEMIE (SIMPLE)</h2>";
    echo "<p>🔄 Connexion à la base de données locale réussie...</p>";
    
    // Récupérer toutes les tables (sauf les vues)
    $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>📋 Tables trouvées : " . count($tables) . "</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Créer le fichier SQL
    $sqlFile = 'asod_academie_simple_' . date('Y-m-d_H-i-s') . '.sql';
    $file = fopen($sqlFile, 'w');
    
    if (!$file) {
        throw new Exception("Impossible de créer le fichier $sqlFile");
    }
    
    // En-tête du fichier SQL
    fwrite($file, "-- Export de la base de données ASOD ACADEMIE (SIMPLE)\n");
    fwrite($file, "-- Date: " . date('Y-m-d H:i:s') . "\n");
    fwrite($file, "-- Compatible InfinityFree (sans vues et sans clés étrangères)\n\n");
    fwrite($file, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
    fwrite($file, "SET AUTOCOMMIT = 0;\n");
    fwrite($file, "START TRANSACTION;\n");
    fwrite($file, "SET time_zone = \"+00:00\";\n\n");
    
    // Désactiver les vérifications de clés étrangères
    fwrite($file, "-- Désactiver les vérifications de clés étrangères\n");
    fwrite($file, "SET FOREIGN_KEY_CHECKS = 0;\n\n");
    
    // Exporter chaque table
    foreach ($tables as $table) {
        echo "<p>📤 Export de la table : $table</p>";
        
        // Structure de la table
        $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $createTableSql = $createTable['Create Table'];
        
        fwrite($file, "-- Structure de la table `$table`\n");
        fwrite($file, "DROP TABLE IF EXISTS `$table`;\n");
        fwrite($file, $createTableSql . ";\n\n");
        
        // Données de la table
        $data = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($data)) {
            fwrite($file, "-- Données de la table `$table`\n");
            
            $columns = array_keys($data[0]);
            $columnNames = '`' . implode('`, `', $columns) . '`';
            
            foreach ($data as $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . addslashes($value) . "'";
                    }
                }
                $valuesStr = implode(', ', $values);
                fwrite($file, "INSERT INTO `$table` ($columnNames) VALUES ($valuesStr);\n");
            }
            fwrite($file, "\n");
        }
    }
    
    // Réactiver les vérifications de clés étrangères
    fwrite($file, "-- Réactiver les vérifications de clés étrangères\n");
    fwrite($file, "SET FOREIGN_KEY_CHECKS = 1;\n\n");
    
    // Pied de page
    fwrite($file, "COMMIT;\n");
    fwrite($file, "-- Fin de l'export\n");
    
    fclose($file);
    
    echo "<p style='color: green;'>✅ <strong>Export terminé avec succès !</strong></p>";
    echo "<p>📁 Fichier créé : <strong>$sqlFile</strong></p>";
    echo "<p>📊 Taille du fichier : " . number_format(filesize($sqlFile) / 1024, 2) . " KB</p>";
    
    // Afficher le lien de téléchargement
    echo "<p><a href='$sqlFile' download style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📥 Télécharger le fichier SQL</a></p>";
    
    echo "<hr>";
    echo "<h3>📋 Instructions d'import sur InfinityFree :</h3>";
    echo "<ol>";
    echo "<li>📥 Télécharger le fichier <strong>$sqlFile</strong></li>";
    echo "<li>🌐 Aller sur phpMyAdmin InfinityFree</li>";
    echo "<li>🗄️ Sélectionner la base <strong>if0_39987344_asod_academie</strong></li>";
    echo "<li>📤 Cliquer sur <strong>Importer</strong></li>";
    echo "<li>📁 Choisir le fichier <strong>$sqlFile</strong></li>";
    echo "<li>✅ Cliquer sur <strong>Exécuter</strong></li>";
    echo "</ol>";
    
    echo "<p style='color: orange;'>⚠️ <strong>Note :</strong> Ce fichier contient la structure complète des tables. Les clés étrangères seront ignorées par InfinityFree.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Erreur :</strong> " . $e->getMessage() . "</p>";
}
?>
