<?php
/**
 * API pour récupérer les partenaires et sponsors
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les partenaires actifs
    $stmt = $pdo->query("SELECT * FROM partenaires WHERE statut = 'actif' ORDER BY ordre_affichage ASC");
    $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les sponsors actifs
    $stmt = $pdo->query("SELECT * FROM sponsors WHERE statut = 'actif' ORDER BY ordre_affichage ASC");
    $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Préparer la réponse
    $response = [
        'success' => true,
        'data' => [
            'partenaires' => $partenaires,
            'sponsors' => $sponsors
        ],
        'count' => [
            'partenaires' => count($partenaires),
            'sponsors' => count($sponsors)
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'error' => 'Erreur de base de données : ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code(500);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Erreur générale : ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    http_response_code(500);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>




