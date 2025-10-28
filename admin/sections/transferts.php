<?php
/**
 * Section Transferts - Liste des membres transférés vers d'autres associations
 */

// Les variables $transferts et $stats sont définies dans le contrôleur
$action = $_GET['action'] ?? 'liste';
$transfert = $transfert ?? null;

// Gestion des messages de succès/erreur depuis les redirections
$message = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'restored':
            $message = 'Le transfert a été restauré avec succès. Le membre est redevenu actif.';
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
?>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-transferts">
    <i class="fas fa-check-circle me-2"></i>
    <strong>Succès !</strong> <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<script>
    // Faire disparaître automatiquement le message de succès après 1 seconde
    setTimeout(function() {
        const successMessage = document.getElementById('success-message-transferts');
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

<?php if ($action === 'restaurer' && $transfert): ?>
<!-- Formulaire de restauration de transfert -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-undo me-2"></i>
            Restaurer le transfert
        </h5>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Êtes-vous sûr de vouloir restaurer ce transfert ? Le membre redeviendra actif dans son équipe d'origine.
        </div>

        <form method="POST" action="mvc_router.php?controller=Transferts&action=restaurer&id=<?= $transfert['id'] ?>">
            <input type="hidden" name="action" value="restaurer">
            <input type="hidden" name="id" value="<?= $transfert['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Membre à restaurer :</label>
                <div class="alert alert-info">
                    <strong><?= htmlspecialchars($transfert['nom'] . ' ' . $transfert['prenom']) ?></strong><br>
                    <small>ID: <?= htmlspecialchars($transfert['membre_id']) ?> | Transfert vers: <strong><?= htmlspecialchars($transfert['association_destination']) ?></strong></small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Équipe d'origine :</label>
                <div class="alert alert-success">
                    <strong><?= htmlspecialchars($transfert['equipe_origine'] ?? 'Non définie') ?></strong>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" onclick="loadSection('transferts')" class="btn btn-secondary">Annuler</button>
                <button type="submit" class="btn btn-success">✅ Restaurer le transfert</button>
            </div>
        </form>
    </div>
</div>

<?php elseif ($action === 'voir' && $transfert): ?>
<!-- Détail d'un transfert -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-exchange-alt me-2"></i>
            Détails du transfert
        </h5>
        <a href="?section=transferts" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center">
                <?php 
                $photoPath = $transfert['photo'];
                if (!empty($transfert['photo']) && file_exists('../../' . $photoPath)): ?>
                    <img src="../<?= htmlspecialchars($photoPath) ?>?v=<?= time() ?>&r=<?= rand(1000, 9999) ?>&bust=<?= md5($transfert['photo']) ?>&force=1" 
                         alt="Photo de <?= htmlspecialchars($transfert['nom'] . ' ' . $transfert['prenom']) ?>" 
                         class="img-fluid rounded-circle mb-3 transfer-detail-photo"
                         style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #0d6efd; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <?php else: ?>
                    <div class="transfer-detail-placeholder rounded-circle d-flex align-items-center justify-content-center mb-3"
                         style="width: 150px; height: 150px; background: linear-gradient(135deg, #0d6efd, #6f42c1); border: 3px solid #0d6efd; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fas fa-user fa-4x text-white"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-9">
                <h4><?= htmlspecialchars($transfert['nom'] . ' ' . $transfert['prenom']) ?></h4>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informations personnelles</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Sexe :</th>
                                <td><?= $transfert['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?></td>
                            </tr>
                            <tr>
                                <th>Date de naissance :</th>
                                <td><?= date('d/m/Y', strtotime($transfert['date_naissance'])) ?></td>
                            </tr>
                            <tr>
                                <th>Téléphone :</th>
                                <td><?= htmlspecialchars($transfert['telephone'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Email :</th>
                                <td><?= htmlspecialchars($transfert['email'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Équipe d'origine :</th>
                                <td><?= htmlspecialchars($transfert['equipe_origine'] ?? 'N/A') ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted">Informations du transfert</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Association :</th>
                                <td><strong><?= htmlspecialchars($transfert['association_destination']) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Ville :</th>
                                <td><?= htmlspecialchars($transfert['ville_destination'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Contact :</th>
                                <td><?= htmlspecialchars($transfert['contact_destination'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Date du transfert :</th>
                                <td><?= date('d/m/Y à H:i', strtotime($transfert['date_transfert'])) ?></td>
                            </tr>
                            <tr>
                                <th>Traité par :</th>
                                <td><?= htmlspecialchars($transfert['traite_par']) ?></td>
                            </tr>
                        </table>
                        
                        <?php if (!empty($transfert['motif'])): ?>
                        <div class="mt-3">
                            <h6 class="text-muted">Motif du transfert</h6>
                            <p class="border p-2 rounded"><?= nl2br(htmlspecialchars($transfert['motif'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Liste des transferts -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-exchange-alt me-2"></i>
            Membres Transférés (Externes)
        </h5>
    </div>
    <div class="card-body">
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3><?= $stats['total'] ?></h3>
                        <p class="mb-0">Total Transférés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3><?= $stats['garcons'] ?></h3>
                        <p class="mb-0">Garçons</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3><?= $stats['filles'] ?></h3>
                        <p class="mb-0">Filles</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Message si aucun transfert -->
        <?php if (empty($transfertsOrganises['garcons']) && empty($transfertsOrganises['filles'])): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Aucun membre transféré pour le moment.
        </div>
        <?php else: ?>
        
        <!-- Onglets Genre -->
        <ul class="nav nav-tabs" id="genreTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="garcons-tab" data-bs-toggle="tab" data-bs-target="#garcons" type="button" role="tab">
                    <i class="fas fa-male me-2"></i>Garçons (<?= $stats['garcons'] ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="filles-tab" data-bs-toggle="tab" data-bs-target="#filles" type="button" role="tab">
                    <i class="fas fa-female me-2"></i>Filles (<?= $stats['filles'] ?>)
                </button>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="genreTabContent">
            <!-- Garçons -->
            <div class="tab-pane fade show active" id="garcons" role="tabpanel">
                <?php if (!empty($transfertsOrganises['garcons'])): ?>
                <div class="table-responsive mt-3">
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 50px;">Photo</th>
                                <th style="width: 40px;">ID</th>
                                <th style="width: 100px;">Nom</th>
                                <th style="width: 100px;">Prénom</th>
                                <th style="width: 80px;">Né le</th>
                                <th style="width: 50px;">Âge</th>
                                <th style="width: 100px;">Équipe</th>
                                <th style="width: 100px;">Association</th>
                                <th style="width: 80px;">Ville</th>
                                <th style="width: 80px;">Date</th>
                                <th style="width: 150px;">Traité par</th>
                                <th style="width: 60px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transfertsOrganises['garcons'] as $t): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $photoPath = $t['photo'];
                                    if (!empty($t['photo']) && file_exists('../../' . $photoPath)): ?>
                                        <img src="../<?= htmlspecialchars($photoPath) ?>?v=<?= time() ?>&r=<?= rand(1000, 9999) ?>&bust=<?= md5($t['photo']) ?>&force=1" 
                                             alt="Photo de <?= htmlspecialchars($t['nom'] . ' ' . $t['prenom']) ?>" 
                                             class="transfer-photo rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #0d6efd;">
                                    <?php else: ?>
                                        <div class="photo-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $t['membre_id'] ?></td>
                                <td><strong><?= htmlspecialchars($t['nom']) ?></strong></td>
                                <td><strong><?= htmlspecialchars($t['prenom']) ?></strong></td>
                                <td><?= date('d/m/y', strtotime($t['date_naissance'])) ?></td>
                                <td><?= date_diff(date_create($t['date_naissance']), date_create('today'))->y ?></td>
                                <td title="<?= htmlspecialchars($t['equipe_origine'] ?? 'N/A') ?>"><?= htmlspecialchars(substr($t['equipe_origine'] ?? 'N/A', 0, 15)) ?><?= strlen($t['equipe_origine'] ?? 'N/A') > 15 ? '...' : '' ?></td>
                                <td title="<?= htmlspecialchars($t['association_destination']) ?>"><strong><?= htmlspecialchars(substr($t['association_destination'], 0, 15)) ?><?= strlen($t['association_destination']) > 15 ? '...' : '' ?></strong></td>
                                <td><?= htmlspecialchars($t['ville_destination'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/y', strtotime($t['date_transfert'])) ?></td>
                                <td title="<?= htmlspecialchars($t['traite_par']) ?>"><?= htmlspecialchars(substr($t['traite_par'], 0, 20)) ?><?= strlen($t['traite_par']) > 20 ? '...' : '' ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-info btn-voir-transfert" 
                                                data-transfert-id="<?= $t['transfert_id'] ?>" 
                                                data-genre="garcons"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success btn-restaurer-transfert" 
                                                data-transfert-id="<?= $t['transfert_id'] ?>" 
                                                data-membre-id="<?= $t['membre_id'] ?>"
                                                data-membre-nom="<?= htmlspecialchars($t['nom'] . ' ' . $t['prenom']) ?>"
                                                title="Restaurer le transfert">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucun garçon transféré pour le moment.
                </div>
                <?php endif; ?>
            </div>

            <!-- Filles -->
            <div class="tab-pane fade" id="filles" role="tabpanel">
                <?php if (!empty($transfertsOrganises['filles'])): ?>
                <div class="table-responsive mt-3">
                    <table class="table table-hover">
                        <thead class="table-pink">
                            <tr>
                                <th style="width: 50px;">Photo</th>
                                <th style="width: 40px;">ID</th>
                                <th style="width: 100px;">Nom</th>
                                <th style="width: 100px;">Prénom</th>
                                <th style="width: 80px;">Né le</th>
                                <th style="width: 50px;">Âge</th>
                                <th style="width: 100px;">Équipe</th>
                                <th style="width: 100px;">Association</th>
                                <th style="width: 80px;">Ville</th>
                                <th style="width: 80px;">Date</th>
                                <th style="width: 150px;">Traité par</th>
                                <th style="width: 60px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transfertsOrganises['filles'] as $t): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $photoPath = $t['photo'];
                                    if (!empty($t['photo']) && file_exists('../../' . $photoPath)): ?>
                                        <img src="../<?= htmlspecialchars($photoPath) ?>?v=<?= time() ?>&r=<?= rand(1000, 9999) ?>&bust=<?= md5($t['photo']) ?>&force=1" 
                                             alt="Photo de <?= htmlspecialchars($t['nom'] . ' ' . $t['prenom']) ?>" 
                                             class="transfer-photo rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #0d6efd;">
                                    <?php else: ?>
                                        <div class="photo-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $t['membre_id'] ?></td>
                                <td><strong><?= htmlspecialchars($t['nom']) ?></strong></td>
                                <td><strong><?= htmlspecialchars($t['prenom']) ?></strong></td>
                                <td><?= date('d/m/y', strtotime($t['date_naissance'])) ?></td>
                                <td><?= date_diff(date_create($t['date_naissance']), date_create('today'))->y ?></td>
                                <td title="<?= htmlspecialchars($t['equipe_origine'] ?? 'N/A') ?>"><?= htmlspecialchars(substr($t['equipe_origine'] ?? 'N/A', 0, 15)) ?><?= strlen($t['equipe_origine'] ?? 'N/A') > 15 ? '...' : '' ?></td>
                                <td title="<?= htmlspecialchars($t['association_destination']) ?>"><strong><?= htmlspecialchars(substr($t['association_destination'], 0, 15)) ?><?= strlen($t['association_destination']) > 15 ? '...' : '' ?></strong></td>
                                <td><?= htmlspecialchars($t['ville_destination'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/y', strtotime($t['date_transfert'])) ?></td>
                                <td title="<?= htmlspecialchars($t['traite_par']) ?>"><?= htmlspecialchars(substr($t['traite_par'], 0, 20)) ?><?= strlen($t['traite_par']) > 20 ? '...' : '' ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-info btn-voir-transfert" 
                                                data-transfert-id="<?= $t['transfert_id'] ?>" 
                                                data-genre="filles"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success btn-restaurer-transfert" 
                                                data-transfert-id="<?= $t['transfert_id'] ?>" 
                                                data-membre-id="<?= $t['membre_id'] ?>"
                                                data-membre-nom="<?= htmlspecialchars($t['nom'] . ' ' . $t['prenom']) ?>"
                                                title="Restaurer le transfert">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucune fille transférée pour le moment.
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<style>
.bg-pink {
    background-color: #e83e8c !important;
}
.text-pink {
    color: #e83e8c !important;
}
.table-pink {
    background-color: #f8d7e4 !important;
}
.table-pink th {
    color: #721c24 !important;
}

/* Amélioration de l'affichage des tableaux */
.table-responsive {
    overflow-x: auto;
    font-size: 0.9rem;
}

.table th, .table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 8px 6px;
    font-size: 0.85rem;
}

/* Colonne "Traité par" pour les emails */
.table th:nth-child(11), .table td:nth-child(11) {
    min-width: 150px;
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Colonnes nom et prénom */
.table th:nth-child(3), .table td:nth-child(3),
.table th:nth-child(4), .table td:nth-child(4) {
    min-width: 100px;
    max-width: 100px;
}

/* Colonnes équipe et association */
.table th:nth-child(7), .table td:nth-child(7),
.table th:nth-child(8), .table td:nth-child(8) {
    min-width: 100px;
    max-width: 100px;
}

/* Tooltip pour les textes tronqués */
.table td[title] {
    cursor: help;
}

/* Styles pour les photos des transferts */
.transfer-photo {
    width: 50px !important;
    height: 50px !important;
    object-fit: cover;
    border: 2px solid #0d6efd;
    border-radius: 50%;
    transition: transform 0.2s, border-color 0.2s;
    display: block;
    margin: 0 auto;
}

.transfer-photo:hover {
    transform: scale(1.1);
    border-color: #28a745;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Placeholder pour les photos manquantes */
.photo-placeholder {
    width: 50px !important;
    height: 50px !important;
    background: linear-gradient(135deg, #0d6efd, #6f42c1);
    border: 2px solid #0d6efd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    transition: transform 0.2s, border-color 0.2s;
}

.photo-placeholder:hover {
    transform: scale(1.1);
    border-color: #28a745;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.photo-placeholder i {
    font-size: 1.2rem;
    color: white;
}

/* Styles pour la photo de détail du transfert */
.transfer-detail-photo {
    transition: transform 0.3s, border-color 0.3s, box-shadow 0.3s;
}

.transfer-detail-photo:hover {
    transform: scale(1.05);
    border-color: #28a745;
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

.transfer-detail-placeholder {
    transition: transform 0.3s, border-color 0.3s, box-shadow 0.3s;
}

.transfer-detail-placeholder:hover {
    transform: scale(1.05);
    border-color: #28a745;
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}
</style>

