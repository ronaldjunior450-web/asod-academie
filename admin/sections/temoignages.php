<?php
// Section Témoignages - Gestion des témoignages Gmail Style
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
        case 'deleted': $message = 'Témoignage supprimé avec succès !'; break;
        case 'published': $message = 'Témoignage publié avec succès !'; break;
        case 'rejected': $message = 'Témoignage rejeté avec succès !'; break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'delete_failed': $error = 'Erreur lors de la suppression du témoignage'; break;
        case 'publish_failed': $error = 'Erreur lors de la publication du témoignage'; break;
        case 'reject_failed': $error = 'Erreur lors du rejet du témoignage'; break;
    }
}

// Le traitement des actions est maintenant géré par le contrôleur MVC

// Récupérer un témoignage spécifique pour modification ou visualisation
$temoignage = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM temoignages WHERE id = ?");
        $stmt->execute([$id]);
        $temoignage = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement du témoignage : " . $e->getMessage();
    }
}

// Récupérer tous les témoignages
try {
    $stmt = $pdo->query("
        SELECT * FROM temoignages 
        ORDER BY date_creation DESC
    ");
    $temoignages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des témoignages : " . $e->getMessage();
    $temoignages = [];
}

// Statistiques des témoignages
$stats = [
    'total' => count($temoignages),
    'en_attente' => count(array_filter($temoignages, fn($t) => $t['statut'] === 'en_attente')),
    'publies' => count(array_filter($temoignages, fn($t) => $t['statut'] === 'publie')),
    'rejetes' => count(array_filter($temoignages, fn($t) => $t['statut'] === 'rejete'))
];
?>


<!-- Header de la section -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-comment-dots me-2"></i>
            Gestion des Témoignages
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
        </div>
    </div>
    <div class="gmail-card-body">
        <!-- Messages Flash -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-temoignages">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                const url = new URL(window.location);
                url.searchParams.delete('success');
                window.history.replaceState({}, '', url);
                setTimeout(function() {
                    const successMessage = document.getElementById('success-message-temoignages');
                    if (successMessage) {
                        successMessage.style.transition = 'opacity 0.5s ease-out';
                        successMessage.style.opacity = '0';
                        setTimeout(function() { successMessage.remove(); }, 500);
                    }
                }, 1000);
            </script>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message-temoignages">
                <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                const urlError = new URL(window.location);
                urlError.searchParams.delete('error');
                window.history.replaceState({}, '', urlError);
                setTimeout(function() {
                    const errorMessage = document.getElementById('error-message-temoignages');
                    if (errorMessage) {
                        errorMessage.style.transition = 'opacity 0.5s ease-out';
                        errorMessage.style.opacity = '0';
                        setTimeout(function() { errorMessage.remove(); }, 500);
                    }
                }, 3000);
            </script>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-comments fa-2x text-primary"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['total'] ?></h3>
                        <p class="text-muted mb-0">Total</p>
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
                            <i class="fas fa-check fa-2x text-success"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['publies'] ?></h3>
                        <p class="text-muted mb-0">Publiés</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-times fa-2x text-danger"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['rejetes'] ?></h3>
                        <p class="text-muted mb-0">Rejetés</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'view' && $temoignage): ?>
        <!-- Modal Voir Témoignage -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails du témoignage</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('temoignages')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($temoignage['prenom'] . ' ' . $temoignage['nom']) ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-<?= $temoignage['statut'] === 'publie' ? 'success' : ($temoignage['statut'] === 'rejete' ? 'danger' : 'warning') ?> me-2">
                                <?= ucfirst($temoignage['statut']) ?>
                            </span>
                            <div class="d-inline-block">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= $temoignage['note'] ? 'text-warning' : 'text-muted' ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-2">(<?= $temoignage['note'] ?>/5)</span>
                            </div>
                        </div>
                        <div class="content">
                            <p><?= nl2br(htmlspecialchars($temoignage['temoignage'] ?? '')) ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6><i class="fas fa-envelope me-2"></i>Contact</h6>
                            <p><?= htmlspecialchars($temoignage['fonction'] ?? '') ?></p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-calendar me-2"></i>Date</h6>
                            <p><?= date('d/m/Y à H:i', strtotime($temoignage['date_creation'])) ?></p>
                        </div>
                        <div class="d-grid gap-2">
                            <?php if ($temoignage['statut'] === 'en_attente'): ?>
                            <button class="gmail-btn" onclick="publishTemoignage(<?= $temoignage['id'] ?>)">
                                <i class="fas fa-check"></i>
                                Publier
                            </button>
                            <button class="gmail-btn gmail-btn-secondary" onclick="rejectTemoignage(<?= $temoignage['id'] ?>)">
                                <i class="fas fa-times"></i>
                                Rejeter
                            </button>
                            <?php endif; ?>
                            <!-- L'admin ne modifie pas les témoignages -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- L'admin ne modifie pas les témoignages, il les valide/rejette seulement -->

        <!-- Les témoignages viennent du site public, pas d'ajout par l'admin -->

        <!-- Liste des témoignages -->
        <?php if ($action === 'list' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des témoignages</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterTemoignages(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="publie">Publiés</option>
                        <option value="rejete">Rejetés</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Fonction</th>
                                <th>Témoignage</th>
                                <th>Note</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($temoignages as $temoignage): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($temoignage['prenom'] . ' ' . $temoignage['nom']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($temoignage['fonction'] ?? '') ?></td>
                                <td>
                                    <?= htmlspecialchars(substr($temoignage['temoignage'] ?? '', 0, 100)) ?><?= strlen($temoignage['temoignage'] ?? '') > 100 ? '...' : '' ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= $temoignage['note'] ? 'text-warning' : 'text-muted' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ms-2">(<?= $temoignage['note'] ?>/5)</span>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($temoignage['date_creation'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $temoignage['statut'] === 'publie' ? 'success' : ($temoignage['statut'] === 'rejete' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($temoignage['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('temoignages', 'view', <?= $temoignage['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- L'admin ne modifie pas les témoignages -->
                                        
                                        <?php if ($temoignage['statut'] === 'en_attente'): ?>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="publishTemoignage(<?= $temoignage['id'] ?>)" 
                                                title="Publier">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="rejectTemoignage(<?= $temoignage['id'] ?>)" 
                                                title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce témoignage ?')) { window.location.href='mvc_router.php?controller=Temoignages&action=supprimer&id=<?= $temoignage['id'] ?>' }" 
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
// Fonction de filtrage des témoignages
function filterTemoignages(statut) {
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

// Fonction de publication de témoignage
function publishTemoignage(id) {
    if (confirm('Êtes-vous sûr de vouloir publier ce témoignage ?')) {
        window.location.href = `mvc_router.php?controller=Temoignages&action=publier&id=${id}`;
    }
}

// Fonction de rejet de témoignage
function rejectTemoignage(id) {
    if (confirm('Êtes-vous sûr de vouloir rejeter ce témoignage ?')) {
        window.location.href = `mvc_router.php?controller=Temoignages&action=rejeter&id=${id}`;
    }
}
</script>
