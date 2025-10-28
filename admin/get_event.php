<?php
require_once dirname(__DIR__) . '/php/config.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID d\'événement invalide']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT e.*, eq.nom as equipe_nom 
        FROM evenements e 
        LEFT JOIN equipes eq ON e.equipe_id = eq.id 
        WHERE e.id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Événement non trouvé']);
        exit;
    }
    
    // Formater les dates pour l'affichage
    $event['date_debut_formatted'] = date('d/m/Y H:i', strtotime($event['date_debut']));
    if ($event['date_fin']) {
        $event['date_fin_formatted'] = date('d/m/Y H:i', strtotime($event['date_fin']));
    }
    
    echo json_encode($event);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>
