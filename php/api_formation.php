<?php
/**
 * API pour récupérer le contenu de formation du site principal
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Paramètres de la requête
    $section = isset($_GET['section']) ? sanitizeInput($_GET['section']) : null;
    $actif = isset($_GET['actif']) ? (bool)$_GET['actif'] : true;
    
    // Construire la requête
    $whereConditions = [];
    $params = [];
    
    if ($actif) {
        $whereConditions[] = "active = 1";
    }
    
    if ($section && $section !== 'tous') {
        $whereConditions[] = "section_slug = ?";
        $params[] = $section;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $stmt = $pdo->prepare("SELECT section_number, section_title, section_slug, content, order_display FROM formation_content $whereClause ORDER BY order_display ASC");
    $stmt->execute($params);
    
    $formationContent = $stmt->fetchAll();
    
    // Formater les données pour le site
    $result = [];
    foreach ($formationContent as $content) {
        $result[] = [
            'section' => $content['section_number'],
            'section_slug' => $content['section_slug'],
            'titre' => $content['section_title'],
            'contenu' => $content['content'],
            'ordre' => $content['order_display']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'count' => count($result),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de base de données',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>




