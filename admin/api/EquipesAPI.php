<?php
require_once dirname(__DIR__, 2) . '/php/config.php';
require_once 'BaseAPI.php';

/**
 * API pour la gestion des équipes
 */
class EquipesAPI extends BaseAPI {
    
    public function __construct() {
        parent::__construct(
            'equipes',
            'id',
            ['nom', 'categorie', 'description', 'entraineur', 'entraineur_adjoint', 'logo', 'couleurs', 'statut', 'date_creation'],
            ['nom', 'categorie', 'statut']
        );
    }
    
    /**
     * Récupérer toutes les équipes
     */
    public function getAll() {
        $this->requireAuth();
        
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM equipes 
                ORDER BY categorie, nom ASC
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
                'error' => 'Erreur lors de la récupération des équipes : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupérer une équipe par ID
     */
    public function getById($id) {
        $this->requireAuth();
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM equipes WHERE id = ?");
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
                    'error' => 'Équipe non trouvée'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération de l\'équipe : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer une nouvelle équipe
     */
    public function create($data) {
        $this->requireAuth();
        $this->verifyCSRF();
        
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
            
            $sql = "INSERT INTO equipes (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($sanitizedData));
            
            $id = $this->pdo->lastInsertId();
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'create_equipe', "Équipe créée: {$sanitizedData['nom']}");
            
            return [
                'success' => true,
                'message' => 'Équipe créée avec succès',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la création de l\'équipe : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour une équipe
     */
    public function update($id, $data) {
        $this->requireAuth();
        $this->verifyCSRF();
        
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
            
            $sql = "UPDATE equipes SET {$setClause} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge(array_values($sanitizedData), [$id]));
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'update_equipe', "Équipe modifiée: ID {$id}");
            
            return [
                'success' => true,
                'message' => 'Équipe mise à jour avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la mise à jour de l\'équipe : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer une équipe
     */
    public function delete($id) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        try {
            // Récupérer le nom pour le log
            $stmt = $this->pdo->prepare("SELECT nom FROM equipes WHERE id = ?");
            $stmt->execute([$id]);
            $equipe = $stmt->fetch();
            
            if (!$equipe) {
                return [
                    'success' => false,
                    'error' => 'Équipe non trouvée'
                ];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM equipes WHERE id = ?");
            $stmt->execute([$id]);
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'delete_equipe', "Équipe supprimée: {$equipe['nom']}");
            
            return [
                'success' => true,
                'message' => 'Équipe supprimée avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression de l\'équipe : ' . $e->getMessage()
            ];
        }
    }
}
?>






