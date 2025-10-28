<?php
require_once dirname(__DIR__, 2) . '/php/config.php';
require_once 'BaseAPI.php';

/**
 * API pour la gestion des inscriptions
 */
class InscriptionsAPI extends BaseAPI {
    
    public function __construct() {
        parent::__construct(
            'inscriptions',
            'id',
            ['nom', 'prenom', 'date_naissance', 'numero_cip', 'email', 'telephone', 'adresse', 'ville', 'code_postal', 'pays', 'equipe_souhaitee', 'niveau', 'experience', 'motivation', 'certificat_medical', 'autorisation_parentale', 'statut', 'date_inscription'],
            ['nom', 'prenom', 'email', 'equipe_souhaitee', 'statut']
        );
    }
    
    /**
     * Récupérer toutes les inscriptions
     */
    public function getAll() {
        $this->requireAuth();
        
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM inscriptions 
                ORDER BY date_inscription DESC
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
                'error' => 'Erreur lors de la récupération des inscriptions : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupérer une inscription par ID
     */
    public function getById($id) {
        $this->requireAuth();
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM inscriptions WHERE id = ?");
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
                    'error' => 'Inscription non trouvée'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération de l\'inscription : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer une nouvelle inscription
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
            
            $sql = "INSERT INTO inscriptions (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($sanitizedData));
            
            $id = $this->pdo->lastInsertId();
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'create_inscription', "Inscription créée: {$sanitizedData['prenom']} {$sanitizedData['nom']}");
            
            return [
                'success' => true,
                'message' => 'Inscription créée avec succès',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la création de l\'inscription : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour une inscription
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
            
            $sql = "UPDATE inscriptions SET {$setClause} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge(array_values($sanitizedData), [$id]));
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'update_inscription', "Inscription modifiée: ID {$id}");
            
            return [
                'success' => true,
                'message' => 'Inscription mise à jour avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la mise à jour de l\'inscription : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer une inscription
     */
    public function delete($id) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        try {
            // Récupérer le nom pour le log
            $stmt = $this->pdo->prepare("SELECT nom, prenom FROM inscriptions WHERE id = ?");
            $stmt->execute([$id]);
            $inscription = $stmt->fetch();
            
            if (!$inscription) {
                return [
                    'success' => false,
                    'error' => 'Inscription non trouvée'
                ];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM inscriptions WHERE id = ?");
            $stmt->execute([$id]);
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'delete_inscription', "Inscription supprimée: {$inscription['prenom']} {$inscription['nom']}");
            
            return [
                'success' => true,
                'message' => 'Inscription supprimée avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression de l\'inscription : ' . $e->getMessage()
            ];
        }
    }
}
?>



