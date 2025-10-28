<?php
// Section Paiements - Gestion des paiements Gmail Style
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Messages flash
$message = '';
$error = '';

// Gestion des messages flash
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $message = 'Paiement enregistré avec succès';
            break;
        case 'updated':
            $message = 'Paiement mis à jour avec succès';
            break;
        case 'deleted':
            $message = 'Paiement supprimé avec succès';
            break;
        case 'validated':
            $message = 'Paiement validé avec succès';
            break;
        case 'cancelled':
            $message = 'Paiement annulé avec succès';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'add_failed':
            $error = 'Erreur lors de l\'enregistrement du paiement';
            break;
        case 'update_failed':
            $error = 'Erreur lors de la mise à jour du paiement';
            break;
        case 'delete_failed':
            $error = 'Erreur lors de la suppression du paiement';
            break;
        case 'validation_failed':
            $error = 'Erreur lors de la validation du paiement';
            break;
        case 'cancel_failed':
            $error = 'Erreur lors de l\'annulation du paiement';
            break;
    }
}

// Récupérer un paiement spécifique pour modification ou visualisation
$paiement = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, m.nom, m.prenom, m.email
            FROM paiements p
            LEFT JOIN membres m ON p.membre_id = m.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $paiement = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement du paiement : " . $e->getMessage();
    }
}

// Récupérer tous les paiements
try {
    $stmt = $pdo->query("
        SELECT p.*, m.nom, m.prenom, m.email
        FROM paiements p
        LEFT JOIN membres m ON p.membre_id = m.id
        ORDER BY p.date_paiement DESC
    ");
    $paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des paiements : " . $e->getMessage();
    $paiements = [];
}

// Récupérer tous les membres pour les formulaires
try {
    $stmt = $pdo->query("SELECT id, nom, prenom, email FROM membres WHERE statut = 'actif' ORDER BY nom");
    $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $membres = [];
}

// Statistiques des paiements
$stats = [
    'total' => count($paiements),
    'valides' => count(array_filter($paiements, fn($p) => $p['statut'] === 'valide')),
    'en_attente' => count(array_filter($paiements, fn($p) => $p['statut'] === 'en_attente')),
    'annules' => count(array_filter($paiements, fn($p) => $p['statut'] === 'annule')),
    'montant_total' => array_sum(array_column(array_filter($paiements, fn($p) => $p['statut'] === 'valide'), 'montant'))
];

// Types de paiement
$types_paiement = [
    'cotisation' => 'Cotisation annuelle',
    'equipement' => 'Équipement',
    'tournoi' => 'Tournoi',
    'formation' => 'Formation',
    'autre' => 'Autre'
];
?>

<!-- Messages Flash -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="flash-message">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        // Masquer automatiquement le message après 1 seconde
        setTimeout(function() {
            const alert = document.getElementById('flash-message');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }
        }, 1000);
    </script>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="flash-error">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        // Masquer automatiquement le message d'erreur après 3 secondes
        setTimeout(function() {
            const alert = document.getElementById('flash-error');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }
        }, 3000);
    </script>
<?php endif; ?>

<!-- Header de la section -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-credit-card me-2"></i>
            Gestion des Paiements
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="gmail-btn" onclick="loadSection('paiements', 'add')">
                <i class="fas fa-plus"></i>
                Nouveau paiement
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
                            <i class="fas fa-receipt fa-2x text-primary"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['total'] ?></h3>
                        <p class="text-muted mb-0">Total paiements</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-check fa-2x text-success"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['valides'] ?></h3>
                        <p class="text-muted mb-0">Validés</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['en_attente'] ?></h3>
                        <p class="text-muted mb-0">En attente</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-money-bill-wave fa-2x text-info"></i>
                        </div>
                        <h3 class="mb-1"><?= number_format($stats['montant_total'], 0, ',', ' ') ?> F CFA</h3>
                        <p class="text-muted mb-0">Montant total</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'view' && $paiement): ?>
        <!-- Modal Voir Paiement -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails du paiement</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('paiements')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Paiement #<?= $paiement['id'] ?></h3>
                        <p><strong>Membre :</strong> <?= htmlspecialchars($paiement['prenom'] . ' ' . $paiement['nom']) ?></p>
                        <p><strong>Email :</strong> <?= htmlspecialchars($paiement['email']) ?></p>
                        <p><strong>Montant :</strong> <?= number_format($paiement['montant'], 0, ',', ' ') ?> F CFA</p>
                        <p><strong>Type :</strong> <?= $types_paiement[$paiement['type_paiement']] ?? ucfirst($paiement['type_paiement']) ?></p>
                        <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?></p>
                        <p><strong>Statut :</strong> 
                            <span class="badge bg-<?= $paiement['statut'] === 'valide' ? 'success' : ($paiement['statut'] === 'en_attente' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($paiement['statut']) ?>
                            </span>
                        </p>
                        <?php if ($paiement['reference']): ?>
                        <p><strong>Référence :</strong> <?= htmlspecialchars($paiement['reference']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('paiements', 'edit', <?= $paiement['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                            <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('paiements')">
                                <i class="fas fa-list"></i>
                                Voir tous les paiements
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $paiement): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier le paiement</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Paiements&action=modifier&id=<?= $paiement['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="membre_id" class="form-label">Membre *</label>
                        <select class="form-control" id="membre_id" name="membre_id" required>
                            <?php foreach ($membres as $membre): ?>
                            <option value="<?= $membre['id'] ?>" <?= $paiement['membre_id'] == $membre['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?> (<?= htmlspecialchars($membre['email']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant (F CFA) *</label>
                                <input type="number" step="1" class="form-control" id="montant" name="montant" 
                                       value="<?= $paiement['montant'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_paiement" class="form-label">Type de paiement *</label>
                                <select class="form-control" id="type_paiement" name="type_paiement" required>
                                    <?php foreach ($types_paiement as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $paiement['type_paiement'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_paiement" class="form-label">Date de paiement *</label>
                                <input type="date" class="form-control" id="date_paiement" name="date_paiement" 
                                       value="<?= date('Y-m-d', strtotime($paiement['date_paiement'])) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="valide" <?= $paiement['statut'] === 'valide' ? 'selected' : '' ?>>Validé</option>
                                    <option value="en_attente" <?= $paiement['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                    <option value="annule" <?= $paiement['statut'] === 'annule' ? 'selected' : '' ?>>Annulé</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" class="form-control" id="reference" name="reference" 
                               value="<?= htmlspecialchars($paiement['reference']) ?>">
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('paiements')">
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
                <h5 class="gmail-card-title mb-0">Nouveau paiement</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Paiements&action=ajouter">
                    
                    <div class="mb-3">
                        <label for="membre_id" class="form-label">Membre *</label>
                        <select class="form-control" id="membre_id" name="membre_id" required>
                            <option value="">Sélectionner un membre</option>
                            <?php foreach ($membres as $membre): ?>
                            <option value="<?= $membre['id'] ?>">
                                <?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?> (<?= htmlspecialchars($membre['email']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant (F CFA) *</label>
                                <input type="number" step="1" class="form-control" id="montant" name="montant" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_paiement" class="form-label">Type de paiement *</label>
                                <select class="form-control" id="type_paiement" name="type_paiement" required>
                                    <option value="">Sélectionner un type</option>
                                    <?php foreach ($types_paiement as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_paiement" class="form-label">Date de paiement *</label>
                                <input type="date" class="form-control" id="date_paiement" name="date_paiement" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('paiements')">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </button>
                        <button type="submit" class="gmail-btn">
                            <i class="fas fa-plus"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Liste des paiements -->
        <?php if ($action === 'list' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des paiements</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterPaiements(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="valide">Validés</option>
                        <option value="en_attente">En attente</option>
                        <option value="annule">Annulés</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Membre</th>
                                <th>Montant</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Référence</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paiements as $paiement): ?>
                            <tr>
                                <td>#<?= $paiement['id'] ?></td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($paiement['prenom'] . ' ' . $paiement['nom']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($paiement['email']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= number_format($paiement['montant'], 0, ',', ' ') ?> F CFA</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $types_paiement[$paiement['type_paiement']] ?? ucfirst($paiement['type_paiement']) ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($paiement['date_paiement'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $paiement['statut'] === 'valide' ? 'success' : ($paiement['statut'] === 'en_attente' ? 'warning' : 'danger') ?>">
                                        <?= ucfirst($paiement['statut']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($paiement['reference'] ?? '') ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('paiements', 'view', <?= $paiement['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="loadSection('paiements', 'edit', <?= $paiement['id'] ?>)" 
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')) { window.location.href='mvc_router.php?controller=Paiements&action=supprimer&id=<?= $paiement['id'] ?>' }" 
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
// Fonction de filtrage des paiements
function filterPaiements(statut) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const statusBadge = row.querySelector('.badge');
        if (statut === '' || statusBadge.textContent.toLowerCase().includes(statut)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
