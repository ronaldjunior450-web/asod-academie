<?php
// Section Sponsors - Contenu de la gestion des sponsors
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
            $message = 'Sponsor ajouté avec succès !';
            break;
        case 'updated':
            $message = 'Sponsor modifié avec succès !';
            break;
        case 'deleted':
            $message = 'Sponsor supprimé avec succès !';
            break;
    }
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = 'success';
    header('Location: ../index.php?section=sponsors');
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

// Récupérer un sponsor spécifique pour modification ou visualisation
$sponsor = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM sponsors WHERE id = ?");
        $stmt->execute([$id]);
        $sponsor = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement du sponsor : " . $e->getMessage();
    }
}

// Récupérer tous les sponsors
try {
    $stmt = $pdo->query("SELECT * FROM sponsors ORDER BY nom");
    $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des sponsors : " . $e->getMessage();
    $sponsors = [];
}
?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-sponsors">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, '', url);
        
        setTimeout(function() {
            const successMessage = document.getElementById('success-message-sponsors');
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
            <i class="fas fa-money-bill-wave me-2"></i>
            Gestion des Sponsors
        </h2>
        <div>
            <button class="gmail-btn" onclick="loadSection('sponsors', 'add')">
                <i class="fas fa-plus"></i>
                Nouveau sponsor
            </button>
        </div>
    </div>
    <div class="gmail-card-body">

        <?php if ($action === 'view' && $sponsor): ?>
        <!-- Modal Voir Sponsor -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails du sponsor</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('sponsors')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php if ($sponsor['logo'] && file_exists('../../' . $sponsor['logo'])): ?>
                        <div class="mb-3">
                            <img src="../<?= htmlspecialchars($sponsor['logo']) ?>" 
                                 alt="Logo de <?= htmlspecialchars($sponsor['nom']) ?>" 
                                 class="img-fluid rounded">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($sponsor['nom']) ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-primary me-2"><?= ucfirst($sponsor['type_sponsoring']) ?></span>
                            <span class="badge bg-<?= $sponsor['statut'] === 'actif' ? 'success' : 'secondary' ?> me-2">
                                <?= ucfirst($sponsor['statut']) ?>
                            </span>
                            <small class="text-muted">
                                Ajouté le <?= date('d/m/Y', strtotime($sponsor['date_creation'])) ?>
                            </small>
                        </div>
                        <p><?= nl2br(htmlspecialchars($sponsor['description'] ?? '')) ?></p>
                        
                        <h6>Informations de contact</h6>
                        <?php if ($sponsor['site_web']): ?>
                        <p><strong>Site web :</strong> <a href="<?= htmlspecialchars($sponsor['site_web']) ?>" target="_blank"><?= htmlspecialchars($sponsor['site_web']) ?></a></p>
                        <?php endif; ?>
                        <?php if ($sponsor['contact_email']): ?>
                        <p><strong>Email :</strong> <?= htmlspecialchars($sponsor['contact_email']) ?></p>
                        <?php endif; ?>
                        <?php if ($sponsor['contact_telephone']): ?>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($sponsor['contact_telephone']) ?></p>
                        <?php endif; ?>
                        <?php if ($sponsor['montant_sponsoring']): ?>
                        <p><strong>Montant :</strong> <?= number_format($sponsor['montant_sponsoring'], 0, ',', ' ') ?> FCFA</p>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('sponsors', 'edit', <?= $sponsor['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $sponsor): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier le sponsor</h5>
            </div>
            <div class="gmail-card-body">
        <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Sponsors&action=modifier&id=<?= $sponsor['id'] ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($sponsor['nom']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="site_web" class="form-label">Site web</label>
                        <input type="url" class="form-control" id="site_web" name="site_web" value="<?= htmlspecialchars($sponsor['site_web'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($sponsor['description'] ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($sponsor['contact_email'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($sponsor['contact_telephone'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($sponsor['adresse'] ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="type_sponsoring" class="form-label">Type sponsor *</label>
                        <select class="form-control" id="type_sponsoring" name="type_sponsoring" required>
                            <option value="principal" <?= $sponsor['type_sponsoring'] === 'principal' ? 'selected' : '' ?>>Principal</option>
                            <option value="secondaire" <?= $sponsor['type_sponsoring'] === 'secondaire' ? 'selected' : '' ?>>Secondaire</option>
                            <option value="technique" <?= $sponsor['type_sponsoring'] === 'technique' ? 'selected' : '' ?>>Technique</option>
                            <option value="media" <?= $sponsor['type_sponsoring'] === 'media' ? 'selected' : '' ?>>Media</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="montant_sponsoring" class="form-label">Montant sponsoring (FCFA)</label>
                        <input type="number" class="form-control" id="montant_sponsoring" name="montant_sponsoring" value="<?= $sponsor['montant_sponsoring'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-control" id="statut" name="statut">
                            <option value="actif" <?= $sponsor['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                            <option value="inactif" <?= $sponsor['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                <?php if ($sponsor['logo']): ?>
                    <small class="text-muted">Logo actuel : <?= basename($sponsor['logo']) ?></small>
                <?php endif; ?>
            </div>
            
                <div class="d-flex justify-content-between">
                    <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('sponsors')">
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
                <h5 class="gmail-card-title mb-0">Nouveau sponsor</h5>
            </div>
            <div class="gmail-card-body">
        <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Sponsors&action=ajouter">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="site_web" class="form-label">Site web</label>
                        <input type="url" class="form-control" id="site_web" name="site_web">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <textarea class="form-control" id="adresse" name="adresse" rows="2"></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type_sponsoring" class="form-label">Type sponsor *</label>
                        <select class="form-control" id="type_sponsoring" name="type_sponsoring" required>
                            <option value="">Sélectionner un type</option>
                            <option value="principal">Principal</option>
                            <option value="secondaire">Secondaire</option>
                            <option value="technique">Technique</option>
                            <option value="media">Media</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="montant_sponsoring" class="form-label">Montant sponsoring (FCFA)</label>
                        <input type="number" class="form-control" id="montant_sponsoring" name="montant_sponsoring">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
            </div>
            
                <div class="d-flex justify-content-between">
                    <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('sponsors')">
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

        <!-- Liste des sponsors -->
        <?php if ($action === 'list' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des sponsors</h5>
            </div>
            <div class="gmail-card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Nom</th>
                        <th>Niveau</th>
                        <th>Montant</th>
                        <th>Description</th>
                        <th>Contact</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sponsors as $sponsor): ?>
                    <tr>
                        <td>
                            <?php if ($sponsor['logo'] && file_exists('../../' . $sponsor['logo'])): ?>
                                <img src="../<?= htmlspecialchars($sponsor['logo']) ?>" alt="Logo de <?= htmlspecialchars($sponsor['nom']) ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($sponsor['nom']) ?></td>
                        <td>
                            <span class="badge bg-primary"><?= ucfirst($sponsor['type_sponsoring']) ?></span>
                        </td>
                        <td>
                            <?php if ($sponsor['montant_sponsoring']): ?>
                                <?= number_format($sponsor['montant_sponsoring'], 0, ',', ' ') ?> FCFA
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(substr($sponsor['description'] ?? '', 0, 100)) ?><?= strlen($sponsor['description'] ?? '') > 100 ? '...' : '' ?></td>
                        <td>
                            <?php if ($sponsor['contact_email']): ?>
                                <div><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($sponsor['contact_email']) ?></div>
                            <?php endif; ?>
                            <?php if ($sponsor['contact_telephone']): ?>
                                <div><i class="fas fa-phone me-1"></i><?= htmlspecialchars($sponsor['contact_telephone']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $sponsor['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($sponsor['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-info" onclick="loadSection('sponsors', 'view', <?= $sponsor['id'] ?>)" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" onclick="loadSection('sponsors', 'edit', <?= $sponsor['id'] ?>)" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce sponsor ?')) { window.location.href='mvc_router.php?controller=Sponsors&action=supprimer&id=<?= $sponsor['id'] ?>' }" title="Supprimer">
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


