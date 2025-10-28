<?php
/**
 * API Actualités avec PhotoManager intégré
 */
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/../php/PhotoManager.php';
require_once 'BaseAPI.php';

class ActualitesAPI_New extends BaseAPI {
    
    private $photoManager;
    
    public function __construct() {
        parent::__construct('actualites');
        
        // Initialiser PhotoManager avec chemin absolu
        $projectRoot = dirname(__DIR__, 2) . '/';
        $this->photoManager = new PhotoManager($this->pdo, $projectRoot . 'uploads/');
    }
    
    /**
     * Créer une actualité avec photo
     */
    public function createWithPhoto($postData, $filesData) {
        try {
            $this->pdo->beginTransaction();
            
            // Traiter la photo si présente
            $photoFilename = null;
            if (isset($filesData['image']) && $filesData['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->photoManager->uploadPhoto($filesData['image'], 'news');
                
                if ($uploadResult['success']) {
                    $photoFilename = 'uploads/news/' . $uploadResult['filename'];
                }
            }
            
            // Récupérer l'auteur depuis la session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $auteur = ($_SESSION['admin_nom'] ?? 'Admin') . ' ' . ($_SESSION['admin_prenom'] ?? 'Utilisateur');
            
            // Insérer l'actualité
            $stmt = $this->pdo->prepare("
                INSERT INTO actualites (titre, contenu, image, statut, auteur, date_creation) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $postData['titre'],
                $postData['contenu'],
                $photoFilename,
                $postData['statut'] ?? 'brouillon',
                $auteur
            ]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Actualité créée avec succès',
                'id' => $this->pdo->lastInsertId()
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            
            // Supprimer la photo si elle a été uploadée
            if ($photoFilename && isset($uploadResult['filename'])) {
                $this->photoManager->deletePhoto($uploadResult['filename'], 'news');
            }
            
            return [
                'success' => false,
                'error' => 'Erreur lors de la création : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour une actualité avec photo
     */
    public function updateWithPhoto($id, $postData, $filesData) {
        try {
            $this->pdo->beginTransaction();
            
            // Récupérer l'ancienne photo
            $stmt = $this->pdo->prepare("SELECT image FROM actualites WHERE id = ?");
            $stmt->execute([$id]);
            $oldPhoto = $stmt->fetchColumn();
            
            // Traiter la nouvelle photo si présente
            $newPhotoPath = null;
            if (isset($filesData['image']) && $filesData['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->photoManager->uploadPhoto($filesData['image'], 'news');
                
                if ($uploadResult['success']) {
                    $newPhotoPath = 'uploads/news/' . $uploadResult['filename'];
                    
                    // Supprimer l'ancienne photo
                    if ($oldPhoto) {
                        $oldFilename = basename($oldPhoto);
                        $this->photoManager->deletePhoto($oldFilename, 'news');
                    }
                }
            }
            
            // Construire la requête UPDATE
            $updateFields = ['titre = ?', 'contenu = ?', 'statut = ?'];
            $values = [
                $postData['titre'],
                $postData['contenu'],
                $postData['statut'] ?? 'brouillon'
            ];
            
            // Ajouter l'image si une nouvelle a été uploadée
            if ($newPhotoPath !== null) {
                $updateFields[] = 'image = ?';
                $values[] = $newPhotoPath;
            }
            
            $values[] = $id;
            
            $stmt = $this->pdo->prepare("
                UPDATE actualites 
                SET " . implode(', ', $updateFields) . " 
                WHERE id = ?
            ");
            $stmt->execute($values);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Actualité mise à jour avec succès'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            
            // Supprimer la nouvelle photo si elle a été uploadée
            if (isset($uploadResult) && $uploadResult['success']) {
                $this->photoManager->deletePhoto($uploadResult['filename'], 'news');
            }
            
            return [
                'success' => false,
                'error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer une actualité avec sa photo
     */
    public function deleteWithPhoto($id) {
        try {
            $this->pdo->beginTransaction();
            
            // Récupérer la photo avant suppression
            $stmt = $this->pdo->prepare("SELECT image FROM actualites WHERE id = ?");
            $stmt->execute([$id]);
            $photo = $stmt->fetchColumn();
            
            // Supprimer l'actualité
            $stmt = $this->pdo->prepare("DELETE FROM actualites WHERE id = ?");
            $stmt->execute([$id]);
            
            // Supprimer la photo physique
            if ($photo) {
                $filename = basename($photo);
                $this->photoManager->deletePhoto($filename, 'news');
            }
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Actualité supprimée avec succès'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            
            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Publier une actualité
     */
    public function publier($id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE actualites 
                SET statut = 'publie', date_publication = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            return [
                'success' => true,
                'message' => 'Actualité publiée avec succès'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la publication : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Dépublier une actualité
     */
    public function depublier($id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE actualites 
                SET statut = 'brouillon' 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            return [
                'success' => true,
                'message' => 'Actualité dépubliée avec succès'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la dépublication : ' . $e->getMessage()
            ];
        }
    }
}
?>










