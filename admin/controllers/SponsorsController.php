<?php
// Contrôleur Sponsors - Fallback vers section existante
require_once dirname(__DIR__) . '/../php/config.php';

class SponsorsController {
    
    /**
     * Affiche la liste des sponsors
     */
    public function liste() {
        // Inclure directement la section existante
        include dirname(__DIR__) . '/sections/sponsors.php';
    }
    
    /**
     * Affiche le formulaire d'ajout ou traite la soumission
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traiter la soumission du formulaire
            try {
                $pdo = getDBConnection();
                
                // Gestion du logo
                $logo_path = null;
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = dirname(__DIR__) . '/../uploads/sponsors/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $logo_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $logo_filename = 'logo_' . time() . '_' . uniqid() . '.' . $logo_extension;
                    $logo_path = 'uploads/sponsors/' . $logo_filename;
                    
                    if (!move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_filename)) {
                        $logo_path = null;
                    }
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO sponsors (nom, description, site_web, contact_email, contact_telephone, logo, type_sponsoring, montant_sponsoring, statut, date_creation) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'actif', NOW())
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['description'],
                    $_POST['site_web'],
                    $_POST['email'],
                    $_POST['telephone'],
                    $logo_path,
                    $_POST['type_sponsoring'],
                    $_POST['montant_sponsoring']
                ]);
                
                echo '<script>window.location.href = "index.php?section=sponsors&success=added";</script>';
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=sponsors&error=add_failed";</script>';
                exit;
            }
        } else {
            // Afficher le formulaire d'ajout
            $_GET['action'] = 'add';
            include dirname(__DIR__) . '/sections/sponsors.php';
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
                
                // Gestion du logo
                $logo_path = null;
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = dirname(__DIR__) . '/../uploads/sponsors/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $logo_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $logo_filename = 'logo_' . $id . '_' . time() . '.' . $logo_extension;
                    $logo_path = 'uploads/sponsors/' . $logo_filename;
                    
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_filename)) {
                        // Supprimer l'ancien logo si il existe
                        $stmt_old = $pdo->prepare("SELECT logo FROM sponsors WHERE id = ?");
                        $stmt_old->execute([$id]);
                        $old_logo = $stmt_old->fetchColumn();
                        if ($old_logo && file_exists(dirname(__DIR__) . '/../' . $old_logo)) {
                            unlink(dirname(__DIR__) . '/../' . $old_logo);
                        }
                    } else {
                        $logo_path = null;
                    }
                }
                
                // Construction de la requête UPDATE
                $update_fields = [
                    'nom = ?', 'description = ?', 'site_web = ?', 'contact_email = ?', 
                    'contact_telephone = ?', 'type_sponsoring = ?', 'montant_sponsoring = ?', 'statut = ?'
                ];
                
                $values = [
                    $_POST['nom'],
                    $_POST['description'],
                    $_POST['site_web'],
                    $_POST['email'],
                    $_POST['telephone'],
                    $_POST['type_sponsoring'],
                    $_POST['montant_sponsoring'],
                    $_POST['statut']
                ];
                
                if ($logo_path !== null) {
                    $update_fields[] = 'logo = ?';
                    $values[] = $logo_path;
                }
                
                $values[] = $id;
                
                $sql = "UPDATE sponsors SET " . implode(', ', $update_fields) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute($values);
                
                if ($result) {
                    echo '<script>window.location.href = "index.php?section=sponsors&success=updated";</script>';
                } else {
                    echo '<script>window.location.href = "index.php?section=sponsors&error=update_failed";</script>';
                }
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=sponsors&error=update_failed";</script>';
                exit;
            }
        } else {
            // Afficher le formulaire de modification
            $_GET['action'] = 'edit';
            $_GET['id'] = $id;
            include dirname(__DIR__) . '/sections/sponsors.php';
        }
    }
    
    /**
     * Affiche les détails d'un sponsor
     */
    public function voir($id) {
        $_GET['action'] = 'view';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/sponsors.php';
    }
    
    /**
     * Supprime un sponsor
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            
            // Supprimer le logo si il existe
            $stmt = $pdo->prepare("SELECT logo FROM sponsors WHERE id = ?");
            $stmt->execute([$id]);
            $logo = $stmt->fetchColumn();
            if ($logo && file_exists(dirname(__DIR__) . '/../' . $logo)) {
                unlink(dirname(__DIR__) . '/../' . $logo);
            }
            
            $stmt = $pdo->prepare("DELETE FROM sponsors WHERE id = ?");
            $stmt->execute([$id]);
            
            echo '<script>window.location.href = "index.php?section=sponsors&success=deleted";</script>';
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=sponsors&error=delete_failed";</script>';
            exit;
        }
    }
}
?>