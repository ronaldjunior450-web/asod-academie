<?php
require_once dirname(__DIR__, 2) . '/php/config.php';
require_once dirname(__DIR__, 2) . '/php/PhotoManager.php';
require_once 'BaseAPI.php';

/**
 * API pour la gestion des membres
 */
class MembresAPI extends BaseAPI {
    
    private $photoManager;
    
    public function __construct() {
        parent::__construct(
            'membres',
            'id',
            ['nom', 'prenom', 'email', 'telephone', 'date_naissance', 'adresse', 'photo', 'statut', 'date_adhesion'],
            ['nom', 'prenom', 'email', 'statut']
        );
        
        // Initialiser PhotoManager
        $projectRoot = dirname(__DIR__, 2) . '/';
        $this->photoManager = new PhotoManager($this->pdo, $projectRoot . 'images/');
    }
    
    /**
     * Récupérer tous les membres
     */
    public function getAll() {
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM membres 
                ORDER BY nom, prenom ASC
            ");
            $data = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $data,
                'count' => count($data)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération des membres : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupérer un membre par ID
     */
    public function getById($id) {
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM membres WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if ($data) {
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Membre non trouvé'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération du membre : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer un nouveau membre
     */
    public function create($data) {
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            return [
                'success' => false,
                'error' => 'Token CSRF invalide'
            ];
        }
        
        // Validation des données
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'error' => 'Données invalides',
                'errors' => $errors
            ];
        }
        
        try {
            $sanitizedData = $this->sanitizeData($data);
            $sanitizedData['date_creation'] = date('Y-m-d H:i:s');
            
            $fields = array_keys($sanitizedData);
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = "INSERT INTO membres (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($sanitizedData));
            
            $id = $this->pdo->lastInsertId();
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'create_membre', "Membre créé: {$sanitizedData['prenom']} {$sanitizedData['nom']}");
            
            return [
                'success' => true,
                'message' => 'Membre créé avec succès',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la création du membre : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour un membre
     */
    public function update($id, $data) {
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            return [
                'success' => false,
                'error' => 'Token CSRF invalide'
            ];
        }
        
        // Validation des données
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            return [
                'success' => false,
                'error' => 'Données invalides',
                'errors' => $errors
            ];
        }
        
        try {
            $sanitizedData = $this->sanitizeData($data);
            $sanitizedData['date_modification'] = date('Y-m-d H:i:s');
            
            $fields = array_keys($sanitizedData);
            $setClause = implode(' = ?, ', $fields) . ' = ?';
            
            $sql = "UPDATE membres SET {$setClause} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge(array_values($sanitizedData), [$id]));
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'update_membre', "Membre modifié: ID {$id}");
            
            return [
                'success' => true,
                'message' => 'Membre mis à jour avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la mise à jour du membre : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer un membre
     */
    public function delete($id) {
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            return [
                'success' => false,
                'error' => 'Token CSRF invalide'
            ];
        }
        
        try {
            // Récupérer le nom pour le log
            $stmt = $this->pdo->prepare("SELECT nom, prenom FROM membres WHERE id = ?");
            $stmt->execute([$id]);
            $membre = $stmt->fetch();
            
            if (!$membre) {
                return [
                    'success' => false,
                    'error' => 'Membre non trouvé'
                ];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM membres WHERE id = ?");
            $stmt->execute([$id]);
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'delete_membre', "Membre supprimé: {$membre['prenom']} {$membre['nom']}");
            
            return [
                'success' => true,
                'message' => 'Membre supprimé avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression du membre : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer un membre avec photo
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
            
            // Gérer l'upload de photo si présent
            if ($files && isset($files['photo']) && $files['photo']['error'] === UPLOAD_ERR_OK) {
                $photoResult = $this->photoManager->uploadPhoto($files['photo'], 'membres');
                if (!$photoResult['success']) {
                    $this->pdo->rollBack();
                    return $photoResult;
                }
                $data['photo'] = $photoResult['filename'];
            }
            
            // Créer le membre
            $result = parent::create($data);
            
            if ($result['success']) {
                $this->pdo->commit();
            } else {
                $this->pdo->rollBack();
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'error' => 'Erreur lors de la création du membre : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Modifier un membre avec photo
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
            $oldPhoto = null;
            $stmt = $this->pdo->prepare("SELECT photo FROM membres WHERE id = ?");
            $stmt->execute([$id]);
            $oldMember = $stmt->fetch();
            if ($oldMember) {
                $oldPhoto = $oldMember['photo'];
            }
            
            // Gérer l'upload de nouvelle photo si présent
            if ($files && isset($files['photo']) && $files['photo']['error'] === UPLOAD_ERR_OK) {
                $photoResult = $this->photoManager->uploadPhoto($files['photo'], 'membres');
                if (!$photoResult['success']) {
                    $this->pdo->rollBack();
                    return $photoResult;
                }
                $data['photo'] = $photoResult['filename'];
                
                // Supprimer l'ancienne photo
                if ($oldPhoto) {
                    $this->photoManager->deletePhoto($oldPhoto, 'membres');
                }
            }
            
            // Modifier le membre
            $result = parent::update($id, $data);
            
            if ($result['success']) {
                $this->pdo->commit();
            } else {
                $this->pdo->rollBack();
            }
            
            return $result;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'error' => 'Erreur lors de la modification du membre : ' . $e->getMessage()
            ];
        }
    }
}
?>


