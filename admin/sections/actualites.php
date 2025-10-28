<?php
// Section Actualités - Gestion des actualités Gmail Style
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$message = '';
$error = '';
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;

// Gestion des messages de succès
if (isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'added':
            $message = 'Actualité ajoutée avec succès !';
            break;
        case 'updated':
            $message = 'Actualité mise à jour avec succès !';
            break;
        case 'deleted':
            $message = 'Actualité supprimée avec succès !';
            break;
        case 'published':
            $message = 'Actualité publiée avec succès !';
            break;
        case 'unpublished':
            $message = 'Actualité dépubliée avec succès !';
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
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        try {
            // Gestion de l'image
            $image_path = null;
            
            // Créer le dossier uploads s'il n'existe pas
            $upload_dir = '../../uploads/news/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Traitement de l'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_filename = 'news_' . time() . '.' . $image_extension;
                $image_path = 'uploads/news/' . $image_filename;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $image_path)) {
                    $image_path = null;
                }
            }
            
            $auteur = ($_SESSION['admin_nom'] ?? 'Admin') . ' ' . ($_SESSION['admin_prenom'] ?? 'Utilisateur');
            
            $stmt = $pdo->prepare("
                INSERT INTO actualites (titre, contenu, image, statut, auteur, date_creation) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_POST['titre'],
                $_POST['contenu'],
                $image_path,
                $_POST['statut'],
                $auteur
            ]);
            
            // Message de succès géré par la redirection
            
        } catch (Exception $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
    
    if ($action === 'update') {
        try {
            // Gestion de l'image
            $image_path = null;
            
            // Créer le dossier uploads s'il n'existe pas
            $upload_dir = '../../uploads/news/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Traitement de l'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_filename = 'news_' . $id . '_' . time() . '.' . $image_extension;
                $image_path = 'uploads/news/' . $image_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $image_path)) {
                    // Supprimer l'ancienne image si elle existe
                    $stmt_old = $pdo->prepare("SELECT image FROM actualites WHERE id = ?");
                    $stmt_old->execute([$id]);
                    $old_image = $stmt_old->fetchColumn();
                    if ($old_image && file_exists('../../' . $old_image)) {
                        unlink('../../' . $old_image);
                    }
                } else {
                    $image_path = null;
                }
            }
            
            // Construire la requête UPDATE
            $update_fields = [
                'titre = ?', 'contenu = ?', 'statut = ?'
            ];
            
            $values = [
                $_POST['titre'],
                $_POST['contenu'],
                $_POST['statut']
            ];
            
            // Ajouter l'image si elle a été uploadée
            if ($image_path !== null) {
                $update_fields[] = 'image = ?';
                $values[] = $image_path;
            }
            
            $values[] = $id; // Pour la clause WHERE
            
            $stmt = $pdo->prepare("
                UPDATE actualites 
                SET " . implode(', ', $update_fields) . "
                WHERE id = ?
            ");
            $stmt->execute($values);
            
            $message = 'Actualité mise à jour avec succès';
            echo '<script>setTimeout(() => loadSection("actualites"), 2000);</script>';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        try {
            // Supprimer l'image si elle existe
            $stmt = $pdo->prepare("SELECT image FROM actualites WHERE id = ?");
            $stmt->execute([$id]);
            $image = $stmt->fetchColumn();
            if ($image && file_exists('../../' . $image)) {
                unlink('../../' . $image);
            }
            
            $stmt = $pdo->prepare("DELETE FROM actualites WHERE id = ?");
            $stmt->execute([$id]);
            
            $message = 'Actualité supprimée avec succès';
            echo '<script>setTimeout(() => loadSection("actualites"), 2000);</script>';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

// Récupérer une actualité spécifique pour modification ou visualisation
$actualite = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("
            SELECT a.*
            FROM actualites a
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $actualite = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement de l'actualité : " . $e->getMessage();
    }
}

// Récupérer toutes les actualités
try {
    $stmt = $pdo->query("
        SELECT a.* 
        FROM actualites a
        ORDER BY a.date_creation DESC
    ");
    $actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des actualités : " . $e->getMessage();
    $actualites = [];
}
?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-actualites-1">
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
            const successMessage = document.getElementById('success-message-actualites-1');
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


<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>Erreur : <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
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
            <i class="fas fa-newspaper me-2"></i>
            Gestion des Actualités
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="gmail-btn" onclick="loadSection('actualites', 'ajouter')">
                <i class="fas fa-plus"></i>
                Nouvelle actualité
            </button>
        </div>
    </div>
    <div class="gmail-card-body">
        <?php if ($action === 'view' && $actualite): ?>
        <!-- Modal Voir Actualité -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails de l'actualité</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('actualites')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($actualite['titre']) ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-<?= $actualite['statut'] === 'publie' ? 'success' : 'warning' ?> me-2">
                                <?= ucfirst($actualite['statut']) ?>
                            </span>
                            <small class="text-muted">
                                Par <?= htmlspecialchars($actualite['auteur'] ?? 'Administrateur') ?> 
                                le <?= date('d/m/Y à H:i', strtotime($actualite['date_creation'])) ?>
                            </small>
                        </div>
                        <div class="content">
                            <?= $actualite['contenu'] ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <?php if ($actualite['image']): ?>
                        <div class="mb-3">
                            <?php 
                            $imagePath = $actualite['image'];
                            $fullImagePath = dirname(__DIR__, 2) . '/' . $imagePath;
                            if (file_exists($fullImagePath)): ?>
                                <img src="../<?= htmlspecialchars($actualite['image']) ?>" 
                                     alt="<?= htmlspecialchars($actualite['titre']) ?>" 
                                     class="img-fluid rounded">
                            <?php elseif (file_exists($actualite['image'])): ?>
                                <img src="<?= htmlspecialchars($actualite['image']) ?>" 
                                     alt="<?= htmlspecialchars($actualite['titre']) ?>" 
                                     class="img-fluid rounded">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center p-3">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                    <small class="text-muted ms-2">Image non trouvée</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('actualites', 'modifier', <?= $actualite['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                            <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('actualites')">
                                <i class="fas fa-list"></i>
                                Voir toutes les actualités
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $actualite): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier l'actualité</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Actualites&action=modifier&id=<?= $actualite['id'] ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $actualite['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" 
                               value="<?= htmlspecialchars($actualite['titre']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contenu" class="form-label">Contenu *</label>
                        <textarea class="form-control" id="contenu" name="contenu" rows="10" required><?= htmlspecialchars($actualite['contenu']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="brouillon" <?= $actualite['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                                    <option value="publie" <?= $actualite['statut'] === 'publie' ? 'selected' : '' ?>>Publié</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <?php if ($actualite['image']): ?>
                                    <small class="text-muted">Image actuelle : <?= basename($actualite['image']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('actualites')">
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
                <h5 class="gmail-card-title mb-0">Nouvelle actualité</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Actualites&action=ajouter">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contenu" class="form-label">Contenu *</label>
                        <textarea class="form-control" id="contenu" name="contenu" rows="10" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="brouillon">Brouillon</option>
                                    <option value="publie">Publié</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('actualites')">
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

        <!-- Liste des actualités -->
        <?php if ($action === 'list' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des actualités</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterActualites(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="publie">Publiées</option>
                        <option value="brouillon">Brouillons</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Image</th>
                                <th style="width: 200px;">Titre</th>
                                <th style="width: 300px;">Contenu</th>
                                <th style="width: 120px;">Auteur</th>
                                <th style="width: 120px;">Date</th>
                                <th style="width: 100px;">Statut</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actualites as $actualite): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $imagePath = $actualite['image'];
                                    $fullImagePath = dirname(__DIR__, 2) . '/' . $imagePath;
                                    if ($actualite['image'] && file_exists($fullImagePath)): ?>
                                        <img src="../<?= htmlspecialchars($actualite['image']) ?>" 
                                             alt="<?= htmlspecialchars($actualite['titre']) ?>" 
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php elseif ($actualite['image'] && file_exists($actualite['image'])): ?>
                                        <img src="<?= htmlspecialchars($actualite['image']) ?>" 
                                             alt="<?= htmlspecialchars($actualite['titre']) ?>" 
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($actualite['titre']) ?></strong>
                                </td>
                                <td style="max-width: 300px; word-wrap: break-word;">
                                    <?= htmlspecialchars(substr(strip_tags($actualite['contenu']), 0, 200)) ?><?= strlen(strip_tags($actualite['contenu'])) > 200 ? '...' : '' ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($actualite['auteur'] ?? 'Administrateur') ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($actualite['date_creation'])) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $actualite['statut'] === 'publie' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($actualite['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('actualites', 'voir', <?= $actualite['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="loadSection('actualites', 'modifier', <?= $actualite['id'] ?>)" 
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($actualite['statut'] === 'publie'): ?>
                                            <button class="btn btn-sm btn-outline-warning" 
                                                    onclick="if(confirm('Dépublier cette actualité ?')) { window.location.href = 'mvc_router.php?controller=Actualites&action=depublier&id=<?= $actualite['id'] ?>' }" 
                                                    title="Dépublier">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-success" 
                                                    onclick="if(confirm('Publier cette actualité ?')) { window.location.href = 'mvc_router.php?controller=Actualites&action=publier&id=<?= $actualite['id'] ?>' }" 
                                                    title="Publier">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?')) { window.location.href = 'mvc_router.php?controller=Actualites&action=supprimer&id=<?= $actualite['id'] ?>' }" 
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


<style>
/* Styles pour améliorer l'affichage du tableau */
.table th, .table td {
    vertical-align: middle;
}

/* Styles pour l'overlay modal */
.overlay-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
}

.overlay-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

.overlay-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    max-width: 90%;
    max-height: 90%;
    width: 800px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.overlay-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #e0e0e0;
    background: #f8f9fa;
}

.overlay-title {
    margin: 0;
    color: #1a73e8;
    font-size: 1.25rem;
    font-weight: 600;
}

.overlay-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.overlay-close:hover {
    background: #e0e0e0;
    color: #333;
}

.overlay-body {
    padding: 25px;
    overflow-y: auto;
    flex: 1;
    max-height: calc(90vh - 100px);
}

/* Animation d'ouverture */
.overlay-modal.show {
    animation: fadeIn 0.3s ease-out;
}

.overlay-content.show {
    animation: slideIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to { 
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .overlay-content {
        width: 95%;
        max-height: 95%;
    }
    
    .overlay-header {
        padding: 15px 20px;
    }
    
    .overlay-body {
        padding: 20px;
    }
}

.table td {
    padding: 12px 8px;
}

/* Amélioration de l'affichage du contenu */
.content-cell {
    max-width: 300px;
    word-wrap: break-word;
    line-height: 1.4;
}

/* Amélioration des boutons d'action */
.btn-group .btn {
    margin: 0 2px;
}

/* Responsive pour mobile */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .table th, .table td {
        padding: 8px 4px;
    }
    
    .content-cell {
        max-width: 200px;
    }
}

/* Styles pour le modal */
.actualite-details h4 {
    color: #1a73e8;
    margin-bottom: 15px;
}

.actualite-details .contenu {
    line-height: 1.6;
    font-size: 14px;
}

.actualite-details img {
    max-height: 300px;
    object-fit: cover;
}
</style>

<script>
// Fonction de soumission du formulaire d'actualité
function submitActualiteForm(form) {
    const formData = new FormData(form);
    
    fetch('admin/mvc_router.php?controller=Actualites&action=ajouter', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Réponse reçue:', response.status);
        return response.text();
    })
    .then(data => {
        console.log('Données reçues:', data.length, 'caractères');
        // Recharger la section actualités
        loadSection('actualites');
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'ajout de l\'actualité: ' + error.message);
    });
    
    return false; // Empêcher la soumission normale du formulaire
}


// Fonction de filtrage des actualités
function filterActualites(statut) {
    // Implémentation du filtrage côté client
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
