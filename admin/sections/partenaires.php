<?php
// Section Partenaires - Vue uniquement (traitement géré par le contrôleur MVC)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;


// Messages de succès/erreur depuis l'URL
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'updated':
            $message = 'Partenaire modifié avec succès';
            break;
        case 'added':
            $message = 'Partenaire ajouté avec succès';
            break;
        case 'deleted':
            $message = 'Partenaire supprimé avec succès';
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
    $error = 'Erreur : ' . htmlspecialchars($_GET['error']);
}

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        try {
            $pdo = getDBConnection();
            
            // Gestion du logo
            $logo_path = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/partenaires/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $logo_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $logo_filename = 'logo_' . time() . '_' . uniqid() . '.' . $logo_extension;
                $logo_path = 'uploads/partenaires/' . $logo_filename;
                
                if (!move_uploaded_file($_FILES['logo']['tmp_name'], '../../' . $logo_path)) {
                    $logo_path = null;
                }
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO partenaires (nom, description, site_web, contact_email, contact_telephone, adresse, logo, statut, date_creation) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'actif', NOW())
            ");
            $stmt->execute([
                $_POST['nom'],
                $_POST['description'],
                $_POST['site_web'],
                $_POST['contact_email'],
                $_POST['contact_telephone'],
                $_POST['adresse'],
                $logo_path
            ]);
            
            $message = 'Partenaire ajouté avec succès';
            echo '<script>setTimeout(() => loadSection("partenaires"), 2000);</script>';
            
        } catch (Exception $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
    
    if ($action === 'update') {
        try {
            $pdo = getDBConnection();
            
            // Vérifier que l'ID existe
            if (!$id) {
                throw new Exception("ID du partenaire manquant");
            }
            
            // Gestion du logo
            $logo_path = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/partenaires/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $logo_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $logo_filename = 'logo_' . $id . '_' . time() . '_' . uniqid() . '.' . $logo_extension;
                $logo_path = 'uploads/partenaires/' . $logo_filename;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], '../../' . $logo_path)) {
                    // Supprimer l'ancien logo si il existe
                    $stmt_old = $pdo->prepare("SELECT logo FROM partenaires WHERE id = ?");
                    $stmt_old->execute([$id]);
                    $old_logo = $stmt_old->fetchColumn();
                    if ($old_logo && file_exists('../../' . $old_logo)) {
                        unlink('../../' . $old_logo);
                    }
                } else {
                    $logo_path = null;
                }
            }
            
            // Construction de la requête UPDATE
            $update_fields = [
                'nom = ?', 'description = ?', 'site_web = ?', 'contact_email = ?', 
                'contact_telephone = ?', 'adresse = ?', 'statut = ?'
            ];
            
            $values = [
                $_POST['nom'],
                $_POST['description'],
                $_POST['site_web'],
                $_POST['contact_email'],
                $_POST['contact_telephone'],
                $_POST['adresse'],
                $_POST['statut']
            ];
            
            if ($logo_path !== null) {
                $update_fields[] = 'logo = ?';
                $values[] = $logo_path;
            }
            
            $values[] = $id;
            
            $sql = "UPDATE partenaires SET " . implode(', ', $update_fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result) {
                $message = 'Partenaire modifié avec succès';
                echo '<script>setTimeout(() => loadSection("partenaires"), 2000);</script>';
            } else {
                $error = 'Erreur lors de la mise à jour';
            }
            
        } catch (Exception $e) {
            $error = 'Erreur : ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        try {
            $pdo = getDBConnection();
            
            // Supprimer le logo si il existe
            $stmt = $pdo->prepare("SELECT logo FROM partenaires WHERE id = ?");
            $stmt->execute([$id]);
            $logo = $stmt->fetchColumn();
            if ($logo && file_exists('../../' . $logo)) {
                unlink('../../' . $logo);
            }
            
            $stmt = $pdo->prepare("DELETE FROM partenaires WHERE id = ?");
            $stmt->execute([$id]);
            
            $message = 'Partenaire supprimé avec succès';
            echo '<script>window.location.href = "index.php?section=partenaires&success=deleted";</script>';
            exit;
            
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

// Récupérer un partenaire spécifique pour modification ou visualisation
$partenaire = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM partenaires WHERE id = ?");
        $stmt->execute([$id]);
        $partenaire = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement du partenaire : " . $e->getMessage();
    }
}

// Récupérer tous les partenaires
try {
    $stmt = $pdo->query("SELECT * FROM partenaires ORDER BY nom");
    $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des partenaires : " . $e->getMessage();
    $partenaires = [];
}
?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-partenaires">
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
            const successMessage = document.getElementById('success-message-partenaires');
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
            <i class="fas fa-handshake me-2"></i>
            Gestion des Partenaires
        </h2>
        <div>
            <button class="gmail-btn" onclick="loadSection('partenaires', 'add')">
                <i class="fas fa-plus"></i>
                Nouveau partenaire
            </button>
        </div>
    </div>
    <div class="gmail-card-body">

        <?php if ($action === 'view' && $partenaire): ?>
        <!-- Vue détaillée -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails du partenaire</h5>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php if ($partenaire['logo']): ?>
                            <img src="/ges_asod/<?= htmlspecialchars($partenaire['logo']) ?>" alt="Logo" class="img-fluid rounded mb-3">
                        <?php else: ?>
                            <div class="bg-light rounded p-5 text-center mb-3">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">Aucun logo</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h4><?= htmlspecialchars($partenaire['nom']) ?></h4>
                        <p><strong>Description :</strong> <?= htmlspecialchars($partenaire['description']) ?></p>
                        <?php if (!empty($partenaire['site_web'])): ?>
                        <p><strong>Site web :</strong> <a href="<?= htmlspecialchars($partenaire['site_web']) ?>" target="_blank"><?= htmlspecialchars($partenaire['site_web']) ?></a></p>
                        <?php endif; ?>
                        <?php if (!empty($partenaire['contact_email'])): ?>
                        <p><strong>Email :</strong> <?= htmlspecialchars($partenaire['contact_email']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($partenaire['contact_telephone'])): ?>
                        <p><strong>Téléphone :</strong> <?= htmlspecialchars($partenaire['contact_telephone']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($partenaire['adresse'])): ?>
                        <p><strong>Adresse :</strong> <?= htmlspecialchars($partenaire['adresse']) ?></p>
                        <?php endif; ?>
                        <p><strong>Statut :</strong> 
                            <span class="badge bg-<?= $partenaire['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($partenaire['statut']) ?>
                            </span>
                        </p>
                        
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('partenaires', 'edit', <?= $partenaire['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                            <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('partenaires')">
                                <i class="fas fa-arrow-left"></i>
                                Retour à la liste
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $partenaire): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier le partenaire</h5>
            </div>
            <div class="gmail-card-body">
        <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Partenaires&action=modifier&id=<?= $partenaire['id'] ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($partenaire['nom']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="site_web" class="form-label">Site web</label>
                        <input type="url" class="form-control" id="site_web" name="site_web" value="<?= htmlspecialchars($partenaire['site_web'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($partenaire['description'] ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="contact_email" value="<?= htmlspecialchars($partenaire['contact_email'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="contact_telephone" value="<?= htmlspecialchars($partenaire['contact_telephone'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($partenaire['adresse'] ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-control" id="statut" name="statut">
                            <option value="actif" <?= $partenaire['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                            <option value="inactif" <?= $partenaire['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <?php if ($partenaire['logo']): ?>
                            <small class="text-muted">Logo actuel : <?= basename($partenaire['logo']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
                <div class="d-flex justify-content-between">
                    <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('partenaires')">
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
                <h5 class="gmail-card-title mb-0">Nouveau partenaire</h5>
            </div>
            <div class="gmail-card-body">
        <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Partenaires&action=ajouter">
            
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
                        <input type="email" class="form-control" id="email" name="contact_email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="contact_telephone">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <textarea class="form-control" id="adresse" name="adresse" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
            </div>
            
                <div class="d-flex justify-content-between">
                    <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('partenaires')">
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


        <!-- Liste des partenaires -->
        <?php if ($action === 'list' || $action === 'liste' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des partenaires</h5>
            </div>
            <div class="gmail-card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Contact</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partenaires as $partenaire): ?>
                        <tr>
                            <td>
                                <?php if ($partenaire['logo']): ?>
                                    <img src="/ges_asod/<?= htmlspecialchars($partenaire['logo']) ?>" alt="Logo" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($partenaire['nom']) ?></strong>
                                <?php if (!empty($partenaire['site_web'])): ?>
                                    <br><small><a href="<?= htmlspecialchars($partenaire['site_web']) ?>" target="_blank"><?= htmlspecialchars($partenaire['site_web']) ?></a></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(substr($partenaire['description'], 0, 100)) ?><?= strlen($partenaire['description']) > 100 ? '...' : '' ?></td>
                            <td>
                                <?php if (!empty($partenaire['contact_email'])): ?>
                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($partenaire['contact_email']) ?><br>
                                <?php endif; ?>
                                <?php if (!empty($partenaire['contact_telephone'])): ?>
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($partenaire['contact_telephone']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $partenaire['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($partenaire['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-info" onclick="loadSection('partenaires', 'view', <?= $partenaire['id'] ?>)" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="loadSection('partenaires', 'edit', <?= $partenaire['id'] ?>)" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce partenaire ?')) loadSection('partenaires', 'delete', <?= $partenaire['id'] ?>)" title="Supprimer">
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
