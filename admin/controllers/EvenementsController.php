<?php
// Contrôleur Événements
require_once dirname(__DIR__) . '/../php/config.php';

class EvenementsController {
    
    /**
     * Affiche la liste des événements
     */
    public function liste() {
        // Rediriger vers la page d'administration
        header("Location: index.php?section=evenements");
        exit;
    }
    
    /**
     * Affiche le formulaire d'ajout
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement de la soumission du formulaire
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    INSERT INTO evenements (titre, description, type_evenement, date_debut, date_fin, lieu, statut) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_POST['titre'],
                    $_POST['description'],
                    $_POST['type_evenement'],
                    $_POST['date_debut'],
                    $_POST['date_fin'],
                    $_POST['lieu'],
                    $_POST['statut'] ?? 'programme'
                ]);
                
                // Redirection vers la liste avec message de succès
                header("Location: index.php?section=evenements&success=add");
                exit;
                
            } catch (Exception $e) {
                header("Location: index.php?section=evenements&error=add_failed");
                exit;
            }
        }
        
        // Rediriger vers la page d'administration pour afficher le formulaire
        header("Location: index.php?section=evenements&action=add");
        exit;
    }
    
    /**
     * Affiche les détails d'un événement
     */
    public function voir($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM evenements WHERE id = ?");
            $stmt->execute([$id]);
            $evenement = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($evenement) {
                // Rediriger vers la page d'administration avec les paramètres
                header("Location: index.php?section=evenements&action=view&id={$id}");
                exit;
            } else {
                header("Location: index.php?section=evenements&error=not_found");
                exit;
            }
        } catch (Exception $e) {
            header("Location: index.php?section=evenements&error=load_failed");
            exit;
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
                    UPDATE evenements 
                    SET titre = ?, description = ?, type_evenement = ?, date_debut = ?, date_fin = ?, lieu = ?, statut = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['titre'],
                    $_POST['description'],
                    $_POST['type_evenement'],
                    $_POST['date_debut'],
                    $_POST['date_fin'],
                    $_POST['lieu'],
                    $_POST['statut'],
                    $id
                ]);
                
                // Redirection vers la liste avec message de succès
                header("Location: index.php?section=evenements&success=update");
                exit;
                
            } catch (Exception $e) {
                header("Location: index.php?section=evenements&error=update_failed");
                exit;
            }
        }
        
        // Rediriger vers la page d'administration pour afficher le formulaire
        header("Location: index.php?section=evenements&action=edit&id={$id}");
        exit;
    }
    
    /**
     * Supprime un événement
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM evenements WHERE id = ?");
            $stmt->execute([$id]);
            
            // Redirection vers la liste avec message de succès
            header("Location: index.php?section=evenements&success=delete");
            exit;
            
        } catch (Exception $e) {
            // Redirection avec message d'erreur
            header("Location: index.php?section=evenements&error=delete_failed");
            exit;
        }
    }
}
?>