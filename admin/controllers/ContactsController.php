<?php
// Contrôleur Contacts - Utilise l'API existante
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/api/BaseAPI.php';
require_once dirname(__DIR__) . '/api/ContactsAPI.php';

class ContactsController {
    private $api;
    
    public function __construct() {
        $this->api = new ContactsAPI();
    }
    
    /**
     * Affiche la liste des contacts
     */
    public function liste() {
        try {
            // Définir l'action pour afficher la liste
            $_GET['action'] = 'list';
            
            // Inclure directement la section existante qui gère tout
            include dirname(__DIR__) . '/sections/contacts.php';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Marque un message comme lu
     */
    public function marquerLu($id) {
        try {
            $result = $this->api->update($id, ['lu' => 1]);
            
            if ($result['success']) {
                echo '<script>window.location.href = "index.php?section=contacts&success=marked_read";</script>';
                exit;
            } else {
                echo '<script>window.location.href = "index.php?section=contacts&error=mark_failed";</script>';
                exit;
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Supprime un contact
     */
    public function supprimer($id) {
        try {
            $result = $this->api->delete($id);
            
            if ($result['success']) {
                echo '<script>window.location.href = "index.php?section=contacts&success=deleted";</script>';
                exit;
            } else {
                echo '<script>window.location.href = "index.php?section=contacts&error=delete_failed";</script>';
                exit;
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
?>