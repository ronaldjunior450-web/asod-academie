<?php
// Contrôleur Membres - Utilise l'API existante
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/../php/PhotoManager.php';
require_once dirname(__DIR__) . '/api/BaseAPI.php';
require_once dirname(__DIR__) . '/api/MembresAPI.php';

class MembresController {
    private $api;
    
    public function __construct() {
        $this->api = new MembresAPI();
    }
    
    /**
     * Affiche la liste des membres
     */
    public function liste() {
        try {
            // Définir l'action pour afficher la liste
            $_GET['action'] = 'list';
            
            // Inclure directement la section existante qui gère tout
            include dirname(__DIR__) . '/sections/membres.php';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Affiche le formulaire d'ajout
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $result = $this->api->createWithPhoto($_POST, $_FILES);
                
                if ($result['success']) {
                    echo '<script>window.location.href = "index.php?section=membres&success=add";</script>';
                    exit;
                } else {
                    echo '<script>window.location.href = "index.php?section=membres&error=add_failed";</script>';
                    exit;
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            include dirname(__DIR__) . '/sections/membres_ajouter.php';
        }
    }
    
    /**
     * Affiche le formulaire de modification ou traite la mise à jour
     */
    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traiter la mise à jour directement
            try {
                $pdo = getDBConnection();
                
                // Gestion de la photo avec PhotoManager
                $photo_path = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    // Utiliser PhotoManager pour l'upload
                    $projectRoot = dirname(__DIR__, 2) . '/';
                    $photoManager = new PhotoManager($pdo, $projectRoot . 'images/');
                    
                    $photoResult = $photoManager->uploadPhoto($_FILES['photo'], 'membres');
                    if ($photoResult['success']) {
                        $photo_path = $photoResult['filename'];
                        
                        // Supprimer l'ancienne photo si elle existe
                        $stmt_old = $pdo->prepare("SELECT photo FROM membres WHERE id = ?");
                        $stmt_old->execute([$id]);
                        $old_photo = $stmt_old->fetchColumn();
                        if ($old_photo) {
                            $photoManager->deletePhoto($old_photo, 'membres');
                        }
                    } else {
                        echo '<div class="alert alert-danger">Erreur upload photo: ' . htmlspecialchars($photoResult['error']) . '</div>';
                        return;
                    }
                }
                
                // Préparer les données à mettre à jour
                $update_fields = [];
                $update_values = [];
                
                $allowed_fields = ['nom', 'prenom', 'email', 'telephone', 'date_naissance', 'adresse', 'statut', 'date_adhesion'];
                foreach ($allowed_fields as $field) {
                    if (isset($_POST[$field])) {
                        $update_fields[] = "$field = ?";
                        $update_values[] = $_POST[$field];
                    }
                }
                
                // Ajouter la photo si uploadée
                if ($photo_path) {
                    $update_fields[] = "photo = ?";
                    $update_values[] = $photo_path;
                }
                
                if (!empty($update_fields)) {
                    $update_values[] = $id;
                    $sql = "UPDATE membres SET " . implode(', ', $update_fields) . " WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($update_values);
                    
                    echo '<script>window.location.href = "index.php?section=membres&success=update";</script>';
                    exit;
                } else {
                    echo '<div class="alert alert-warning">Aucune donnée à mettre à jour</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            // Afficher le formulaire de modification
            try {
                // Définir les variables nécessaires pour la section
                $_GET['action'] = 'edit';
                $_GET['id'] = $id;
                
                // Inclure le formulaire de modification
                include dirname(__DIR__) . '/sections/membres.php';
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
    }
    
    /**
     * Méthode temporaire pour la logique d'upload (à supprimer)
     */
    private function oldModifierLogic($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traiter la mise à jour directement
            try {
                $pdo = getDBConnection();
                
                // Gestion de la photo
                $photo_path = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../../uploads/membres/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $photo_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $photo_filename = 'photo_' . $id . '_' . time() . '.' . $photo_extension;
                    $photo_path = 'uploads/membres/' . $photo_filename;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], '../../' . $photo_path)) {
                        // Supprimer l'ancienne photo si elle existe
                        $stmt_old = $pdo->prepare("SELECT photo FROM membres WHERE id = ?");
                        $stmt_old->execute([$id]);
                        $old_photo = $stmt_old->fetchColumn();
                        if ($old_photo && file_exists('../../' . $old_photo)) {
                            unlink('../../' . $old_photo);
                        }
                    } else {
                        $photo_path = null;
                    }
                }
                
                // Construction de la requête UPDATE avec tous les champs
                $update_fields = [
                    'nom = ?', 'prenom = ?', 'date_naissance = ?', 'telephone = ?', 
                    'email = ?', 'adresse = ?', 'lieu_naissance = ?', 'ville = ?', 
                    'code_postal = ?', 'pays = ?', 'poste = ?', 'numero_licence = ?', 
                    'equipe_id = ?', 'statut = ?', 'nom_parent = ?', 'prenom_parent = ?', 
                    'telephone_parent = ?', 'email_parent = ?', 'profession_parent = ?', 
                    'date_adhesion = ?', 'numero_cip = ?'
                ];
                $values = [
                    $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'], 
                    $_POST['telephone'], $_POST['email'], $_POST['adresse'], 
                    $_POST['lieu_naissance'] ?? '', $_POST['ville'] ?? '', 
                    $_POST['code_postal'] ?? '', $_POST['pays'] ?? '', 
                    $_POST['poste'] ?? '', $_POST['numero_licence'] ?? '', 
                    $_POST['equipe_id'] ?? '', $_POST['statut'] ?? 'actif', 
                    $_POST['nom_parent'] ?? '', $_POST['prenom_parent'] ?? '', 
                    $_POST['telephone_parent'] ?? '', $_POST['email_parent'] ?? '', 
                    $_POST['profession_parent'] ?? '', $_POST['date_adhesion'] ?? '', 
                    $_POST['numero_cip'] ?? ''
                ];
                
                if ($photo_path !== null) {
                    $update_fields[] = 'photo = ?';
                    $values[] = $photo_path;
                }
                
                $values[] = $id;
                
                $stmt = $pdo->prepare("UPDATE membres SET " . implode(', ', $update_fields) . " WHERE id = ?");
                $stmt->execute($values);
                
                // Utiliser le genre depuis l'URL ou déterminer depuis la base de données
                $genre = $_GET['genre'] ?? 'garcons';
                
                // Si pas de genre dans l'URL, le déterminer depuis la base de données
                if (!isset($_GET['genre'])) {
                    $stmt = $pdo->prepare("SELECT sexe FROM membres WHERE id = ?");
                    $stmt->execute([$id]);
                    $membre_modifie = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($membre_modifie && ($membre_modifie['sexe'] === 'F' || $membre_modifie['sexe'] === 'Féminin' || $membre_modifie['sexe'] === 'Feminin')) {
                        $genre = 'filles';
                    }
                }
                
                // Redirection immédiate vers la liste avec message de succès
                header("Location: index.php?section=membres&genre={$genre}&success=updated");
                exit;
                
            } catch (Exception $e) {
                // En cas d'erreur, rediriger avec message d'erreur
                header('Location: index.php?section=membres&error=' . urlencode($e->getMessage()));
                exit;
            }
        } else {
            // Afficher le formulaire de modification dans la même page
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("SELECT * FROM membres WHERE id = ?");
                $stmt->execute([$id]);
                $membre = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$membre) {
                    echo '<div class="alert alert-danger">Membre non trouvé.</div>';
                    return;
                }
                
                // Récupérer les équipes
                $stmt_equipes = $pdo->query("SELECT * FROM equipes WHERE actif = 1 ORDER BY genre, age_min");
                $equipes = $stmt_equipes->fetchAll(PDO::FETCH_ASSOC);
                
                // Afficher le formulaire de modification
                echo '<div class="card">';
                echo '<div class="card-header d-flex justify-content-between align-items-center">';
                echo '<h5 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier le membre</h5>';
                echo '<button class="btn btn-sm btn-outline-secondary" onclick="loadSection(\'membres\')">';
                echo '<i class="fas fa-arrow-left me-1"></i>Retour à la liste';
                echo '</button>';
                echo '</div>';
                echo '<div class="card-body">';
                
                echo '<form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Membres&action=modifier&id=' . $id . '">';
                
                // Photo actuelle
                if ($membre['photo'] && file_exists('../../' . $membre['photo'])) {
                    echo '<div class="mb-3 text-center">';
                    echo '<label class="form-label">Photo actuelle</label><br>';
                    echo '<img src="../../' . htmlspecialchars($membre['photo']) . '" alt="Photo actuelle" class="img-thumbnail" style="max-width: 150px; max-height: 150px; border-radius: 50%;">';
                    echo '</div>';
                }
                
                // Nouvelle photo
                echo '<div class="mb-3">';
                echo '<label for="photo" class="form-label">Nouvelle photo</label>';
                echo '<input type="file" class="form-control" id="photo" name="photo" accept="image/*">';
                echo '<small class="form-text text-muted">Laisser vide pour conserver la photo actuelle</small>';
                echo '</div>';
                
                // Informations personnelles obligatoires
                echo '<div class="row">';
                echo '<div class="col-md-6">';
                echo '<h6 class="text-primary mb-3">Informations personnelles (obligatoires)</h6>';
                echo '<div class="mb-3">';
                echo '<label for="nom" class="form-label">Nom *</label>';
                echo '<input type="text" class="form-control" id="nom" name="nom" value="' . htmlspecialchars($membre['nom']) . '" required>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="prenom" class="form-label">Prénom *</label>';
                echo '<input type="text" class="form-control" id="prenom" name="prenom" value="' . htmlspecialchars($membre['prenom']) . '" required>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="date_naissance" class="form-label">Date de naissance *</label>';
                echo '<input type="date" class="form-control" id="date_naissance" name="date_naissance" value="' . $membre['date_naissance'] . '" required>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="telephone" class="form-label">Téléphone *</label>';
                echo '<input type="tel" class="form-control" id="telephone" name="telephone" value="' . htmlspecialchars($membre['telephone'] ?? '') . '" required>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="email" class="form-label">Email *</label>';
                echo '<input type="email" class="form-control" id="email" name="email" value="' . htmlspecialchars($membre['email'] ?? '') . '" required>';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="col-md-6">';
                echo '<h6 class="text-primary mb-3">Adresse (obligatoire)</h6>';
                echo '<div class="mb-3">';
                echo '<label for="adresse" class="form-label">Adresse *</label>';
                echo '<textarea class="form-control" id="adresse" name="adresse" rows="3" required>' . htmlspecialchars($membre['adresse'] ?? '') . '</textarea>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                // Informations complémentaires optionnelles
                echo '<div class="row mt-4">';
                echo '<div class="col-md-6">';
                echo '<h6 class="text-secondary mb-3">Informations complémentaires (optionnelles)</h6>';
                echo '<div class="mb-3">';
                echo '<label for="lieu_naissance" class="form-label">Lieu de naissance</label>';
                echo '<input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" value="' . htmlspecialchars($membre['lieu_naissance'] ?? '') . '">';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="pays" class="form-label">Pays</label>';
                echo '<select class="form-select" id="pays" name="pays" onchange="updateVilles()">';
                echo '<option value="">Sélectionner un pays</option>';
                // Pays principal
                echo '<option value="Bénin"' . ($membre['pays'] === 'Bénin' ? ' selected' : '') . '>Bénin</option>';
                // Pays voisins
                echo '<option value="Nigeria"' . ($membre['pays'] === 'Nigeria' ? ' selected' : '') . '>Nigeria</option>';
                echo '<option value="Togo"' . ($membre['pays'] === 'Togo' ? ' selected' : '') . '>Togo</option>';
                echo '<option value="Burkina Faso"' . ($membre['pays'] === 'Burkina Faso' ? ' selected' : '') . '>Burkina Faso</option>';
                echo '<option value="Niger"' . ($membre['pays'] === 'Niger' ? ' selected' : '') . '>Niger</option>';
                // Pays francophones
                echo '<option value="France"' . ($membre['pays'] === 'France' ? ' selected' : '') . '>France</option>';
                echo '<option value="Sénégal"' . ($membre['pays'] === 'Sénégal' ? ' selected' : '') . '>Sénégal</option>';
                // Autre
                echo '<option value="Autre"' . ($membre['pays'] === 'Autre' ? ' selected' : '') . '>Autre</option>';
                echo '</select>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="ville" class="form-label">Ville</label>';
                echo '<select class="form-select" id="ville" name="ville" onchange="updatePays()">';
                echo '<option value="">Sélectionner une ville</option>';
                // Villes béninoises (pays principal)
                echo '<optgroup label="Bénin">';
                echo '<option value="Cotonou"' . ($membre['ville'] === 'Cotonou' ? ' selected' : '') . '>Cotonou</option>';
                echo '<option value="Porto-Novo"' . ($membre['ville'] === 'Porto-Novo' ? ' selected' : '') . '>Porto-Novo</option>';
                echo '<option value="Parakou"' . ($membre['ville'] === 'Parakou' ? ' selected' : '') . '>Parakou</option>';
                echo '<option value="Djougou"' . ($membre['ville'] === 'Djougou' ? ' selected' : '') . '>Djougou</option>';
                echo '<option value="Abomey-Calavi"' . ($membre['ville'] === 'Abomey-Calavi' ? ' selected' : '') . '>Abomey-Calavi</option>';
                echo '<option value="Natitingou"' . ($membre['ville'] === 'Natitingou' ? ' selected' : '') . '>Natitingou</option>';
                echo '<option value="Lokossa"' . ($membre['ville'] === 'Lokossa' ? ' selected' : '') . '>Lokossa</option>';
                echo '<option value="Ouidah"' . ($membre['ville'] === 'Ouidah' ? ' selected' : '') . '>Ouidah</option>';
                echo '<option value="Kandi"' . ($membre['ville'] === 'Kandi' ? ' selected' : '') . '>Kandi</option>';
                echo '<option value="Savalou"' . ($membre['ville'] === 'Savalou' ? ' selected' : '') . '>Savalou</option>';
                echo '<option value="Bohicon"' . ($membre['ville'] === 'Bohicon' ? ' selected' : '') . '>Bohicon</option>';
                echo '<option value="Comé"' . ($membre['ville'] === 'Comé' ? ' selected' : '') . '>Comé</option>';
                echo '<option value="Malanville"' . ($membre['ville'] === 'Malanville' ? ' selected' : '') . '>Malanville</option>';
                echo '<option value="Pobè"' . ($membre['ville'] === 'Pobè' ? ' selected' : '') . '>Pobè</option>';
                echo '<option value="Sakété"' . ($membre['ville'] === 'Sakété' ? ' selected' : '') . '>Sakété</option>';
                echo '</optgroup>';
                // Villes nigérianes (pays voisin)
                echo '<optgroup label="Nigeria">';
                echo '<option value="Lagos"' . ($membre['ville'] === 'Lagos' ? ' selected' : '') . '>Lagos</option>';
                echo '<option value="Abuja"' . ($membre['ville'] === 'Abuja' ? ' selected' : '') . '>Abuja</option>';
                echo '<option value="Kano"' . ($membre['ville'] === 'Kano' ? ' selected' : '') . '>Kano</option>';
                echo '</optgroup>';
                // Villes togolaises (pays voisin)
                echo '<optgroup label="Togo">';
                echo '<option value="Lomé"' . ($membre['ville'] === 'Lomé' ? ' selected' : '') . '>Lomé</option>';
                echo '<option value="Sokodé"' . ($membre['ville'] === 'Sokodé' ? ' selected' : '') . '>Sokodé</option>';
                echo '<option value="Kara"' . ($membre['ville'] === 'Kara' ? ' selected' : '') . '>Kara</option>';
                echo '</optgroup>';
                // Villes burkinabées (pays voisin)
                echo '<optgroup label="Burkina Faso">';
                echo '<option value="Ouagadougou"' . ($membre['ville'] === 'Ouagadougou' ? ' selected' : '') . '>Ouagadougou</option>';
                echo '<option value="Bobo-Dioulasso"' . ($membre['ville'] === 'Bobo-Dioulasso' ? ' selected' : '') . '>Bobo-Dioulasso</option>';
                echo '</optgroup>';
                // Villes nigériennes (pays voisin)
                echo '<optgroup label="Niger">';
                echo '<option value="Niamey"' . ($membre['ville'] === 'Niamey' ? ' selected' : '') . '>Niamey</option>';
                echo '<option value="Zinder"' . ($membre['ville'] === 'Zinder' ? ' selected' : '') . '>Zinder</option>';
                echo '</optgroup>';
                // Villes françaises (pays francophone)
                echo '<optgroup label="France">';
                echo '<option value="Paris"' . ($membre['ville'] === 'Paris' ? ' selected' : '') . '>Paris</option>';
                echo '<option value="Lyon"' . ($membre['ville'] === 'Lyon' ? ' selected' : '') . '>Lyon</option>';
                echo '<option value="Marseille"' . ($membre['ville'] === 'Marseille' ? ' selected' : '') . '>Marseille</option>';
                echo '</optgroup>';
                // Villes sénégalaises (pays francophone)
                echo '<optgroup label="Sénégal">';
                echo '<option value="Dakar"' . ($membre['ville'] === 'Dakar' ? ' selected' : '') . '>Dakar</option>';
                echo '<option value="Thiès"' . ($membre['ville'] === 'Thiès' ? ' selected' : '') . '>Thiès</option>';
                echo '</optgroup>';
                // Autres villes
                echo '<optgroup label="Autres">';
                echo '<option value="Autre"' . ($membre['ville'] === 'Autre' ? ' selected' : '') . '>Autre</option>';
                echo '</optgroup>';
                echo '</select>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="code_postal" class="form-label">Code postal</label>';
                echo '<input type="text" class="form-control" id="code_postal" name="code_postal" value="' . htmlspecialchars($membre['code_postal'] ?? '') . '">';
                echo '</div>';
                echo '</div>';
                
                echo '<div class="col-md-6">';
                echo '<h6 class="text-secondary mb-3">Informations sportives (optionnelles)</h6>';
                echo '<div class="mb-3">';
                echo '<label for="poste" class="form-label">Poste de jeu</label>';
                echo '<select class="form-select" id="poste" name="poste">';
                echo '<option value="">Sélectionner un poste</option>';
                echo '<option value="Gardien"' . ($membre['poste'] === 'Gardien' ? ' selected' : '') . '>Gardien</option>';
                echo '<option value="Défenseur"' . ($membre['poste'] === 'Défenseur' ? ' selected' : '') . '>Défenseur</option>';
                echo '<option value="Milieu"' . ($membre['poste'] === 'Milieu' ? ' selected' : '') . '>Milieu</option>';
                echo '<option value="Attaquant"' . ($membre['poste'] === 'Attaquant' ? ' selected' : '') . '>Attaquant</option>';
                echo '</select>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="numero_licence" class="form-label">Numéro de licence FFF</label>';
                echo '<input type="text" class="form-control" id="numero_licence" name="numero_licence" value="' . htmlspecialchars($membre['numero_licence'] ?? '') . '">';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="equipe_id" class="form-label">Équipe</label>';
                echo '<select class="form-select" id="equipe_id" name="equipe_id">';
                echo '<option value="">Sélectionner une équipe</option>';
                foreach ($equipes as $equipe) {
                    $selected = ($membre['equipe_id'] == $equipe['id']) ? ' selected' : '';
                    echo '<option value="' . $equipe['id'] . '"' . $selected . '>' . htmlspecialchars($equipe['nom']) . ' (' . $equipe['genre'] . ')</option>';
                }
                echo '</select>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="statut" class="form-label">Statut</label>';
                echo '<select class="form-select" id="statut" name="statut">';
                echo '<option value="actif"' . ($membre['statut'] === 'actif' ? ' selected' : '') . '>Actif</option>';
                echo '<option value="suspendu"' . ($membre['statut'] === 'suspendu' ? ' selected' : '') . '>Suspendu</option>';
                echo '<option value="radie"' . ($membre['statut'] === 'radie' ? ' selected' : '') . '>Radié</option>';
                echo '</select>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="date_adhesion" class="form-label">Date d\'adhésion</label>';
                echo '<input type="text" class="form-control" id="date_adhesion" name="date_adhesion" value="' . ($membre['date_adhesion'] ? date('Y-m-d H:i:s', strtotime($membre['date_adhesion'])) : date('Y-m-d H:i:s')) . '" readonly style="background-color: #f8f9fa;">';
                echo '<small class="form-text text-muted">Date d\'entrée dans l\'association (non modifiable)</small>';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="numero_cip" class="form-label">Numéro CIP</label>';
                echo '<input type="text" class="form-control" id="numero_cip" name="numero_cip" value="' . htmlspecialchars($membre['numero_cip'] ?? '') . '">';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                // Informations parents optionnelles
                echo '<div class="row mt-4">';
                echo '<div class="col-12">';
                echo '<h6 class="text-secondary mb-3">Informations parents (optionnelles)</h6>';
                echo '</div>';
                echo '<div class="col-md-6">';
                echo '<div class="mb-3">';
                echo '<label for="nom_parent" class="form-label">Nom du parent</label>';
                echo '<input type="text" class="form-control" id="nom_parent" name="nom_parent" value="' . htmlspecialchars($membre['nom_parent'] ?? '') . '">';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="prenom_parent" class="form-label">Prénom du parent</label>';
                echo '<input type="text" class="form-control" id="prenom_parent" name="prenom_parent" value="' . htmlspecialchars($membre['prenom_parent'] ?? '') . '">';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="telephone_parent" class="form-label">Téléphone du parent</label>';
                echo '<input type="tel" class="form-control" id="telephone_parent" name="telephone_parent" value="' . htmlspecialchars($membre['telephone_parent'] ?? '') . '">';
                echo '</div>';
                echo '</div>';
                echo '<div class="col-md-6">';
                echo '<div class="mb-3">';
                echo '<label for="email_parent" class="form-label">Email du parent</label>';
                echo '<input type="email" class="form-control" id="email_parent" name="email_parent" value="' . htmlspecialchars($membre['email_parent'] ?? '') . '">';
                echo '</div>';
                echo '<div class="mb-3">';
                echo '<label for="profession_parent" class="form-label">Profession du parent</label>';
                echo '<input type="text" class="form-control" id="profession_parent" name="profession_parent" value="' . htmlspecialchars($membre['profession_parent'] ?? '') . '">';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                // Boutons
                echo '<div class="d-flex justify-content-between">';
                echo '<button type="button" class="btn btn-secondary" onclick="loadSection(\'membres\')">';
                echo '<i class="fas fa-times me-1"></i>Annuler';
                echo '</button>';
                echo '<button type="submit" class="btn btn-primary">';
                echo '<i class="fas fa-save me-1"></i>Enregistrer les modifications';
                echo '</button>';
                echo '</div>';
                
                echo '</form>';
                echo '</div>';
                echo '</div>';
                
                // JavaScript pour l'interaction dynamique pays/ville
                echo '<script>';
                echo 'function updateVilles() {';
                echo '    const pays = document.getElementById("pays").value;';
                echo '    const ville = document.getElementById("ville");';
                echo '    const options = ville.querySelectorAll("option");';
                echo '    ';
                echo '    // Masquer toutes les options sauf la première';
                echo '    options.forEach(option => {';
                echo '        if (option.value === "") {';
                echo '            option.style.display = "block";';
                echo '        } else {';
                echo '            option.style.display = "none";';
                echo '        }';
                echo '    });';
                echo '    ';
                echo '    // Afficher les villes du pays sélectionné';
                echo '    if (pays) {';
                echo '        const optgroups = ville.querySelectorAll("optgroup");';
                echo '        optgroups.forEach(group => {';
                echo '            if (group.label === pays) {';
                echo '                const groupOptions = group.querySelectorAll("option");';
                echo '                groupOptions.forEach(option => {';
                echo '                    option.style.display = "block";';
                echo '                });';
                echo '            }';
                echo '        });';
                echo '    }';
                echo '    ';
                echo '    // Réinitialiser la sélection de ville';
                echo '    ville.value = "";';
                echo '}';
                echo '';
                echo 'function updatePays() {';
                echo '    const ville = document.getElementById("ville").value;';
                echo '    const pays = document.getElementById("pays");';
                echo '    ';
                echo '    // Mapping ville -> pays';
                echo '    const villePays = {';
                // Bénin (pays principal)
                echo '        "Cotonou": "Bénin", "Porto-Novo": "Bénin", "Parakou": "Bénin", "Djougou": "Bénin", "Abomey-Calavi": "Bénin", "Natitingou": "Bénin", "Lokossa": "Bénin", "Ouidah": "Bénin", "Kandi": "Bénin", "Savalou": "Bénin", "Bohicon": "Bénin", "Comé": "Bénin", "Malanville": "Bénin", "Pobè": "Bénin", "Sakété": "Bénin",';
                // Nigeria (pays voisin)
                echo '        "Lagos": "Nigeria", "Abuja": "Nigeria", "Kano": "Nigeria",';
                // Togo (pays voisin)
                echo '        "Lomé": "Togo", "Sokodé": "Togo", "Kara": "Togo",';
                // Burkina Faso (pays voisin)
                echo '        "Ouagadougou": "Burkina Faso", "Bobo-Dioulasso": "Burkina Faso",';
                // Niger (pays voisin)
                echo '        "Niamey": "Niger", "Zinder": "Niger",';
                // France (pays francophone)
                echo '        "Paris": "France", "Lyon": "France", "Marseille": "France",';
                // Sénégal (pays francophone)
                echo '        "Dakar": "Sénégal", "Thiès": "Sénégal"';
                echo '    };';
                echo '    ';
                echo '    if (ville && villePays[ville]) {';
                echo '        pays.value = villePays[ville];';
                echo '    }';
                echo '}';
                echo '';
                echo '// Initialiser les sélections au chargement';
                echo 'document.addEventListener("DOMContentLoaded", function() {';
                echo '    updateVilles();';
                echo '});';
                echo '</script>';
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Erreur lors du chargement du membre : ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
    }
    
    public function restaurer($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = getDBConnection();
                $id = $_POST['id'] ?? $id;
                $traite_par = $_SESSION['admin_nom_complet'] ?? $_SESSION['admin_username'];
                
                error_log("=== RESTAURATION MEMBRE (Controller) ===");
                error_log("ID membre: " . $id);
                error_log("Traité par: " . $traite_par);
                
                // Vérifier que le membre est bien radié
                $stmt = $pdo->prepare("SELECT statut FROM membres WHERE id = ?");
                $stmt->execute([$id]);
                $membre = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$membre || $membre['statut'] !== 'radie') {
                    throw new Exception("Ce membre n'est pas radié");
                }
                
                // Restaurer le membre
                $stmt = $pdo->prepare("
                    UPDATE membres 
                    SET statut = 'actif', 
                        equipe_id = NULL,
                        motif_renvoi = NULL,
                        traite_par = NULL,
                        date_radiation = NULL
                    WHERE id = ?
                ");
                
                $result = $stmt->execute([$id]);
                
                if ($result) {
                    error_log("Membre restauré avec succès");
                    
                    // Utiliser le genre depuis POST, GET ou déterminer depuis la base de données
                    $genre = $_POST['genre'] ?? $_GET['genre'] ?? 'garcons';
                    error_log("=== DEBUG REDIRECTION RESTAURATION ===");
                    error_log("Genre depuis POST: " . ($_POST['genre'] ?? 'non défini'));
                    error_log("Genre depuis GET: " . ($_GET['genre'] ?? 'non défini'));
                    error_log("Genre final: " . $genre);
                    
                    // Si pas de genre dans l'URL, le déterminer depuis la base de données
                    if (!isset($_GET['genre'])) {
                        error_log("Genre non trouvé dans GET, détermination depuis la base de données");
                        $stmt = $pdo->prepare("SELECT sexe FROM membres WHERE id = ?");
                        $stmt->execute([$id]);
                        $membre_restaure = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($membre_restaure && ($membre_restaure['sexe'] === 'F' || $membre_restaure['sexe'] === 'Féminin' || $membre_restaure['sexe'] === 'Feminin')) {
                            $genre = 'filles';
                            error_log("Genre déterminé depuis la base: filles");
                        } else {
                            error_log("Genre déterminé depuis la base: garcons");
                        }
                    }
                    
                    $redirect_url = "index.php?section=membres&genre={$genre}&success=restored";
                    error_log("URL de redirection: " . $redirect_url);
                    header("Location: " . $redirect_url);
                    exit;
                } else {
                    throw new Exception("Erreur lors de la restauration du membre");
                }
                
            } catch (Exception $e) {
                error_log("=== ERREUR RESTAURATION (Controller) ===");
                error_log($e->getMessage());
                echo '<div class="alert alert-danger">Erreur lors de la restauration : ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            error_log("=== AFFICHAGE FORMULAIRE RESTAURATION ===");
            error_log("ID reçu en paramètre: " . $id);
            
            // Vérifier que le membre existe et est radié
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("SELECT * FROM membres WHERE id = ? AND statut = 'radie'");
                $stmt->execute([$id]);
                $membre = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$membre) {
                    error_log("❌ Membre non trouvé ou non radié (ID: $id)");
                    echo '<div class="alert alert-warning">Membre non trouvé ou non radié.</div>';
                    return;
                }
                
                error_log("✅ Membre trouvé: " . $membre['nom'] . " " . $membre['prenom'] . " (statut: " . $membre['statut'] . ")");
                
            } catch (Exception $e) {
                error_log("❌ Erreur lors de la vérification du membre: " . $e->getMessage());
                echo '<div class="alert alert-danger">Erreur lors de la vérification du membre.</div>';
                return;
            }
            
            $_GET['action'] = 'restaurer';
            $_GET['id'] = $id;
            include dirname(__DIR__) . '/sections/membres.php';
        }
    }
    
    /**
     * Affiche les détails d'un membre
     */
    public function voir($id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT m.*, e.nom as equipe_nom, e.genre as equipe_genre, e.age_min, e.age_max
                FROM membres m
                LEFT JOIN equipes e ON m.equipe_id = e.id
                WHERE m.id = ?
            ");
            $stmt->execute([$id]);
            $membre = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$membre) {
                echo '<div class="alert alert-danger">Membre non trouvé.</div>';
                return;
            }
            
            // Afficher les détails dans un format adapté à la même page
            echo '<div class="card">';
            echo '<div class="card-header d-flex justify-content-between align-items-center">';
            echo '<h5 class="mb-0"><i class="fas fa-user me-2"></i>Détails du membre</h5>';
            echo '<button class="btn btn-sm btn-outline-secondary" onclick="loadSection(\'membres\')">';
            echo '<i class="fas fa-arrow-left me-1"></i>Retour à la liste';
            echo '</button>';
            echo '</div>';
            echo '<div class="card-body">';
            
            // Photo du membre
            if ($membre['photo'] && file_exists('../../' . $membre['photo'])) {
                echo '<div class="text-center mb-4">';
                echo '<img src="../../' . htmlspecialchars($membre['photo']) . '" alt="Photo du membre" class="img-thumbnail" style="max-width: 150px; max-height: 150px; border-radius: 50%;">';
                echo '</div>';
            }
            
            // Informations personnelles
            echo '<div class="row mb-3">';
            echo '<div class="col-md-6">';
            echo '<h6 class="text-primary">Informations personnelles</h6>';
            echo '<p><strong>Nom :</strong> ' . htmlspecialchars($membre['nom']) . '</p>';
            echo '<p><strong>Prénom :</strong> ' . htmlspecialchars($membre['prenom']) . '</p>';
            echo '<p><strong>Date de naissance :</strong> ' . date('d/m/Y', strtotime($membre['date_naissance'])) . '</p>';
            echo '<p><strong>Âge :</strong> ' . (date('Y') - date('Y', strtotime($membre['date_naissance']))) . ' ans</p>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<h6 class="text-primary">Contact</h6>';
            echo '<p><strong>Téléphone :</strong> ' . htmlspecialchars($membre['telephone'] ?? 'Non renseigné') . '</p>';
            echo '<p><strong>Email :</strong> ' . htmlspecialchars($membre['email'] ?? 'Non renseigné') . '</p>';
            echo '<p><strong>Adresse :</strong> ' . htmlspecialchars($membre['adresse'] ?? 'Non renseignée') . '</p>';
            echo '</div>';
            echo '</div>';
            
            // Informations sportives
            echo '<div class="row mb-3">';
            echo '<div class="col-md-6">';
            echo '<h6 class="text-primary">Informations sportives</h6>';
            echo '<p><strong>Équipe :</strong> ' . htmlspecialchars($membre['equipe_nom'] ?? 'Non assigné') . '</p>';
            echo '<p><strong>Poste :</strong> ' . htmlspecialchars($membre['poste'] ?? 'Non renseigné') . '</p>';
            echo '<p><strong>Numéro de maillot :</strong> ' . htmlspecialchars($membre['numero_maillot'] ?? 'Non renseigné') . '</p>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<h6 class="text-primary">Statut</h6>';
            echo '<p><strong>Statut :</strong> ';
            $statutClass = $membre['statut'] === 'actif' ? 'success' : ($membre['statut'] === 'suspendu' ? 'warning' : 'danger');
            echo '<span class="badge bg-' . $statutClass . '">' . ucfirst($membre['statut'] ?? 'Non assigné') . '</span></p>';
            echo '<p><strong>Date d\'inscription/adhésion :</strong> ' . (isset($membre['date_adhesion']) && $membre['date_adhesion'] ? date('d/m/Y à H:i', strtotime($membre['date_adhesion'])) : (isset($membre['date_creation']) && $membre['date_creation'] ? date('d/m/Y à H:i', strtotime($membre['date_creation'])) : 'Non renseignée')) . '</p>';
            echo '</div>';
            echo '</div>';
            
            // Informations parents
            if ($membre['nom_parent'] || $membre['telephone_parent']) {
                echo '<div class="row mb-3">';
                echo '<div class="col-12">';
                echo '<h6 class="text-primary">Informations parents</h6>';
                echo '<p><strong>Nom du parent :</strong> ' . htmlspecialchars($membre['nom_parent'] ?? 'Non renseigné') . '</p>';
                echo '<p><strong>Téléphone parent :</strong> ' . htmlspecialchars($membre['telephone_parent'] ?? 'Non renseigné') . '</p>';
                echo '</div>';
                echo '</div>';
            }
            
            // Boutons d'action
            echo '<div class="mt-4 pt-3 border-top">';
            echo '<div class="btn-group" role="group">';
            echo '<button class="btn btn-primary btn-sm" onclick="loadSection(\'membres\', \'modifier\', ' . $membre['id'] . ')">';
            echo '<i class="fas fa-edit me-1"></i> Modifier';
            echo '</button>';
            echo '<button class="btn btn-warning btn-sm" onclick="loadSection(\'membres\', \'transfer\', ' . $membre['id'] . ')">';
            echo '<i class="fas fa-exchange-alt me-1"></i> Transférer';
            echo '</button>';
            echo '<button class="btn btn-danger btn-sm" onclick="if(confirm(\'Êtes-vous sûr de vouloir radier ce membre ?\')) { loadSection(\'membres\', \'renvoyer\', ' . $membre['id'] . '); }">';
            echo '<i class="fas fa-user-times me-1"></i> Radier';
            echo '</button>';
            echo '</div>';
            echo '</div>';
            
            echo '</div>';
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur lors du chargement du membre : ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Supprime un membre
     */
    public function supprimer($id) {
        try {
            $result = $this->api->delete($id);
            
            if ($result['success']) {
                echo '<script>window.location.href = "index.php?section=membres&success=delete";</script>';
                exit;
            } else {
                echo '<script>window.location.href = "index.php?section=membres&error=delete_failed";</script>';
                exit;
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    /**
     * Affiche le formulaire de transfert de membre et traite la soumission
     */
    public function transfer($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement du transfert
            try {
                $pdo = getDBConnection();
                
                // IMPORTANT : Utiliser l'ID du POST, pas l'ID de l'URL
                $id = $_POST['id'] ?? $id;
                
                $type_transfert = $_POST['type_transfert'];
                $motif = $_POST['motif'] ?? '';
                $traite_par = $_SESSION['admin_nom_complet'] ?? $_SESSION['admin_username'];
                
                if ($type_transfert === 'interne') {
                    $equipe_destination_id = $_POST['equipe_destination_id'];
                    
                    // Mettre à jour l'équipe du membre
                    $stmt = $pdo->prepare("UPDATE membres SET equipe_id = ? WHERE id = ?");
                    $stmt->execute([$equipe_destination_id, $id]);
                    
                    // Enregistrer le transfert
                    $stmt = $pdo->prepare("
                        INSERT INTO transferts_membres (
                            membre_id, type_transfert, equipe_destination,
                            motif, traite_par
                        ) VALUES (?, 'interne', ?, ?, ?)
                    ");
                    $stmt->execute([
                        $id,
                        $equipe_destination_id,
                        $motif,
                        $traite_par
                    ]);
                    
                    error_log("Transfert interne effectué");
                    
                    // Utiliser le genre depuis l'URL ou déterminer depuis la base de données
                    $genre = $_GET['genre'] ?? 'garcons';
                    
                    // Si pas de genre dans l'URL, le déterminer depuis la base de données
                    if (!isset($_GET['genre'])) {
                        $stmt = $pdo->prepare("SELECT sexe FROM membres WHERE id = ?");
                        $stmt->execute([$id]);
                        $membre_transfere = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($membre_transfere && ($membre_transfere['sexe'] === 'F' || $membre_transfere['sexe'] === 'Féminin' || $membre_transfere['sexe'] === 'Feminin')) {
                            $genre = 'filles';
                        }
                    }
                    
                    header("Location: index.php?section=membres&genre={$genre}&success=transfer_interne");
                    exit;
                    
                } else { // transfert externe
                    $association_destination = $_POST['association_destination'];
                    $ville_destination = $_POST['ville_destination'] ?? '';
                    $contact_destination = $_POST['contact_destination'] ?? '';
                    
                    error_log("=== TRANSFERT EXTERNE (Controller) ===");
                    error_log("Association: " . $association_destination);
                    error_log("Ville: " . $ville_destination);
                    
                    // Changer le statut du membre
                    $stmt = $pdo->prepare("UPDATE membres SET statut = 'transfere' WHERE id = ?");
                    $result = $stmt->execute([$id]);
                    error_log("UPDATE statut result: " . ($result ? "OK" : "FAILED"));
                    
                    // Enregistrer le transfert
                    $stmt = $pdo->prepare("
                        INSERT INTO transferts_membres (
                            membre_id, type_transfert, association_destination, ville_destination, contact_destination,
                            motif, traite_par
                        ) VALUES (?, 'externe', ?, ?, ?, ?, ?)
                    ");
                    $result = $stmt->execute([
                        $id,
                        $association_destination,
                        $ville_destination,
                        $contact_destination,
                        $motif,
                        $traite_par
                    ]);
                    error_log("INSERT transfert result: " . ($result ? "OK" : "FAILED"));
                    error_log("Last insert ID: " . $pdo->lastInsertId());
                    
                    // Utiliser le genre depuis l'URL ou déterminer depuis la base de données
                    $genre = $_GET['genre'] ?? 'garcons';
                    
                    // Si pas de genre dans l'URL, le déterminer depuis la base de données
                    if (!isset($_GET['genre'])) {
                        $stmt = $pdo->prepare("SELECT sexe FROM membres WHERE id = ?");
                        $stmt->execute([$id]);
                        $membre_transfere = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($membre_transfere && ($membre_transfere['sexe'] === 'F' || $membre_transfere['sexe'] === 'Féminin' || $membre_transfere['sexe'] === 'Feminin')) {
                            $genre = 'filles';
                        }
                    }
                    
                    header("Location: index.php?section=membres&genre={$genre}&success=transfer_externe");
                    exit;
                }
                
            } catch (Exception $e) {
                error_log("=== ERREUR TRANSFERT (Controller) ===");
                error_log($e->getMessage());
                echo '<div class="alert alert-danger">Erreur lors du transfert : ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            // Affichage du formulaire
            error_log("=== AFFICHAGE FORMULAIRE TRANSFERT ===");
            error_log("ID reçu en paramètre: " . $id);
            error_log("GET id avant: " . ($_GET['id'] ?? 'non défini'));
            error_log("POST id: " . ($_POST['id'] ?? 'non défini'));
            
            // Forcer l'ID correct et s'assurer qu'il est bien utilisé
            $id = (int)$id;
            $_GET['action'] = 'transfer';
            $_GET['id'] = $id;
            
            error_log("ID final utilisé: " . $id);
            error_log("GET id après: " . $_GET['id']);
            
            // Inclure directement la section existante qui gère tout
            include dirname(__DIR__) . '/sections/membres.php';
        }
    }
    
    /**
     * Radier un membre
     */
    public function renvoyer($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement de la radiation
            try {
                $pdo = getDBConnection();
                
                // Utiliser l'ID du POST, pas l'ID de l'URL
                $id = $_POST['id'] ?? $id;
                
                $motif_renvoi = $_POST['motif_renvoi'] ?? '';
                $traite_par = $_SESSION['admin_nom_complet'] ?? $_SESSION['admin_username'];
                
                error_log("=== RADIATION MEMBRE (Controller) ===");
                error_log("ID membre: " . $id);
                error_log("Motif: " . $motif_renvoi);
                error_log("Traité par: " . $traite_par);
                
                // Mettre à jour le membre : statut = radie, equipe_id = NULL
                $stmt = $pdo->prepare("
                    UPDATE membres 
                    SET statut = 'radie', 
                        equipe_id = NULL,
                        motif_renvoi = ?,
                        traite_par = ?,
                        date_radiation = NOW()
                    WHERE id = ?
                ");
                $result = $stmt->execute([$motif_renvoi, $traite_par, $id]);
                
                if ($result) {
                    error_log("Membre radié avec succès");
                    
                    // Utiliser le genre depuis POST, GET ou déterminer depuis la base de données
                    $genre = $_POST['genre'] ?? $_GET['genre'] ?? 'garcons';
                    error_log("=== DEBUG REDIRECTION RADIATION ===");
                    error_log("Genre depuis POST: " . ($_POST['genre'] ?? 'non défini'));
                    error_log("Genre depuis GET: " . ($_GET['genre'] ?? 'non défini'));
                    error_log("Genre final: " . $genre);
                    
                    // Si pas de genre dans l'URL, le déterminer depuis la base de données
                    if (!isset($_GET['genre'])) {
                        $stmt = $pdo->prepare("SELECT sexe FROM membres WHERE id = ?");
                        $stmt->execute([$id]);
                        $membre_radie = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($membre_radie && ($membre_radie['sexe'] === 'F' || $membre_radie['sexe'] === 'Féminin' || $membre_radie['sexe'] === 'Feminin')) {
                            $genre = 'filles';
                        }
                    }
                    
                    $redirect_url = "index.php?section=membres&genre={$genre}&success=delete";
                    error_log("URL de redirection: " . $redirect_url);
                    header("Location: " . $redirect_url);
                    exit;
                } else {
                    throw new Exception("Erreur lors de la radiation du membre");
                }
                
            } catch (Exception $e) {
                error_log("=== ERREUR RADIATION (Controller) ===");
                error_log($e->getMessage());
                echo '<div class="alert alert-danger">Erreur lors de la radiation : ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            // Affichage du formulaire de radiation
            error_log("=== AFFICHAGE FORMULAIRE RADIATION ===");
            error_log("ID reçu en paramètre: " . $id);
            
            $_GET['action'] = 'renvoyer';
            $_GET['id'] = $id;
            
            // Inclure directement la section existante qui gère tout
            include dirname(__DIR__) . '/sections/membres.php';
        }
    }
    
}
?>