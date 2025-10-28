<?php
/**
 * Configuration de sécurité pour les APIs
 */

// Configuration des permissions par rôle
define('ROLE_PERMISSIONS', [
    'moderateur' => [
        'actualites' => ['read', 'update'],
        'contacts' => ['read', 'update'],
        'inscriptions' => ['read']
    ],
    'admin' => [
        'actualites' => ['create', 'read', 'update', 'delete'],
        'membres' => ['create', 'read', 'update', 'delete'],
        'equipes' => ['create', 'read', 'update', 'delete'],
        'bureau' => ['create', 'read', 'update', 'delete'],
        'inscriptions' => ['create', 'read', 'update', 'delete', 'validate', 'reject'],
        'contacts' => ['create', 'read', 'update', 'delete', 'reply'],
        'galerie' => ['create', 'read', 'update', 'delete'],
        'evenements' => ['create', 'read', 'update', 'delete'],
        'temoignages' => ['create', 'read', 'update', 'delete'],
        'formations' => ['create', 'read', 'update', 'delete'],
        'paiements' => ['read', 'update'],
        'contact_info' => ['read', 'update']
    ],
    'super_admin' => [
        '*' => ['*'] // Accès complet à tout
    ]
]);

// Configuration des limites de taux
define('RATE_LIMITS', [
    'api_access' => ['limit' => 1000, 'window' => 3600], // 1000 requêtes par heure
    'file_upload' => ['limit' => 50, 'window' => 3600],  // 50 uploads par heure
    'login_attempts' => ['limit' => 5, 'window' => 900], // 5 tentatives par 15 minutes
    'password_reset' => ['limit' => 3, 'window' => 3600] // 3 reset par heure
]);

// Types de fichiers autorisés
define('ALLOWED_FILE_TYPES', [
    'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'documents' => ['pdf', 'doc', 'docx', 'txt'],
    'archives' => ['zip', 'rar']
]);

// Tailles maximales de fichiers (en octets)
define('MAX_FILE_SIZES', [
    'images' => 5 * 1024 * 1024,    // 5MB
    'documents' => 10 * 1024 * 1024, // 10MB
    'archives' => 50 * 1024 * 1024   // 50MB
]);

// Configuration des images
define('IMAGE_SETTINGS', [
    'max_width' => 1920,
    'max_height' => 1080,
    'quality' => 85,
    'thumbnails' => [
        'small' => ['width' => 150, 'height' => 150],
        'medium' => ['width' => 300, 'height' => 300],
        'large' => ['width' => 800, 'height' => 600]
    ]
]);

// Configuration des sessions
define('SESSION_SETTINGS', [
    'timeout' => 3600,        // 1 heure
    'regenerate_id' => 300,   // Régénérer l'ID toutes les 5 minutes
    'secure' => true,         // HTTPS uniquement en production
    'httponly' => true,       // Cookie HTTPOnly
    'samesite' => 'Strict'    // Protection CSRF
]);

// Configuration des logs
define('LOG_SETTINGS', [
    'enabled' => true,
    'level' => 'INFO',        // DEBUG, INFO, WARNING, ERROR
    'max_size' => 10 * 1024 * 1024, // 10MB
    'max_files' => 5,
    'retention_days' => 30
]);

// Configuration des sauvegardes
define('BACKUP_SETTINGS', [
    'enabled' => true,
    'frequency' => 'daily',   // daily, weekly, monthly
    'retention' => 30,        // jours
    'compress' => true,
    'encrypt' => false
]);

// Configuration des notifications
define('NOTIFICATION_SETTINGS', [
    'email' => [
        'enabled' => true,
        'smtp_host' => 'localhost',
        'smtp_port' => 587,
        'smtp_secure' => 'tls',
        'from_email' => 'noreply@asodfc.com',
        'from_name' => 'ASOD ACADEMIE Administration'
    ],
    'sms' => [
        'enabled' => false,
        'provider' => 'twilio',
        'api_key' => '',
        'api_secret' => ''
    ]
]);

// Configuration des alertes de sécurité
define('SECURITY_ALERTS', [
    'failed_logins' => [
        'enabled' => true,
        'threshold' => 5,
        'timeframe' => 900,   // 15 minutes
        'action' => 'block_ip'
    ],
    'suspicious_activity' => [
        'enabled' => true,
        'patterns' => [
            'sql_injection' => true,
            'xss_attempts' => true,
            'file_upload_abuse' => true
        ]
    ]
]);

// Configuration des audits
define('AUDIT_SETTINGS', [
    'enabled' => true,
    'log_actions' => [
        'login' => true,
        'logout' => true,
        'create' => true,
        'update' => true,
        'delete' => true,
        'export' => true,
        'import' => true
    ],
    'log_data_changes' => true,
    'log_file_access' => true
]);
?>


