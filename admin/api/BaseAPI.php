<?php
require_once dirname(__DIR__, 2) . '/php/config.php';
require_once 'SecurityHelper.php';

/**
 * Classe de base pour toutes les APIs CRUD
 */
class BaseAPI {
    protected $pdo;
    protected $table;
    protected $primaryKey;
    protected $allowedFields;
    protected $requiredFields;
    
    public function __construct($table, $primaryKey = 'id', $allowedFields = [], $requiredFields = []) {
        $this->pdo = getDBConnection();
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->allowedFields = $allowedFields;
        $this->requiredFields = $requiredFields;
    }
    
    /**
     * Vérifier l'authentification admin
     */
    protected function requireAuth($requiredRole = 'admin') {
        if (!isAdminLoggedIn()) {
            $this->sendResponse(['error' => 'Non autorisé'], 401);
        }
        
        // Vérifier les permissions selon le rôle
        SecurityHelper::checkPermission($requiredRole);
        
        // Vérifier la limite de taux
        SecurityHelper::checkRateLimit('api_access', 1000, 3600);
    }
    
    /**
     * Vérifier le token CSRF
     */
    protected function verifyCSRF() {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        if (!verifyCSRFToken($token)) {
            $this->sendResponse(['error' => 'Token CSRF invalide'], 403);
        }
    }
    
    /**
     * Valider les données d'entrée
     */
    protected function validateData($data) {
        $errors = [];
        
        // Vérifier les champs requis
        foreach ($this->requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "Le champ {$field} est requis";
            }
        }
        
        // Vérifier les champs autorisés
        foreach ($data as $field => $value) {
            if (!in_array($field, $this->allowedFields)) {
                $errors[$field] = "Le champ {$field} n'est pas autorisé";
            }
        }
        
        return $errors;
    }
    
    /**
     * Nettoyer les données
     */
    protected function sanitizeData($data) {
        $sanitized = [];
        foreach ($data as $field => $value) {
            if (in_array($field, $this->allowedFields)) {
                $sanitized[$field] = SecurityHelper::sanitizeInput($value);
            }
        }
        return $sanitized;
    }
    
    /**
     * Créer un nouvel enregistrement
     */
    public function create($data) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            $this->sendResponse(['errors' => $errors], 400);
        }
        
        $sanitizedData = $this->sanitizeData($data);
        $sanitizedData['date_creation'] = date('Y-m-d H:i:s');
        
        $fields = array_keys($sanitizedData);
        $placeholders = array_map(function($field) { return ":$field"; }, $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($sanitizedData);
            
            $id = $this->pdo->lastInsertId();
            logAdminActivity($_SESSION['admin_id'], 'CREATE', "Création {$this->table} ID: $id");
            
            $this->sendResponse(['success' => true, 'id' => $id, 'message' => 'Enregistrement créé avec succès']);
        } catch (PDOException $e) {
            error_log("Erreur création {$this->table}: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de la création'], 500);
        }
    }
    
    /**
     * Lire un ou plusieurs enregistrements
     */
    public function read($id = null, $filters = []) {
        $this->requireAuth();
        
        try {
            if ($id) {
                // Lire un enregistrement spécifique
                $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id' => $id]);
                $result = $stmt->fetch();
                
                if (!$result) {
                    $this->sendResponse(['error' => 'Enregistrement non trouvé'], 404);
                }
                
                $this->sendResponse(['success' => true, 'data' => $result]);
            } else {
                // Lire plusieurs enregistrements avec filtres
                $sql = "SELECT * FROM {$this->table}";
                $params = [];
                
                if (!empty($filters)) {
                    $conditions = [];
                    foreach ($filters as $field => $value) {
                        $conditions[] = "$field = :$field";
                        $params[$field] = $value;
                    }
                    $sql .= " WHERE " . implode(' AND ', $conditions);
                }
                
                $sql .= " ORDER BY date_creation DESC";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                $results = $stmt->fetchAll();
                
                $this->sendResponse(['success' => true, 'data' => $results, 'count' => count($results)]);
            }
        } catch (PDOException $e) {
            error_log("Erreur lecture {$this->table}: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de la lecture'], 500);
        }
    }
    
    /**
     * Mettre à jour un enregistrement
     */
    public function update($id, $data) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            $this->sendResponse(['errors' => $errors], 400);
        }
        
        $sanitizedData = $this->sanitizeData($data);
        $sanitizedData['date_modification'] = date('Y-m-d H:i:s');
        
        $fields = array_keys($sanitizedData);
        $setClause = array_map(function($field) { return "$field = :$field"; }, $fields);
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $sanitizedData['id'] = $id;
            $stmt->execute($sanitizedData);
            
            if ($stmt->rowCount() === 0) {
                $this->sendResponse(['error' => 'Enregistrement non trouvé'], 404);
            }
            
            logAdminActivity($_SESSION['admin_id'], 'UPDATE', "Modification {$this->table} ID: $id");
            $this->sendResponse(['success' => true, 'message' => 'Enregistrement mis à jour avec succès']);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour {$this->table}: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de la mise à jour'], 500);
        }
    }
    
    /**
     * Supprimer un enregistrement
     */
    public function delete($id) {
        $this->requireAuth();
        $this->verifyCSRF();
        
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            if ($stmt->rowCount() === 0) {
                $this->sendResponse(['error' => 'Enregistrement non trouvé'], 404);
            }
            
            logAdminActivity($_SESSION['admin_id'], 'DELETE', "Suppression {$this->table} ID: $id");
            $this->sendResponse(['success' => true, 'message' => 'Enregistrement supprimé avec succès']);
        } catch (PDOException $e) {
            error_log("Erreur suppression {$this->table}: " . $e->getMessage());
            $this->sendResponse(['error' => 'Erreur lors de la suppression'], 500);
        }
    }
    
    /**
     * Envoyer une réponse JSON
     */
    protected function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Gérer les requêtes HTTP
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id'] ?? null;
        
        switch ($method) {
            case 'GET':
                $filters = $_GET;
                unset($filters['id']);
                $this->read($id, $filters);
                break;
                
            case 'POST':
                $this->create($_POST);
                break;
                
            case 'PUT':
            case 'PATCH':
                parse_str(file_get_contents('php://input'), $data);
                $this->update($id, $data);
                break;
                
            case 'DELETE':
                $this->delete($id);
                break;
                
            default:
                $this->sendResponse(['error' => 'Méthode non autorisée'], 405);
        }
    }
}
?>
