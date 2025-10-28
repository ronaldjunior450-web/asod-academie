<?php
/**
 * Contrôleur pour la gestion des transferts externes
 */

require_once __DIR__ . '/../../php/config.php';

class TransfertsController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Afficher la liste des membres transférés (externes uniquement)
     */
    public function liste() {
        try {
            // Récupérer tous les transferts externes avec les informations des membres
            $stmt = $this->pdo->query("
                SELECT 
                    m.id as membre_id,
                    m.nom,
                    m.prenom,
                    m.sexe,
                    m.date_naissance,
                    m.telephone,
                    m.email,
                    m.photo,
                    m.equipe_id,
                    e.nom as equipe_origine,
                    e.categorie as categorie_origine,
                    t.id as transfert_id,
                    t.association_destination,
                    t.ville_destination,
                    t.contact_destination,
                    t.motif,
                    t.traite_par,
                    t.date_transfert,
                    t.created_at
                FROM membres m
                INNER JOIN transferts_membres t ON m.id = t.membre_id
                LEFT JOIN equipes e ON m.equipe_id = e.id
                WHERE m.statut = 'transfere' AND t.type_transfert = 'externe'
                ORDER BY m.sexe, m.nom, m.prenom
            ");
            
            $transferts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Organiser par genre seulement (comme les membres)
            $transfertsOrganises = [
                'garcons' => [],
                'filles' => []
            ];
            
            foreach ($transferts as $t) {
                // Normaliser le genre
                $genre_normalise = '';
                if ($t['sexe'] === 'M' || $t['sexe'] === 'Masculin') {
                    $genre_normalise = 'garcons';
                } else if ($t['sexe'] === 'F' || $t['sexe'] === 'Féminin' || $t['sexe'] === 'Feminin') {
                    $genre_normalise = 'filles';
                }
                
                // Ajouter à la bonne catégorie
                if ($genre_normalise) {
                    $transfertsOrganises[$genre_normalise][] = $t;
                }
            }
            
            // Statistiques
            $stats = [
                'total' => count($transferts),
                'garcons' => count($transfertsOrganises['garcons']),
                'filles' => count($transfertsOrganises['filles'])
            ];
            
            // Inclure la vue
            include __DIR__ . '/../sections/transferts.php';
            
        } catch (Exception $e) {
            error_log("Erreur TransfertsController::liste - " . $e->getMessage());
            echo '<div class="alert alert-danger">Erreur lors du chargement des transferts.</div>';
        }
    }
    
    /**
     * Voir les détails d'un transfert
     */
    public function voir($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    m.*,
                    e.nom as equipe_origine,
                    e.categorie as categorie_origine,
                    t.id as transfert_id,
                    t.association_destination,
                    t.ville_destination,
                    t.contact_destination,
                    t.motif,
                    t.traite_par,
                    t.date_transfert,
                    t.created_at
                FROM membres m
                INNER JOIN transferts_membres t ON m.id = t.membre_id
                LEFT JOIN equipes e ON t.equipe_destination = e.id
                WHERE t.id = ? AND m.statut = 'transfere' AND t.type_transfert = 'externe'
            ");
            
            $stmt->execute([$id]);
            $transfert = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$transfert) {
                echo '<div class="alert alert-warning">Transfert non trouvé.</div>';
                return;
            }
            
            // Inclure la vue détaillée
            include __DIR__ . '/../sections/transferts.php';
            
        } catch (Exception $e) {
            error_log("Erreur TransfertsController::voir - " . $e->getMessage());
            echo '<div class="alert alert-danger">Erreur lors du chargement du transfert.</div>';
        }
    }
    
    /**
     * Restaurer un transfert (annuler le transfert et remettre le membre en actif)
     */
    public function restaurer($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = getDBConnection();
                $id = $_POST['id'] ?? $id;
                $traite_par = $_SESSION['admin_nom_complet'] ?? $_SESSION['admin_username'];

                error_log("=== RESTAURATION TRANSFERT (Controller) ===");
                error_log("ID transfert: " . $id);
                error_log("Traité par: " . $traite_par);

                // Vérifier que le transfert existe
                $stmt = $pdo->prepare("
                    SELECT tm.*, m.nom, m.prenom, m.sexe, m.equipe_id as equipe_origine
                    FROM transferts_membres tm
                    JOIN membres m ON tm.membre_id = m.id
                    WHERE tm.id = ?
                ");
                $stmt->execute([$id]);
                $transfert = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$transfert) {
                    throw new Exception("Transfert non trouvé");
                }

                error_log("Transfert trouvé: " . $transfert['nom'] . " " . $transfert['prenom']);

                // Commencer une transaction
                $pdo->beginTransaction();

                // 1. Remettre le membre en actif
                $stmt = $pdo->prepare("
                    UPDATE membres 
                    SET statut = 'actif', 
                        equipe_id = ?
                    WHERE id = ?
                ");
                $stmt->execute([$transfert['equipe_origine'], $transfert['membre_id']]);

                // 2. Supprimer l'enregistrement de transfert
                $stmt = $pdo->prepare("DELETE FROM transferts_membres WHERE id = ?");
                $stmt->execute([$id]);

                // Valider la transaction
                $pdo->commit();

                error_log("Transfert restauré avec succès");

                // Déterminer le genre pour la redirection
                $genre = 'garcons';
                if ($transfert['sexe'] === 'F' || $transfert['sexe'] === 'Féminin' || $transfert['sexe'] === 'Feminin') {
                    $genre = 'filles';
                }

                header("Location: index.php?section=transferts&success=restored&genre={$genre}");
                exit;

            } catch (Exception $e) {
                if (isset($pdo)) {
                    $pdo->rollBack();
                }
                error_log("=== ERREUR RESTAURATION TRANSFERT (Controller) ===");
                error_log($e->getMessage());
                echo '<div class="alert alert-danger">Erreur lors de la restauration : ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            error_log("=== AFFICHAGE FORMULAIRE RESTAURATION TRANSFERT ===");
            error_log("ID reçu en paramètre: " . $id);

            // Vérifier que le transfert existe
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    SELECT tm.*, m.nom, m.prenom, m.sexe, m.equipe_id as equipe_origine
                    FROM transferts_membres tm
                    JOIN membres m ON tm.membre_id = m.id
                    WHERE tm.id = ?
                ");
                $stmt->execute([$id]);
                $transfert = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$transfert) {
                    error_log("❌ Transfert non trouvé (ID: $id)");
                    echo '<div class="alert alert-warning">Transfert non trouvé.</div>';
                    return;
                }

                error_log("✅ Transfert trouvé: " . $transfert['nom'] . " " . $transfert['prenom']);

            } catch (Exception $e) {
                error_log("❌ Erreur lors de la vérification du transfert: " . $e->getMessage());
                echo '<div class="alert alert-danger">Erreur lors de la vérification du transfert.</div>';
                return;
            }

            $_GET['action'] = 'restaurer';
            $_GET['id'] = $id;
            include dirname(__DIR__) . '/sections/transferts.php';
        }
    }
}

// Router
$action = $_GET['action'] ?? 'liste';
$id = $_GET['id'] ?? null;

$controller = new TransfertsController();

switch ($action) {
    case 'voir':
        if ($id) {
            $controller->voir($id);
        } else {
            $controller->liste();
        }
        break;
    
    case 'restaurer':
        $controller->restaurer($_GET['id'] ?? null);
        break;
    
    case 'liste':
    default:
        $controller->liste();
        break;
}
?>

