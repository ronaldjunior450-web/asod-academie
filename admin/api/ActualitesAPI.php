<?php
require_once dirname(__DIR__, 2) . '/php/config.php';
require_once 'BaseAPI.php';

/**
 * API pour la gestion des actualités
 */
class ActualitesAPI extends BaseAPI {
    
    public function __construct() {
        parent::__construct(
            'actualites',
            'id',
            ['titre', 'contenu', 'resume', 'image', 'statut', 'date_publication', 'auteur'],
            ['titre', 'contenu', 'resume', 'statut']
        );
    }
    
    /**
     * Récupérer toutes les actualités
     */
    public function getAll() {
        // Vérification simplifiée
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM actualites 
                ORDER BY date_creation DESC
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
                'error' => 'Erreur lors de la récupération des actualités : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupérer une actualité par ID
     */
    public function getById($id) {
        // Vérification simplifiée
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM actualites WHERE id = ?");
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
                    'error' => 'Actualité non trouvée'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la récupération de l\'actualité : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer une nouvelle actualité
     */
    public function create($data) {
        // Vérification simplifiée
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        // Vérification CSRF simplifiée
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($token)) {
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
            $sanitizedData['auteur'] = $_SESSION['admin_username'] ?? 'Admin';
            $sanitizedData['date_creation'] = date('Y-m-d H:i:s');
            
            $fields = array_keys($sanitizedData);
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = "INSERT INTO actualites (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($sanitizedData));
            
            $id = $this->pdo->lastInsertId();
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'create_actualite', "Actualité créée: {$sanitizedData['titre']}");
            
            return [
                'success' => true,
                'message' => 'Actualité créée avec succès',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la création de l\'actualité : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour une actualité
     */
    public function update($id, $data) {
        // DEBUG: Log de l'entrée
        error_log("API UPDATE - ID: $id, Data: " . print_r($data, true));
        
        // Vérification simplifiée sans exit
        if (!isAdminLoggedIn()) {
            error_log("API UPDATE - Non autorisé");
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        // Vérification CSRF simplifiée
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($token)) {
            error_log("API UPDATE - Token CSRF invalide");
            return [
                'success' => false,
                'error' => 'Token CSRF invalide'
            ];
        }
        
        error_log("API UPDATE - Vérifications OK");
        
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
            
            error_log("API UPDATE - Sanitized data: " . print_r($sanitizedData, true));
            
            $fields = array_keys($sanitizedData);
            $setClause = implode(' = ?, ', $fields) . ' = ?';
            
            $sql = "UPDATE actualites SET {$setClause} WHERE id = ?";
            error_log("API UPDATE - SQL: $sql");
            error_log("API UPDATE - Values: " . print_r(array_merge(array_values($sanitizedData), [$id]), true));
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute(array_merge(array_values($sanitizedData), [$id]));
            
            error_log("API UPDATE - Execute result: " . ($result ? 'true' : 'false'));
            error_log("API UPDATE - Rows affected: " . $stmt->rowCount());
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'update_actualite', "Actualité modifiée: ID {$id}");
            
            return [
                'success' => true,
                'message' => 'Actualité mise à jour avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la mise à jour de l\'actualité : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer une actualité
     */
    public function delete($id) {
        // Vérification simplifiée
        if (!isAdminLoggedIn()) {
            return [
                'success' => false,
                'error' => 'Non autorisé'
            ];
        }
        
        // Vérification CSRF simplifiée
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($token)) {
            return [
                'success' => false,
                'error' => 'Token CSRF invalide'
            ];
        }
        
        try {
            // Récupérer le titre pour le log
            $stmt = $this->pdo->prepare("SELECT titre FROM actualites WHERE id = ?");
            $stmt->execute([$id]);
            $actualite = $stmt->fetch();
            
            if (!$actualite) {
                return [
                    'success' => false,
                    'error' => 'Actualité non trouvée'
                ];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM actualites WHERE id = ?");
            $stmt->execute([$id]);
            
            // Logger l'activité
            logAdminActivity($_SESSION['admin_id'], 'delete_actualite', "Actualité supprimée: {$actualite['titre']}");
            
            return [
                'success' => true,
                'message' => 'Actualité supprimée avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors de la suppression de l\'actualité : ' . $e->getMessage()
            ];
        }
    }
}
?>


