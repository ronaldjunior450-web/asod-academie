<?php
/**
 * API pour récupérer les évaluations de formation
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Paramètres de la requête
    $joueur_id = isset($_GET['joueur_id']) ? (int)$_GET['joueur_id'] : null;
    $session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : null;
    $actif = isset($_GET['actif']) ? (bool)$_GET['actif'] : true;
    
    // Construire la requête
    $whereConditions = [];
    $params = [];
    
    if ($actif) {
        $whereConditions[] = "actif = 1";
    }
    
    if ($joueur_id) {
        $whereConditions[] = "joueur_id = ?";
        $params[] = $joueur_id;
    }
    
    if ($session_id) {
        $whereConditions[] = "session_id = ?";
        $params[] = $session_id;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    $stmt = $pdo->prepare("
        SELECT e.*, j.nom, j.prenom, s.titre as session_titre, s.date_session
        FROM formation_evaluations e
        LEFT JOIN inscriptions j ON e.joueur_id = j.id
        LEFT JOIN formation_sessions s ON e.session_id = s.id
        $whereClause 
        ORDER BY e.date_evaluation DESC
    ");
    $stmt->execute($params);
    
    $evaluations = $stmt->fetchAll();
    
    // Formater les données pour le site
    $result = [];
    foreach ($evaluations as $eval) {
        $result[] = [
            'id' => $eval['id'],
            'joueur_id' => $eval['joueur_id'],
            'joueur_nom' => $eval['nom'] . ' ' . $eval['prenom'],
            'session_id' => $eval['session_id'],
            'session_titre' => $eval['session_titre'],
            'date_session' => $eval['date_session'],
            'date_evaluation' => $eval['date_evaluation'],
            'note_technique' => $eval['note_technique'],
            'note_tactique' => $eval['note_tactique'],
            'note_physique' => $eval['note_physique'],
            'note_mentale' => $eval['note_mentale'],
            'note_generale' => $eval['note_generale'],
            'commentaires' => $eval['commentaires'],
            'recommandations' => $eval['recommandations'],
            'actif' => $eval['actif']
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



