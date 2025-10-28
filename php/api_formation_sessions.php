<?php
/**
 * API pour récupérer les sessions de formation
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Paramètres de la requête
    $type_session = isset($_GET['type_session']) ? sanitizeInput($_GET['type_session']) : null;
    $statut = isset($_GET['statut']) ? sanitizeInput($_GET['statut']) : null;
    $actif = isset($_GET['actif']) ? (bool)$_GET['actif'] : true;
    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
    
    // Construire la requête
    $whereConditions = [];
    $params = [];
    
    if ($actif) {
        $whereConditions[] = "actif = 1";
    }
    
    if ($type_session && $type_session !== 'tous') {
        $whereConditions[] = "type_session = ?";
        $params[] = $type_session;
    }
    
    if ($statut && $statut !== 'tous') {
        $whereConditions[] = "statut = ?";
        $params[] = $statut;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $stmt = $pdo->prepare("
        SELECT * FROM formation_sessions 
        $whereClause 
        ORDER BY date_session DESC 
        LIMIT ?
    ");
    $params[] = $limite;
    $stmt->execute($params);
    
    $sessions = $stmt->fetchAll();
    
    // Formater les données pour le site
    $result = [];
    foreach ($sessions as $session) {
        $result[] = [
            'id' => $session['id'],
            'titre' => $session['titre'],
            'description' => $session['description'],
            'type_session' => $session['type_session'],
            'date_session' => $session['date_session'],
            'heure_debut' => $session['heure_debut'],
            'heure_fin' => $session['heure_fin'],
            'lieu' => $session['lieu'],
            'entraineur_responsable' => $session['entraineur_responsable'],
            'niveau_requis' => $session['niveau_requis'],
            'nombre_places' => $session['nombre_places'],
            'nombre_inscrits' => $session['nombre_inscrits'],
            'statut' => $session['statut'],
            'objectifs' => $session['objectifs'],
            'materiel_requis' => $session['materiel_requis'],
            'notes' => $session['notes'],
            'actif' => $session['actif']
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



