<?php
/**
 * Contrôleur pour la gestion des messages de contact
 */
require_once dirname(__DIR__, 2) . '/php/config.php';

class MessagesController {
    
    /**
     * Afficher la liste des messages
     */
    public function liste() {
        $_GET['action'] = 'list';
        include dirname(__DIR__) . '/sections/messages.php';
    }
    
    /**
     * Afficher un message spécifique
     */
    public function voir($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $pdo = getDBConnection();
                
                // Marquer comme lu
                $stmt = $pdo->prepare("UPDATE contacts SET lu = 1, date_lecture = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                $_GET['action'] = 'voir';
                $_GET['id'] = $id;
                include dirname(__DIR__) . '/sections/messages.php';
                
            } catch (Exception $e) {
                header("Location: index.php?section=messages&error=" . urlencode($e->getMessage()));
                exit;
            }
        }
    }
    
    /**
     * Afficher le formulaire de réponse
     */
    public function repondre($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $_GET['action'] = 'repondre';
            $_GET['id'] = $id;
            include dirname(__DIR__) . '/sections/messages.php';
        }
    }
    
    /**
     * Traiter l'envoi de réponse
     */
    public function envoyerReponse($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = getDBConnection();
                
                // Valider la réponse
                $reponse = trim($_POST['reponse'] ?? '');
                if (empty($reponse)) {
                    header("Location: index.php?section=messages&error=reponse_vide");
                    exit;
                }
                
                // Récupérer l'ID de l'admin connecté (simulation)
                $admin_id = $_SESSION['admin_id'] ?? 1;
                
                // Mettre à jour le message avec la réponse
                $stmt = $pdo->prepare("
                    UPDATE contacts 
                    SET reponse = ?, 
                        repondu = 1, 
                        repondu_par = ?, 
                        date_reponse = NOW(),
                        statut = 'repondu'
                    WHERE id = ?
                ");
                $stmt->execute([$reponse, $admin_id, $id]);
                
                // Envoyer l'email de réponse (optionnel)
                $this->envoyerEmailReponse($id, $reponse);
                
                // Redirection vers l'index avec message de succès (comme supprimer)
                header("Location: index.php?section=messages&success=replied");
                exit;
                
            } catch (Exception $e) {
                // Redirection vers l'index avec message d'erreur (comme supprimer)
                header("Location: index.php?section=messages&error=reponse_failed");
                exit;
            }
        }
    }
    
    /**
     * Supprimer un message
     */
    public function supprimer($id) {
        // Debug: Log de l'appel
        error_log("MessagesController::supprimer appelé avec ID: " . $id);
        
        try {
            $pdo = getDBConnection();
            
            // Vérifier que le message existe
            $stmt = $pdo->prepare("SELECT id FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                // Redirection vers l'index avec message d'avertissement
                header("Location: index.php?section=messages&error=message_not_found");
                exit;
            }
            
            // Supprimer le message
            $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                // Redirection vers l'index avec la section Messages (comme Entraîneurs)
                header("Location: index.php?section=messages&success=deleted");
                exit;
            } else {
                // Redirection vers l'index avec message d'erreur
                header("Location: index.php?section=messages&error=delete_failed");
                exit;
            }
            exit;
            
        } catch (Exception $e) {
            error_log("Erreur suppression message: " . $e->getMessage());
            // Redirection vers l'index avec message d'erreur
            header("Location: index.php?section=messages&error=delete_failed");
            exit;
        }
    }
    
    /**
     * Marquer un message comme lu/non lu
     */
    public function marquerLu($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $pdo = getDBConnection();
                
                $lu = $_GET['lu'] ?? 1;
                $statut = $lu ? 'lu' : 'non_lu';
                
                $stmt = $pdo->prepare("
                    UPDATE contacts 
                    SET lu = ?, 
                        date_lecture = NOW(),
                        statut = ?
                    WHERE id = ?
                ");
                $stmt->execute([$lu, $statut, $id]);
                
                header("Location: index.php?section=messages&success=marked_read");
                exit;
                
            } catch (Exception $e) {
                header("Location: index.php?section=messages&error=" . urlencode($e->getMessage()));
                exit;
            }
        }
    }
    
    /**
     * Envoyer un email de réponse (simulation)
     */
    private function envoyerEmailReponse($message_id, $reponse) {
        try {
            $pdo = getDBConnection();
            
            // Récupérer les informations du message original
            $stmt = $pdo->prepare("SELECT nom, prenom, email, sujet FROM contacts WHERE id = ?");
            $stmt->execute([$message_id]);
            $message = $stmt->fetch();
            
            if ($message) {
                // Simulation d'envoi d'email
                $to = $message['email'];
                $subject = "Re: " . $message['sujet'];
                $body = "Bonjour " . $message['prenom'] . ",\n\n";
                $body .= "Merci pour votre message. Voici notre réponse :\n\n";
                $body .= $reponse . "\n\n";
                $body .= "Cordialement,\n";
                $body .= "L'équipe ASOD ACADEMIE";
                
                // En production, utiliser une vraie fonction d'envoi d'email
                // mail($to, $subject, $body);
                
                // Log de l'envoi (pour debug)
                error_log("Email de réponse envoyé à: {$to} pour le message ID: {$message_id}");
            }
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email de réponse: " . $e->getMessage());
        }
    }
    
    
    /**
     * Obtenir les statistiques des messages (pour l'API)
     */
    public function getStats() {
        header('Content-Type: application/json');
        
        try {
            $pdo = getDBConnection();
            
            $stats = [];
            
            // Total des messages
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM contacts");
            $stats['total'] = $stmt->fetchColumn();
            
            // Messages non lus
            $stmt = $pdo->query("SELECT COUNT(*) as non_lus FROM contacts WHERE lu = 0");
            $stats['non_lus'] = $stmt->fetchColumn();
            
            // Messages non répondus
            $stmt = $pdo->query("SELECT COUNT(*) as non_repondus FROM contacts WHERE repondu = 0");
            $stats['non_repondus'] = $stmt->fetchColumn();
            
            // Messages d'aujourd'hui
            $stmt = $pdo->query("SELECT COUNT(*) as aujourd_hui FROM contacts WHERE DATE(date_contact) = CURDATE()");
            $stats['aujourd_hui'] = $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    
}
?>
