<?php
// Section Inscriptions - Gestion des inscriptions Gmail Style
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$message = '';
$error = '';
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;

// Gestion des messages de succès depuis l'URL
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'validated':
            $message = 'Inscription validée avec succès !';
            break;
        case 'rejected':
            $message = 'Inscription rejetée avec succès !';
            break;
        case 'deleted':
            $message = 'Inscription supprimée avec succès !';
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

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action) {
    if ($action === 'validate') {
        try {
            // Récupérer les données de l'inscription
            $stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE id = ?");
            $stmt->execute([$id]);
            $inscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($inscription) {
                // Mapper le genre de l'inscription vers le genre du membre
                $genre_membre = 'garcon'; // par défaut
                if ($inscription['sexe'] === 'Feminin' || $inscription['genre'] === 'filles') {
                    $genre_membre = 'fille';
                }
                
                // Trouver l'équipe correspondante
                $equipe_id = null;
                
                // Utiliser le champ equipe ou categorie pour déterminer l'équipe
                $equipe_nom = !empty($inscription['equipe']) ? $inscription['equipe'] : $inscription['categorie'];
                $genre = $inscription['sexe'] === 'Masculin' ? 'Garçons' : 'Filles';
                
                // Extraire l'âge de l'inscription (ex: U10-U12 -> 10, U12-U14 -> 12)
                $age_demande = null;
                if (preg_match('/U(\d+)/', $equipe_nom, $matches)) {
                    $age_demande = (int)$matches[1];
                }
                    
                    if ($age_demande) {
                        // Chercher l'équipe la plus proche par âge
                        $stmt_equipe = $pdo->prepare("
                            SELECT id, nom FROM equipes 
                            WHERE nom LIKE ? AND nom LIKE ?
                            ORDER BY nom ASC
                        ");
                        $stmt_equipe->execute(['%U%', '%' . $genre . '%']);
                        $equipes = $stmt_equipe->fetchAll();
                        
                        // Trouver l'équipe avec l'âge le plus proche
                        $meilleure_equipe = null;
                        $diff_min = PHP_INT_MAX;
                        
                        foreach ($equipes as $equipe) {
                            // Extraire l'âge de l'équipe
                            if (preg_match('/U(\d+)/', $equipe['nom'], $matches)) {
                                $age_equipe = (int)$matches[1];
                                $diff = abs($age_equipe - $age_demande);
                                
                                if ($diff < $diff_min) {
                                    $diff_min = $diff;
                                    $meilleure_equipe = $equipe;
                                }
                            }
                        }
                        
                        if ($meilleure_equipe) {
                            $equipe_id = $meilleure_equipe['id'];
                        }
                    }
                
                // Insérer dans la table membres
                $stmt = $pdo->prepare("
                    INSERT INTO membres (
                        nom, prenom, email, telephone, date_naissance, lieu_naissance,
                        genre, adresse, equipe_id, statut, date_adhesion, nom_parent, prenom_parent,
                        telephone_parent, email_parent, profession_parent, adresse_parent, categorie, poste
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'actif', NOW(), ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $inscription['nom'],
                    $inscription['prenom'],
                    $inscription['email'],
                    $inscription['telephone'],
                    $inscription['date_naissance'],
                    $inscription['lieu_naissance'] ?: 'Non renseigné',
                    $genre_membre,
                    $inscription['adresse'],
                    $equipe_id, // équipe trouvée ou NULL
                    $inscription['nom_parent'] ?? '',
                    $inscription['prenom_parent'] ?? '',
                    $inscription['telephone_parent'] ?? '',
                    $inscription['email_parent'] ?? '',
                    $inscription['profession_parent'] ?? '',
                    $inscription['adresse_parent'] ?? '',
                    $inscription['categorie'] ?? '',
                    $inscription['poste'] ?? ''
                ]);
                
                // Mettre à jour le statut de l'inscription
                $stmt = $pdo->prepare("UPDATE inscriptions SET statut = 'valide' WHERE id = ?");
                $stmt->execute([$id]);
                
                $message = 'Inscription validée avec succès';
            }
            
        } catch (Exception $e) {
            $error = "Erreur lors de la validation : " . $e->getMessage();
        }
    }
    
    if ($action === 'reject') {
        try {
            $stmt = $pdo->prepare("UPDATE inscriptions SET statut = 'refuse' WHERE id = ?");
            $stmt->execute([$id]);
            
            $message = 'Inscription rejetée';
            
        } catch (Exception $e) {
            $error = "Erreur lors du rejet : " . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM inscriptions WHERE id = ?");
            $stmt->execute([$id]);
            
            $message = 'Inscription supprimée avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

// Traitement des actions POST (pour compatibilité)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rediriger vers GET pour éviter la duplication
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    if ($action && $id) {
        header("Location: index.php?section=inscriptions&action={$action}&id={$id}");
        exit;
    }
}

// Récupérer une inscription spécifique pour visualisation
$inscription = null;
if ($id && $action === 'view') {
    try {
        $stmt = $pdo->prepare("
            SELECT i.*, e.nom as equipe_nom
            FROM inscriptions i
            LEFT JOIN equipes e ON i.equipe_id = e.id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        $inscription = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement de l'inscription : " . $e->getMessage();
    }
}

// Récupérer toutes les inscriptions
try {
    $stmt = $pdo->query("
        SELECT i.*, e.nom as equipe_nom
        FROM inscriptions i
        LEFT JOIN equipes e ON i.equipe_id = e.id
        ORDER BY i.date_inscription DESC
    ");
    $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des inscriptions : " . $e->getMessage();
    $inscriptions = [];
}

// Statistiques des inscriptions
$stats = [
    'total' => count($inscriptions),
    'en_attente' => count(array_filter($inscriptions, fn($i) => $i['statut'] === 'en_attente')),
    'validees' => count(array_filter($inscriptions, fn($i) => $i['statut'] === 'valide')),
    'rejetees' => count(array_filter($inscriptions, fn($i) => $i['statut'] === 'rejete'))
];
?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-inscriptions">
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
            const successMessage = document.getElementById('success-message-inscriptions');
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

<script>
// Auto-disparition de toutes les alertes (succès, suppression, erreur)
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alertEl) {
        // Laisser l'utilisateur voir le message puis le faire disparaître
        setTimeout(function() {
            alertEl.style.transition = 'opacity 0.5s ease-out';
            alertEl.style.opacity = '0';
            setTimeout(function() {
                if (alertEl && alertEl.parentNode) {
                    alertEl.parentNode.removeChild(alertEl);
                }
            }, 600);
        }, 2500); // 2,5 secondes d'affichage
    });
});
</script>

<!-- Header de la section -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-user-plus me-2"></i>
            Gestion des Inscriptions
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
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
                            <i class="fas fa-users fa-2x text-primary"></i>
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
                        <h3 class="mb-1"><?= $stats['validees'] ?></h3>
                        <p class="text-muted mb-0">Validées</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-times fa-2x text-danger"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['rejetees'] ?></h3>
                        <p class="text-muted mb-0">Rejetées</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'view' && $inscription): ?>
        <!-- Modal Voir Inscription -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails de l'inscription</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('inscriptions')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3><?= htmlspecialchars($inscription['prenom'] . ' ' . $inscription['nom']) ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-<?= $inscription['statut'] === 'valide' ? 'success' : ($inscription['statut'] === 'rejete' ? 'danger' : 'warning') ?> me-2">
                                <?= ucfirst($inscription['statut']) ?>
                            </span>
                            <small class="text-muted">
                                Inscrit le <?= date('d/m/Y à H:i', strtotime($inscription['date_inscription'])) ?>
                            </small>
                        </div>
                        
                        <h6>Informations personnelles</h6>
                        <p><strong>Email :</strong> <?= htmlspecialchars($inscription['email']) ?></p>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($inscription['telephone']) ?></p>
                        <p><strong>Date de naissance :</strong> <?= date('d/m/Y', strtotime($inscription['date_naissance'])) ?></p>
                        <p><strong>Lieu de naissance :</strong> <?= htmlspecialchars($inscription['lieu_naissance']) ?></p>
                        <p><strong>Genre :</strong> <?= ucfirst($inscription['genre']) ?></p>
                        
                        <?php if ($inscription['adresse']): ?>
                        <p><strong>Adresse :</strong> <?= htmlspecialchars($inscription['adresse']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6>Informations sportives</h6>
                        <p><strong>Équipe souhaitée :</strong> <?= htmlspecialchars($inscription['equipe_nom'] ?? 'Non spécifiée') ?></p>
                        
                        <div class="d-grid gap-2">
                            <?php if ($inscription['statut'] === 'en_attente'): ?>
                            <button class="gmail-btn" onclick="validateInscription(<?= $inscription['id'] ?>)">
                                <i class="fas fa-check"></i>
                                Valider l'inscription
                            </button>
                            <button class="gmail-btn gmail-btn-secondary" onclick="rejectInscription(<?= $inscription['id'] ?>)">
                                <i class="fas fa-times"></i>
                                Rejeter l'inscription
                            </button>
                            <?php endif; ?>
                            
                            <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('inscriptions')">
                                <i class="fas fa-list"></i>
                                Voir toutes les inscriptions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Liste des inscriptions -->
        <?php if ($action === 'list' || empty($action) || $action === 'delete'): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des inscriptions</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterInscriptions(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="valide">Validées</option>
                        <option value="rejete">Rejetées</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th class="d-none-mobile">Email</th>
                                <th class="d-none-mobile">Téléphone</th>
                                <th class="d-none-mobile">Date naissance</th>
                                <th>Genre</th>
                                <th class="d-none-mobile">Équipe</th>
                                <th class="d-none-mobile">Date inscription</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inscriptions as $inscription): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($inscription['prenom'] . ' ' . $inscription['nom']) ?></strong>
                                    <div class="d-block d-md-none text-muted small">
                                        <?= htmlspecialchars($inscription['email']) ?><br>
                                        <?= htmlspecialchars($inscription['telephone']) ?><br>
                                        <?= date('d/m/Y', strtotime($inscription['date_naissance'])) ?><br>
                                        <?= htmlspecialchars($inscription['equipe_nom'] ?? 'Non spécifiée') ?>
                                    </div>
                                </td>
                                <td class="d-none-mobile"><?= htmlspecialchars($inscription['email']) ?></td>
                                <td class="d-none-mobile"><?= htmlspecialchars($inscription['telephone']) ?></td>
                                <td class="d-none-mobile"><?= date('d/m/Y', strtotime($inscription['date_naissance'])) ?></td>
                                <td>
                                    <i class="fas fa-<?= $inscription['genre'] === 'garcon' ? 'male' : 'female' ?> me-1"></i>
                                    <?= ucfirst($inscription['genre']) ?>
                                </td>
                                <td class="d-none-mobile"><?= htmlspecialchars($inscription['equipe_nom'] ?? 'Non spécifiée') ?></td>
                                <td class="d-none-mobile"><?= date('d/m/Y H:i', strtotime($inscription['date_inscription'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $inscription['statut'] === 'valide' ? 'success' : ($inscription['statut'] === 'rejete' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($inscription['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info btn-action" 
                                                onclick="loadSection('inscriptions', 'view', <?= $inscription['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($inscription['statut'] === 'en_attente'): ?>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="validateInscription(<?= $inscription['id'] ?>)" 
                                                title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="rejectInscription(<?= $inscription['id'] ?>)" 
                                                title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')) { loadSection('inscriptions', 'delete', <?= $inscription['id'] ?>) }" 
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
// Fonction de filtrage des inscriptions
function filterInscriptions(statut) {
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

// Fonction de validation d'inscription
function validateInscription(id) {
    if (confirm('Êtes-vous sûr de vouloir valider cette inscription ?')) {
        loadSection('inscriptions', 'validate', id);
    }
}

// Fonction de rejet d'inscription
function rejectInscription(id) {
    if (confirm('Êtes-vous sûr de vouloir rejeter cette inscription ?')) {
        loadSection('inscriptions', 'reject', id);
    }
}
</script>
