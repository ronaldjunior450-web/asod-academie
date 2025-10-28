<?php
// Section Formations - Gestion des formations Gmail Style
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$message = '';
$error = '';
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;

// Gestion des messages flash
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $message = 'Formation ajoutée avec succès !';
            break;
        case 'updated':
            $message = 'Formation modifiée avec succès !';
            break;
        case 'deleted':
            $message = 'Formation supprimée avec succès !';
            break;
    }
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = 'success';
    header('Location: ../index.php?section=formations');
    exit;
}

if (isset($_SESSION['flash_message'])) {
    if ($_SESSION['flash_type'] === 'success') {
        $message = $_SESSION['flash_message'];
    }
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

// Le traitement POST est maintenant géré par le contrôleur MVC

// Récupérer une formation spécifique pour modification ou visualisation
$formation = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM formations WHERE id = ?");
        $stmt->execute([$id]);
        $formation = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement de la formation : " . $e->getMessage();
    }
}

// Récupérer toutes les formations
try {
    $stmt = $pdo->query("
        SELECT * FROM formations 
        ORDER BY id DESC
    ");
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des formations : " . $e->getMessage();
    $formations = [];
}

// Statistiques des formations
$stats = [
    'total' => count($formations),
    'planifiees' => count(array_filter($formations, fn($f) => $f['statut'] === 'planifiee')),
    'en_cours' => count(array_filter($formations, fn($f) => $f['statut'] === 'en_cours')),
    'terminees' => count(array_filter($formations, fn($f) => $f['statut'] === 'terminee')),
    'annulees' => count(array_filter($formations, fn($f) => $f['statut'] === 'annulee'))
];
?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-formations">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, '', url);
        
        setTimeout(function() {
            const successMessage = document.getElementById('success-message-formations');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease-out';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.remove();
                }, 500);
            }
        }, 1000);
    </script>
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
            <i class="fas fa-graduation-cap me-2"></i>
            Gestion des Formations
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="gmail-btn" onclick="loadSection('formations', 'add')">
                <i class="fas fa-plus"></i>
                Nouvelle formation
            </button>
        </div>
    </div>
    <div class="gmail-card-body">
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['total'] ?></h3>
                        <p class="text-muted mb-0">Total formations</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-calendar fa-2x text-info"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['planifiees'] ?></h3>
                        <p class="text-muted mb-0">Planifiées</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-play fa-2x text-warning"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['en_cours'] ?></h3>
                        <p class="text-muted mb-0">En cours</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-check fa-2x text-success"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['terminees'] ?></h3>
                        <p class="text-muted mb-0">Terminées</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'view' && $formation): ?>
        <!-- Modal Voir Formation -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails de la formation</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('formations')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($formation['titre']) ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-<?= $formation['statut'] === 'actif' ? 'success' : ($formation['statut'] === 'planifiee' ? 'info' : ($formation['statut'] === 'en_cours' ? 'warning' : ($formation['statut'] === 'terminee' ? 'dark' : ($formation['statut'] === 'annulee' ? 'danger' : 'secondary')))) ?> me-2">
                                <?= ucfirst($formation['statut'] ?: 'Non défini') ?>
                            </span>
                        </div>
                        <div class="content">
                            <p><?= nl2br(htmlspecialchars($formation['description'])) ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6><i class="fas fa-graduation-cap me-2"></i>Niveau</h6>
                            <p><?= htmlspecialchars($formation['niveau'] ?? '') ?></p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-clock me-2"></i>Durée</h6>
                            <p><?= htmlspecialchars($formation['duree'] ?? '') ?></p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-money-bill me-2"></i>Coût</h6>
                            <p><?= number_format($formation['cout_formation'] ?? 0, 0, ',', ' ') ?> FCFA</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-tag me-2"></i>Type</h6>
                            <p><?= htmlspecialchars($formation['type_formation'] ?? '') ?></p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-users me-2"></i>Places</h6>
                            <p><?= $formation['places_disponibles'] ?? 0 ?> places disponibles</p>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('formations', 'edit', <?= $formation['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $formation): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier la formation</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Formations&action=modifier&id=<?= $formation['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" 
                               value="<?= htmlspecialchars($formation['titre']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($formation['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="niveau" class="form-label">Niveau *</label>
                                <input type="text" class="form-control" id="niveau" name="niveau" 
                                       value="<?= htmlspecialchars($formation['niveau'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duree" class="form-label">Durée *</label>
                                <input type="text" class="form-control" id="duree" name="duree" 
                                       value="<?= htmlspecialchars($formation['duree'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cout_formation" class="form-label">Coût formation *</label>
                                <input type="number" class="form-control" id="cout_formation" name="cout_formation" 
                                       value="<?= $formation['cout_formation'] ?? 0 ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="places_disponibles" class="form-label">Places disponibles *</label>
                                <input type="number" class="form-control" id="places_disponibles" name="places_disponibles" 
                                       value="<?= $formation['places_disponibles'] ?? 0 ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_formation" class="form-label">Type de formation *</label>
                                <select class="form-control" id="type_formation" name="type_formation" required>
                                    <option value="technique" <?= ($formation['type_formation'] ?? '') === 'technique' ? 'selected' : '' ?>>Technique</option>
                                    <option value="physique" <?= ($formation['type_formation'] ?? '') === 'physique' ? 'selected' : '' ?>>Physique</option>
                                    <option value="mentale" <?= ($formation['type_formation'] ?? '') === 'mentale' ? 'selected' : '' ?>>Mentale</option>
                                    <option value="tactique" <?= ($formation['type_formation'] ?? '') === 'tactique' ? 'selected' : '' ?>>Tactique</option>
                                    <option value="complete" <?= ($formation['type_formation'] ?? '') === 'complete' ? 'selected' : '' ?>>Complète</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="actif" <?= $formation['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                                    <option value="planifiee" <?= $formation['statut'] === 'planifiee' ? 'selected' : '' ?>>Planifiée</option>
                                    <option value="en_cours" <?= $formation['statut'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                                    <option value="terminee" <?= $formation['statut'] === 'terminee' ? 'selected' : '' ?>>Terminée</option>
                                    <option value="annulee" <?= $formation['statut'] === 'annulee' ? 'selected' : '' ?>>Annulée</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('formations')">
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

        <?php if ($action === 'add'): ?>
        <!-- Formulaire d'ajout -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Nouvelle formation</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Formations&action=ajouter">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="niveau" class="form-label">Niveau *</label>
                                <input type="text" class="form-control" id="niveau" name="niveau" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duree" class="form-label">Durée *</label>
                                <input type="text" class="form-control" id="duree" name="duree" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cout_formation" class="form-label">Coût formation *</label>
                                <input type="number" class="form-control" id="cout_formation" name="cout_formation" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="places_disponibles" class="form-label">Places disponibles *</label>
                                <input type="number" class="form-control" id="places_disponibles" name="places_disponibles" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_formation" class="form-label">Type de formation *</label>
                        <select class="form-control" id="type_formation" name="type_formation" required>
                            <option value="">Sélectionner un type</option>
                            <option value="technique">Technique</option>
                            <option value="physique">Physique</option>
                            <option value="mentale">Mentale</option>
                            <option value="tactique">Tactique</option>
                            <option value="complete">Complète</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-control" id="statut" name="statut">
                            <option value="actif" selected>Actif</option>
                            <option value="planifiee">Planifiée</option>
                            <option value="en_cours">En cours</option>
                            <option value="terminee">Terminée</option>
                            <option value="annulee">Annulée</option>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('formations')">
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

        <!-- Liste des formations -->
        <?php if ($action === 'list' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des formations</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterFormations(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="actif">Actives</option>
                        <option value="planifiee">Planifiées</option>
                        <option value="en_cours">En cours</option>
                        <option value="terminee">Terminées</option>
                        <option value="annulee">Annulées</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Niveau</th>
                                <th>Durée</th>
                                <th>Coût</th>
                                <th>Type</th>
                                <th>Places</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formations as $formation): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($formation['titre']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($formation['niveau'] ?? '') ?></td>
                                <td><?= htmlspecialchars($formation['duree'] ?? '') ?></td>
                                <td><?= number_format($formation['cout_formation'] ?? 0, 0, ',', ' ') ?> FCFA</td>
                                <td><?= htmlspecialchars($formation['type_formation'] ?? '') ?></td>
                                <td><?= $formation['places_disponibles'] ?? 0 ?></td>
                                <td>
                                    <span class="badge bg-<?= $formation['statut'] === 'actif' ? 'success' : ($formation['statut'] === 'planifiee' ? 'info' : ($formation['statut'] === 'en_cours' ? 'warning' : ($formation['statut'] === 'terminee' ? 'dark' : ($formation['statut'] === 'annulee' ? 'danger' : 'secondary')))) ?>">
                                        <?= ucfirst($formation['statut'] ?: 'Non défini') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('formations', 'view', <?= $formation['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="loadSection('formations', 'edit', <?= $formation['id'] ?>)" 
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')) { loadSection('formations', 'delete', <?= $formation['id'] ?>) }" 
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
// Fonction de filtrage des formations
function filterFormations(statut) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const statusBadge = row.querySelector('.badge');
        if (statusBadge) {
            const badgeText = statusBadge.textContent.toLowerCase().trim();
            const statutValue = statut.toLowerCase().trim();
            
            if (statut === '' || badgeText === statutValue) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}
</script>
