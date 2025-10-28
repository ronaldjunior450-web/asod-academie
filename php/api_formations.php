<?php
/**
 * API complète pour les formations ASOD ACADEMIE
 * Gère les formations, contenus, sessions et évaluations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Fonction de nettoyage des données
function sanitizeFormationData($data) {
    if (is_array($data)) {
        foreach ($data as &$item) {
            $item = sanitizeFormationData($item);
        }
    } else {
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

try {
    $pdo = getDBConnection();
    
    // Paramètres de la requête
    $type = $_GET['type'] ?? 'formations';
    $statut = $_GET['statut'] ?? 'actif';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    $response = [
        'success' => true,
        'type' => $type,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    switch ($type) {
        case 'formations':
            // Récupérer les formations
            $whereClause = '';
            $params = [];
            
            if ($statut !== 'all') {
                $whereClause = 'WHERE f.statut = ?';
                $params[] = $statut;
            }
            
            $stmt = $pdo->prepare("
                SELECT f.*, 
                       e.nom as formateur_nom, 
                       e.prenom as formateur_prenom,
                       COUNT(fs.id) as nombre_sessions,
                       COUNT(fe.id) as nombre_evaluations
                FROM formations f 
                LEFT JOIN entraineurs e ON f.formateur_id = e.id 
                LEFT JOIN formation_sessions fs ON f.id = fs.formation_id
                LEFT JOIN formation_evaluations fe ON f.id = fe.formation_id
                $whereClause
                GROUP BY f.id
                ORDER BY f.date_creation DESC 
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            
            $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Nettoyer et enrichir les données
            foreach ($formations as &$formation) {
                $formation = sanitizeFormationData($formation);
                $formation['prix_formate'] = $formation['prix'] ? number_format($formation['prix'], 2) . ' €' : 'Gratuit';
                $formation['formateur_complet'] = $formation['formateur_nom'] ? 
                    $formation['formateur_nom'] . ' ' . $formation['formateur_prenom'] : 
                    'Non assigné';
                $formation['date_creation_formate'] = date('d/m/Y', strtotime($formation['date_creation']));
                $formation['paiement_requis'] = $formation['paiement_obligatoire'] == 1;
                
                // Calculer le statut d'inscription
                if ($formation['date_limite_inscription']) {
                    $formation['inscription_ouverte'] = strtotime($formation['date_limite_inscription']) > time();
                    $formation['date_limite_formate'] = date('d/m/Y', strtotime($formation['date_limite_inscription']));
                } else {
                    $formation['inscription_ouverte'] = true;
                    $formation['date_limite_formate'] = null;
                }
            }
            
            $response['formations'] = $formations;
            $response['count'] = count($formations);
            break;
            
        case 'contenus':
            // Récupérer les contenus de formation
            $section = $_GET['section'] ?? null;
            $actif = isset($_GET['actif']) ? (bool)$_GET['actif'] : true;
            
            $whereConditions = [];
            $params = [];
            
            if ($actif) {
                $whereConditions[] = "active = 1";
            }
            
            if ($section && $section !== 'all') {
                $whereConditions[] = "section_slug = ?";
                $params[] = $section;
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            $stmt = $pdo->prepare("
                SELECT section_number, section_title, section_slug, content, order_display, 
                       active, date_creation, date_modification
                FROM formation_content 
                $whereClause 
                ORDER BY order_display ASC, section_number ASC
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            
            $contenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Nettoyer les données
            foreach ($contenus as &$contenu) {
                $contenu = sanitizeFormationData($contenu);
                $contenu['date_creation_formate'] = date('d/m/Y', strtotime($contenu['date_creation']));
                $contenu['actif'] = $contenu['active'] == 1;
            }
            
            $response['contenus'] = $contenus;
            $response['count'] = count($contenus);
            break;
            
        case 'sessions':
            // Récupérer les sessions de formation
            $periode = $_GET['periode'] ?? 'futures'; // futures, passees, all
            
            $whereClause = '';
            $params = [];
            
            switch ($periode) {
                case 'futures':
                    $whereClause = 'WHERE fs.date_session >= CURDATE()';
                    break;
                case 'passees':
                    $whereClause = 'WHERE fs.date_session < CURDATE()';
                    break;
                case 'all':
                default:
                    // Pas de filtre
                    break;
            }
            
            $stmt = $pdo->prepare("
                SELECT fs.*, 
                       f.titre as formation_titre,
                       f.niveau as formation_niveau,
                       f.prix as formation_prix,
                       e.nom as formateur_nom,
                       e.prenom as formateur_prenom
                FROM formation_sessions fs
                LEFT JOIN formations f ON fs.formation_id = f.id
                LEFT JOIN entraineurs e ON fs.formateur_id = e.id
                $whereClause
                ORDER BY fs.date_session ASC
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Nettoyer et enrichir les données
            foreach ($sessions as &$session) {
                $session = sanitizeFormationData($session);
                $session['date_session_formate'] = date('d/m/Y', strtotime($session['date_session']));
                $session['formateur_complet'] = $session['formateur_nom'] ? 
                    $session['formateur_nom'] . ' ' . $session['formateur_prenom'] : 
                    'Non assigné';
                $session['est_future'] = strtotime($session['date_session']) >= strtotime('today');
                $session['duree_formate'] = $session['duree'] ?: 'Non définie';
            }
            
            $response['sessions'] = $sessions;
            $response['count'] = count($sessions);
            break;
            
        case 'evaluations':
            // Récupérer les évaluations
            $formation_id = $_GET['formation_id'] ?? null;
            
            $whereClause = '';
            $params = [];
            
            if ($formation_id) {
                $whereClause = 'WHERE fe.formation_id = ?';
                $params[] = $formation_id;
            }
            
            $stmt = $pdo->prepare("
                SELECT fe.*, 
                       f.titre as formation_titre,
                       m.nom as membre_nom,
                       m.prenom as membre_prenom
                FROM formation_evaluations fe
                LEFT JOIN formations f ON fe.formation_id = f.id
                LEFT JOIN membres m ON fe.membre_id = m.id
                $whereClause
                ORDER BY fe.date_evaluation DESC
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            
            $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Nettoyer les données
            foreach ($evaluations as &$evaluation) {
                $evaluation = sanitizeFormationData($evaluation);
                $evaluation['date_evaluation_formate'] = date('d/m/Y H:i', strtotime($evaluation['date_evaluation']));
                $evaluation['membre_complet'] = $evaluation['membre_nom'] . ' ' . $evaluation['membre_prenom'];
                $evaluation['note_sur_5'] = $evaluation['note'] ? $evaluation['note'] . '/5' : 'Non noté';
            }
            
            $response['evaluations'] = $evaluations;
            $response['count'] = count($evaluations);
            break;
            
        case 'statistiques':
            // Statistiques globales
            $stats = [];
            
            // Formations
            $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as actives FROM formations");
            $formations_stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['formations'] = $formations_stats;
            
            // Contenus
            $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as actifs FROM formation_content");
            $contenus_stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['contenus'] = $contenus_stats;
            
            // Sessions
            $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN date_session >= CURDATE() THEN 1 ELSE 0 END) as futures FROM formation_sessions");
            $sessions_stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['sessions'] = $sessions_stats;
            
            // Évaluations
            $stmt = $pdo->query("SELECT COUNT(*) as total, AVG(note) as note_moyenne FROM formation_evaluations WHERE note IS NOT NULL");
            $evaluations_stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['evaluations'] = $evaluations_stats;
            $stats['evaluations']['note_moyenne'] = $stats['evaluations']['note_moyenne'] ? 
                round($stats['evaluations']['note_moyenne'], 1) : null;
            
            $response['statistiques'] = $stats;
            break;
            
        default:
            throw new Exception("Type de requête non supporté : $type");
    }
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log("Erreur API formations (PDO): " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de base de données',
        'message' => 'Une erreur est survenue lors de l\'accès aux données',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    error_log("Erreur API formations: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de traitement',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
