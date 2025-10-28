<?php
/**
 * Script pour récupérer les informations de contact depuis la base de données
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $pdo = getDBConnection();
    
    // Récupérer toutes les informations de contact actives
    $stmt = $pdo->query("
        SELECT type_contact, libelle, valeur, description, icone, couleur, ordre_affichage
        FROM contact_info 
        WHERE actif = 1 
        ORDER BY ordre_affichage ASC
    ");
    
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organiser les contacts par catégorie
    $organizedContacts = [
        'contact_direct' => [],
        'messagerie' => [],
        'reseaux_sociaux' => [],
        'autres' => []
    ];
    
    foreach ($contacts as $contact) {
        $type = $contact['type_contact'];
        
        if (in_array($type, ['telephone', 'email', 'adresse', 'site_web'])) {
            $category = 'contact_direct';
        } elseif (in_array($type, ['whatsapp', 'telegram'])) {
            $category = 'messagerie';
        } elseif (in_array($type, ['facebook', 'instagram', 'twitter', 'youtube', 'tiktok', 'linkedin'])) {
            $category = 'reseaux_sociaux';
        } else {
            $category = 'autres';
        }
        
        $organizedContacts[$category][] = $contact;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $organizedContacts,
        'count' => count($contacts)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
