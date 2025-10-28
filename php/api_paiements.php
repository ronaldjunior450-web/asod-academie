<?php
/**
 * API Paiements Formations - ASOD ACADEMIE
 * Système préparé pour activation future
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Fonction pour vérifier si le système de paiement est actif
function isPaymentSystemActive() {
    // Pour l'instant, toujours inactif - à modifier pour activation
    return false;
}

// Fonction de nettoyage des données
function sanitizePaymentData($data) {
    if (is_array($data)) {
        foreach ($data as &$item) {
            $item = sanitizePaymentData($item);
        }
    } else {
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

try {
    $pdo = getDBConnection();
    
    // Vérifier si le système est actif
    if (!isPaymentSystemActive()) {
        echo json_encode([
            'success' => false,
            'message' => 'Système de paiement en préparation',
            'status' => 'inactive',
            'activation_prevue' => 'Prochainement',
            'formations_disponibles' => true,
            'paiement_requis' => false,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // Paramètres de la requête
    $action = $_GET['action'] ?? 'formations_tarifs';
    $formation_id = $_GET['formation_id'] ?? null;
    $membre_id = $_GET['membre_id'] ?? null;
    
    $response = [
        'success' => true,
        'action' => $action,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    switch ($action) {
        case 'formations_tarifs':
            // Récupérer les formations avec leurs tarifs
            $stmt = $pdo->query("
                SELECT f.id, f.titre, f.description, f.cout_formation, f.type_formation,
                       f.age_min, f.age_max, f.places_disponibles, f.duree,
                       e.nom as formateur_nom, e.prenom as formateur_prenom,
                       COUNT(fip.id) as nombre_inscrits,
                       COUNT(CASE WHEN fip.statut_paiement = 'paye' THEN 1 END) as nombre_payes
                FROM formations f
                LEFT JOIN entraineurs e ON f.formateur_id = e.id
                LEFT JOIN formation_inscriptions_paiement fip ON f.id = fip.formation_id
                WHERE f.statut = 'actif' AND f.cout_formation > 0
                GROUP BY f.id
                ORDER BY f.cout_formation DESC
            ");
            $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Enrichir les données
            foreach ($formations as &$formation) {
                $formation = sanitizePaymentData($formation);
                $formation['prix_formate'] = number_format($formation['cout_formation']) . ' FCFA';
                $formation['formateur_complet'] = $formation['formateur_nom'] ? 
                    $formation['formateur_nom'] . ' ' . $formation['formateur_prenom'] : 
                    'Non assigné';
                $formation['places_restantes'] = $formation['places_disponibles'] - $formation['nombre_inscrits'];
                $formation['taux_remplissage'] = $formation['places_disponibles'] > 0 ? 
                    round(($formation['nombre_inscrits'] / $formation['places_disponibles']) * 100, 1) : 0;
            }
            
            $response['formations'] = $formations;
            $response['count'] = count($formations);
            break;
            
        case 'methodes_paiement':
            // Récupérer les méthodes de paiement disponibles
            $stmt = $pdo->query("
                SELECT nom, code, frais_pourcentage, frais_fixe, description
                FROM methodes_paiement_config 
                WHERE actif = 1 
                ORDER BY ordre_affichage
            ");
            $methodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response['methodes'] = $methodes;
            $response['count'] = count($methodes);
            break;
            
        case 'statut_paiement':
            // Vérifier le statut de paiement d'un membre pour une formation
            if (!$formation_id || !$membre_id) {
                throw new Exception('formation_id et membre_id requis');
            }
            
            $stmt = $pdo->prepare("
                SELECT fip.*, f.titre as formation_titre, f.cout_formation,
                       m.nom as membre_nom, m.prenom as membre_prenom
                FROM formation_inscriptions_paiement fip
                JOIN formations f ON fip.formation_id = f.id
                JOIN membres m ON fip.membre_id = m.id
                WHERE fip.formation_id = ? AND fip.membre_id = ?
            ");
            $stmt->execute([$formation_id, $membre_id]);
            $inscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($inscription) {
                $inscription = sanitizePaymentData($inscription);
                $response['inscription'] = $inscription;
                $response['paiement_requis'] = $inscription['cout_formation'] > 0;
                $response['acces_autorise'] = $inscription['statut_paiement'] === 'paye' || 
                                            $inscription['statut_paiement'] === 'non_requis';
            } else {
                $response['inscription'] = null;
                $response['paiement_requis'] = false;
                $response['acces_autorise'] = true;
            }
            break;
            
        case 'statistiques':
            // Statistiques globales des paiements
            $stmt = $pdo->query("
                SELECT 
                    COUNT(*) as total_paiements,
                    COUNT(CASE WHEN statut = 'paye' THEN 1 END) as paiements_reussis,
                    COUNT(CASE WHEN statut = 'en_attente' THEN 1 END) as en_attente,
                    SUM(CASE WHEN statut = 'paye' THEN montant ELSE 0 END) as revenus_total,
                    AVG(CASE WHEN statut = 'paye' THEN montant ELSE NULL END) as montant_moyen
                FROM paiements_formations
            ");
            $stats_paiements = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->query("
                SELECT methode_paiement, COUNT(*) as count, SUM(montant) as total
                FROM paiements_formations 
                WHERE statut = 'paye'
                GROUP BY methode_paiement
            ");
            $stats_methodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response['statistiques'] = [
                'global' => $stats_paiements,
                'par_methode' => $stats_methodes
            ];
            break;
            
        default:
            throw new Exception("Action non supportée : $action");
    }
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    error_log("Erreur API paiements (PDO): " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de base de données',
        'message' => 'Système de paiement en préparation',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    error_log("Erreur API paiements: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de traitement',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>












