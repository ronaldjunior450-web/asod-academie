<?php
require_once 'BaseAPI.php';

/**
 * API CRUD pour les inscriptions
 */
class InscriptionsAPI extends BaseAPI {
    
    public function __construct() {
        parent::__construct(
            'inscriptions',
            'id',
            [
                'nom', 'prenom', 'date_naissance', 'lieu_naissance', 'adresse', 'telephone', 
                'email', 'nom_parent', 'prenom_parent', 'telephone_parent', 'email_parent', 
                'profession_parent', 'adresse_parent', 'categorie', 'niveau', 'experience', 
                'motivation', 'statut', 'notes', 'equipe', 'assurance', 'reglement', 
                'newsletter', 'membre_id', 'equipe_id', 'formation_id', 'sexe', 
                'nom_pere', 'prenom_pere', 'nom_mere', 'prenom_mere'
            ],
            ['nom', 'prenom', 'date_naissance', 'lieu_naissance', 'adresse', 'telephone', 'categorie', 'equipe']
        );
    }
    
    /**
     * Créer une inscription avec validation spéciale
     */
    public function create($data) {
        // Validation spéciale pour les inscriptions
        if (isset($data['date_naissance']) && !empty($data['date_naissance'])) {
            if (!strtotime($data['date_naissance'])) {
                $this->sendResponse(['error' => 'Format de date de naissance invalide'], 400);
            }
        }
        
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->sendResponse(['error' => 'Format d\'email invalide'], 400);
            }
        }
        
        if (isset($data['email_parent']) && !empty($data['email_parent'])) {
            if (!filter_var($data['email_parent'], FILTER_VALIDATE_EMAIL)) {
                $this->sendResponse(['error' => 'Format d\'email parent invalide'], 400);
            }
        }
        
        // Vérifier l'unicité de l'inscription
        if (isset($data['nom']) && isset($data['prenom']) && isset($data['date_naissance'])) {
            $stmt = $this->pdo->prepare("
                SELECT id FROM inscriptions 
                WHERE nom = ? AND prenom = ? AND date_naissance = ? AND statut != 'refuse'
            ");
            $stmt->execute([$data['nom'], $data['prenom'], $data['date_naissance']]);
            if ($stmt->fetch()) {
                $this->sendResponse(['error' => 'Une inscription existe déjà pour cette personne'], 400);
            }
        }
        
        // Définir le statut par défaut
        if (!isset($data['statut']) || empty($data['statut'])) {
            $data['statut'] = 'en_attente';
        }
        
        parent::create($data);
    }
    
    /**
     * Lire les inscriptions avec filtres avancés
     */
    public function read($id = null, $filters = []) {
        $this->requireAuth();
        
        try {
            if ($id) {
                // Lire une inscription spécifique
                $sql = "SELECT * FROM inscriptions WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id' => $id]);
                $result = $stmt->fetch();
                
                if (!$result) {
                    $this->sendResponse(['error' => 'Inscription non trouvée'], 404);
                }
                
                $this->sendResponse(['success' => true, 'data' => $result]);
            } else {
                // Lire les inscriptions avec filtres
                $sql = "SELECT * FROM inscriptions";
                $params = [];
                $conditions = [];
                
                // Filtre par statut
                if (isset($filters['statut']) && !empty($filters['statut'])) {
                    $conditions[] = "statut = :statut";
                    $params['statut'] = $filters['statut'];
                }
                
                // Filtre par équipe
                if (isset($filters['equipe']) && !empty($filters['equipe'])) {
                    $conditions[] = "equipe = :equipe";
                    $params['equipe'] = $filters['equipe'];
                }
                
                // Filtre par catégorie
                if (isset($filters['categorie']) && !empty($filters['categorie'])) {
                    $conditions[] = "categorie = :categorie";
                    $params['categorie'] = $filters['categorie'];
                }
                
                // Filtre par sexe
                if (isset($filters['sexe']) && !empty($filters['sexe'])) {
                    $conditions[] = "sexe = :sexe";
                    $params['sexe'] = $filters['sexe'];
                }
                
                // Filtre par nom/prénom
                if (isset($filters['search']) && !empty($filters['search'])) {
                    $conditions[] = "(nom LIKE :search OR prenom LIKE :search)";
                    $params['search'] = '%' . $filters['search'] . '%';
                }
                
                // Filtre par date
                if (isset($filters['date_debut']) && !empty($filters['date_debut'])) {
                    $conditions[] = "date_inscription >= :date_debut";
                    $params['date_debut'] = $filters['date_debut'];
                }
                
                if (isset($filters['date_fin']) && !empty($filters['date_fin'])) {
                    $conditions[] = "date_inscription <= :date_fin";
                    $params['date_fin'] = $filters['date_fin'];
                }
                
                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(' AND ', $conditions);
                }
                
                // Tri
                $orderBy = $filters['order_by'] ?? 'date_inscription';
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
                $countSql = "SELECT COUNT(*) as total FROM inscriptions";
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
            error_log("Erreur lecture inscriptions: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de la lecture des inscriptions'], 500);
        }
    }
    
    /**
     * Valider une inscription (créer un membre)
     */
    public function validate($id) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        try {
            // Récupérer l'inscription
            $stmt = $this->pdo->prepare("SELECT * FROM inscriptions WHERE id = ? AND statut = 'en_attente'");
            $stmt->execute([$id]);
            $inscription = $stmt->fetch();
            
            if (!$inscription) {
                $this->sendResponse(['error' => 'Inscription non trouvée ou déjà traitée'], 404);
            }
            
            $this->pdo->beginTransaction();
            
            // Trouver l'équipe correspondante
            $equipe_id = null;
            if (!empty($inscription['equipe'])) {
                $equipe_nom = $inscription['equipe'];
                $genre = $inscription['sexe'] === 'Masculin' ? 'garcons' : 'filles';
                
                // Gestion spéciale pour "Seniors"
                if (strtolower($equipe_nom) === 'seniors') {
                    $stmt_equipe = $this->pdo->prepare("
                        SELECT id FROM equipes 
                        WHERE categorie = 'Seniors' AND genre = ?
                        LIMIT 1
                    ");
                    $stmt_equipe->execute([$genre]);
                    $equipe = $stmt_equipe->fetch();
                    
                    if ($equipe) {
                        $equipe_id = $equipe['id'];
                    }
                } else {
                    // Extraire l'âge de l'inscription (ex: U12-U14 -> 12)
                    $age_demande = null;
                    if (preg_match('/U(\d+)/', $equipe_nom, $matches)) {
                        $age_demande = (int)$matches[1];
                    }
                    
                    if ($age_demande) {
                        // Chercher l'équipe la plus proche par âge (approche simplifiée)
                        $stmt_equipe = $this->pdo->prepare("
                            SELECT id, nom FROM equipes 
                            WHERE nom LIKE ? AND genre = ?
                            ORDER BY nom ASC
                        ");
                        $stmt_equipe->execute(['%U%', $genre]);
                        $equipes = $stmt_equipe->fetchAll();
                        
                        // Trouver l'équipe avec l'âge le plus proche
                        $meilleure_equipe = null;
                        $diff_min = PHP_INT_MAX;
                        
                        foreach ($equipes as $equipe) {
                            // Extraire l'âge de l'équipe
                            if (preg_match('/U(\d+)/', $equipe['nom'], $matches)) {
                                $age_equipe = (int)$matches[1];
                                $diff = abs($age_equipe - $age_demande);
                                
                                if ($diff < $diff_min) {
                                    $diff_min = $diff;
                                    $meilleure_equipe = $equipe;
                                }
                            }
                        }
                        
                        if ($meilleure_equipe) {
                            $equipe_id = $meilleure_equipe['id'];
                        }
                    }
                    
                    // Si pas trouvé, essayer une correspondance plus large
                    if (!$equipe_id) {
                        $stmt_equipe = $this->pdo->prepare("
                            SELECT id FROM equipes 
                            WHERE nom LIKE ? AND genre = ?
                            ORDER BY nom ASC
                            LIMIT 1
                        ");
                        $stmt_equipe->execute(['%' . $equipe_nom . '%', $genre]);
                        $equipe = $stmt_equipe->fetch();
                        
                        if ($equipe) {
                            $equipe_id = $equipe['id'];
                        }
                    }
                }
            }
            
            // Debug: Vérifier l'équipe trouvée
            error_log("DEBUG - Équipe ID trouvée: " . ($equipe_id ?? 'NULL'));
            error_log("DEBUG - Inscription équipe: " . $inscription['equipe']);
            error_log("DEBUG - Inscription sexe: " . $inscription['sexe']);
            
            // Créer le membre
            $membreData = [
                'nom' => $inscription['nom'],
                'prenom' => $inscription['prenom'],
                'genre' => $inscription['sexe'] === 'Masculin' ? 'garcon' : 'fille',
                'date_naissance' => $inscription['date_naissance'],
                'lieu_naissance' => $inscription['lieu_naissance'],
                'adresse' => $inscription['adresse'],
                'telephone' => $inscription['telephone'],
                'email' => $inscription['email'],
                'nom_parent' => $inscription['nom_parent'],
                'prenom_parent' => $inscription['prenom_parent'],
                'telephone_parent' => $inscription['telephone_parent'],
                'email_parent' => $inscription['email_parent'],
                'profession_parent' => $inscription['profession_parent'],
                'adresse_parent' => $inscription['adresse_parent'],
                'categorie' => $inscription['categorie'],
                'equipe_id' => $equipe_id,
                'poste' => $inscription['poste'] ?? null,
                'statut' => 'actif',
                'date_adhesion' => date('Y-m-d H:i:s')
            ];
            
            $fields = array_keys($membreData);
            $placeholders = array_map(function($field) { return ":$field"; }, $fields);
            
            $sql = "INSERT INTO membres (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($membreData);
            
            $membre_id = $this->pdo->lastInsertId();
            
            // Mettre à jour l'inscription
            $stmt = $this->pdo->prepare("
                UPDATE inscriptions 
                SET statut = 'valide', date_traitement = NOW(), traite_par = ?, membre_id = ?
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['admin_nom_complet'] ?? $_SESSION['admin_username'], $membre_id, $id]);
            
            $this->pdo->commit();
            
            // Envoyer email d'acceptation
            $this->sendAcceptanceEmail($inscription);
            
            logAdminActivity($_SESSION['admin_id'], 'VALIDATE_INSCRIPTION', "Validation inscription ID: $id, Membre ID: $membre_id");
            
            $this->sendResponse([
                'success' => true, 
                'message' => 'Inscription validée avec succès et email d\'acceptation envoyé',
                'membre_id' => $membre_id
            ]);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur validation inscription: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de la validation de l\'inscription'], 500);
        }
    }
    
    /**
     * Refuser une inscription
     */
    public function reject($id, $reason = '') {
        $this->requireAuth();
        $this->verifyCSRF();
        
        try {
            $stmt = $this->pdo->prepare("
                UPDATE inscriptions 
                SET statut = 'refuse', date_traitement = NOW(), traite_par = ?, notes = ?
                WHERE id = ? AND statut = 'en_attente'
            ");
            $stmt->execute([$_SESSION['admin_nom_complet'] ?? $_SESSION['admin_username'], $reason, $id]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendResponse(['error' => 'Inscription non trouvée ou déjà traitée'], 404);
            }
            
            logAdminActivity($_SESSION['admin_id'], 'REJECT_INSCRIPTION', "Refus inscription ID: $id");
            
            $this->sendResponse(['success' => true, 'message' => 'Inscription refusée avec succès']);
        } catch (PDOException $e) {
            error_log("Erreur refus inscription: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors du refus de l\'inscription'], 500);
        }
    }
    
    /**
     * Envoyer un email d'acceptation
     */
    private function sendAcceptanceEmail($inscription) {
        try {
            $to = $inscription['email'];
            $subject = "ASOD ACADEMIE - Inscription acceptée !";
            
            $message = "
            <html>
            <head>
                <title>Inscription acceptée</title>
            </head>
            <body>
                <h2>Félicitations {$inscription['prenom']} {$inscription['nom']} !</h2>
                <p>Votre inscription à ASOD ACADEMIE a été acceptée.</p>
                <p><strong>Équipe :</strong> {$inscription['equipe']}</p>
                <p><strong>Prochaines étapes :</strong></p>
                <ul>
                    <li>Rendez-vous au club pour compléter votre dossier</li>
                    <li>Apportez votre photo d'identité</li>
                    <li>Informations parentales et numéro CIP (si nécessaire)</li>
                </ul>
                <p>Bienvenue dans la famille ASOD ACADEMIE !</p>
                <p>L'équipe ASOD ACADEMIE</p>
            </body>
            </html>
            ";
            
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: ASOD ACADEMIE <noreply@asodacademie.com>',
                'Reply-To: contact@asodacademie.com'
            ];
            
            mail($to, $subject, $message, implode("\r\n", $headers));
            
        } catch (Exception $e) {
            error_log("Erreur envoi email acceptation: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir les statistiques des inscriptions
     */
    public function getStats() {
        $this->requireAuth();
        
        try {
            $stats = [];
            
            // Total des inscriptions
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM inscriptions");
            $stats['total'] = $stmt->fetch()['total'];
            
            // Par statut
            $stmt = $this->pdo->query("SELECT statut, COUNT(*) as count FROM inscriptions GROUP BY statut");
            $stats['par_statut'] = $stmt->fetchAll();
            
            // Par équipe
            $stmt = $this->pdo->query("SELECT equipe, COUNT(*) as count FROM inscriptions GROUP BY equipe");
            $stats['par_equipe'] = $stmt->fetchAll();
            
            // Par catégorie
            $stmt = $this->pdo->query("SELECT categorie, COUNT(*) as count FROM inscriptions GROUP BY categorie");
            $stats['par_categorie'] = $stmt->fetchAll();
            
            // Par sexe
            $stmt = $this->pdo->query("SELECT sexe, COUNT(*) as count FROM inscriptions GROUP BY sexe");
            $stats['par_sexe'] = $stmt->fetchAll();
            
            $this->sendResponse(['success' => true, 'data' => $stats]);
        } catch (PDOException $e) {
            error_log("Erreur stats inscriptions: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors du calcul des statistiques'], 500);
        }
    }
}

// Gérer la requête
$api = new InscriptionsAPI();

// Gérer les requêtes spéciales
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'stats':
            $api->getStats();
            break;
        case 'validate':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $api->validate($id);
            } else {
                $api->sendResponse(['error' => 'ID d\'inscription requis'], 400);
            }
            break;
        case 'reject':
            $id = $_GET['id'] ?? null;
            $reason = $_POST['reason'] ?? '';
            if ($id) {
                $api->reject($id, $reason);
            } else {
                $api->sendResponse(['error' => 'ID d\'inscription requis'], 400);
            }
            break;
        default:
            $api->sendResponse(['error' => 'Action non reconnue'], 400);
    }
} else {
    $api->handleRequest();
}
?>


