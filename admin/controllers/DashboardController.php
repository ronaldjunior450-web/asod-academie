<?php
// Contrôleur Dashboard - Utilise les APIs existantes
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/api/BaseAPI.php';

class DashboardController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Affiche le dashboard principal
     */
    public function show() {
        try {
            // Utiliser les APIs existantes pour récupérer les données
            $data = $this->getDashboardData();
            
            // Inclure la vue existante
            include dirname(__DIR__) . '/sections/dashboard.php';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Récupère toutes les données du dashboard via les APIs
     */
    private function getDashboardData() {
        // Utiliser directement les requêtes SQL pour les statistiques
        return $this->getDashboardDataFallback();
    }
    
    /**
     * Récupère les données du dashboard avec requêtes directes
     */
    private function getDashboardDataFallback() {
        $data = [];
        
        // Compter les actualités
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM actualites WHERE statut = 'publie'");
            $data['actualites_count'] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $data['actualites_count'] = 0;
        }
        
        // Compter les membres
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM membres WHERE statut = 'actif'");
            $data['membres_count'] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $data['membres_count'] = 0;
        }
        
        // Compter les équipes
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM equipes WHERE actif = 1");
            $data['equipes_count'] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $data['equipes_count'] = 0;
        }
        
        // Compter les inscriptions en attente
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM inscriptions WHERE statut = 'en_attente'");
            $data['inscriptions_pending'] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $data['inscriptions_pending'] = 0;
        }
        
        // Compter les messages non lus
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM contacts WHERE lu = 0");
            $data['contacts_unread'] = $stmt->fetch()['count'];
        } catch (Exception $e) {
            $data['contacts_unread'] = 0;
        }
        
        // Récupérer les dernières actualités
        try {
            $stmt = $this->pdo->query("SELECT * FROM actualites ORDER BY date_creation DESC LIMIT 5");
            $data['latest_actualites'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $data['latest_actualites'] = [];
        }
        
        // Récupérer les dernières inscriptions
        try {
            $stmt = $this->pdo->query("SELECT * FROM inscriptions ORDER BY date_inscription DESC LIMIT 5");
            $data['latest_inscriptions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $data['latest_inscriptions'] = [];
        }
        
        return $data;
    }
}
?>