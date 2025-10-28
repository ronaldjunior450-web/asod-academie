<?php
// API pour récupérer les partenaires et sponsors
header('Content-Type: application/json');
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les partenaires actifs
    $stmt = $pdo->prepare("
        SELECT 
            id, nom, description, logo, site_web, 
            contact_email, contact_telephone, adresse,
            type_partenaire, ordre_affichage
        FROM partenaires 
        WHERE statut = 'actif' 
        ORDER BY ordre_affichage ASC, nom ASC
    ");
    $stmt->execute();
    $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les sponsors actifs
    $stmt = $pdo->prepare("
        SELECT 
            id, nom, description, logo, site_web, 
            contact_email, contact_telephone,
            type_sponsoring as type_partenaire, ordre_affichage
        FROM sponsors 
        WHERE statut = 'actif' 
        ORDER BY ordre_affichage ASC, nom ASC
    ");
    $stmt->execute();
    $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combiner les données
    $data = [
        'partenaires' => $partenaires,
        'sponsors' => $sponsors,
        'total' => count($partenaires) + count($sponsors)
    ];
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des données: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
