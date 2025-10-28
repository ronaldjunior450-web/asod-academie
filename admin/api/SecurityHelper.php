<?php
require_once dirname(__DIR__, 2) . '/php/config.php';

/**
 * Classe d'aide pour la sécurité des APIs
 */
class SecurityHelper {
    
    /**
     * Vérifier les permissions selon le rôle
     */
    public static function checkPermission($requiredRole = 'admin') {
        if (!isAdminLoggedIn()) {
            self::sendUnauthorized();
        }
        
        $userRole = $_SESSION['admin_role'] ?? '';
        $roleHierarchy = [
            'moderateur' => 1,
            'admin' => 2,
            'super_admin' => 3
        ];
        
        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
        
        if ($userLevel < $requiredLevel) {
            self::sendForbidden();
        }
    }
    
    /**
     * Vérifier la limite de taux (rate limiting)
     * TEMPORAIREMENT DÉSACTIVÉ pour éviter les erreurs de permissions
     */
    public static function checkRateLimit($action, $limit = 100, $window = 3600) {
        // Rate limiting temporairement désactivé
        // TODO: Réactiver une fois les permissions résolues
        return true;
        
        /*
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $key = "rate_limit_{$action}_{$ip}";
        
        // Utiliser le dossier logs/rate_limits
        $file = dirname(__DIR__, 2) . "/logs/rate_limits/{$key}.json";
        
        // Créer le dossier s'il n'existe pas
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                // Si la création échoue, utiliser le dossier temporaire
                $file = sys_get_temp_dir() . "/{$key}.json";
                $dir = dirname($file);
                // S'assurer que le dossier temporaire existe
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
            }
        }
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if ($data['count'] >= $limit && (time() - $data['start']) < $window) {
                self::sendTooManyRequests();
            }
            if ((time() - $data['start']) >= $window) {
                $data = ['count' => 1, 'start' => time()];
            } else {
                $data['count']++;
            }
        } else {
            $data = ['count' => 1, 'start' => time()];
        }
        
        // Écrire le fichier avec gestion d'erreur robuste
        try {
            if (!file_put_contents($file, json_encode($data))) {
                // Si l'écriture échoue, ignorer silencieusement (ne pas bloquer l'application)
                error_log("Impossible d'écrire le fichier de rate limiting: $file");
            }
        } catch (Exception $e) {
            // Ignorer les erreurs de rate limiting pour ne pas bloquer l'application
            error_log("Erreur rate limiting: " . $e->getMessage());
        }
        */
    }
    
    /**
     * Valider les types de fichiers uploadés
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'Fichier invalide'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['error' => 'Fichier trop volumineux (max: ' . ($maxSize / 1024 / 1024) . 'MB)'];
        }
        
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($fileType, $allowedTypes)) {
            return ['error' => 'Type de fichier non autorisé'];
        }
        
        // Vérifier le type MIME réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (isset($allowedMimes[$fileType]) && $mimeType !== $allowedMimes[$fileType]) {
            return ['error' => 'Type MIME invalide'];
        }
        
        return ['success' => true];
    }
    
    /**
     * Nettoyer et valider les données d'entrée
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        // Supprimer les balises HTML dangereuses
        $input = strip_tags($input, '<p><br><strong><em><ul><ol><li><a>');
        
        // Échapper les caractères spéciaux
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // Supprimer les caractères de contrôle
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        return trim($input);
    }
    
    /**
     * Valider une URL
     */
    public static function validateUrl($url) {
        if (empty($url)) {
            return true; // URL optionnelle
        }
        
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Valider une date
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        if (empty($date)) {
            return true; // Date optionnelle
        }
        
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Valider un email
     */
    public static function validateEmail($email) {
        if (empty($email)) {
            return true; // Email optionnel
        }
        
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Générer un nom de fichier sécurisé
     */
    public static function generateSecureFilename($originalName) {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Nettoyer le nom de base
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '', $basename);
        $basename = substr($basename, 0, 50); // Limiter la longueur
        
        // Générer un nom unique
        $timestamp = time();
        $random = bin2hex(random_bytes(4));
        
        return "{$basename}_{$timestamp}_{$random}.{$extension}";
    }
    
    /**
     * Vérifier la taille d'une image et la redimensionner si nécessaire
     */
    public static function processImage($file, $maxWidth = 1920, $maxHeight = 1080, $quality = 85) {
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            return ['error' => 'Fichier image invalide'];
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Si l'image est déjà dans les bonnes dimensions, pas besoin de redimensionner
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return ['success' => true, 'processed' => false];
        }
        
        // Calculer les nouvelles dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);
        
        // Créer l'image source
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $source = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($file['tmp_name']);
                break;
            default:
                return ['error' => 'Type d\'image non supporté'];
        }
        
        if (!$source) {
            return ['error' => 'Impossible de traiter l\'image'];
        }
        
        // Créer l'image redimensionnée
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Préserver la transparence pour PNG et GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Redimensionner
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Sauvegarder l'image redimensionnée
        $tempFile = tempnam(sys_get_temp_dir(), 'resized_');
        
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($resized, $tempFile, $quality);
                break;
            case 'image/png':
                imagepng($resized, $tempFile, 9);
                break;
            case 'image/gif':
                imagegif($resized, $tempFile);
                break;
        }
        
        // Nettoyer la mémoire
        imagedestroy($source);
        imagedestroy($resized);
        
        return [
            'success' => true,
            'processed' => true,
            'temp_file' => $tempFile,
            'original_size' => ['width' => $width, 'height' => $height],
            'new_size' => ['width' => $newWidth, 'height' => $newHeight]
        ];
    }
    
    /**
     * Réponses d'erreur sécurisées
     */
    public static function sendUnauthorized() {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Non autorisé'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    public static function sendForbidden() {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Accès interdit'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    public static function sendTooManyRequests() {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Trop de requêtes'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    public static function sendBadRequest($message = 'Requête invalide') {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>
