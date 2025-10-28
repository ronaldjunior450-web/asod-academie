<?php
/**
 * Configuration de la base de données ASOD ACADEMIE - PRODUCTION
 */

// Configuration de la base de données - LOCAL
define('DB_HOST', 'localhost');
define('DB_NAME', 'asod_fc');
define('DB_USER', 'root');
define('DB_PASS', ''); // Pas de mot de passe en local WAMP
define('DB_CHARSET', 'utf8mb4');

// Configuration de l'administration
define('ADMIN_SESSION_NAME', 'asod_admin_session');
define('ADMIN_SESSION_TIMEOUT', 3600); // 1 heure
define('ADMIN_LOGIN_ATTEMPTS_LIMIT', 5);
define('ADMIN_LOGIN_ATTEMPTS_TIMEOUT', 900); // 15 minutes

// Configuration des emails - PRODUCTION
define('SMTP_HOST', 'smtp.gmail.com'); // Serveur SMTP Gmail
define('SMTP_PORT', 587); // Port sécurisé Gmail
define('SMTP_USERNAME', 'asod.academie@gmail.com'); // Votre email Gmail
define('SMTP_PASSWORD', 'votre-mot-de-passe-app'); // Mot de passe d'application Gmail
define('USE_SMTP', false);
define('SMTP_FROM_EMAIL', 'noreply@asodfc.com'); // Email d'expédition
define('SMTP_FROM_NAME', 'ASOD ACADEMIE');

// Configuration des uploads
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', 'uploads/');
define('UPLOAD_IMAGES_PATH', 'uploads/images/');
define('UPLOAD_DOCUMENTS_PATH', 'uploads/documents/');

/**
 * Connexion à la base de données
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ]);
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        }
    }
    
    return $pdo;
}

/**
 * Fonction pour nettoyer les entrées utilisateur
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction pour préparer les données pour les formulaires
 */
function prepareForForm($input) {
    return htmlspecialchars(html_entity_decode($input, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction pour préparer les données pour JSON
 */
function prepareForJson($data) {
    if (is_array($data)) {
        return array_map('prepareForJson', $data);
    }
    return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction pour envoyer des emails
 */
function sendEmail($to, $subject, $message, $headers = []) {
    if (USE_SMTP) {
        return sendSMTPEmail($to, $subject, $message, $headers);
    } else {
        // En mode local, on logge l'email au lieu de l'envoyer
        if (DEBUG_MODE) {
            error_log("EMAIL SIMULÉ - À: $to, Sujet: $subject");
            return true; // Simuler un envoi réussi
        } else {
            return mail($to, $subject, $message, implode("\r\n", $headers));
        }
    }
}

/**
 * Fonction pour envoyer des emails via SMTP
 */
function sendSMTPEmail($to, $subject, $message, $headers = []) {
    try {
        // Configuration SMTP simple
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>';
        
        return mail($to, $subject, $message, implode("\r\n", $headers));
    } catch (Exception $e) {
        error_log("Erreur envoi email: " . $e->getMessage());
        return false;
    }
}

/**
 * Fonction pour logger les activités admin
 */
function logAdminActivity($admin_id, $action, $details = '') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (admin_id, action, details, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $admin_id,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        error_log("Erreur log admin: " . $e->getMessage());
    }
}

/**
 * Fonction pour vérifier la session admin
 */
function checkAdminSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(ADMIN_SESSION_NAME);
        session_start();
    }
    
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
        return false;
    }
    
    // Vérifier le timeout
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > ADMIN_SESSION_TIMEOUT)) {
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Fonction pour vérifier si l'admin est connecté
 */
function isAdminLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(ADMIN_SESSION_NAME);
        session_start();
    }
    
    return isset($_SESSION['admin_id']) && (isset($_SESSION['admin_username']) || isset($_SESSION['admin_nom']));
}

/**
 * Fonction pour exiger la connexion admin (alias)
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirectToLogin();
    }
}

/**
 * Fonction pour rediriger vers la page de connexion
 */
function redirectToLogin() {
    header('Location: login.php');
    exit;
}

/**
 * Fonction pour afficher les erreurs en mode développement
 */
function displayError($message, $details = '') {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "<div class='alert alert-danger'>";
        echo "<strong>Erreur:</strong> " . htmlspecialchars($message);
        if ($details) {
            echo "<br><small>" . htmlspecialchars($details) . "</small>";
        }
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>Une erreur est survenue. Veuillez réessayer.</div>";
    }
}

// Mode debug (activer en local)
define('DEBUG_MODE', true);

// Configuration des timezones
date_default_timezone_set('Africa/Douala');

// Configuration des erreurs
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>