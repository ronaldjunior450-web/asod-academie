<?php
/**
 * Configuration de la base de données ASOD ACADEMIE - INFINITYFREE
 * Ce fichier sera utilisé en production sur InfinityFree
 */

// Configuration de la base de données - INFINITYFREE
define('DB_HOST', 'sql301.infinityfree.com');
define('DB_NAME', 'if0_39987344_asod_academie');
define('DB_USER', 'if0_39987344');
define('DB_PASS', 'lnGWxSJQ7dwQE3S'); // À remplacer par votre vrai mot de passe InfinityFree

// Configuration de l'administration
define('ADMIN_SESSION_NAME', 'asod_admin_session');
define('ADMIN_SESSION_TIMEOUT', 3600); // 1 heure
define('ADMIN_LOGIN_ATTEMPTS_LIMIT', 5);
define('ADMIN_LOGIN_ATTEMPTS_TIMEOUT', 900); // 15 minutes

// Configuration des emails - INFINITYFREE
define('SMTP_HOST', 'smtp.gmail.com'); // Serveur SMTP Gmail
define('SMTP_PORT', 587); // Port sécurisé Gmail
define('SMTP_USERNAME', 'asodacademie@gmail.com'); // Votre email Gmail
define('SMTP_PASSWORD', 'VOTRE_MOT_DE_PASSE_APP_GMAIL'); // Mot de passe d'application Gmail
define('USE_SMTP', true); // Activer SMTP en production
define('SMTP_FROM_EMAIL', 'noreply@asodfc.com'); // Email d'expédition
define('SMTP_FROM_NAME', 'ASOD ACADEMIE');

// Configuration des uploads
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', 'uploads/');
define('UPLOAD_IMAGES_PATH', 'uploads/images/');
define('UPLOAD_DOCUMENTS_PATH', 'uploads/documents/');

// Configuration de l'URL de base pour InfinityFree
define('BASE_URL', 'https://votre-domaine.infinityfreeapp.com'); // À remplacer par votre vraie URL InfinityFree

/**
 * Connexion à la base de données
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
    }
    
    return $pdo;
}

/**
 * Configuration de l'environnement
 */
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);

// Configuration des erreurs pour la production
if (!DEBUG_MODE) {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', 'logs/error.log');
}

// Configuration de la session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); // HTTPS seulement

// Configuration de la timezone
date_default_timezone_set('Africa/Douala');

// Configuration de la mémoire
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 30);

// Headers de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Configuration CORS si nécessaire
header('Access-Control-Allow-Origin: ' . BASE_URL);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gestion des requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>
