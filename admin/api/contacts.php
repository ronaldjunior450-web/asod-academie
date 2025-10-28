<?php
require_once 'BaseAPI.php';

/**
 * API CRUD pour les messages de contact
 */
class ContactsAPI extends BaseAPI {
    
    public function __construct() {
        parent::__construct(
            'contacts',
            'id',
            [
                'nom', 'email', 'telephone', 'sujet', 'message', 'lu', 'repondu', 
                'statut', 'date_lecture', 'date_reponse', 'reponse', 'repondu_par'
            ],
            ['nom', 'email', 'sujet', 'message']
        );
    }
    
    /**
     * Créer un message de contact (depuis le formulaire public)
     */
    public function create($data) {
        // Validation spéciale pour les messages de contact
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->sendResponse(['error' => 'Format d\'email invalide'], 400);
            }
        }
        
        // Définir les valeurs par défaut
        $data['statut'] = 'non_lu';
        $data['lu'] = 0;
        $data['repondu'] = 0;
        $data['date_contact'] = date('Y-m-d H:i:s');
        
        parent::create($data);
    }
    
    /**
     * Lire les messages avec filtres avancés
     */
    public function read($id = null, $filters = []) {
        $this->requireAuth();
        
        try {
            if ($id) {
                // Lire un message spécifique et le marquer comme lu
                $sql = "SELECT * FROM contacts WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id' => $id]);
                $result = $stmt->fetch();
                
                if (!$result) {
                    $this->sendResponse(['error' => 'Message non trouvé'], 404);
                }
                
                // Marquer comme lu si pas encore lu
                if (!$result['lu']) {
                    $stmt = $this->pdo->prepare("
                        UPDATE contacts 
                        SET lu = 1, statut = 'lu', date_lecture = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->execute([$id]);
                    $result['lu'] = 1;
                    $result['statut'] = 'lu';
                    $result['date_lecture'] = date('Y-m-d H:i:s');
                }
                
                $this->sendResponse(['success' => true, 'data' => $result]);
            } else {
                // Lire les messages avec filtres
                $sql = "SELECT * FROM contacts";
                $params = [];
                $conditions = [];
                
                // Filtre par statut
                if (isset($filters['statut']) && !empty($filters['statut'])) {
                    $conditions[] = "statut = :statut";
                    $params['statut'] = $filters['statut'];
                }
                
                // Filtre par lu/non lu
                if (isset($filters['lu']) && $filters['lu'] !== '') {
                    $conditions[] = "lu = :lu";
                    $params['lu'] = $filters['lu'];
                }
                
                // Filtre par repondu/non repondu
                if (isset($filters['repondu']) && $filters['repondu'] !== '') {
                    $conditions[] = "repondu = :repondu";
                    $params['repondu'] = $filters['repondu'];
                }
                
                // Filtre par sujet
                if (isset($filters['sujet']) && !empty($filters['sujet'])) {
                    $conditions[] = "sujet LIKE :sujet";
                    $params['sujet'] = '%' . $filters['sujet'] . '%';
                }
                
                // Filtre par nom/email
                if (isset($filters['search']) && !empty($filters['search'])) {
                    $conditions[] = "(nom LIKE :search OR email LIKE :search)";
                    $params['search'] = '%' . $filters['search'] . '%';
                }
                
                // Filtre par date
                if (isset($filters['date_debut']) && !empty($filters['date_debut'])) {
                    $conditions[] = "date_contact >= :date_debut";
                    $params['date_debut'] = $filters['date_debut'];
                }
                
                if (isset($filters['date_fin']) && !empty($filters['date_fin'])) {
                    $conditions[] = "date_contact <= :date_fin";
                    $params['date_fin'] = $filters['date_fin'];
                }
                
                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(' AND ', $conditions);
                }
                
                // Tri
                $orderBy = $filters['order_by'] ?? 'date_contact';
                $orderDir = $filters['order_dir'] ?? 'DESC';
                $sql .= " ORDER BY $orderBy $orderDir";
                
                // Pagination
                $limit = $filters['limit'] ?? 50;
                $offset = $filters['offset'] ?? 0;
                $sql .= " LIMIT $limit OFFSET $offset";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                $results = $stmt->fetchAll();
                
                // Compter le total
                $countSql = "SELECT COUNT(*) as total FROM contacts";
                if (!empty($conditions)) {
                    $countSql .= " WHERE " . implode(' AND ', $conditions);
                }
                $countStmt = $this->pdo->prepare($countSql);
                $countStmt->execute($params);
                $total = $countStmt->fetch()['total'];
                
                $this->sendResponse([
                    'success' => true, 
                    'data' => $results, 
                    'count' => count($results),
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ]);
            }
        } catch (PDOException $e) {
            error_log("Erreur lecture contacts: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de la lecture des messages'], 500);
        }
    }
    
    /**
     * Répondre à un message
     */
    public function reply($id, $data) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        if (!isset($data['reponse']) || empty($data['reponse'])) {
            $this->sendResponse(['error' => 'Réponse requise'], 400);
        }
        
        try {
            $stmt = $this->pdo->prepare("
                UPDATE contacts 
                SET reponse = ?, repondu = 1, statut = 'repondu', 
                    date_reponse = NOW(), repondu_par = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['reponse'], 
                $_SESSION['admin_nom_complet'] ?? $_SESSION['admin_username'], 
                $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendResponse(['error' => 'Message non trouvé'], 404);
            }
            
            logAdminActivity($_SESSION['admin_id'], 'REPLY_CONTACT', "Réponse au message ID: $id");
            
            $this->sendResponse(['success' => true, 'message' => 'Réponse envoyée avec succès']);
        } catch (PDOException $e) {
            error_log("Erreur réponse contact: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de l\'envoi de la réponse'], 500);
        }
    }
    
    /**
     * Marquer comme lu/non lu
     */
    public function markAsRead($id, $read = true) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        try {
            $statut = $read ? 'lu' : 'non_lu';
            $date_lecture = $read ? 'NOW()' : 'NULL';
            
            $stmt = $this->pdo->prepare("
                UPDATE contacts 
                SET lu = ?, statut = ?, date_lecture = $date_lecture
                WHERE id = ?
            ");
            $stmt->execute([$read ? 1 : 0, $statut, $id]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendResponse(['error' => 'Message non trouvé'], 404);
            }
            
            $action = $read ? 'MARK_READ' : 'MARK_UNREAD';
            logAdminActivity($_SESSION['admin_id'], $action, "Message ID: $id");
            
            $this->sendResponse(['success' => true, 'message' => 'Message mis à jour avec succès']);
        } catch (PDOException $e) {
            error_log("Erreur marquage message: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de la mise à jour'], 500);
        }
    }
    
    /**
     * Obtenir les statistiques des messages
     */
    public function getStats() {
        $this->requireAuth();
        
        try {
            $stats = [];
            
            // Total des messages
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM contacts");
            $stats['total'] = $stmt->fetch()['total'];
            
            // Messages non lus
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM contacts WHERE lu = 0");
            $stats['non_lus'] = $stmt->fetch()['count'];
            
            // Messages non répondus
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM contacts WHERE repondu = 0");
            $stats['non_repondus'] = $stmt->fetch()['count'];
            
            // Par statut
            $stmt = $this->pdo->query("SELECT statut, COUNT(*) as count FROM contacts GROUP BY statut");
            $stats['par_statut'] = $stmt->fetchAll();
            
            // Par sujet (top 10)
            $stmt = $this->pdo->query("
                SELECT sujet, COUNT(*) as count 
                FROM contacts 
                GROUP BY sujet 
                ORDER BY count DESC 
                LIMIT 10
            ");
            $stats['par_sujet'] = $stmt->fetchAll();
            
            $this->sendResponse(['success' => true, 'data' => $stats]);
        } catch (PDOException $e) {
            error_log("Erreur stats contacts: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors du calcul des statistiques'], 500);
        }
    }
}

// Gérer la requête
$api = new ContactsAPI();

// Gérer les requêtes spéciales
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'stats':
            $api->getStats();
            break;
        case 'reply':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $api->reply($id, $_POST);
            } else {
                $api->sendResponse(['error' => 'ID de message requis'], 400);
            }
            break;
        case 'mark_read':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $api->markAsRead($id, true);
            } else {
                $api->sendResponse(['error' => 'ID de message requis'], 400);
            }
            break;
        case 'mark_unread':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $api->markAsRead($id, false);
            } else {
                $api->sendResponse(['error' => 'ID de message requis'], 400);
            }
            break;
        default:
            $api->sendResponse(['error' => 'Action non reconnue'], 400);
    }
} else {
    $api->handleRequest();
}
?>


