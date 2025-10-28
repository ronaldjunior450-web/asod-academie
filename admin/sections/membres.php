<?php
// Section Membres - Gestion des membres Gmail Style
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$message = '';
$error = '';
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;
// Déterminer le genre avec une logique intelligente
$genre = $_GET['genre'] ?? $_POST['genre'] ?? 'garcons';

// Si on est dans un contexte d'action avec un membre spécifique, déterminer le genre depuis le membre
if (($action === 'restaurer' || $action === 'edit' || $action === 'transfer' || $action === 'renvoyer') && $id && !isset($_GET['genre'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT sexe FROM membres WHERE id = ?");
        $stmt->execute([$id]);
        $membre_genre = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($membre_genre && ($membre_genre['sexe'] === 'F' || $membre_genre['sexe'] === 'Féminin' || $membre_genre['sexe'] === 'Feminin')) {
            $genre = 'filles';
        } else {
            $genre = 'garcons';
        }
        
        error_log("=== GENRE DÉTERMINÉ DEPUIS LE MEMBRE ===");
        error_log("ID membre: $id, Sexe: " . ($membre_genre['sexe'] ?? 'non trouvé') . ", Genre final: $genre");
        
    } catch (Exception $e) {
        error_log("Erreur lors de la détermination du genre: " . $e->getMessage());
        $genre = 'garcons'; // Fallback
    }
}

// Gestion des messages de succès/erreur depuis les redirections
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'transfer_interne':
            $message = 'Membre transféré vers une autre équipe avec succès !';
            break;
        case 'transfer_externe':
            $message = 'Membre transféré vers une autre association avec succès !';
            break;
        case 'add':
            $message = 'Membre ajouté avec succès !';
            break;
        case 'update':
            $message = 'Membre modifié avec succès !';
            break;
        case 'delete':
            $message = 'Membre supprimé avec succès !';
            break;
        case 'restored':
            $message = 'Membre restauré avec succès !';
            break;
    }
    // Stocker le message en session pour affichage unique
    if (!isset($_SESSION['message_displayed_' . $_GET['success']])) {
        $_SESSION['message_displayed_' . $_GET['success']] = true;
    } else {
        // Message déjà affiché, ne pas le réafficher
        $message = '';
    }
}

if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

// Récupérer les équipes avec leurs entraîneurs pour les transferts
$equipes = [];
try {
    $stmt = $pdo->query("
        SELECT e.*, 
               GROUP_CONCAT(CONCAT(en.prenom, ' ', en.nom, ' (', ee.role, ')') SEPARATOR ', ') as entraineurs
        FROM equipes e
        LEFT JOIN equipe_entraineurs ee ON e.id = ee.equipe_id AND ee.actif = 1
        LEFT JOIN entraineurs en ON ee.entraineur_id = en.id AND en.actif = 1
        WHERE e.actif = 1
        GROUP BY e.id
        ORDER BY e.genre, e.age_min
    ");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des équipes : " . $e->getMessage();
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<div class='alert alert-warning'>DEBUG: Formulaire soumis - Action: " . ($_POST['action'] ?? 'non définie') . "</div>";
    
    if ($action === 'update') {
        echo "<div class='alert alert-warning'>DEBUG: Traitement de la mise à jour pour l'ID: " . ($_POST['id'] ?? 'non défini') . "</div>";
        try {
            // Gestion de la photo avec PhotoManager
            $photo_path = null;
            
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                // Utiliser PhotoManager pour l'upload
                require_once dirname(__DIR__, 2) . '/php/PhotoManager.php';
                $projectRoot = dirname(__DIR__, 2) . '/';
                $photoManager = new PhotoManager($pdo, $projectRoot . 'images/');
                
                $photoResult = $photoManager->uploadPhoto($_FILES['photo'], 'membres');
                if ($photoResult['success']) {
                    $photo_path = $photoResult['filename'];
                    echo "<div class='alert alert-info'>DEBUG: Photo uploadée avec succès - " . htmlspecialchars($photo_path) . "</div>";
                    
                    // Supprimer l'ancienne photo si elle existe
                    $stmt_old = $pdo->prepare("SELECT photo FROM membres WHERE id = ?");
                    $stmt_old->execute([$id]);
                    $old_photo = $stmt_old->fetchColumn();
                    if ($old_photo) {
                        $photoManager->deletePhoto($old_photo, 'membres');
                        echo "<div class='alert alert-info'>DEBUG: Ancienne photo supprimée - " . htmlspecialchars($old_photo) . "</div>";
                    }
                } else {
                    $error = "Erreur upload photo: " . $photoResult['error'];
                    echo "<div class='alert alert-danger'>DEBUG: Erreur upload - " . htmlspecialchars($photoResult['error']) . "</div>";
                }
            }
            
            // Construire la requête UPDATE avec tous les champs (sauf date_adhesion qui est automatique)
            $update_fields = [
                'nom = ?', 'prenom = ?', 'email = ?', 'telephone = ?', 
                'date_naissance = ?', 'genre = ?', 'statut = ?', 'adresse = ?', 'equipe_id = ?',
                'nom_parent = ?', 'prenom_parent = ?', 'telephone_parent = ?', 
                'email_parent = ?', 'profession_parent = ?', 'adresse_parent = ?',
                'poste = ?', 'numero_licence = ?', 'numero_cip = ?', 'lieu_naissance = ?'
            ];
            
            $values = [
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['telephone'],
                $_POST['date_naissance'],
                $_POST['genre'],
                $_POST['statut'],
                $_POST['adresse'],
                $_POST['equipe_id'] ?: null,
                $_POST['nom_parent'],
                $_POST['prenom_parent'],
                $_POST['telephone_parent'],
                $_POST['email_parent'],
                $_POST['profession_parent'],
                $_POST['adresse_parent'],
                $_POST['poste'],
                $_POST['numero_licence'],
                $_POST['numero_cip'],
                $_POST['lieu_naissance']
            ];
            
            // Ajouter la photo si elle a été uploadée
            if ($photo_path !== null) {
                $update_fields[] = 'photo = ?';
                $values[] = $photo_path;
                echo "<div class='alert alert-info'>DEBUG: Photo ajoutée à la requête UPDATE - " . htmlspecialchars($photo_path) . "</div>";
            }
            
            $values[] = $id; // Pour la clause WHERE
            
            echo "<div class='alert alert-info'>DEBUG: Exécution de la requête UPDATE pour l'ID " . $id . "</div>";
            
            $stmt = $pdo->prepare("
                UPDATE membres 
                SET " . implode(', ', $update_fields) . "
                WHERE id = ?
            ");
            $stmt->execute($values);
            
            echo "<div class='alert alert-success'>DEBUG: Mise à jour réussie !</div>";
            
            // Debug: Afficher les valeurs mises à jour
            error_log("Membre mis à jour - ID: $id, Nom: " . $_POST['nom'] . ", Prénom: " . $_POST['prenom']);
            
            $message = 'Membre mis à jour avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
    
    if ($action === 'transfer') {
        // Debug: Logger les données reçues
        error_log("=== TRANSFERT DÉCLENCHÉ ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("ID membre: " . $id);
        
        try {
            $type_transfert = $_POST['type_transfert'];
            $motif = $_POST['motif'] ?? '';
            $traite_par = $_SESSION['admin_nom_complet'] ?? $_SESSION['admin_username'];
            
            error_log("Type transfert: " . $type_transfert);
            
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
                
                
                $message = 'Transfert interne effectué avec succès';
                
                // Redirection après transfert interne
                header('Location: ?section=membres&success=transfer_interne');
                exit;
                
            } else { // transfert externe
                $association_destination = $_POST['association_destination'];
                $ville_destination = $_POST['ville_destination'] ?? '';
                $contact_destination = $_POST['contact_destination'] ?? '';
                
                error_log("=== TRANSFERT EXTERNE ===");
                error_log("Association: " . $association_destination);
                error_log("Ville: " . $ville_destination);
                error_log("Contact: " . $contact_destination);
                
                // Changer le statut du membre
                $stmt = $pdo->prepare("UPDATE membres SET statut = 'transfere' WHERE id = ?");
                $result = $stmt->execute([$id]);
                error_log("UPDATE statut result: " . ($result ? "OK" : "FAILED"));
                error_log("Rows affected: " . $stmt->rowCount());
                
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
                
                $message = 'Transfert externe effectué avec succès';
                
                // Redirection après transfert externe
                error_log("Redirection vers: ?section=membres&success=transfer_externe");
                header('Location: ?section=membres&success=transfer_externe');
                exit;
            }
            
        } catch (Exception $e) {
            $error = "Erreur lors du transfert : " . $e->getMessage();
            error_log("=== ERREUR TRANSFERT ===");
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }
    }
    
    // Le traitement de la radiation se fait maintenant dans le contrôleur
}

// Récupérer un membre spécifique pour modification, transfert ou visualisation
$membre = null;
if ($id && ($action === 'edit' || $action === 'transfer' || $action === 'view' || $action === 'renvoyer' || $action === 'restaurer')) {
    // Debug temporaire
    error_log("DEBUG: Récupération membre - Action: $action, ID: $id");
    error_log("DEBUG: GET params: " . print_r($_GET, true));
    error_log("DEBUG: POST params: " . print_r($_POST, true));
    try {
        $stmt = $pdo->prepare("
            SELECT 
                m.*,
                e.nom as equipe_nom,
                e.categorie as equipe_categorie
            FROM membres m
            LEFT JOIN equipes e ON m.equipe_id = e.id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        $membre = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug détaillé pour identifier le problème
        error_log("=== DEBUG SECTION MEMBRES ===");
        error_log("ID utilisé pour la requête: " . $id);
        error_log("GET id dans section: " . ($_GET['id'] ?? 'non défini'));
        error_log("POST id dans section: " . ($_POST['id'] ?? 'non défini'));
        
        if ($membre) {
            error_log("✅ Membre récupéré - ID: {$membre['id']}, Nom: {$membre['nom']}, Prénom: {$membre['prenom']}, Sexe: {$membre['sexe']}");
        } else {
            error_log("❌ Aucun membre trouvé pour ID: $id");
        }
    } catch (Exception $e) {
        $error = "Erreur lors du chargement du membre : " . $e->getMessage();
        error_log("DEBUG: Erreur récupération membre - " . $e->getMessage());
    }
}

// Récupérer les membres organisés par genre et catégorie
$membresOrganises = [
    'garcons' => [
        'U8-U10' => [],
        'U12-U14' => [],
        'U16-U18' => [],
        'Seniors' => []
    ],
    'filles' => [
        'U8-U10' => [],
        'U12-U14' => [],
        'U16-U18' => [],
        'Seniors' => []
    ]
];

$statistiques = [
    'garcons' => ['total' => 0, 'actif' => 0, 'suspendus' => 0, 'radie' => 0, 'transfere' => 0],
    'filles' => ['total' => 0, 'actif' => 0, 'suspendus' => 0, 'radie' => 0, 'transfere' => 0]
];

try {
    $stmt = $pdo->query("
        SELECT e.*, 
               GROUP_CONCAT(CONCAT(en.prenom, ' ', en.nom, ' (', ee.role, ')') SEPARATOR ', ') as entraineurs
        FROM equipes e
        LEFT JOIN equipe_entraineurs ee ON e.id = ee.equipe_id AND ee.actif = 1
        LEFT JOIN entraineurs en ON ee.entraineur_id = en.id AND en.actif = 1
        WHERE e.actif = 1
        GROUP BY e.id
        ORDER BY e.genre, e.age_min
    ");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des équipes : " . $e->getMessage();
}

// Récupérer les membres organisés par genre et catégorie
$membresOrganises = [
    'garcons' => [
        'U8-U10' => [],
        'U12-U14' => [],
        'U16-U18' => [],
        'Seniors' => []
    ],
    'filles' => [
        'U8-U10' => [],
        'U12-U14' => [],
        'U16-U18' => [],
        'Seniors' => []
    ]
];

try {
    $stmt = $pdo->query("
        SELECT m.*, e.nom as equipe_nom, e.genre as equipe_genre
        FROM membres m
        LEFT JOIN equipes e ON m.equipe_id = e.id
        WHERE m.statut != 'supprime' AND m.statut != 'transfere'
        ORDER BY m.nom ASC, m.prenom ASC
    ");
    $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organiser les membres par genre et catégorie
    foreach ($membres as $membreItem) {
        $age = date_diff(date_create($membreItem['date_naissance']), date_create('today'))->y;
        $categorie = '';
        
        if ($age <= 10) $categorie = 'U8-U10';
        elseif ($age <= 14) $categorie = 'U12-U14';
        elseif ($age <= 18) $categorie = 'U16-U18';
        else $categorie = 'Seniors';
        
        // Normaliser le genre (garcons/garçon -> garcons, filles/fille -> filles)
        $genre_normalise = '';
        if (in_array($membreItem['genre'], ['garcons', 'garçon', 'garcon', 'male', 'm'])) {
            $genre_normalise = 'garcons';
        } elseif (in_array($membreItem['genre'], ['filles', 'fille', 'femelle', 'f', 'Feminin', 'Féminin'])) {
            $genre_normalise = 'filles';
        } else {
            // Si le genre n'est pas reconnu, essayer de le deviner depuis l'équipe
            if ($membreItem['equipe_genre'] === 'filles') {
                $genre_normalise = 'filles';
            } else {
                $genre_normalise = 'garcons'; // Par défaut
            }
        }
        
        $membresOrganises[$genre_normalise][$categorie][] = $membreItem;
    }
} catch (Exception $e) {
    $error = "Erreur lors du chargement des membres : " . $e->getMessage();
    $membres = [];
}

// Statistiques (les transférés ne sont plus dans cette liste)
$stats = [
    'total' => count($membres),
    'actifs' => count(array_filter($membres, fn($m) => $m['statut'] === 'actif')),
    'suspendus' => count(array_filter($membres, fn($m) => $m['statut'] === 'suspendu')),
    'radies' => count(array_filter($membres, fn($m) => $m['statut'] === 'radie'))
];
?>
                
                <!-- Messages -->
                <?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-membres">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        // Nettoyer l'URL immédiatement pour éviter le réaffichage
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, '', url);
        
        // Faire disparaître automatiquement le message de succès après 1 seconde
        setTimeout(function() {
            const successMessage = document.getElementById('success-message-membres');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease-out';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.remove();
                }, 500);
            }
        }, 1000);
    </script>
    <?php 
    // Nettoyer la variable de session après affichage
    if (isset($_GET['success'])) {
        unset($_SESSION['message_displayed_' . $_GET['success']]);
    }
    ?>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                

<!-- Statistiques -->
                <div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <h3 class="mb-1"><?= $stats['total'] ?></h3>
                <p class="mb-0">Total</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <h3 class="mb-1"><?= $stats['actifs'] ?></h3>
                <p class="mb-0">Actifs</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <h3 class="mb-1"><?= $stats['suspendus'] ?></h3>
                <p class="mb-0">Suspendus</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <h3 class="mb-1"><?= $stats['radies'] ?></h3>
                <p class="mb-0">Radiés</p>
            </div>
        </div>
    </div>
</div>

<?php if ($action === 'view' && $membre): ?>
<!-- Modal Voir Membre -->
<div class="modal fade show" id="viewMembreModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du membre</h5>
                <a href="?" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations personnelles</h6>
                        <p><strong>Nom :</strong> <?= htmlspecialchars($membre['nom']) ?></p>
                        <p><strong>Prénom :</strong> <?= htmlspecialchars($membre['prenom']) ?></p>
                        <p><strong>Date de naissance :</strong> <?= date('d/m/Y', strtotime($membre['date_naissance'])) ?></p>
                        <p><strong>Genre :</strong> <?= ucfirst($membre['genre']) ?></p>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($membre['telephone'] ?? 'Non renseigné') ?></p>
                        <p><strong>Email :</strong> <?= htmlspecialchars($membre['email'] ?? 'Non renseigné') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informations sportives</h6>
                        <p><strong>Équipe :</strong> <?= htmlspecialchars($membre['equipe_nom'] ?? 'Non assigné') ?></p>
                        <p><strong>Statut :</strong> <span class="badge bg-<?= $membre['statut'] === 'actif' ? 'success' : ($membre['statut'] === 'transfere' ? 'warning' : 'secondary') ?>"><?= ucfirst($membre['statut']) ?></span></p>
                        <p><strong>Date d'adhésion :</strong> <?= isset($membre['date_adhesion']) && $membre['date_adhesion'] ? date('d/m/Y H:i:s', strtotime($membre['date_adhesion'])) : 'Non renseignée' ?></p>
                    </div>
                </div>
                <?php if ($membre['adresse']): ?>
                <div class="mt-3">
                    <h6>Adresse</h6>
                    <p><?= htmlspecialchars($membre['adresse']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <a href="?" class="btn btn-secondary">Fermer</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($action === 'edit' && $membre): ?>
<!-- Formulaire de modification -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Modifier le membre</h5>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Membres&action=modifier&id=<?= $membre['id'] ?>" onsubmit="console.log('DEBUG: Formulaire soumis pour ID <?= $membre['id'] ?>'); setTimeout(function(){ window.location.href = 'index.php?section=membres'; }, 2000); return true;">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $membre['id'] ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($membre['nom']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($membre['prenom']) ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($membre['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($membre['telephone'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?= $membre['date_naissance'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="genre" class="form-label">Genre</label>
                        <select class="form-control" id="genre" name="genre">
                            <option value="garcon" <?= $membre['genre'] === 'garcon' ? 'selected' : '' ?>>Garçon</option>
                            <option value="fille" <?= $membre['genre'] === 'fille' ? 'selected' : '' ?>>Fille</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="equipe_id" class="form-label">Équipe</label>
                        <select class="form-control" id="equipe_id" name="equipe_id">
                            <option value="">Non assigné</option>
                            <?php foreach ($equipes as $equipe): ?>
                                <option value="<?= $equipe['id'] ?>" <?= $membre['equipe_id'] == $equipe['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($equipe['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                                    </div>
                                    </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-control" id="statut" name="statut">
                            <option value="actif" <?= $membre['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                            <option value="inactif" <?= $membre['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                            <option value="suspendu" <?= $membre['statut'] === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                            <option value="transfere" <?= $membre['statut'] === 'transfere' ? 'selected' : '' ?>>Transféré</option>
                            <option value="radie" <?= $membre['statut'] === 'radie' ? 'selected' : '' ?>>Radié</option>
                        </select>
                                    </div>
                                </div>
                            </div>
            
            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($membre['adresse'] ?? '') ?></textarea>
            </div>
            
            <!-- Informations des parents -->
            <h6 class="mt-4 mb-3">Informations des parents</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom_parent" class="form-label">Nom du parent</label>
                        <input type="text" class="form-control" id="nom_parent" name="nom_parent" value="<?= htmlspecialchars($membre['nom_parent'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="prenom_parent" class="form-label">Prénom du parent</label>
                        <input type="text" class="form-control" id="prenom_parent" name="prenom_parent" value="<?= htmlspecialchars($membre['prenom_parent'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone_parent" class="form-label">Téléphone du parent</label>
                        <input type="tel" class="form-control" id="telephone_parent" name="telephone_parent" value="<?= htmlspecialchars($membre['telephone_parent'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email_parent" class="form-label">Email du parent</label>
                        <input type="email" class="form-control" id="email_parent" name="email_parent" value="<?= htmlspecialchars($membre['email_parent'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="profession_parent" class="form-label">Profession du parent</label>
                        <input type="text" class="form-control" id="profession_parent" name="profession_parent" value="<?= htmlspecialchars($membre['profession_parent'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="adresse_parent" class="form-label">Adresse du parent</label>
                        <textarea class="form-control" id="adresse_parent" name="adresse_parent" rows="2"><?= htmlspecialchars($membre['adresse_parent'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Informations sportives -->
            <h6 class="mt-4 mb-3">Informations sportives</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="poste" class="form-label">Poste</label>
                        <input type="text" class="form-control" id="poste" name="poste" value="<?= htmlspecialchars($membre['poste'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="numero_licence" class="form-label">Numéro de licence</label>
                        <input type="text" class="form-control" id="numero_licence" name="numero_licence" value="<?= htmlspecialchars($membre['numero_licence'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="numero_cip" class="form-label">Numéro CIP</label>
                        <input type="text" class="form-control" id="numero_cip" name="numero_cip" value="<?= htmlspecialchars($membre['numero_cip'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Date d'adhésion</label>
                        <input type="text" class="form-control" value="<?= $membre['date_adhesion'] ? date('d/m/Y H:i:s', strtotime($membre['date_adhesion'])) : 'Non définie' ?>" readonly style="background-color: #f8f9fa;">
                        <small class="text-muted">Cette date est définie automatiquement lors de la validation de l'inscription</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="lieu_naissance" class="form-label">Lieu de naissance</label>
                <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" value="<?= htmlspecialchars($membre['lieu_naissance'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
                <label for="photo" class="form-label">Photo</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                <?php if ($membre['photo']): ?>
                    <small class="text-muted">Photo actuelle : <?= basename($membre['photo']) ?></small>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="loadSection('membres')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($action === 'transfer' && $membre): ?>
<!-- Formulaire de transfert -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Transférer le membre</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Transfert de :</strong> <?= htmlspecialchars($membre['nom'] . ' ' . $membre['prenom']) ?>
        </div>
        <form method="POST" action="mvc_router.php?controller=Membres&action=transfer&id=<?= $membre['id'] ?>&genre=<?= $genre ?>" id="transferForm">
            <input type="hidden" name="action" value="transfer">
            <input type="hidden" name="id" value="<?= $membre['id'] ?>">
            <input type="hidden" name="equipe_origine_id" value="<?= $membre['equipe_id'] ?>">
            
            <div class="mb-3">
                <label class="form-label">Type de transfert *</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type_transfert" id="interne" value="interne" checked>
                    <label class="form-check-label" for="interne">Transfert interne (vers une autre équipe)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type_transfert" id="externe" value="externe">
                    <label class="form-check-label" for="externe">Transfert externe (vers une autre association)</label>
                </div>
            </div>
            
            <!-- Champs pour transfert interne -->
            <div id="transfert-interne">
                <div class="mb-3">
                    <label for="equipe_destination_id" class="form-label">Nouvelle équipe *</label>
                    <select class="form-control" id="equipe_destination_id" name="equipe_destination_id">
                        <option value="">Sélectionner une équipe</option>
                        <?php foreach ($equipes as $equipe): ?>
                            <?php if ($equipe['id'] != $membre['equipe_id']): ?>
                                <option value="<?= $equipe['id'] ?>">
                                    <?= htmlspecialchars($equipe['nom']) ?> (<?= htmlspecialchars($equipe['categorie']) ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Champs pour transfert externe -->
            <div id="transfert-externe" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="association_destination" class="form-label">Association de destination *</label>
                            <input type="text" class="form-control" id="association_destination" name="association_destination" placeholder="Nom de l'association">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ville_destination" class="form-label">Ville *</label>
                            <input type="text" class="form-control" id="ville_destination" name="ville_destination" placeholder="Ville de l'association">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="contact_destination" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="contact_destination" name="contact_destination" placeholder="Email ou téléphone">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="motif" class="form-label">Motif du transfert</label>
                <textarea class="form-control" id="motif" name="motif" rows="3"></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="loadSection('membres')">Annuler</button>
                <button type="submit" class="btn btn-warning">Transférer</button>
            </div>
        </form>
    </div>
</div>

<script>
// Script de transfert - Exécution immédiate pour SPA
(function() {
    function initTransferForm() {
        const radioInterne = document.getElementById('interne');
        const radioExterne = document.getElementById('externe');
        const interne = document.getElementById('transfert-interne');
        const externe = document.getElementById('transfert-externe');
        
        if (radioInterne && radioExterne && interne && externe) {
            console.log('✅ Formulaire de transfert initialisé');
            
            function toggleFields() {
                if (radioInterne.checked) {
                    interne.style.display = 'block';
                    externe.style.display = 'none';
                } else {
                    interne.style.display = 'none';
                    externe.style.display = 'block';
                }
                console.log('Toggle:', radioInterne.checked ? 'interne' : 'externe');
            }
            
            radioInterne.addEventListener('change', toggleFields);
            radioExterne.addEventListener('change', toggleFields);
            toggleFields();
            
            return true;
        }
        return false;
    }
    
    // Essayer immédiatement
    if (!initTransferForm()) {
        // Retry après un délai
        setTimeout(initTransferForm, 100);
    }
})();
</script>

<?php endif; ?>

<?php if ($action === 'restaurer' && $membre): ?>
<!-- Formulaire de restauration -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Restaurer le membre</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <i class="fas fa-user-check me-2"></i>
            Êtes-vous sûr de vouloir restaurer ce membre ? Il redeviendra actif.
        </div>
        
        <form method="POST" action="mvc_router.php?controller=Membres&action=restaurer&id=<?= $membre['id'] ?>&genre=<?= $genre ?>">
            <!-- Debug: Genre = <?= $genre ?>, Action = <?= $action ?>, ID = <?= $id ?> -->
            <input type="hidden" name="action" value="restaurer">
            <input type="hidden" name="id" value="<?= $membre['id'] ?>">
            <input type="hidden" name="genre" value="<?= $genre ?>">
            
            <div class="mb-3">
                <label class="form-label">Membre à restaurer :</label>
                <div class="alert alert-info">
                    <strong><?= htmlspecialchars($membre['nom'] . ' ' . $membre['prenom']) ?></strong><br>
                    <small>ID: <?= htmlspecialchars($membre['id']) ?> | Statut actuel: <span class="badge bg-danger">Radié</span></small>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="button" onclick="loadSection('membres')" class="btn btn-secondary">Annuler</button>
                <button type="submit" class="btn btn-success">✅ Restaurer le membre</button>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>

<?php if ($action === 'renvoyer' && $membre): ?>
<!-- Formulaire de radiation -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Radier le membre</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Êtes-vous sûr de vouloir radier ce membre ? Cette action est irréversible.
        </div>
        
        <form method="POST" action="mvc_router.php?controller=Membres&action=renvoyer&id=<?= $membre['id'] ?>&genre=<?= $genre ?>">
            <!-- Debug: Genre = <?= $genre ?>, Action = <?= $action ?>, ID = <?= $id ?> -->
            <input type="hidden" name="action" value="renvoyer">
            <input type="hidden" name="id" value="<?= $membre['id'] ?>">
            <input type="hidden" name="genre" value="<?= $genre ?>">
            
            <div class="mb-3">
                <label for="motif_renvoi" class="form-label">Motif de la radiation *</label>
                <textarea class="form-control" id="motif_renvoi" name="motif_renvoi" rows="3" required></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="?" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-danger">Radier définitivement</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Bouton Ajouter et Onglets Genre -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <button class="btn btn-success" onclick="loadSection('membres', 'add')">
        <i class="fas fa-plus me-2"></i>Ajouter un membre
    </button>
    
    <ul class="nav nav-tabs" id="genreTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $genre === 'garcons' ? 'active' : '' ?>" id="garcons-tab" data-bs-toggle="tab" data-bs-target="#garcons" type="button" role="tab">
                <i class="fas fa-male me-2"></i>Garçons
            </button>
                    </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $genre === 'filles' ? 'active' : '' ?>" id="filles-tab" data-bs-toggle="tab" data-bs-target="#filles" type="button" role="tab">
                <i class="fas fa-female me-2"></i>Filles
            </button>
                    </li>
                </ul>
</div>

<!-- Contenu des onglets -->
<div class="tab-content" id="genreTabContent">
    <!-- Garçons -->
    <div class="tab-pane fade <?= $genre === 'garcons' ? 'show active' : '' ?>" id="garcons" role="tabpanel">
        <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                    <i class="fas fa-male me-2"></i>Liste des Garçons
                        </h5>
                    </div>
            <div class="card-body">
                        <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center; background-color: #f8f9fa; font-weight: bold;">📸 Photo</th>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Date de naissance</th>
                                <th>Âge</th>
                                <th>Équipe</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                                <tbody>
                            <?php 
                            $membresGarcons = array_merge(
                                $membresOrganises['garcons']['U8-U10'],
                                $membresOrganises['garcons']['U12-U14'],
                                $membresOrganises['garcons']['U16-U18'],
                                $membresOrganises['garcons']['Seniors']
                            );
                            
                            // Trier par ordre alphabétique
                            usort($membresGarcons, function($a, $b) {
                                $result = strcmp($a['nom'], $b['nom']);
                                if ($result === 0) {
                                    $result = strcmp($a['prenom'], $b['prenom']);
                                }
                                return $result;
                            });
                            foreach ($membresGarcons as $membre): 
                                $age = date_diff(date_create($membre['date_naissance']), date_create('today'))->y;
                            ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $photoPath = $membre['photo'];
                                        // Construire le bon chemin selon le format stocké
                                        if ($photoPath && strpos($photoPath, 'uploads/membres/') === 0) {
                                            $fullPhotoPath = dirname(__DIR__, 2) . '/' . $photoPath;
                                            $displayPath = '/ges_asod/' . $photoPath;
                                        } elseif ($photoPath && strpos($photoPath, 'images/membres/') === 0) {
                                            $fullPhotoPath = dirname(__DIR__, 2) . '/' . $photoPath;
                                            $displayPath = '/ges_asod/' . $photoPath;
                                        } elseif ($photoPath) {
                                            // Juste le nom du fichier (format PhotoManager)
                                            $fullPhotoPath = dirname(__DIR__, 2) . '/images/membres/' . $photoPath;
                                            $displayPath = '/ges_asod/images/membres/' . $photoPath;
                                        } else {
                                            $fullPhotoPath = '';
                                            $displayPath = '';
                                        }
                                        
                                        if ($membre['photo'] && file_exists($fullPhotoPath)): ?>
                                            <img src="<?= htmlspecialchars($displayPath) ?>" alt="Photo de <?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?>" class="member-photo rounded-circle">
                                            <!-- DEBUG: <?= htmlspecialchars($displayPath) ?> -->
                                        <?php else: ?>
                                            <div class="photo-placeholder rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $membre['id'] ?></td>
                                    <td><?= htmlspecialchars($membre['nom']) ?></td>
                                    <td><?= htmlspecialchars($membre['prenom']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($membre['date_naissance'])) ?></td>
                                    <td><?= $age ?> ans</td>
                                    <td><?= htmlspecialchars($membre['equipe_nom'] ?? 'Non assigné') ?></td>
                                    <td>
                                        <span class="badge-status status-<?= $membre['statut'] ?: 'non-assigne' ?>">
                                            <?= ucfirst($membre['statut'] ?: 'Non assigné') ?>
                                        </span>
                                    </td>
                                        <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-info btn-voir-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="garcons" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary btn-modifier-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="garcons" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning btn-transfer-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="garcons" title="Transférer">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <?php if ($membre['statut'] === 'radie'): ?>
                                                <button class="btn btn-sm btn-outline-success btn-restaurer-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="garcons" title="Restaurer">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-danger btn-renvoyer-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="garcons" title="Radier">
                                                    <i class="fas fa-user-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Filles -->
    <div class="tab-pane fade <?= $genre === 'filles' ? 'show active' : '' ?>" id="filles" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-female me-2"></i>Liste des Filles
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center; background-color: #f8f9fa; font-weight: bold;">📸 Photo</th>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Date de naissance</th>
                                <th>Âge</th>
                                <th>Équipe</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $membresFilles = array_merge(
                                $membresOrganises['filles']['U8-U10'],
                                $membresOrganises['filles']['U12-U14'],
                                $membresOrganises['filles']['U16-U18'],
                                $membresOrganises['filles']['Seniors']
                            );
                            
                            // Trier par ordre alphabétique
                            usort($membresFilles, function($a, $b) {
                                $result = strcmp($a['nom'], $b['nom']);
                                if ($result === 0) {
                                    $result = strcmp($a['prenom'], $b['prenom']);
                                }
                                return $result;
                            });
                            foreach ($membresFilles as $membre): 
                                $age = date_diff(date_create($membre['date_naissance']), date_create('today'))->y;
                            ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $photoPath = $membre['photo'];
                                        // Construire le bon chemin selon le format stocké
                                        if ($photoPath && strpos($photoPath, 'uploads/membres/') === 0) {
                                            $fullPhotoPath = dirname(__DIR__, 2) . '/' . $photoPath;
                                            $displayPath = '/ges_asod/' . $photoPath;
                                        } elseif ($photoPath && strpos($photoPath, 'images/membres/') === 0) {
                                            $fullPhotoPath = dirname(__DIR__, 2) . '/' . $photoPath;
                                            $displayPath = '/ges_asod/' . $photoPath;
                                        } elseif ($photoPath) {
                                            // Juste le nom du fichier (format PhotoManager)
                                            $fullPhotoPath = dirname(__DIR__, 2) . '/images/membres/' . $photoPath;
                                            $displayPath = '/ges_asod/images/membres/' . $photoPath;
                                        } else {
                                            $fullPhotoPath = '';
                                            $displayPath = '';
                                        }
                                        
                                        if ($membre['photo'] && file_exists($fullPhotoPath)): ?>
                                            <img src="<?= htmlspecialchars($displayPath) ?>" alt="Photo de <?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?>" class="member-photo rounded-circle">
                                            <!-- DEBUG: <?= htmlspecialchars($displayPath) ?> -->
                                        <?php else: ?>
                                            <div class="photo-placeholder rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $membre['id'] ?></td>
                                    <td><?= htmlspecialchars($membre['nom']) ?></td>
                                    <td><?= htmlspecialchars($membre['prenom']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($membre['date_naissance'])) ?></td>
                                    <td><?= $age ?> ans</td>
                                    <td><?= htmlspecialchars($membre['equipe_nom'] ?? 'Non assigné') ?></td>
                                    <td>
                                        <span class="badge-status status-<?= $membre['statut'] ?: 'non-assigne' ?>">
                                            <?= ucfirst($membre['statut'] ?: 'Non assigné') ?>
                                        </span>
                                    </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-info btn-voir-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="filles" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary btn-modifier-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="filles" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning btn-transfer-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="filles" title="Transférer">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <?php if ($membre['statut'] === 'radie'): ?>
                                                <button class="btn btn-sm btn-outline-success btn-restaurer-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="filles" title="Restaurer">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-danger btn-renvoyer-membre" data-membre-id="<?= $membre['id'] ?>" data-genre="filles" title="Radier">
                                                    <i class="fas fa-user-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles spécifiques à la section Membres */
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.badge-status {
    font-size: 0.8rem;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-actif {
    background-color: #28a745;
    color: white;
}

.status-suspendu {
    background-color: #ffc107;
    color: #212529;
}

.status-radie {
    background-color: #dc3545;
    color: white;
}

.member-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.member-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.btn-transfer {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    border: none;
    border-radius: 25px;
    color: #333;
    font-weight: 600;
}

.btn-edit {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    border: none;
    border-radius: 25px;
    color: #333;
    font-weight: 600;
}

.categorie-section {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    border-left: 5px solid #0d6efd;
}
/* Styles pour les photos des membres */
.member-photo {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border: 3px solid #0d6efd;
    border-radius: 50%;
    transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
    display: block;
    margin: 0 auto;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.member-photo:hover {
    transform: scale(1.15);
    border-color: #28a745;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* Placeholder pour les photos manquantes des membres */
.photo-placeholder {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #0d6efd, #6f42c1);
    border: 3px solid #0d6efd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.photo-placeholder:hover {
    transform: scale(1.15);
    border-color: #28a745;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.photo-placeholder i {
    font-size: 1.5rem;
    color: white;
}

/* S'assurer que la colonne Photo est visible */
table th:first-child,
table td:first-child {
    width: 80px !important;
    min-width: 80px !important;
    text-align: center !important;
    background-color: #f8f9fa !important;
    border: 2px solid #dee2e6 !important;
}

/* Améliorer la largeur des colonnes */
table {
    table-layout: fixed;
    width: 100%;
}

table th {
    padding: 12px 8px;
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

table td {
    padding: 10px 8px;
    vertical-align: middle;
}

/* Debug: rendre la colonne Photo très visible */
table th:first-child {
    background-color: #ffc107 !important;
    color: #000 !important;
    font-weight: bold !important;
    font-size: 14px !important;
    padding: 10px !important;
}

table td:first-child {
    background-color: #fff3cd !important;
    border: 2px solid #ffc107 !important;
    padding: 8px !important;
    vertical-align: middle !important;
}

/* Forcer la visibilité de la colonne Photo */
.member-photo, .photo-placeholder {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Gérer le débordement de texte */
table td {
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding: 8px 12px;
}

/* Colonnes spécifiques */
table td:nth-child(2) { max-width: 60px; } /* ID */
table td:nth-child(3) { max-width: 120px; } /* Nom */
table td:nth-child(4) { max-width: 120px; } /* Prénom */
table td:nth-child(5) { max-width: 100px; } /* Date naissance */
table td:nth-child(6) { max-width: 60px; } /* Âge */
table td:nth-child(7) { max-width: 150px; } /* Équipe */
table td:nth-child(8) { max-width: 100px; } /* Statut */
table td:nth-child(9) { max-width: 200px; min-width: 200px; } /* Actions */

/* Améliorer l'affichage des statuts */
.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

/* Améliorer l'affichage des boutons d'action */
.btn-group .btn {
    margin: 0 1px;
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    min-width: 35px;
    height: 30px;
}

/* S'assurer que les boutons d'action ne sont pas coupés */
.table td:nth-child(9) .btn-group {
    display: flex;
    flex-wrap: nowrap;
    gap: 1px;
    justify-content: space-between;
    width: 100%;
}

.table td:nth-child(9) .btn {
    flex-shrink: 0;
    flex: 1;
    max-width: 45px;
}

/* Forcer la largeur de la colonne Actions */
.table th:nth-child(9),
.table td:nth-child(9) {
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
}

/* Styles pour les statuts des membres */
.badge-status {
    padding: 0.4em 0.8em;
    border-radius: 0.375rem;
    font-size: 0.75em;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-actif {
    background-color: #d1edff;
    color: #0c5aa6;
    border: 1px solid #b3d7ff;
}

.status-radie {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status-suspendu {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-transfere {
    background-color: #ffeaa7;
    color: #856404;
    border: 1px solid #ffd32a;
}

.status-non-assigne {
    background-color: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}

/* Gérer les statuts vides ou inconnus */
.status- {
    background-color: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}
</style>

<!-- Modales -->
<!-- Modal Voir Membre -->
<div class="modal fade" id="viewMembreModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du membre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewMembreContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Membre -->
<div class="modal fade" id="membreModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="membreModalTitle">Ajouter un membre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="membreForm">
                <div class="modal-body">
                    <input type="hidden" id="membreId" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_naissance" class="form-label">Date de naissance *</label>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="genre" class="form-label">Genre *</label>
                                <select class="form-control" id="genre" name="genre" required>
                                    <option value="">Sélectionner</option>
                                    <option value="garcons">Garçon</option>
                                    <option value="filles">Fille</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="equipe_id" class="form-label">Équipe</label>
                                <select class="form-control" id="equipe_id" name="equipe_id">
                                    <option value="">Non assigné</option>
                                    <!-- Options chargées dynamiquement -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                                <option value="suspendu">Suspendu</option>
                                <option value="transfere">Transféré</option>
                                    <option value="radie">Radié</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Transférer Membre -->
<div class="modal fade" id="transferMembreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transférer le membre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="transferForm">
                <div class="modal-body">
                    <input type="hidden" id="transferMembreId" name="membre_id">
                    <div class="mb-3">
                        <label for="nouvelle_equipe" class="form-label">Nouvelle équipe *</label>
                        <select class="form-control" id="nouvelle_equipe" name="equipe_id" required>
                            <option value="">Sélectionner une équipe</option>
                            <!-- Options chargées dynamiquement -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="motif_transfert" class="form-label">Motif du transfert</label>
                        <textarea class="form-control" id="motif_transfert" name="motif" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Transférer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmation Radier -->
<div class="modal fade" id="radierMembreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la radiation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="radierForm">
                <div class="modal-body">
                    <input type="hidden" id="radierMembreId" name="membre_id">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Êtes-vous sûr de vouloir radier ce membre ? Cette action est irréversible.
                    </div>
                    <div class="mb-3">
                        <label for="motif_radiation" class="form-label">Motif de la radiation *</label>
                        <textarea class="form-control" id="motif_radiation" name="motif" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Radier définitivement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonctions pour les actions des membres - dans le contexte global
window.viewMembre = function(id) {
    if (typeof loadSection === 'function') {
        loadSection('membres', 'view', id);
    }
};

window.editMembre = function(id) {
    if (typeof loadSection === 'function') {
        loadSection('membres', 'edit', id);
    }
};

window.addMembre = function() {
    if (typeof loadSection === 'function') {
        loadSection('membres', 'add');
    }
};

window.transferMembre = function(id) {
    if (typeof loadSection === 'function') {
        loadSection('membres', 'transfer', id);
    }
};

window.radierMembre = function(id) {
    if (typeof loadSection === 'function') {
        loadSection('membres', 'renvoyer', id);
    }
};

// Script global pour gérer le formulaire de transfert
window.initTransferForm = function() {
    console.log('🔧 Initialisation globale du formulaire de transfert');
    
    // Fonction pour gérer le changement de type de transfert
    function toggleTransfertType() {
        console.log('🔄 toggleTransfertType appelée (version globale)');
        
        const interne = document.getElementById('transfert-interne');
        const externe = document.getElementById('transfert-externe');
        const radioInterne = document.getElementById('interne');
        const radioExterne = document.getElementById('externe');
        
        console.log('📋 Éléments trouvés:', {
            interne: !!interne, 
            externe: !!externe, 
            radioInterne: !!radioInterne, 
            radioExterne: !!radioExterne
        });
        
        if (radioInterne && radioExterne && interne && externe) {
            if (radioInterne.checked) {
                console.log('✅ Transfert interne sélectionné - Affichage des champs internes');
                interne.style.display = 'block';
                externe.style.display = 'none';
            } else if (radioExterne.checked) {
                console.log('✅ Transfert externe sélectionné - Affichage des champs externes');
                interne.style.display = 'none';
                externe.style.display = 'block';
            } else {
                console.log('⚠️ Aucun type sélectionné - Affichage par défaut (interne)');
                interne.style.display = 'block';
                externe.style.display = 'none';
            }
        } else {
            console.log('❌ Éléments non trouvés');
        }
    }
    
    // Rendre la fonction globale
    window.toggleTransfertType = toggleTransfertType;
    
    // Initialiser immédiatement
    console.log('🚀 Initialisation immédiate du toggle (global)');
    toggleTransfertType();
    
    // Ajouter des event listeners
    const radioInterne = document.getElementById('interne');
    const radioExterne = document.getElementById('externe');
    
    if (radioInterne) {
        radioInterne.addEventListener('change', toggleTransfertType);
        console.log('📻 Event listener ajouté sur radio interne (global)');
    }
    
    if (radioExterne) {
        radioExterne.addEventListener('change', toggleTransfertType);
        console.log('📻 Event listener ajouté sur radio externe (global)');
    }
    
    // Initialiser après un délai
    setTimeout(function() {
        console.log('⏰ Initialisation différée du toggle (global)');
        toggleTransfertType();
    }, 500);
};

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM chargé, initialisation globale du formulaire de transfert');
    window.initTransferForm();
});

// Script spécifique pour le formulaire de transfert - s'exécute après le chargement du contenu
setTimeout(function() {
    console.log('🎯 Script spécifique transfert - recherche des éléments');
    
    const interne = document.getElementById('transfert-interne');
    const externe = document.getElementById('transfert-externe');
    const radioInterne = document.getElementById('interne');
    const radioExterne = document.getElementById('externe');
    
    if (interne && externe && radioInterne && radioExterne) {
        console.log('✅ Éléments du formulaire de transfert trouvés');
        
        // Fonction de basculement
        function switchTransferType() {
            console.log('🔄 switchTransferType appelée');
            if (radioInterne.checked) {
                console.log('📋 Affichage transfert interne');
                interne.style.display = 'block';
                externe.style.display = 'none';
            } else if (radioExterne.checked) {
                console.log('📋 Affichage transfert externe');
                interne.style.display = 'none';
                externe.style.display = 'block';
            }
        }
        
        // Initialiser
        switchTransferType();
        
        // Ajouter les event listeners
        radioInterne.addEventListener('change', switchTransferType);
        radioExterne.addEventListener('change', switchTransferType);
        
        console.log('✅ Event listeners ajoutés sur les boutons radio');
    } else {
        console.log('❌ Éléments du formulaire de transfert non trouvés');
    }
}, 1000);

// Ajouter les gestionnaires d'événements pour les boutons d'action
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire pour les boutons de modification
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-modifier-membre')) {
            e.preventDefault();
            const button = e.target.closest('.btn-modifier-membre');
            const membreId = button.getAttribute('data-membre-id');
            if (membreId && typeof window.editMembre === 'function') {
                window.editMembre(membreId);
            }
        }
    });
    
    // Gestionnaire pour les boutons de visualisation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-voir-membre')) {
            e.preventDefault();
            const button = e.target.closest('.btn-voir-membre');
            const membreId = button.getAttribute('data-membre-id');
            if (membreId && typeof window.viewMembre === 'function') {
                window.viewMembre(membreId);
            }
        }
    });
    
    // Gestionnaire pour les boutons de transfert
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-transfer-membre')) {
            e.preventDefault();
            const button = e.target.closest('.btn-transfer-membre');
            const membreId = button.getAttribute('data-membre-id');
            if (membreId && typeof window.transferMembre === 'function') {
                window.transferMembre(membreId);
            }
        }
    });
    
    // Gestionnaire pour les boutons de suppression
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-supprimer-membre')) {
            e.preventDefault();
            const button = e.target.closest('.btn-supprimer-membre');
            const membreId = button.getAttribute('data-membre-id');
            if (membreId && typeof window.deleteMembre === 'function') {
                window.deleteMembre(membreId);
            }
        }
    });
    
    // Gestionnaire pour les boutons de restauration
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-restaurer-membre')) {
            e.preventDefault();
            const button = e.target.closest('.btn-restaurer-membre');
            const membreId = button.getAttribute('data-membre-id');
            if (membreId && typeof window.restoreMembre === 'function') {
                window.restoreMembre(membreId);
            }
        }
    });
});

// Script membres chargé
</script>

