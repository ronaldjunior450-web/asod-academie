<?php
// Contrôleur Bureau - Utilise l'API existante
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/api/BaseAPI.php';
require_once dirname(__DIR__) . '/api/BureauAPI_New.php';

class BureauController {
    private $api;
    
    public function __construct() {
        $this->api = new BureauAPI_New();
    }
    
    /**
     * Affiche la liste du bureau
     */
    public function liste() {
        try {
            // Définir l'action pour afficher la liste par défaut
            $_GET['action'] = 'list';
            
            // Inclure directement la section existante qui gère tout
            include dirname(__DIR__) . '/sections/bureau.php';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Affiche le formulaire d'ajout ou traite la soumission
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Utiliser l'API avec PhotoManager
            $result = $this->api->createWithPhoto($_POST, $_FILES);
            
            if ($result['success']) {
                echo '<script>window.location.href = "index.php?section=bureau&success=ajout";</script>';
            } else {
                echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($result['error']) . '</div>';
            }
        } else {
            // Afficher le formulaire d'ajout
            $_GET['action'] = 'ajouter';
            include dirname(__DIR__) . '/sections/bureau.php';
        }
    }
    
    /**
     * Affiche le formulaire de modification ou traite la mise à jour
     */
    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Utiliser l'API avec PhotoManager
            $result = $this->api->updateWithPhoto($id, $_POST, $_FILES);
            
            if ($result['success']) {
                echo '<script>window.location.href = "index.php?section=bureau&success=modification";</script>';
            } else {
                echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($result['error']) . '</div>';
            }
        } else {
            // Afficher le formulaire de modification
            $_GET['action'] = 'modifier';
            $_GET['id'] = $id;
            include dirname(__DIR__) . '/sections/bureau.php';
        }
    }
    
    /**
     * Supprime un membre du bureau
     */
    public function supprimer($id) {
        try {
            $pdo = getDBConnection();
            
            // Supprimer la photo si elle existe
            $stmt = $pdo->prepare("SELECT photo FROM bureau WHERE id = ?");
            $stmt->execute([$id]);
            $photo = $stmt->fetchColumn();
            if ($photo && file_exists('../../' . $photo)) {
                unlink('../../' . $photo);
            }
            
            // Supprimer le membre du bureau
            $stmt = $pdo->prepare("DELETE FROM bureau WHERE id = ?");
            $stmt->execute([$id]);
            
            // Redirection immédiate vers la liste avec message de succès
            header('Location: index.php?section=bureau&success=deleted');
            exit;
            
        } catch (Exception $e) {
            // En cas d'erreur, rediriger avec message d'erreur
            header('Location: index.php?section=bureau&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
    
    /**
     * Affiche les détails d'un membre du bureau
     */
    public function voir($id) {
        // Afficher le formulaire de vue détaillée
        $_GET['action'] = 'voir';
        $_GET['id'] = $id;
        include dirname(__DIR__) . '/sections/bureau.php';
    }
}
?>