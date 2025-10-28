<?php
// Contrôleur Témoignages - Fallback vers section existante
require_once dirname(__DIR__) . '/../php/config.php';

class TemoignagesController {
    
    /**
     * Affiche la liste des témoignages
     */
    public function liste() {
        $_GET['action'] = 'list';
        include dirname(__DIR__) . '/sections/temoignages.php';
    }
    
    /**
     * Les témoignages viennent du site public, pas d'ajout par l'admin
     */
    
    /**
     * L'admin ne modifie pas les témoignages, il les valide/rejette seulement
     */
    
    /**
     * Affiche les détails d'un témoignage
     */
    public function voir($id) {
        $_GET['action'] = 'view';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/temoignages.php';
    }
    
    /**
     * Supprime un témoignage
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM temoignages WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo '<script>window.location.href = "index.php?section=temoignages&success=deleted";</script>';
            } else {
                echo '<script>window.location.href = "index.php?section=temoignages&error=delete_failed";</script>';
            }
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=temoignages&error=delete_failed";</script>';
            exit;
        }
    }
    
    /**
     * Approuve un témoignage
     */
    public function publier($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE temoignages SET statut = 'publie' WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo '<script>window.location.href = "index.php?section=temoignages&success=published";</script>';
            } else {
                echo '<script>window.location.href = "index.php?section=temoignages&error=publish_failed";</script>';
            }
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=temoignages&error=publish_failed";</script>';
            exit;
        }
    }
    
    /**
     * Rejette un témoignage
     */
    public function rejeter($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE temoignages SET statut = 'rejete' WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo '<script>window.location.href = "index.php?section=temoignages&success=rejected";</script>';
            } else {
                echo '<script>window.location.href = "index.php?section=temoignages&error=reject_failed";</script>';
            }
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=temoignages&error=reject_failed";</script>';
            exit;
        }
    }
}
?>