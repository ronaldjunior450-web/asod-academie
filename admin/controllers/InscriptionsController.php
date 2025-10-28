<?php
// Contrôleur Inscriptions - Utilise l'API existante
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/api/BaseAPI.php';
require_once dirname(__DIR__) . '/api/InscriptionsAPI.php';

class InscriptionsController {
    private $api;
    
    public function __construct() {
        $this->api = new InscriptionsAPI();
    }
    
    /**
     * Affiche la liste des inscriptions
     */
    public function liste() {
        try {
            // Définir l'action pour afficher la liste
            $_GET['action'] = 'list';
            
            // Inclure directement la section existante qui gère tout
            include dirname(__DIR__) . '/sections/inscriptions.php';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Valide une inscription
     */
    public function valider($id) {
        try {
            // Marquer comme validé
            $result = $this->api->update($id, ['statut' => 'valide']);
            
            if ($result['success']) {
                echo '<script>window.location.href = "index.php?section=inscriptions&success=validated";</script>';
                exit;
            } else {
                echo '<script>window.location.href = "index.php?section=inscriptions&error=validation_failed";</script>';
                exit;
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Rejette une inscription
     */
    public function rejeter($id) {
        try {
            $result = $this->api->update($id, ['statut' => 'rejete']);
            
            if ($result['success']) {
                echo '<script>window.location.href = "index.php?section=inscriptions&success=rejected";</script>';
                exit;
            } else {
                echo '<script>window.location.href = "index.php?section=inscriptions&error=rejection_failed";</script>';
                exit;
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Supprime une inscription
     */
    public function supprimer($id) {
        try {
            $result = $this->api->delete($id);
            
            if ($result['success']) {
                echo '<script>window.location.href = "index.php?section=inscriptions&success=deleted";</script>';
                exit;
            } else {
                echo '<script>window.location.href = "index.php?section=inscriptions&error=delete_failed";</script>';
                exit;
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
?>