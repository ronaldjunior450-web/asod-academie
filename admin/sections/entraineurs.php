<?php
// Section Entraîneurs - Gestion des entraîneurs Gmail Style
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$message = '';
$error = '';
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;

// Gestion des messages de succès et d'erreur
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'add':
            $message = "Entraîneur ajouté avec succès !";
            break;
        case 'updated':
            $message = "Entraîneur modifié avec succès !";
            break;
        case 'delete':
            $message = "Entraîneur supprimé avec succès !";
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
    switch ($_GET['error']) {
        case 'has_teams':
            $error = "Impossible de supprimer cet entraîneur car il est associé à une ou plusieurs équipes.";
            break;
        case 'delete_failed':
            $error = "Erreur lors de la suppression de l'entraîneur.";
            break;
        default:
            $error = "Erreur : " . htmlspecialchars($_GET['error']);
    }
}


// Traitement des actions GET (pour l'affichage des formulaires)
// La suppression est maintenant gérée par le contrôleur

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO entraineurs (nom, prenom, email, telephone, specialite, statut) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['telephone'],
                $_POST['specialite'],
                $_POST['statut'] ?? 'actif'
            ]);
            
            $message = 'Entraîneur ajouté avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
    
    if ($action === 'update') {
        try {
            $stmt = $pdo->prepare("
                UPDATE entraineurs 
                SET nom = ?, prenom = ?, email = ?, telephone = ?, specialite = ?, statut = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['telephone'],
                $_POST['specialite'],
                $_POST['statut'],
                $id
            ]);
            
            $message = 'Entraîneur modifié avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}

// Récupérer un entraîneur spécifique pour modification ou visualisation
$entraineur = null;
if ($id && ($action === 'modifier' || $action === 'voir')) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM entraineurs WHERE id = ?");
        $stmt->execute([$id]);
        $entraineur = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement de l'entraîneur : " . $e->getMessage();
    }
}

// Récupérer tous les entraîneurs avec leurs équipes
try {
    $stmt = $pdo->query("
        SELECT e.*, 
               GROUP_CONCAT(eq.nom SEPARATOR ', ') as equipes_noms
        FROM entraineurs e
        LEFT JOIN equipes eq ON FIND_IN_SET(e.id, eq.entraineur_id)
        GROUP BY e.id
        ORDER BY e.nom ASC, e.prenom ASC
    ");
    $entraineurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des entraîneurs : " . $e->getMessage();
    $entraineurs = [];
}

// Récupérer toutes les équipes pour les formulaires
try {
    $stmt = $pdo->query("SELECT id, nom, genre, categorie FROM equipes WHERE actif = 1 ORDER BY nom ASC");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $equipes = [];
}
?>

<!-- Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-entraineurs">
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
                            const successMessage = document.getElementById('success-message-entraineurs');
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

<!-- Header de la section -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-user-tie me-2"></i>
            Gestion des Entraîneurs
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="gmail-btn" onclick="loadSection('entraineurs', 'ajouter')">
                <i class="fas fa-plus"></i>
                Nouvel entraîneur
            </button>
        </div>
    </div>
    <div class="gmail-card-body">
        <?php if ($action === 'voir' && $entraineur): ?>
        <!-- Modal Voir Entraîneur -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails de l'entraîneur</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('entraineurs', 'liste')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php 
                        $photoPath = $entraineur['photo'];
                        $fullPhotoPath = '';
                        $displayPath = '';
                        
                        if ($photoPath && strpos($photoPath, 'uploads/entraineurs/') === 0) {
                            $fullPhotoPath = dirname(__DIR__, 2) . '/' . $photoPath;
                            $displayPath = '/ges_asod/' . $photoPath;
                        } elseif ($photoPath && strpos($photoPath, 'images/entraineurs/') === 0) {
                            $fullPhotoPath = dirname(__DIR__, 2) . '/' . $photoPath;
                            $displayPath = '/ges_asod/' . $photoPath;
                        } elseif ($photoPath) {
                            $fullPhotoPath = dirname(__DIR__, 2) . '/images/entraineurs/' . $photoPath;
                            $displayPath = '/ges_asod/images/entraineurs/' . $photoPath;
                        } else {
                            $fullPhotoPath = dirname(__DIR__, 2) . '/images/entraineurs/default-avatar.svg';
                            $displayPath = '/ges_asod/images/entraineurs/default-avatar.svg';
                        }
                        
                        if (file_exists($fullPhotoPath)): ?>
                        <div class="mb-3">
                            <img src="<?= htmlspecialchars($displayPath) ?>" 
                                 alt="<?= htmlspecialchars($entraineur['prenom'] . ' ' . $entraineur['nom']) ?>" 
                                 class="img-fluid rounded">
                        </div>
                        <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center mb-3" 
                             style="height: 200px;">
                            <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($entraineur['prenom'] . ' ' . $entraineur['nom']) ?></h3>
                        <p><strong>Spécialité :</strong> <?= htmlspecialchars($entraineur['specialite']) ?></p>
                        <p><strong>Email :</strong> <?= htmlspecialchars($entraineur['email']) ?></p>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($entraineur['telephone']) ?></p>
                        <p><strong>Statut :</strong> 
                            <span class="badge bg-<?= $entraineur['statut'] === 'actif' ? 'success' : ($entraineur['statut'] === 'suspendu' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($entraineur['statut']) ?>
                            </span>
                        </p>
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('entraineurs', 'modifier', <?= $entraineur['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                <?php endif; ?>

        <?php if ($action === 'modifier' && $entraineur): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier l'entraîneur</h5>
                                    </div>
            <div class="gmail-card-body">
                <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Entraineurs&action=modifier&id=<?= $entraineur['id'] ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $entraineur['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?= htmlspecialchars($entraineur['nom']) ?>" required>
                                </div>
                                </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                       value="<?= htmlspecialchars($entraineur['prenom']) ?>" required>
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($entraineur['email']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?= htmlspecialchars($entraineur['telephone']) ?>" required>
                            </div>
                        </div>
                                    </div>
    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="specialite" class="form-label">Spécialité *</label>
                                <select class="form-control" id="specialite" name="specialite" required>
                                    <option value="">Sélectionner une spécialité</option>
                                    <option value="technique" <?= $entraineur['specialite'] === 'technique' ? 'selected' : '' ?>>Technique</option>
                                    <option value="physique" <?= $entraineur['specialite'] === 'physique' ? 'selected' : '' ?>>Physique</option>
                                    <option value="gardien" <?= $entraineur['specialite'] === 'gardien' ? 'selected' : '' ?>>Gardien</option>
                                    <option value="general" <?= $entraineur['specialite'] === 'general' ? 'selected' : '' ?>>Général</option>
                                </select>
                                </div>
                                </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="actif" <?= $entraineur['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                                    <option value="suspendu" <?= $entraineur['statut'] === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                                    <option value="radie" <?= $entraineur['statut'] === 'radie' ? 'selected' : '' ?>>Radié</option>
                                </select>
                            </div>
                        </div>
                    </div>
    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <?php if ($entraineur['photo']): ?>
                            <small class="text-muted">Photo actuelle : <?= basename($entraineur['photo']) ?></small>
                        <?php endif; ?>
                                    </div>
    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('entraineurs', 'liste')">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </button>
                        <button type="submit" class="gmail-btn">
                            <i class="fas fa-save"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
                                </div>
        <?php endif; ?>

        <?php if ($action === 'ajouter'): ?>
        <!-- Formulaire d'ajout -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Nouvel entraîneur</h5>
                                </div>
            <div class="gmail-card-body">
                <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Entraineurs&action=ajouter">
                    <input type="hidden" name="action" value="add">
                    
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
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" required>
                            </div>
                        </div>
                                    </div>
    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="specialite" class="form-label">Spécialité *</label>
                                <select class="form-control" id="specialite" name="specialite" required>
                                    <option value="">Sélectionner une spécialité</option>
                                    <option value="technique">Technique</option>
                                    <option value="physique">Physique</option>
                                    <option value="gardien">Gardien</option>
                                    <option value="general">Général</option>
                                </select>
                                </div>
                                </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="actif" selected>Actif</option>
                                    <option value="suspendu">Suspendu</option>
                                    <option value="radie">Radié</option>
                                </select>
                            </div>
                        </div>
                    </div>
    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                </div>
    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('entraineurs', 'liste')">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </button>
                        <button type="submit" class="gmail-btn">
                            <i class="fas fa-plus"></i>
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Liste des entraîneurs -->
        <?php if (!in_array($action, ['modifier', 'voir', 'ajouter'])): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des entraîneurs</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterEntraineurs(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="actif">Actifs</option>
                        <option value="suspendu">Suspendus</option>
                        <option value="radie">Radiés</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                        <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                                    <tr>
                                <th>Photo</th>
                                        <th>Nom</th>
                                <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Spécialité</th>
                                <th>Équipes</th>
                                <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($entraineurs as $entraineur): ?>
                                    <tr>
                                        <td>
                                    <?php 
                                    $photoPath = $entraineur['photo'];
                                    $fullPhotoPath = '';
                                    $displayPath = '';
                                    
                                    if ($photoPath && strpos($photoPath, 'uploads/entraineurs/') === 0) {
                                        $fullPhotoPath = dirname(__DIR__, 2) . '/' . $photoPath;
                                        $displayPath = '/ges_asod/' . $photoPath;
                                    } elseif ($photoPath && strpos($photoPath, 'images/entraineurs/') === 0) {
                                        $fullPhotoPath = dirname(__DIR__, 2) . '/' . $photoPath;
                                        $displayPath = '/ges_asod/' . $photoPath;
                                    } elseif ($photoPath) {
                                        $fullPhotoPath = dirname(__DIR__, 2) . '/images/entraineurs/' . $photoPath;
                                        $displayPath = '/ges_asod/images/entraineurs/' . $photoPath;
                                    } else {
                                        $fullPhotoPath = dirname(__DIR__, 2) . '/images/entraineurs/default-avatar.svg';
                                        $displayPath = '/ges_asod/images/entraineurs/default-avatar.svg';
                                    }
                                    
                                    if (file_exists($fullPhotoPath)): ?>
                                        <img src="<?= htmlspecialchars($displayPath) ?>" 
                                             alt="<?= htmlspecialchars($entraineur['prenom'] . ' ' . $entraineur['nom']) ?>" 
                                             class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-user text-muted"></i>
                                                </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($entraineur['prenom'] . ' ' . $entraineur['nom']) ?></strong>
                                        </td>
                                <td><?= htmlspecialchars($entraineur['email']) ?></td>
                                        <td><?= htmlspecialchars($entraineur['telephone']) ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= htmlspecialchars($entraineur['specialite']) ?></span>
                                        </td>
                                <td>
                                    <small><?= htmlspecialchars($entraineur['equipes_noms'] ?? 'Aucune équipe') ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-statut bg-<?= $entraineur['statut'] === 'actif' ? 'success' : ($entraineur['statut'] === 'suspendu' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($entraineur['statut']) ?>
                                    </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('entraineurs', 'voir', <?= $entraineur['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="loadSection('entraineurs', 'modifier', <?= $entraineur['id'] ?>)" 
                                                title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet entraîneur ?')) { loadSection('entraineurs', 'supprimer', <?= $entraineur['id'] ?>) }" 
                                                title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
    
<script>
// Fonction de filtrage des entraîneurs
if (typeof filterEntraineurs === 'undefined') {
function filterEntraineurs(statut) {
    console.log('🔍 Filtrage des entraîneurs avec statut:', statut);
    
    const rows = document.querySelectorAll('tbody tr');
    console.log('📊 Nombre de lignes trouvées:', rows.length);
    
    rows.forEach((row, index) => {
        const statusBadge = row.querySelector('.badge-statut');
        if (statusBadge) {
            const badgeText = statusBadge.textContent.toLowerCase().trim();
            console.log(`📋 Ligne ${index + 1}: Badge texte = "${badgeText}"`);
            
            let shouldShow = false;
            
            if (statut === '') {
                shouldShow = true;
                console.log(`✅ Ligne ${index + 1}: Affichée (tous les statuts)`);
            } else {
                shouldShow = badgeText.includes(statut);
                console.log(`🔍 Ligne ${index + 1}: Comparaison "${badgeText}" avec "${statut}" = ${shouldShow}`);
            }
            
            row.style.display = shouldShow ? '' : 'none';
        } else {
            console.log(`❌ Ligne ${index + 1}: Pas de badge trouvé`);
        }
    });
    
    console.log('✅ Filtrage terminé');
}

}
</script>

<script>
// Initialiser le filtrage immédiatement
setTimeout(function() {
    const select = document.querySelector('select[onchange*="filterEntraineurs"]');
    if (select) {
        console.log('🔧 Initialisation du filtrage avec valeur:', select.value);
        filterEntraineurs(select.value);
    }
}, 100);
</script>
