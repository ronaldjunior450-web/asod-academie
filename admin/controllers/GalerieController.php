<?php
// Contrôleur Galerie - Fallback vers section existante
require_once dirname(__DIR__) . '/../php/config.php';

class GalerieController {
    
    /**
     * Affiche la galerie
     */
    public function liste() {
        // Inclure directement la section existante
        include dirname(__DIR__) . '/sections/galerie.php';
    }
    
    /**
     * Affiche le formulaire d'ajout
     */
    public function ajouter() {
        include dirname(__DIR__) . '/sections/galerie_ajouter.php';
    }
    
    /**
     * Affiche le formulaire de modification
     */
    public function modifier($id) {
        include dirname(__DIR__) . '/sections/galerie_modifier.php';
    }
    
    /**
     * Supprime une image
     */
    public function supprimer($id) {
        include dirname(__DIR__) . '/sections/galerie_supprimer.php';
    }
}
?>