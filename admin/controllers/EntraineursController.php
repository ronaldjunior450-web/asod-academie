<?php
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/../php/PhotoManager.php';

class EntraineursController {
    
    /**
     * Affiche la liste des entraîneurs
     */
    public function liste() {
        include dirname(__DIR__) . '/sections/entraineurs.php';
    }
    
    /**
     * Affiche le formulaire d'ajout ou traite l'ajout
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = getDBConnection();
                
                // Gestion de la photo avec PhotoManager
                $photo_path = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    // Utiliser PhotoManager pour l'upload
                    $projectRoot = dirname(__DIR__, 2) . '/';
                    $photoManager = new PhotoManager($pdo, $projectRoot . 'images/');
                    
                    $photoResult = $photoManager->uploadPhoto($_FILES['photo'], 'entraineurs');
                    if ($photoResult['success']) {
                        $photo_path = $photoResult['filename'];
                    } else {
                        echo '<div class="alert alert-danger">Erreur upload photo: ' . htmlspecialchars($photoResult['error']) . '</div>';
                        return;
                    }
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO entraineurs (nom, prenom, email, telephone, specialite, statut, photo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['prenom'],
                    $_POST['email'],
                    $_POST['telephone'],
                    $_POST['specialite'],
                    $_POST['statut'] ?? 'actif',
                    $photo_path
                ]);
                
                header("Location: index.php?section=entraineurs&success=add");
                exit;
            } catch (Exception $e) {
                header("Location: index.php?section=entraineurs&error=add_failed");
                exit;
            }
        }
        // Afficher le formulaire d'ajout
        $_GET['action'] = 'ajouter';
        include dirname(__DIR__) . '/sections/entraineurs.php';
    }
    
    /**
     * Affiche le formulaire de modification ou traite la mise à jour
     */
    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = getDBConnection();
                
                // Gestion de la photo avec PhotoManager
                $photo_path = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    // Utiliser PhotoManager pour l'upload
                    $projectRoot = dirname(__DIR__, 2) . '/';
                    $photoManager = new PhotoManager($pdo, $projectRoot . 'images/');
                    
                    $photoResult = $photoManager->uploadPhoto($_FILES['photo'], 'entraineurs');
                    if ($photoResult['success']) {
                        $photo_path = $photoResult['filename'];
                        
                        // Supprimer l'ancienne photo si elle existe
                        $stmt_old = $pdo->prepare("SELECT photo FROM entraineurs WHERE id = ?");
                        $stmt_old->execute([$id]);
                        $old_photo = $stmt_old->fetchColumn();
                        if ($old_photo) {
                            $photoManager->deletePhoto($old_photo, 'entraineurs');
                        }
                    } else {
                        echo '<div class="alert alert-danger">Erreur upload photo: ' . htmlspecialchars($photoResult['error']) . '</div>';
                        return;
                    }
                }
                
                // Construction de la requête UPDATE
                $update_fields = ['nom = ?', 'prenom = ?', 'email = ?', 'telephone = ?', 'specialite = ?', 'statut = ?'];
                $values = [$_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'], $_POST['specialite'], $_POST['statut']];
                
                if ($photo_path !== null) {
                    $update_fields[] = 'photo = ?';
                    $values[] = $photo_path;
                }
                
                $values[] = $id;
                
                $stmt = $pdo->prepare("UPDATE entraineurs SET " . implode(', ', $update_fields) . " WHERE id = ?");
                $stmt->execute($values);
                
                // Redirection immédiate vers la liste avec message de succès
                header('Location: index.php?section=entraineurs&success=updated');
                exit;
                
            } catch (Exception $e) {
                // En cas d'erreur, rediriger avec message d'erreur
                header('Location: index.php?section=entraineurs&error=' . urlencode($e->getMessage()));
                exit;
            }
        } else {
            // Afficher le formulaire de modification
            $_GET['action'] = 'modifier';
            $_GET['id'] = $id;
            include dirname(__DIR__) . '/sections/entraineurs.php';
        }
    }
    
    /**
     * Supprime un entraîneur
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            
            // Vérifier s'il y a des équipes associées
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM equipes WHERE entraineur_id = ?");
            $stmt->execute([$id]);
            $equipes_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($equipes_count > 0) {
                $_GET['error'] = 'has_teams';
            } else {
                // Supprimer la photo si elle existe
                $stmt = $pdo->prepare("SELECT photo FROM entraineurs WHERE id = ?");
                $stmt->execute([$id]);
                $entraineur = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($entraineur && $entraineur['photo'] && file_exists($entraineur['photo'])) {
                    unlink($entraineur['photo']);
                }
                
                // Supprimer l'entraîneur
                $stmt = $pdo->prepare("DELETE FROM entraineurs WHERE id = ?");
                $stmt->execute([$id]);
                
                $_GET['success'] = 'delete';
            }
        } catch (Exception $e) {
            $_GET['error'] = 'delete_failed';
        }
        
        // Afficher la liste des entraîneurs
        $_GET['action'] = 'liste';
        include dirname(__DIR__) . '/sections/entraineurs.php';
    }
    
    /**
     * Affiche les détails d'un entraîneur
     */
    public function voir($id) {
        // Afficher la vue détaillée
        $_GET['action'] = 'voir';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/entraineurs.php';
    }
}
?>