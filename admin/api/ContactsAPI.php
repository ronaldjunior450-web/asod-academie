<?php
require_once dirname(__DIR__, 2) . '/php/config.php';
require_once 'BaseAPI.php';

/**
 * API pour la gestion des contacts
 */
class ContactsAPI extends BaseAPI {
    
    public function __construct() {
        parent::__construct(
            'contacts',
            'id',
            ['nom', 'email', 'sujet', 'message', 'lu', 'repondu', 'reponse', 'date_contact', 'date_reponse'],
            ['nom', 'email', 'sujet', 'message']
        );
    }
    
    /**
     * Récupérer tous les contacts
     */
    public function getAll() {
        $this->requireAuth();
        
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM contacts 
                ORDER BY date_contact DESC
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
                'error' => 'Erreur lors de la récupération des contacts : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupérer un contact par ID
     */
    public function getById($id) {
        $this->requireAuth();
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE id = ?");
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
                    'error' => 'Contact non trouvé'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération du contact : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer un nouveau contact
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
            $sanitizedData['lu'] = 0;
            $sanitizedData['statut'] = 'nouveau';
            
            $fields = array_keys($sanitizedData);
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = "INSERT INTO contacts (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($sanitizedData));
            
            $id = $this->pdo->lastInsertId();
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'create_contact', "Contact créé: {$sanitizedData['nom']}");
            
            return [
                'success' => true,
                'message' => 'Contact créé avec succès',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la création du contact : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour un contact
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
            
            // Si une réponse est ajoutée, mettre à jour la date de réponse
            if (isset($sanitizedData['reponse']) && !empty($sanitizedData['reponse'])) {
                $sanitizedData['date_reponse'] = date('Y-m-d H:i:s');
                $sanitizedData['statut'] = 'repondu';
            }
            
            $fields = array_keys($sanitizedData);
            $setClause = implode(' = ?, ', $fields) . ' = ?';
            
            $sql = "UPDATE contacts SET {$setClause} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_merge(array_values($sanitizedData), [$id]));
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'update_contact', "Contact modifié: ID {$id}");
            
            return [
                'success' => true,
                'message' => 'Contact mis à jour avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la mise à jour du contact : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer un contact
     */
    public function delete($id) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        try {
            // Récupérer le nom pour le log
            $stmt = $this->pdo->prepare("SELECT nom FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            $contact = $stmt->fetch();
            
            if (!$contact) {
                return [
                    'success' => false,
                    'error' => 'Contact non trouvé'
                ];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'delete_contact', "Contact supprimé: {$contact['nom']}");
            
            return [
                'success' => true,
                'message' => 'Contact supprimé avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression du contact : ' . $e->getMessage()
            ];
        }
    }
}
?>

