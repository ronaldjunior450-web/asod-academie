<?php
// Contrôleur Paiements - Gestion des paiements
require_once dirname(__DIR__) . '/../php/config.php';

class PaiementsController {
    
    /**
     * Affiche la liste des paiements
     */
    public function liste() {
        $_GET['action'] = 'list';
        include dirname(__DIR__) . '/sections/paiements.php';
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
                    INSERT INTO paiements (membre_id, montant, type_paiement, date_paiement, statut, reference) 
                    VALUES (?, ?, ?, ?, 'valide', ?)
                ");
                $stmt->execute([
                    $_POST['membre_id'],
                    $_POST['montant'],
                    $_POST['type_paiement'],
                    $_POST['date_paiement'],
                    $_POST['reference']
                ]);
                
                echo '<script>window.location.href = "index.php?section=paiements&success=added";</script>';
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=paiements&error=add_failed";</script>';
                exit;
            }
        }
        
        $_GET['action'] = 'add';
        include dirname(__DIR__) . '/sections/paiements.php';
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
                    UPDATE paiements 
                    SET membre_id = ?, montant = ?, type_paiement = ?, date_paiement = ?, statut = ?, reference = ?
                    WHERE id = ?
                ");
                $result = $stmt->execute([
                    $_POST['membre_id'],
                    $_POST['montant'],
                    $_POST['type_paiement'],
                    $_POST['date_paiement'],
                    $_POST['statut'],
                    $_POST['reference'],
                    $id
                ]);
                
                if ($result) {
                    echo '<script>window.location.href = "index.php?section=paiements&success=updated";</script>';
                } else {
                    echo '<script>window.location.href = "index.php?section=paiements&error=update_failed";</script>';
                }
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=paiements&error=update_failed";</script>';
                exit;
            }
        }
        
        $_GET['action'] = 'edit';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/paiements.php';
    }
    
    /**
     * Affiche les détails d'un paiement
     */
    public function voir($id) {
        $_GET['action'] = 'view';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/paiements.php';
    }
    
    /**
     * Supprime un paiement
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM paiements WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo '<script>window.location.href = "index.php?section=paiements&success=deleted";</script>';
            } else {
                echo '<script>window.location.href = "index.php?section=paiements&error=delete_failed";</script>';
            }
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=paiements&error=delete_failed";</script>';
            exit;
        }
    }
    
    /**
     * Valide un paiement
     */
    public function valider($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE paiements SET statut = 'valide' WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo '<script>window.location.href = "index.php?section=paiements&success=validated";</script>';
            } else {
                echo '<script>window.location.href = "index.php?section=paiements&error=validation_failed";</script>';
            }
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=paiements&error=validation_failed";</script>';
            exit;
        }
    }
    
    /**
     * Annule un paiement
     */
    public function annuler($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE paiements SET statut = 'annule' WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo '<script>window.location.href = "index.php?section=paiements&success=cancelled";</script>';
            } else {
                echo '<script>window.location.href = "index.php?section=paiements&error=cancel_failed";</script>';
            }
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=paiements&error=cancel_failed";</script>';
            exit;
        }
    }
}
?>