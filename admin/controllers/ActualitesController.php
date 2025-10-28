<?php
// Contrôleur Actualités - Utilise l'API avec PhotoManager
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/api/BaseAPI.php';
require_once dirname(__DIR__) . '/api/ActualitesAPI_New.php';

class ActualitesController {
    private $api;
    
    public function __construct() {
        $this->api = new ActualitesAPI_New();
    }
    
    /**
     * Affiche la liste des actualités
     */
    public function liste() {
        try {
            // Définir l'action pour afficher la liste
            $_GET['action'] = 'list';
            
            // Inclure directement la section existante qui gère tout
            include dirname(__DIR__) . '/sections/actualites.php';
            
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
                header('Location: index.php?section=actualites&success=added');
                exit;
            } else {
                header('Location: index.php?section=actualites&error=' . urlencode($result['error']));
                exit;
            }
        } else {
            // Afficher le formulaire d'ajout
            $_GET['action'] = 'add';
            include dirname(__DIR__) . '/sections/actualites.php';
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
                header('Location: index.php?section=actualites&success=updated');
                exit;
            } else {
                header('Location: index.php?section=actualites&error=' . urlencode($result['error']));
                exit;
            }
        } else {
            // Afficher le formulaire de modification dans la même page
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = ?");
                $stmt->execute([$id]);
                $actualite = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$actualite) {
                    echo '<div class="alert alert-danger">Actualité non trouvée.</div>';
                    return;
                }
                
                // Afficher le formulaire de modification
                echo '<div class="card">';
                echo '<div class="card-header d-flex justify-content-between align-items-center">';
                echo '<h5 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier l\'actualité</h5>';
                echo '<button class="btn btn-sm btn-outline-secondary" onclick="loadSection(\'actualites\')">';
                echo '<i class="fas fa-arrow-left me-1"></i>Retour à la liste';
                echo '</button>';
                echo '</div>';
                echo '<div class="card-body">';
                
                echo '<form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Actualites&action=modifier&id=' . $id . '">';
                
                // Titre
                echo '<div class="mb-3">';
                echo '<label for="titre" class="form-label">Titre *</label>';
                echo '<input type="text" class="form-control" id="titre" name="titre" value="' . htmlspecialchars($actualite['titre']) . '" required>';
                echo '</div>';
                
                // Image actuelle
                if ($actualite['image'] && file_exists('../../' . $actualite['image'])) {
                    echo '<div class="mb-3">';
                    echo '<label class="form-label">Image actuelle</label><br>';
                    echo '<img src="../../' . htmlspecialchars($actualite['image']) . '" alt="Image actuelle" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">';
                    echo '</div>';
                }
                
                // Nouvelle image
                echo '<div class="mb-3">';
                echo '<label for="image" class="form-label">Nouvelle image</label>';
                echo '<input type="file" class="form-control" id="image" name="image" accept="image/*">';
                echo '<small class="form-text text-muted">Laisser vide pour conserver l\'image actuelle</small>';
                echo '</div>';
                
                // Contenu
                echo '<div class="mb-3">';
                echo '<label for="contenu" class="form-label">Contenu *</label>';
                echo '<textarea class="form-control" id="contenu" name="contenu" rows="10" required>' . htmlspecialchars($actualite['contenu']) . '</textarea>';
                echo '</div>';
                
                // Statut
                echo '<div class="mb-3">';
                echo '<label for="statut" class="form-label">Statut *</label>';
                echo '<select class="form-select" id="statut" name="statut" required>';
                echo '<option value="brouillon"' . ($actualite['statut'] === 'brouillon' ? ' selected' : '') . '>Brouillon</option>';
                echo '<option value="publie"' . ($actualite['statut'] === 'publie' ? ' selected' : '') . '>Publié</option>';
                echo '</select>';
                echo '</div>';
                
                // Boutons
                echo '<div class="d-flex justify-content-between">';
                echo '<button type="button" class="btn btn-secondary" onclick="loadSection(\'actualites\')">';
                echo '<i class="fas fa-times me-1"></i>Annuler';
                echo '</button>';
                echo '<button type="submit" class="btn btn-primary">';
                echo '<i class="fas fa-save me-1"></i>Enregistrer les modifications';
                echo '</button>';
                echo '</div>';
                
                echo '</form>';
                echo '</div>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Erreur lors du chargement de l\'actualité : ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
    }
    
    /**
     * Supprime une actualité
     */
    public function supprimer($id) {
        // Utiliser l'API avec PhotoManager
        $result = $this->api->deleteWithPhoto($id);
        
        if ($result['success']) {
            header('Location: index.php?section=actualites&success=deleted');
            exit;
        } else {
            header('Location: index.php?section=actualites&error=' . urlencode($result['error']));
            exit;
        }
    }
    
    /**
     * Affiche les détails d'une actualité dans la même page
     */
    public function voir($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = ?");
            $stmt->execute([$id]);
            $actualite = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$actualite) {
                echo '<div class="alert alert-danger">Actualité non trouvée.</div>';
                return;
            }
            
            // Afficher les détails dans un format adapté à la même page
            echo '<div class="card">';
            echo '<div class="card-header d-flex justify-content-between align-items-center">';
            echo '<h5 class="mb-0"><i class="fas fa-eye me-2"></i>Détails de l\'actualité</h5>';
            echo '<button class="btn btn-sm btn-outline-secondary" onclick="loadSection(\'actualites\')">';
            echo '<i class="fas fa-arrow-left me-1"></i>Retour à la liste';
            echo '</button>';
            echo '</div>';
            echo '<div class="card-body">';
            
            // Titre
            echo '<h4 class="mb-3" style="color: #1a73e8; border-bottom: 2px solid #e8f0fe; padding-bottom: 10px;">';
            echo htmlspecialchars($actualite['titre']);
            echo '</h4>';
            
            // Informations générales
            echo '<div class="row mb-3">';
            echo '<div class="col-md-6">';
            echo '<strong>Auteur :</strong> ' . htmlspecialchars($actualite['auteur'] ?? 'Administrateur') . '<br>';
            echo '<strong>Catégorie :</strong> ' . htmlspecialchars($actualite['categorie'] ?? 'Général') . '<br>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<strong>Date de création :</strong> ' . date('d/m/Y à H:i', strtotime($actualite['date_creation'])) . '<br>';
            echo '<strong>Statut :</strong> ';
            $statutClass = $actualite['statut'] === 'publie' ? 'success' : 'warning';
            echo '<span class="badge bg-' . $statutClass . '">' . ucfirst($actualite['statut']) . '</span><br>';
            echo '</div>';
            echo '</div>';
            
            // Image si elle existe
            if ($actualite['image'] && file_exists('../../' . $actualite['image'])) {
                echo '<div class="mb-3 text-center">';
                echo '<img src="../../' . htmlspecialchars($actualite['image']) . '" alt="' . htmlspecialchars($actualite['titre']) . '" class="img-fluid rounded" style="max-width: 100%; height: auto; border: 1px solid #ddd;">';
                echo '</div>';
            }
            
            // Résumé si disponible
            if (!empty($actualite['resume'])) {
                echo '<div class="mb-3">';
                echo '<strong>Résumé :</strong><br>';
                echo '<p class="text-muted" style="background: #f8f9fa; padding: 10px; border-radius: 5px; border-left: 4px solid #1a73e8;">';
                echo htmlspecialchars($actualite['resume']);
                echo '</p>';
                echo '</div>';
            }
            
            // Contenu principal
            echo '<div class="mb-3">';
            echo '<strong>Contenu :</strong><br>';
            echo '<div class="content-body" style="line-height: 1.6; color: #333; background: #fff; padding: 15px; border-radius: 5px; border: 1px solid #e0e0e0;">';
            echo $actualite['contenu'];
            echo '</div>';
            echo '</div>';
            
            // Date de modification si différente
            if ($actualite['date_modification'] && $actualite['date_modification'] !== $actualite['date_creation']) {
                echo '<div class="mb-3">';
                echo '<small class="text-muted">';
                echo '<i class="fas fa-edit"></i> Dernière modification : ' . date('d/m/Y à H:i', strtotime($actualite['date_modification']));
                echo '</small>';
                echo '</div>';
            }
            
            // Boutons d'action
            echo '<div class="mt-4 pt-3 border-top">';
            echo '<div class="btn-group" role="group">';
            echo '<button class="btn btn-primary btn-sm" onclick="loadSection(\'actualites\', \'modifier\', ' . $actualite['id'] . ')">';
            echo '<i class="fas fa-edit me-1"></i> Modifier';
            echo '</button>';
            
            if ($actualite['statut'] === 'publie') {
                echo '<button class="btn btn-warning btn-sm" onclick="window.location.href=\'mvc_router.php?controller=Actualites&action=depublier&id=' . $actualite['id'] . '\'">';
                echo '<i class="fas fa-eye-slash me-1"></i> Dépublier';
                echo '</button>';
            } else {
                echo '<button class="btn btn-success btn-sm" onclick="window.location.href=\'mvc_router.php?controller=Actualites&action=publier&id=' . $actualite['id'] . '\'">';
                echo '<i class="fas fa-eye me-1"></i> Publier';
                echo '</button>';
            }
            
            echo '<button class="btn btn-danger btn-sm" onclick="if(confirm(\'Êtes-vous sûr de vouloir supprimer cette actualité ?\')) { window.location.href=\'mvc_router.php?controller=Actualites&action=supprimer&id=' . $actualite['id'] . '\'; }">';
            echo '<i class="fas fa-trash me-1"></i> Supprimer';
            echo '</button>';
            echo '</div>';
            echo '</div>';
            
            echo '</div>';
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur lors du chargement de l\'actualité : ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Publie une actualité
     */
    public function publier($id) {
        $result = $this->api->publier($id);
        
        if ($result['success']) {
            header('Location: index.php?section=actualites&success=published');
            exit;
        } else {
            header('Location: index.php?section=actualites&error=' . urlencode($result['error']));
            exit;
        }
    }
    
    /**
     * Dépublie une actualité
     */
    public function depublier($id) {
        $result = $this->api->depublier($id);
        
        if ($result['success']) {
            header('Location: index.php?section=actualites&success=unpublished');
            exit;
        } else {
            header('Location: index.php?section=actualites&error=' . urlencode($result['error']));
            exit;
        }
    }
}
?>