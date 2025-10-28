<?php
// Section Infos Contact - Gestion des informations de contact Gmail Style
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
            $message = 'Information de contact ajoutée avec succès';
            break;
        case 'updated':
            $message = 'Information de contact mise à jour avec succès';
            break;
        case 'deleted':
            $message = 'Information de contact supprimée avec succès';
            break;
        case 'toggled':
            $message = 'Statut mis à jour avec succès';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'add_failed':
            $error = 'Erreur lors de l\'ajout de l\'information';
            break;
        case 'update_failed':
            $error = 'Erreur lors de la mise à jour de l\'information';
            break;
        case 'delete_failed':
            $error = 'Erreur lors de la suppression de l\'information';
            break;
        case 'toggle_failed':
            $error = 'Erreur lors de la mise à jour du statut';
            break;
    }
}

// Récupérer une information de contact spécifique pour modification ou visualisation
$contact_info = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_info WHERE id = ?");
        $stmt->execute([$id]);
        $contact_info = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement de l'information : " . $e->getMessage();
    }
}

// Récupérer toutes les informations de contact avec ordre automatique par type
try {
    $stmt = $pdo->query("
        SELECT * FROM contact_info 
        ORDER BY 
            CASE type_contact 
                WHEN 'telephone' THEN 1
                WHEN 'email' THEN 2
                WHEN 'adresse' THEN 3
                WHEN 'site_web' THEN 4
                WHEN 'facebook' THEN 5
                WHEN 'instagram' THEN 6
                WHEN 'twitter' THEN 7
                WHEN 'youtube' THEN 8
                WHEN 'tiktok' THEN 9
                WHEN 'linkedin' THEN 10
                WHEN 'whatsapp' THEN 11
                WHEN 'telegram' THEN 12
                ELSE 99
            END,
            id ASC
    ");
    $contact_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des informations : " . $e->getMessage();
    $contact_infos = [];
}

// Types de contact disponibles (correspondent aux valeurs ENUM de la base)
$types_contact = [
    'telephone' => 'Téléphone',
    'email' => 'Email',
    'adresse' => 'Adresse',
    'facebook' => 'Facebook',
    'instagram' => 'Instagram',
    'twitter' => 'Twitter',
    'youtube' => 'YouTube',
    'tiktok' => 'TikTok',
    'linkedin' => 'LinkedIn',
    'whatsapp' => 'WhatsApp',
    'telegram' => 'Telegram',
    'site_web' => 'Site web',
    'autre' => 'Autre'
];
?>

<!-- Messages Flash -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="flash-message-contact-info">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        // Masquer automatiquement le message après 1 seconde
        setTimeout(function() {
            const alert = document.getElementById('flash-message-contact-info');
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
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="flash-error-contact-info">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        // Masquer automatiquement le message d'erreur après 3 secondes
        setTimeout(function() {
            const alert = document.getElementById('flash-error-contact-info');
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
            <i class="fas fa-info-circle me-2"></i>
            Informations de Contact
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="gmail-btn" onclick="loadSection('contact_info', 'add')">
                <i class="fas fa-plus"></i>
                Nouvelle information
            </button>
        </div>
    </div>
    <div class="gmail-card-body">
        <?php if ($action === 'view' && $contact_info): ?>
        <!-- Modal Voir Information -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails de l'information</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('contact_info')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($contact_info['description']) ?></h3>
                        <p><strong>Type :</strong> <?= $types_contact[$contact_info['type_contact']] ?? ucfirst($contact_info['type_contact'] ?? 'Non défini') ?></p>
                        <p><strong>Valeur :</strong> <?= htmlspecialchars($contact_info['valeur']) ?></p>
                        <p><strong>Statut :</strong> 
                            <span class="badge bg-<?= $contact_info['actif'] ? 'success' : 'secondary' ?>">
                                <?= $contact_info['actif'] ? 'Actif' : 'Inactif' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('contact_info', 'edit', <?= $contact_info['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $contact_info): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier l'information</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=ContactInfo&action=modifier&id=<?= $contact_info['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-control" id="type_contact" name="type_contact" required>
                                    <?php foreach ($types_contact as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $contact_info['type_contact'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <input type="text" class="form-control" id="description" name="description" 
                               value="<?= htmlspecialchars($contact_info['description']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="valeur" class="form-label">Valeur *</label>
                        <input type="text" class="form-control" id="valeur" name="valeur" 
                               value="<?= htmlspecialchars($contact_info['valeur']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="actif" class="form-label">Statut</label>
                        <select class="form-control" id="actif" name="actif">
                            <option value="1" <?= $contact_info['actif'] ? 'selected' : '' ?>>Actif</option>
                            <option value="0" <?= !$contact_info['actif'] ? 'selected' : '' ?>>Inactif</option>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('contact_info')">
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
                <h5 class="gmail-card-title mb-0">Nouvelle information de contact</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=ContactInfo&action=ajouter">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-control" id="type_contact" name="type_contact" required>
                                    <option value="">Sélectionner un type</option>
                                    <?php foreach ($types_contact as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="valeur" class="form-label">Valeur *</label>
                        <input type="text" class="form-control" id="valeur" name="valeur" required>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('contact_info')">
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

        <!-- Liste des informations de contact -->
        <?php if ($action === 'list' || $action === '' || !isset($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des informations de contact</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterContactInfo(this.value)">
                        <option value="">Tous les types</option>
                        <?php foreach ($types_contact as $key => $label): ?>
                        <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Valeur</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contact_infos as $info): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary"><?= $types_contact[$info['type_contact']] ?? ucfirst($info['type_contact'] ?? 'Non défini') ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($info['description'] ?? 'Non défini') ?></strong>
                                </td>
                                <td><?= htmlspecialchars($info['valeur'] ?? 'Non défini') ?></td>
                                <td>
                                    <span class="badge bg-<?= $info['actif'] ? 'success' : 'secondary' ?>">
                                        <?= $info['actif'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('contact_info', 'view', <?= $info['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="loadSection('contact_info', 'edit', <?= $info['id'] ?>)" 
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette information ?')) { window.location.href='mvc_router.php?controller=ContactInfo&action=supprimer&id=<?= $info['id'] ?>' }" 
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
// Fonction de filtrage des informations de contact
function filterContactInfo(type) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        if (type === '') {
            // Afficher toutes les lignes
            row.style.display = '';
        } else {
            // Vérifier si la ligne correspond au type sélectionné
            const typeCell = row.cells[0]; // Première colonne (Type)
            const typeBadge = typeCell.querySelector('.badge');
            const badgeText = typeBadge.textContent.trim();
            
            // Mapper les types pour la comparaison
            const typeMapping = {
                'telephone': 'Téléphone',
                'email': 'Email',
                'adresse': 'Adresse',
                'facebook': 'Facebook',
                'instagram': 'Instagram',
                'twitter': 'Twitter',
                'youtube': 'YouTube',
                'tiktok': 'TikTok',
                'linkedin': 'LinkedIn',
                'whatsapp': 'WhatsApp',
                'telegram': 'Telegram',
                'site_web': 'Site web',
                'autre': 'Autre'
            };
            
            const expectedText = typeMapping[type] || type;
            if (badgeText === expectedText) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}
</script>
