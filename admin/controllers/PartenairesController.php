<?php
// Contrôleur Partenaires - Fallback vers section existante
require_once dirname(__DIR__) . '/../php/config.php';

class PartenairesController {
    
    /**
     * Affiche la liste des partenaires
     */
    public function liste() {
        // Inclure directement la section existante
        include dirname(__DIR__) . '/sections/partenaires.php';
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
                    $upload_dir = dirname(__DIR__) . '/../uploads/partenaires/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $logo_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $logo_filename = 'logo_' . time() . '_' . uniqid() . '.' . $logo_extension;
                    $logo_path = 'uploads/partenaires/' . $logo_filename;
                    
                    if (!move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_filename)) {
                        $logo_path = null;
                    }
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO partenaires (nom, description, site_web, contact_email, contact_telephone, adresse, logo, statut, date_creation) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'actif', NOW())
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['description'],
                    $_POST['site_web'],
                    $_POST['contact_email'],
                    $_POST['contact_telephone'],
                    $_POST['adresse'],
                    $logo_path
                ]);
                
                // Redirection vers la liste avec message de succès
                echo '<script>window.location.href = "index.php?section=partenaires&success=added";</script>';
                exit;
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=partenaires&error=add_failed";</script>';
                exit;
            }
        } else {
            // Afficher le formulaire d'ajout
            $_GET['action'] = 'add';
            include dirname(__DIR__) . '/sections/partenaires.php';
        }
    }
    
    /**
     * Affiche le formulaire de modification ou traite la soumission
     */
    public function modifier($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traiter la soumission du formulaire
            try {
                $pdo = getDBConnection();
                
                // Vérifier que l'ID existe
                if (!$id) {
                    throw new Exception("ID du partenaire manquant");
                }
                
                // Gestion du logo
                $logo_path = null;
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = dirname(__DIR__) . '/../uploads/partenaires/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $logo_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $logo_filename = 'logo_' . $id . '_' . time() . '_' . uniqid() . '.' . $logo_extension;
                    $logo_path = 'uploads/partenaires/' . $logo_filename;
                    
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_filename)) {
                        // Supprimer l'ancien logo si il existe
                        $stmt_old = $pdo->prepare("SELECT logo FROM partenaires WHERE id = ?");
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
                    'contact_telephone = ?', 'adresse = ?', 'statut = ?'
                ];
                
                $values = [
                    $_POST['nom'],
                    $_POST['description'],
                    $_POST['site_web'],
                    $_POST['contact_email'],
                    $_POST['contact_telephone'],
                    $_POST['adresse'],
                    $_POST['statut']
                ];
                
                if ($logo_path !== null) {
                    $update_fields[] = 'logo = ?';
                    $values[] = $logo_path;
                }
                
                $values[] = $id;
                
                $sql = "UPDATE partenaires SET " . implode(', ', $update_fields) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute($values);
                
                if ($result) {
                // Redirection vers la liste avec message de succès
                echo '<script>window.location.href = "index.php?section=partenaires&success=updated";</script>';
                exit;
                } else {
                    echo '<script>window.location.href = "index.php?section=partenaires&error=update_failed";</script>';
                    exit;
                }
                
            } catch (Exception $e) {
                echo '<script>window.location.href = "index.php?section=partenaires&error=update_failed";</script>';
                exit;
            }
        } else {
            // Afficher le formulaire de modification
            $_GET['action'] = 'edit';
            $_GET['id'] = $id;
            include dirname(__DIR__) . '/sections/partenaires.php';
        }
    }
    
    /**
     * Affiche la vue détaillée
     */
    public function voir($id = null) {
        $_GET['action'] = 'view';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/partenaires.php';
    }
    
    /**
     * Supprime un partenaire
     */
    public function supprimer($id = null) {
        try {
            $pdo = getDBConnection();
            
            // Supprimer le logo si il existe
            $stmt = $pdo->prepare("SELECT logo FROM partenaires WHERE id = ?");
            $stmt->execute([$id]);
            $logo = $stmt->fetchColumn();
            if ($logo && file_exists(dirname(__DIR__) . '/../' . $logo)) {
                unlink(dirname(__DIR__) . '/../' . $logo);
            }
            
            $stmt = $pdo->prepare("DELETE FROM partenaires WHERE id = ?");
            $stmt->execute([$id]);
            
            // Redirection vers la liste avec message de succès
            echo '<script>window.location.href = "index.php?section=partenaires&success=deleted";</script>';
            exit;
            
        } catch (Exception $e) {
            echo '<script>window.location.href = "index.php?section=partenaires&error=delete_failed";</script>';
            exit;
        }
    }
}
?>