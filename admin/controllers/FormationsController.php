<?php
// Contrôleur Formations - Fallback vers section existante
require_once dirname(__DIR__) . '/../php/config.php';

class FormationsController {
    
    /**
     * Affiche la liste des formations
     */
    public function liste() {
        // Inclure directement la section existante
        include dirname(__DIR__) . '/sections/formations.php';
    }
    
    /**
     * Affiche le formulaire d'ajout ou traite la soumission
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traiter la soumission du formulaire
            try {
                $pdo = getDBConnection();
                
                $stmt = $pdo->prepare("
                    INSERT INTO formations (titre, description, niveau, duree, cout_formation, places_disponibles, type_formation, statut) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_POST['titre'],
                    $_POST['description'],
                    $_POST['niveau'],
                    $_POST['duree'],
                    $_POST['cout_formation'],
                    $_POST['places_disponibles'],
                    $_POST['type_formation'],
                    $_POST['statut']
                ]);
                
                echo '<script>window.location.href = "index.php?section=formations&success=added";</script>';
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=formations&error=add_failed";</script>';
                exit;
            }
        } else {
            // Afficher le formulaire d'ajout
            $_GET['action'] = 'add';
            include dirname(__DIR__) . '/sections/formations.php';
        }
    }
    
    /**
     * Affiche le formulaire de modification ou traite la soumission
     */
    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traiter la soumission du formulaire
            try {
                $pdo = getDBConnection();
                
                $stmt = $pdo->prepare("
                    UPDATE formations 
                    SET titre = ?, description = ?, niveau = ?, duree = ?, cout_formation = ?, places_disponibles = ?, type_formation = ?, statut = ?
                    WHERE id = ?
                ");
                $result = $stmt->execute([
                    $_POST['titre'],
                    $_POST['description'],
                    $_POST['niveau'],
                    $_POST['duree'],
                    $_POST['cout_formation'],
                    $_POST['places_disponibles'],
                    $_POST['type_formation'],
                    $_POST['statut'],
                    $id
                ]);
                
                if ($result) {
                    echo '<script>window.location.href = "index.php?section=formations&success=updated";</script>';
                } else {
                    echo '<script>window.location.href = "index.php?section=formations&error=update_failed";</script>';
                }
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=formations&error=update_failed";</script>';
                exit;
            }
        } else {
            // Afficher le formulaire de modification
            $_GET['action'] = 'edit';
            $_GET['id'] = $id;
            include dirname(__DIR__) . '/sections/formations.php';
        }
    }
    
    /**
     * Affiche les détails d'une formation
     */
    public function voir($id) {
        $_GET['action'] = 'view';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/formations.php';
    }
    
    /**
     * Supprime une formation
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            
            $stmt = $pdo->prepare("DELETE FROM formations WHERE id = ?");
            $stmt->execute([$id]);
            
            echo '<script>window.location.href = "index.php?section=formations&success=deleted";</script>';
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=formations&error=delete_failed";</script>';
            exit;
        }
    }
}
?>