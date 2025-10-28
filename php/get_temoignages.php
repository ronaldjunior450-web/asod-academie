<?php
/**
 * Script pour récupérer les témoignages approuvés pour l'affichage public
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    // Connexion à la base de données
    $pdo = getDBConnection();
    
    // Récupérer les témoignages approuvés (statut = 'publie')
    $stmt = $pdo->prepare("
        SELECT nom, prenom, temoignage, note, date_creation
        FROM temoignages 
        WHERE statut = 'publie' 
        ORDER BY date_creation DESC 
        LIMIT 10
    ");
    
    $stmt->execute();
    $temoignages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Nettoyer les données pour l'affichage
    foreach ($temoignages as &$tem) {
        $tem['nom'] = htmlspecialchars($tem['nom'], ENT_QUOTES, 'UTF-8');
        $tem['prenom'] = htmlspecialchars($tem['prenom'], ENT_QUOTES, 'UTF-8');
        $tem['temoignage'] = htmlspecialchars($tem['temoignage'], ENT_QUOTES, 'UTF-8');
        $tem['note'] = $tem['note'] ? (int)$tem['note'] : null;
        
        // Formater la date
        $tem['date_creation'] = date('Y-m-d H:i:s', strtotime($tem['date_creation']));
    }
    
    echo json_encode([
        'success' => true,
        'temoignages' => $temoignages,
        'count' => count($temoignages)
    ]);
    
} catch (Exception $e) {
    error_log("Erreur get_temoignages: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du chargement des témoignages',
        'temoignages' => [],
        'count' => 0
    ]);
}
?>

