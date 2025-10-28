<?php
require_once dirname(__DIR__, 2) . '/php/config.php';
require_once dirname(__DIR__, 2) . '/php/PhotoManager.php';
require_once 'BaseAPI.php';

/**
 * API améliorée pour la gestion du bureau avec PhotoManager
 */
class BureauAPI_New extends BaseAPI {
    
    private $photoManager;
    
    public function __construct() {
        parent::__construct(
            'bureau',
            'id',
            ['nom', 'prenom', 'email', 'telephone', 'poste', 'profession', 'adresse', 'biographie', 'photo', 'actif', 'ordre_affichage'],
            ['nom', 'prenom', 'email', 'poste']
        );
        // Utiliser un chemin absolu vers la racine du projet
        $projectRoot = dirname(__DIR__, 2) . '/';
        $this->photoManager = new PhotoManager($this->pdo, $projectRoot . 'images/');
    }
    
    /**
     * Ajouter un nouveau membre du bureau avec gestion photo améliorée
     */
    public function createWithPhoto($data, $files = null) {
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Gestion de la photo
            $photo_filename = null;
            if (isset($files['photo']) && $files['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->photoManager->uploadPhoto($files['photo'], 'bureau');
                
                if ($uploadResult['success']) {
                    $photo_filename = $uploadResult['filename'];
                } else {
                    throw new Exception('Erreur upload photo: ' . $uploadResult['error']);
                }
            }
            
            // Calculer automatiquement l'ordre d'affichage
            $stmt_max = $this->pdo->query("SELECT COALESCE(MAX(ordre_affichage), 0) + 1 as next_order FROM bureau");
            $next_order = $stmt_max->fetchColumn();
            
            // Préparer les données
            $fields = ['nom', 'prenom', 'poste', 'profession', 'email', 'telephone', 'adresse', 'biographie', 'ordre_affichage', 'photo', 'actif', 'date_creation'];
            $values = [
                $data['nom'],
                $data['prenom'],
                $data['poste'],
                $data['profession'] ?? null,
                $data['email'] ?? null,
                $data['telephone'] ?? null,
                $data['adresse'] ?? null,
                $data['biographie'] ?? null,
                $next_order,
                $photo_filename,
                $data['actif'] ?? 1,
                date('Y-m-d H:i:s')
            ];
            
            $placeholders = array_fill(0, count($values), '?');
            
            $sql = "INSERT INTO bureau (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result) {
                $this->pdo->commit();
                return [
                    'success' => true,
                    'message' => 'Membre du bureau ajouté avec succès',
                    'id' => $this->pdo->lastInsertId()
                ];
            } else {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'error' => 'Erreur lors de l\'ajout'
                ];
            }
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Modifier un membre du bureau avec gestion photo améliorée
     */
    public function updateWithPhoto($id, $data, $files = null) {
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Récupérer l'ancienne photo
            $stmt = $this->pdo->prepare("SELECT photo FROM bureau WHERE id = ?");
            $stmt->execute([$id]);
            $old_photo = $stmt->fetchColumn();
            
            // Gestion de la nouvelle photo
            $photo_filename = $old_photo; // Garder l'ancienne par défaut
            
            if (isset($files['photo']) && $files['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->photoManager->uploadPhoto($files['photo'], 'bureau');
                
                if ($uploadResult['success']) {
                    $photo_filename = $uploadResult['filename'];
                    
                    // Supprimer l'ancienne photo
                    if ($old_photo) {
                        $this->photoManager->deletePhoto($old_photo, 'bureau');
                    }
                } else {
                    throw new Exception('Erreur upload photo: ' . $uploadResult['error']);
                }
            }
            
            // Préparer les données
            $fields = ['nom', 'prenom', 'poste', 'profession', 'email', 'telephone', 'adresse', 'biographie', 'photo', 'actif'];
            $values = [
                $data['nom'],
                $data['prenom'],
                $data['poste'],
                $data['profession'] ?? null,
                $data['email'] ?? null,
                $data['telephone'] ?? null,
                $data['adresse'] ?? null,
                $data['biographie'] ?? null,
                $photo_filename,
                $data['actif'] ?? 1
            ];
            
            $setClause = array_map(function($field) { return "$field = ?"; }, $fields);
            $values[] = $id;
            
            $sql = "UPDATE bureau SET " . implode(', ', $setClause) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result) {
                $this->pdo->commit();
                return [
                    'success' => true,
                    'message' => 'Membre du bureau modifié avec succès'
                ];
            } else {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'error' => 'Erreur lors de la modification'
                ];
            }
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer un membre du bureau avec suppression de la photo
     */
    public function delete($id) {
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Récupérer la photo avant suppression
            $stmt = $this->pdo->prepare("SELECT photo FROM bureau WHERE id = ?");
            $stmt->execute([$id]);
            $photo = $stmt->fetchColumn();
            
            // Supprimer de la base de données
            $stmt = $this->pdo->prepare("DELETE FROM bureau WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                // Supprimer la photo physique
                if ($photo) {
                    $this->photoManager->deletePhoto($photo, 'bureau');
                }
                
                $this->pdo->commit();
                return [
                    'success' => true,
                    'message' => 'Membre du bureau supprimé avec succès'
                ];
            } else {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'error' => 'Erreur lors de la suppression'
                ];
            }
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Vérifier si une photo existe et retourner l'URL
     */
    public function getPhotoUrl($filename) {
        if (empty($filename)) return '';
        
        return $this->photoManager->getPhotoUrl($filename, 'bureau');
    }
    
    /**
     * Nettoyer les photos orphelines
     */
    public function cleanupOrphanPhotos() {
        return $this->photoManager->cleanupOrphanPhotos('bureau', 'bureau', 'photo');
    }
}
?>
