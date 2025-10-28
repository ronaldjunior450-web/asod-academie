<?php
// Contrôleur Infos Contact - Gestion des informations de contact
require_once dirname(__DIR__) . '/../php/config.php';

class ContactInfoController {
    
    /**
     * Affiche la liste des informations de contact
     */
    public function liste() {
        $_GET['action'] = 'list';
        include dirname(__DIR__) . '/sections/contact_info.php';
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
                    INSERT INTO contact_info (type_contact, valeur, description, actif) 
                    VALUES (?, ?, ?, 1)
                ");
                $stmt->execute([
                    $_POST['type_contact'],
                    $_POST['valeur'],
                    $_POST['description'] ?? ''
                ]);
                
                echo '<script>window.location.href = "index.php?section=contact_info&success=added";</script>';
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=contact_info&error=add_failed";</script>';
                exit;
            }
        }
        
        $_GET['action'] = 'add';
        include dirname(__DIR__) . '/sections/contact_info.php';
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
                    UPDATE contact_info 
                    SET type_contact = ?, valeur = ?, description = ?, actif = ?
                    WHERE id = ?
                ");
                $result = $stmt->execute([
                    $_POST['type_contact'],
                    $_POST['valeur'],
                    $_POST['description'] ?? '',
                    $_POST['actif'] ?? 1,
                    $id
                ]);
                
                if ($result) {
                    echo '<script>window.location.href = "index.php?section=contact_info&success=updated";</script>';
                } else {
                    echo '<script>window.location.href = "index.php?section=contact_info&error=update_failed";</script>';
                }
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=contact_info&error=update_failed";</script>';
                exit;
            }
        }
        
        $_GET['action'] = 'edit';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/contact_info.php';
    }
    
    /**
     * Affiche les détails d'une information de contact
     */
    public function voir($id) {
        $_GET['action'] = 'view';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/contact_info.php';
    }
    
    /**
     * Supprime une information de contact
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM contact_info WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo '<script>window.location.href = "index.php?section=contact_info&success=deleted";</script>';
            } else {
                echo '<script>window.location.href = "index.php?section=contact_info&error=delete_failed";</script>';
            }
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=contact_info&error=delete_failed";</script>';
            exit;
        }
    }
    
    /**
     * Active/Désactive une information de contact
     */
    public function toggleActif($id) {
        try {
            $pdo = getDBConnection();
            
            // Récupérer l'état actuel
            $stmt = $pdo->prepare("SELECT actif FROM contact_info WHERE id = ?");
            $stmt->execute([$id]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($info) {
                $nouvel_etat = $info['actif'] ? 0 : 1;
                $stmt = $pdo->prepare("UPDATE contact_info SET actif = ? WHERE id = ?");
                $result = $stmt->execute([$nouvel_etat, $id]);
                
                if ($result) {
                    echo '<script>window.location.href = "index.php?section=contact_info&success=toggled";</script>';
                } else {
                    echo '<script>window.location.href = "index.php?section=contact_info&error=toggle_failed";</script>';
                }
            }
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=contact_info&error=toggle_failed";</script>';
            exit;
        }
    }
}
?>