<?php
// Section Dashboard - Tableau de bord Gmail Style
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/../php/config.php';
require_once dirname(__DIR__) . '/api/BaseAPI.php';

try {
    $pdo = getDBConnection();
    
    // Compter les actualités (si la table existe)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM actualites WHERE statut = 'publie'");
        $actualites_count = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $actualites_count = 0;
    }
    
    // Compter les membres (si la table existe)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM membres WHERE statut = 'actif'");
        $membres_count = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $membres_count = 0;
    }
    
    // Compter les équipes (si la table existe)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM equipes WHERE actif = 1");
        $equipes_count = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $equipes_count = 0;
    }
    
    // Compter les inscriptions en attente (si la table existe)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM inscriptions WHERE statut = 'en_attente'");
        $inscriptions_pending = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $inscriptions_pending = 0;
    }
    
    // Compter les messages de contact non lus (si la table existe)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM contacts WHERE lu = 0");
        $contacts_unread = $stmt->fetch()['count'];
    } catch (Exception $e) {
        $contacts_unread = 0;
    }
    
    // Dernières actualités (si la table existe)
    try {
        $stmt = $pdo->query("SELECT * FROM actualites ORDER BY date_creation DESC LIMIT 5");
        $dernieres_actualites = $stmt->fetchAll();
    } catch (Exception $e) {
        $dernieres_actualites = [];
    }
    
    // Dernières inscriptions (si la table existe)
    try {
        $stmt = $pdo->query("SELECT * FROM inscriptions ORDER BY date_inscription DESC LIMIT 5");
        $dernieres_inscriptions = $stmt->fetchAll();
    } catch (Exception $e) {
        $dernieres_inscriptions = [];
    }
    
} catch (Exception $e) {
    $actualites_count = $membres_count = $equipes_count = $inscriptions_pending = $contacts_unread = 0;
    $dernieres_actualites = $dernieres_inscriptions = [];
}
?>

<!-- Dashboard Gmail Style -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-tachometer-alt me-2"></i>
            Tableau de bord
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="gmail-btn" onclick="loadSection('actualites', 'add')">
                <i class="fas fa-plus"></i>
                Nouvelle actualité
            </button>
        </div>
    </div>
    <div class="gmail-card-body">
        <!-- Statistiques principales -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-newspaper fa-2x text-primary"></i>
                        </div>
                        <h3 class="mb-1"><?= $actualites_count ?></h3>
                        <p class="text-muted mb-0">Actualités publiées</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                        <h3 class="mb-1"><?= $membres_count ?></h3>
                        <p class="text-muted mb-0">Membres actifs</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-futbol fa-2x text-info"></i>
                        </div>
                        <h3 class="mb-1"><?= $equipes_count ?></h3>
                        <p class="text-muted mb-0">Équipes actives</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-user-plus fa-2x text-warning"></i>
                        </div>
                        <h3 class="mb-1"><?= $inscriptions_pending ?></h3>
                        <p class="text-muted mb-0">Inscriptions en attente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes importantes -->
        <?php if ($inscriptions_pending > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Attention !</strong> Vous avez <?= $inscriptions_pending ?> inscription(s) en attente de validation.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($contacts_unread > 0): ?>
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-envelope me-2"></i>
            <strong>Nouveaux messages !</strong> Vous avez <?= $contacts_unread ?> message(s) non lu(s).
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Contenu principal -->
        <div class="row">
            <!-- Dernières actualités -->
            <div class="col-lg-6 mb-4">
                <div class="gmail-card">
                    <div class="gmail-card-header">
                        <h6 class="gmail-card-title mb-0">
                            <i class="fas fa-newspaper me-2"></i>Dernières actualités
                        </h6>
                        <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('actualites')">
                            Voir tout
                        </button>
                    </div>
                    <div class="gmail-card-body">
                        <?php if (empty($dernieres_actualites)): ?>
                            <p class="text-muted text-center py-4">
                                <i class="fas fa-newspaper fa-2x mb-2 d-block"></i>
                                Aucune actualité publiée.
                            </p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($dernieres_actualites as $actualite): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($actualite['titre']) ?></h6>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($actualite['date_creation'])) ?></small>
                                    </div>
                                    <span class="badge bg-<?= $actualite['statut'] === 'publie' ? 'success' : 'warning' ?> rounded-pill">
                                        <?= ucfirst($actualite['statut']) ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Dernières inscriptions -->
            <div class="col-lg-6 mb-4">
                <div class="gmail-card">
                    <div class="gmail-card-header">
                        <h6 class="gmail-card-title mb-0">
                            <i class="fas fa-user-plus me-2"></i>Dernières inscriptions
                        </h6>
                        <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('inscriptions')">
                            Voir tout
                        </button>
                    </div>
                    <div class="gmail-card-body">
                        <?php if (empty($dernieres_inscriptions)): ?>
                            <p class="text-muted text-center py-4">
                                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                Aucune inscription récente.
                            </p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($dernieres_inscriptions as $inscription): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($inscription['prenom'] . ' ' . $inscription['nom']) ?></h6>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($inscription['date_inscription'])) ?></small>
                                    </div>
                                    <span class="badge bg-<?= $inscription['statut'] === 'valide' ? 'success' : ($inscription['statut'] === 'rejete' ? 'danger' : 'warning') ?> rounded-pill">
                                        <?= ucfirst($inscription['statut']) ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h6 class="gmail-card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Actions rapides
                </h6>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <button class="gmail-btn w-100" onclick="loadSection('actualites', 'add')">
                            <i class="fas fa-plus me-2"></i>Nouvelle actualité
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="gmail-btn w-100" onclick="loadSection('inscriptions')">
                            <i class="fas fa-user-check me-2"></i>Valider inscriptions
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="gmail-btn w-100" onclick="loadSection('membres')">
                            <i class="fas fa-users me-2"></i>Gérer membres
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="gmail-btn w-100" onclick="loadSection('contacts')">
                            <i class="fas fa-envelope me-2"></i>Messages
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles spécifiques au Dashboard */
.gmail-card .gmail-card-body .row .col-lg-3 .gmail-card {
    border: 1px solid var(--gmail-border);
    transition: all 0.3s ease;
}

.gmail-card .gmail-card-body .row .col-lg-3 .gmail-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.text-primary { color: var(--gmail-primary) !important; }
.text-success { color: #34a853 !important; }
.text-info { color: #17a2b8 !important; }
.text-warning { color: #ffc107 !important; }

.list-group-item {
    border-left: none !important;
    border-right: none !important;
}

.list-group-item:first-child {
    border-top: none !important;
}

.list-group-item:last-child {
    border-bottom: none !important;
}
</style>
