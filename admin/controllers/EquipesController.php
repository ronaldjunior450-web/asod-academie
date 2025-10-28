<?php
// Contrôleur Équipes - Utilise l'API existante
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/api/BaseAPI.php';
require_once dirname(__DIR__) . '/api/EquipesAPI.php';

class EquipesController {
    private $api;
    
    public function __construct() {
        $this->api = new EquipesAPI();
    }
    
    /**
     * Affiche la liste des équipes
     */
    public function liste() {
        try {
            // Définir l'action pour afficher la liste
            $_GET['action'] = 'list';
            
            // Inclure directement la section existante qui gère tout
            include dirname(__DIR__) . '/sections/equipes.php';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Affiche le formulaire d'ajout
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement de la soumission du formulaire
            try {
                $pdo = getDBConnection();
                
                // Calculer automatiquement l'ordre d'affichage (dernier + 1)
                $stmt = $pdo->query("SELECT MAX(ordre_affichage) as max_ordre FROM equipes");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $nouvel_ordre = ($result['max_ordre'] ?? 0) + 1;
                
                $stmt = $pdo->prepare("
                    INSERT INTO equipes (nom, categorie, genre, age_min, age_max, entraineur_id, 
                        couleur_maillot, couleur_short, couleur_chaussettes, actif, ordre_affichage, horaires_entrainement) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['categorie'],
                    $_POST['genre'],
                    $_POST['age_min'],
                    $_POST['age_max'],
                    $_POST['entraineur_id'] ?? null,
                    $_POST['couleur_maillot'] ?? '',
                    $_POST['couleur_short'] ?? '',
                    $_POST['couleur_chaussettes'] ?? '',
                    $_POST['actif'] ?? 1,
                    $nouvel_ordre,
                    $_POST['horaires_entrainement'] ?? ''
                ]);
                
                // Redirection vers la liste avec message de succès
                header("Location: index.php?section=equipes&success=add");
                exit;
                
            } catch (Exception $e) {
                $error = "Erreur lors de l'ajout : " . $e->getMessage();
            }
        }
        
        $_GET['action'] = 'add';
        include dirname(__DIR__) . '/sections/equipes.php';
    }
    
    /**
     * Affiche les détails d'une équipe
     */
    public function voir($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM equipes WHERE id = ?");
            $stmt->execute([$id]);
            $equipe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($equipe) {
                $_GET['action'] = 'view';
                $_GET['id'] = $id;
                include dirname(__DIR__) . '/sections/equipes.php';
            } else {
                echo '<div class="alert alert-danger">Équipe non trouvée.</div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Affiche le formulaire de modification
     */
    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement de la soumission du formulaire
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    UPDATE equipes 
                    SET nom = ?, categorie = ?, genre = ?, age_min = ?, age_max = ?, 
                        entraineur_id = ?, couleur_maillot = ?, couleur_short = ?, couleur_chaussettes = ?, 
                        actif = ?, ordre_affichage = ?, horaires_entrainement = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['categorie'],
                    $_POST['genre'],
                    $_POST['age_min'],
                    $_POST['age_max'],
                    $_POST['entraineur_id'] ?? null,
                    $_POST['couleur_maillot'] ?? '',
                    $_POST['couleur_short'] ?? '',
                    $_POST['couleur_chaussettes'] ?? '',
                    $_POST['actif'],
                    $_POST['ordre_affichage'] ?? 0,
                    $_POST['horaires_entrainement'] ?? '',
                    $id
                ]);
                
                // Redirection vers la liste avec message de succès
                header("Location: index.php?section=equipes&success=update");
                exit;
                
            } catch (Exception $e) {
                $error = "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        }
        
        $_GET['action'] = 'edit';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/equipes.php';
    }
    
    /**
     * Supprime une équipe
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            
            // Vérifier s'il y a des membres dans cette équipe
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM membres WHERE equipe_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                // Redirection avec message d'erreur
                header("Location: index.php?section=equipes&error=has_members");
                exit;
            }
            
            // Supprimer l'équipe
            $stmt = $pdo->prepare("DELETE FROM equipes WHERE id = ?");
            $stmt->execute([$id]);
            
            // Redirection vers la liste avec message de succès
            header("Location: index.php?section=equipes&success=delete");
            exit;
            
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            header("Location: index.php?section=equipes&error=delete_failed");
            exit;
        }
    }
}
?>