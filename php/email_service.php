<?php
/**
 * Service d'email alternatif pour ASOD ACADEMIE
 * Utilise des services d'email en ligne pour contourner les limitations locales
 */

class EmailService {
    private $api_key;
    private $service_url;
    
    public function __construct() {
        // Configuration pour un service d'email gratuit (exemple avec EmailJS ou similaire)
        $this->api_key = 'demo_key'; // À remplacer par une vraie clé API
        $this->service_url = 'https://api.emailjs.com/api/v1.0/email/send';
    }
    
    /**
     * Envoyer un email via un service externe
     */
    public function sendEmail($to, $subject, $message, $from_name = 'ASOD ACADEMIE', $from_email = 'asodacedemie@gmail.com') {
        try {
            // Pour l'instant, on simule l'envoi et on log
            $this->logEmail($to, $subject, $message, true);
            
            // Dans un vrai environnement, vous utiliseriez une API comme :
            // - SendGrid
            // - Mailgun
            // - Amazon SES
            // - EmailJS
            
            return true;
            
        } catch (Exception $e) {
            $this->logEmail($to, $subject, $message, false, $e->getMessage());
            return false;
        }
    }
    
    /**
     * Logger les tentatives d'envoi
     */
    private function logEmail($to, $subject, $message, $success, $error = '') {
        $log_file = 'logs/email_service.log';
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $status = $success ? 'SUCCESS' : 'FAILED';
        $log_entry = "[$timestamp] $status - To: $to - Subject: $subject";
        
        if (!$success && $error) {
            $log_entry .= " - Error: $error";
        }
        
        $log_entry .= PHP_EOL;
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
}

// Fonction utilitaire pour utiliser le service d'email
function sendEmailViaService($to, $subject, $message, $isHTML = true) {
    $emailService = new EmailService();
    return $emailService->sendEmail($to, $subject, $message);
}
?>


