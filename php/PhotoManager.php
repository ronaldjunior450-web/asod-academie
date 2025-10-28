<?php
/**
 * PhotoManager - Système unifié de gestion des photos
 * Évite les conflits de noms et standardise les uploads
 */
class PhotoManager {
    
    private $pdo;
    private $baseUploadDir;
    
    public function __construct($pdo, $baseUploadDir = 'images/') {
        $this->pdo = $pdo;
        $this->baseUploadDir = rtrim($baseUploadDir, '/') . '/';
    }
    
    /**
     * Upload une photo avec nommage automatique
     * @param array $file $_FILES['photo']
     * @param string $category catégorie (bureau, entraineurs, membres, etc.)
     * @param int $entityId ID de l'entité (optionnel)
     * @return array ['success' => bool, 'filename' => string, 'path' => string, 'error' => string]
     */
    public function uploadPhoto($file, $category, $entityId = null) {
        try {
            // Validation du fichier
            if (!$this->validateFile($file)) {
                return ['success' => false, 'error' => 'Fichier invalide'];
            }
            
            // Créer le dossier de destination
            $uploadDir = $this->baseUploadDir . $category . '/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    return ['success' => false, 'error' => 'Impossible de créer le dossier'];
                }
            }
            
            // Générer un nom unique
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $timestamp = time();
            $random = substr(md5(uniqid(rand(), true)), 0, 8);
            
            // Format: category_timestamp_random.extension
            $filename = $category . '_' . $timestamp . '_' . $random . '.' . $extension;
            $fullPath = $uploadDir . $filename;
            
            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                return [
                    'success' => true,
                    'filename' => 'images/' . $category . '/' . $filename,  // Chemin complet pour la base de données
                    'path' => $category . '/' . $filename,
                    'full_path' => $fullPath
                ];
            } else {
                return ['success' => false, 'error' => 'Échec du déplacement du fichier'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Supprimer une photo
     * @param string $filename nom du fichier
     * @param string $category catégorie
     * @return bool
     */
    public function deletePhoto($filename, $category) {
        if (empty($filename)) return true;
        
        // Gérer les deux formats : chemin complet ou juste le nom
        if (strpos($filename, 'images/' . $category . '/') === 0) {
            // Format nouveau : "images/membres/filename.jpg"
            $actualFilename = basename($filename);
        } else {
            // Format ancien : juste "filename.jpg"
            $actualFilename = $filename;
        }
        
        $filePath = $this->baseUploadDir . $category . '/' . $actualFilename;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return true; // Fichier déjà supprimé
    }
    
    /**
     * Vérifier si une photo existe
     * @param string $filename nom du fichier
     * @param string $category catégorie
     * @return bool
     */
    public function photoExists($filename, $category) {
        if (empty($filename)) return false;
        
        // Gérer les deux formats : chemin complet ou juste le nom
        if (strpos($filename, 'images/' . $category . '/') === 0) {
            // Format nouveau : "images/membres/filename.jpg"
            $actualFilename = basename($filename);
        } else {
            // Format ancien : juste "filename.jpg"
            $actualFilename = $filename;
        }
        
        $filePath = $this->baseUploadDir . $category . '/' . $actualFilename;
        return file_exists($filePath);
    }
    
    /**
     * Obtenir l'URL complète d'une photo
     * @param string $filename nom du fichier
     * @param string $category catégorie
     * @return string
     */
    public function getPhotoUrl($filename, $category) {
        if (empty($filename)) return '';
        
        return $this->baseUploadDir . $category . '/' . $filename;
    }
    
    /**
     * Valider un fichier uploadé
     * @param array $file $_FILES
     * @return bool
     */
    private function validateFile($file) {
        // Vérifier les erreurs d'upload
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Vérifier la taille (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }
        
        // Vérifier l'extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }
        
        // Vérifier le type MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        return in_array($mimeType, $allowedMimes);
    }
    
    /**
     * Nettoyer les photos orphelines (non référencées en BDD)
     * @param string $category catégorie
     * @param string $table nom de la table
     * @param string $photoColumn nom de la colonne photo
     * @return int nombre de fichiers supprimés
     */
    public function cleanupOrphanPhotos($category, $table, $photoColumn = 'photo') {
        $uploadDir = $this->baseUploadDir . $category . '/';
        if (!is_dir($uploadDir)) return 0;
        
        // Récupérer tous les fichiers dans le dossier
        $files = scandir($uploadDir);
        $deletedCount = 0;
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            // Vérifier si le fichier est référencé en BDD
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE {$photoColumn} = ?");
            $stmt->execute([$file]);
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                // Fichier orphelin, le supprimer
                if (unlink($uploadDir . $file)) {
                    $deletedCount++;
                }
            }
        }
        
        return $deletedCount;
    }
}
?>





