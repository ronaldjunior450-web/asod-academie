<?php
/**
 * Script pour traiter les réponses par email aux messages de contact
 * Ce script peut être appelé par un webhook ou un cron job pour traiter les emails entrants
 */

require_once 'config.php';

// Fonction pour traiter une réponse par email
function processEmailReply($from_email, $subject, $message_content, $reply_to_message_id = null) {
    try {
        $pdo = getDBConnection();
        
        // Si on a un ID de message original, on met à jour ce message
        if ($reply_to_message_id) {
            $stmt = $pdo->prepare("
                UPDATE contacts 
                SET reponse = ?, repondu = 1, statut = 'repondu', 
                    date_reponse = NOW(), repondu_par = 'Email'
                WHERE id = ?
            ");
            $stmt->execute([$message_content, $reply_to_message_id]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Réponse sauvegardée'];
            }
        }
        
        // Sinon, chercher le message original par email et sujet
        $stmt = $pdo->prepare("
            SELECT id FROM contacts 
            WHERE email = ? AND sujet LIKE ? AND repondu = 0
            ORDER BY date_contact DESC 
            LIMIT 1
        ");
        $stmt->execute([$from_email, '%' . $subject . '%']);
        $original_message = $stmt->fetch();
        
        if ($original_message) {
            $stmt = $pdo->prepare("
                UPDATE contacts 
                SET reponse = ?, repondu = 1, statut = 'repondu', 
                    date_reponse = NOW(), repondu_par = 'Email'
                WHERE id = ?
            ");
            $stmt->execute([$message_content, $original_message['id']]);
            
            return ['success' => true, 'message' => 'Réponse sauvegardée'];
        }
        
        return ['success' => false, 'message' => 'Message original non trouvé'];
        
    } catch (Exception $e) {
        error_log("Erreur traitement réponse email: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
    }
}

// Fonction pour créer un webhook pour recevoir les emails
function setupEmailWebhook() {
    // Cette fonction peut être utilisée pour configurer un webhook
    // avec un service comme SendGrid, Mailgun, ou un serveur email local
    return [
        'webhook_url' => SITE_URL . '/php/email_webhook.php',
        'instructions' => 'Configurez votre serveur email pour envoyer les réponses à cette URL'
    ];
}

// Si appelé directement, traiter les paramètres POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_email = $_POST['from_email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message_content = $_POST['message'] ?? '';
    $reply_to_message_id = $_POST['reply_to_message_id'] ?? null;
    
    $result = processEmailReply($from_email, $subject, $message_content, $reply_to_message_id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// Si appelé en GET, afficher les instructions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $webhook_info = setupEmailWebhook();
    
    echo "<h2>Configuration du système de réponses par email</h2>";
    echo "<p><strong>URL du webhook :</strong> " . $webhook_info['webhook_url'] . "</p>";
    echo "<p><strong>Instructions :</strong> " . $webhook_info['instructions'] . "</p>";
    
    echo "<h3>Paramètres POST attendus :</h3>";
    echo "<ul>";
    echo "<li><strong>from_email :</strong> Email de l'expéditeur</li>";
    echo "<li><strong>subject :</strong> Sujet de l'email</li>";
    echo "<li><strong>message :</strong> Contenu du message</li>";
    echo "<li><strong>reply_to_message_id :</strong> (optionnel) ID du message original</li>";
    echo "</ul>";
}
?>


