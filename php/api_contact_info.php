<?php
// API pour récupérer les informations de contact depuis la base de données
header('Content-Type: application/json');
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les informations de contact actives
    $stmt = $pdo->prepare("
        SELECT 
            type_contact, libelle, valeur, description, icone, couleur, ordre_affichage
        FROM contact_info 
        WHERE actif = 1 
        ORDER BY ordre_affichage ASC, type_contact ASC
    ");
    $stmt->execute();
    $contact_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organiser les données par type
    $organized_data = [];
    foreach ($contact_infos as $info) {
        $organized_data[$info['type_contact']] = $info;
    }
    
    echo json_encode($organized_data, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des informations de contact: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

