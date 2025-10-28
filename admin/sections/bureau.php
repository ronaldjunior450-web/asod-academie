<?php
// session_start() déjà appelé dans mvc_router.php
require_once dirname(__DIR__) . '/../php/config.php';

$pdo = getDBConnection();

// Récupérer l'action et l'ID depuis l'URL
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Messages de succès et d'erreur
$message = '';
$error = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $message = 'Membre du bureau ajouté avec succès';
            break;
        case 'updated':
            $message = 'Membre du bureau modifié avec succès';
            break;
        case 'deleted':
            $message = 'Membre du bureau supprimé avec succès';
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
    $error = "Erreur : " . htmlspecialchars($_GET['error']);
}

// Récupérer les membres du bureau pour la liste
if ($action === 'list' || empty($action)) {
    $stmt = $pdo->query("
        SELECT * FROM bureau 
        ORDER BY ordre_affichage ASC, nom ASC
    ");
    $membres_bureau = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer un membre spécifique pour la modification ou la vue
if ($id && in_array($action, ['modifier', 'voir'])) {
    $stmt = $pdo->prepare("SELECT * FROM bureau WHERE id = ?");
    $stmt->execute([$id]);
    $membre_bureau = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$membre_bureau) {
        $error = "Membre du bureau non trouvé";
        $action = 'list';
    }
}
?>

<!-- Messages -->
<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-bureau">
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<script>
    // Nettoyer l'URL immédiatement pour éviter le réaffichage
    const url = new URL(window.location);
    url.searchParams.delete('success');
    window.history.replaceState({}, '', url);
    
    // Faire disparaître automatiquement le message de succès après 1 seconde
    setTimeout(function() {
        const successMessage = document.getElementById('success-message-bureau');
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
    <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

<!-- Header de la section -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-building me-2"></i>
            Gestion du Bureau
        </h2>
        <div>
            <button class="gmail-btn" onclick="loadSection('bureau', 'ajouter')">
                <i class="fas fa-plus"></i>
                Nouveau membre
                                                </button>
                        </div>
                    </div>
    <div class="gmail-card-body">

        <?php if ($action === 'modifier' && $membre_bureau): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier le membre du bureau</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Bureau&action=modifier&id=<?= $membre_bureau['id'] ?>" enctype="multipart/form-data">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nom" class="form-label">Nom *</label>
                                         <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($membre_bureau['nom']) ?>" required>
                                     </div>
                                    </div>
                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="prenom" class="form-label">Prénom *</label>
                                         <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($membre_bureau['prenom']) ?>" required>
                                     </div>
                                 </div>
                                    </div>
                                    
                             <div class="row">
                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="poste" class="form-label">Poste *</label>
                                         <input type="text" class="form-control" id="poste" name="poste" value="<?= htmlspecialchars($membre_bureau['poste']) ?>" required>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label for="profession" class="form-label">Profession</label>
                                         <input type="text" class="form-control" id="profession" name="profession" value="<?= htmlspecialchars($membre_bureau['profession'] ?? '') ?>">
                                     </div>
                                 </div>
                             </div>
                             
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label for="email" class="form-label">Email</label>
                                         <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($membre_bureau['email'] ?? '') ?>">
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="mb-3">
                                         <label for="telephone" class="form-label">Téléphone</label>
                                         <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($membre_bureau['telephone'] ?? '') ?>">
                                     </div>
                                 </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                 <label for="adresse" class="form-label">Adresse</label>
                                 <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($membre_bureau['adresse'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                 <label for="biographie" class="form-label">Biographie</label>
                                 <textarea class="form-control" id="biographie" name="biographie" rows="4"><?= htmlspecialchars($membre_bureau['biographie'] ?? '') ?></textarea>
                             </div>
                             
                             <div class="row">
                                 <div class="col-md-4">
                                     <div class="mb-3">
                                         <label for="ordre_affichage" class="form-label">Ordre d'affichage</label>
                                         <input type="number" class="form-control" id="ordre_affichage" name="ordre_affichage" value="<?= $membre_bureau['ordre_affichage'] ?>" required>
                                         <small class="text-muted">Modifiable pour réorganiser l'affichage</small>
                                     </div>
                                 </div>
                                 <div class="col-md-4">
                                     <div class="mb-3">
                                         <label for="actif" class="form-label">Statut</label>
                                         <select class="form-control" id="actif" name="actif">
                                             <option value="1" <?= $membre_bureau['actif'] ? 'selected' : '' ?>>Actif</option>
                                             <option value="0" <?= !$membre_bureau['actif'] ? 'selected' : '' ?>>Inactif</option>
                                         </select>
                                     </div>
                                 </div>
                                 <div class="col-md-4">
                                     <div class="mb-3">
                                         <label for="photo" class="form-label">Photo</label>
                                         <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                         <?php if ($membre_bureau['photo']): ?>
                                             <small class="text-muted">Photo actuelle : <?= basename($membre_bureau['photo']) ?></small>
                                         <?php endif; ?>
                                     </div>
                                 </div>
                                    </div>
                                    
                            <div class="d-flex justify-content-between">
                                <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('bureau')">
                                    <i class="fas fa-arrow-left"></i>
                                    Annuler
                                </button>
                                <button type="submit" class="gmail-btn">
                                    <i class="fas fa-save"></i>
                                    Modifier
                                </button>
                            </div>
                        </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'voir' && $membre_bureau): ?>
        <!-- Vue détaillée -->
        <div class="gmail-card">
            <div class="gmail-card-header d-flex justify-content-between align-items-center">
                <h5 class="gmail-card-title mb-0">Détails du membre du bureau</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('bureau')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-4">
                         <?php if ($membre_bureau['photo'] && file_exists('../images/bureau/' . $membre_bureau['photo'])): ?>
                                    <div class="mb-3">
                             <img src="../images/bureau/<?= htmlspecialchars($membre_bureau['photo']) ?>" 
                                  alt="<?= htmlspecialchars($membre_bureau['prenom'] . ' ' . $membre_bureau['nom']) ?>" 
                                  class="img-fluid rounded">
                         </div>
                         <?php else: ?>
                         <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                              style="height: 200px;">
                             <i class="fas fa-user fa-4x text-muted"></i>
                         </div>
                         <?php endif; ?>
                    </div>
                     <div class="col-md-8">
                         <h3><?= htmlspecialchars($membre_bureau['prenom'] . ' ' . $membre_bureau['nom']) ?></h3>
                         
                         <div class="row">
                             <div class="col-md-6">
                                 <p><strong>Poste :</strong> <?= htmlspecialchars($membre_bureau['poste']) ?></p>
                                 <?php if (!empty($membre_bureau['profession'])): ?>
                                 <p><strong>Profession :</strong> <?= htmlspecialchars($membre_bureau['profession']) ?></p>
                                 <?php endif; ?>
                                 <p><strong>Ordre d'affichage :</strong> <?= $membre_bureau['ordre_affichage'] ?></p>
                                 <p><strong>Statut :</strong> 
                                     <span class="badge bg-<?= $membre_bureau['actif'] ? 'success' : 'secondary' ?>">
                                         <?= $membre_bureau['actif'] ? 'Actif' : 'Inactif' ?>
                                     </span>
                                 </p>
                             </div>
                             <div class="col-md-6">
                                 <?php if (!empty($membre_bureau['email'])): ?>
                                 <p><strong>Email :</strong> <a href="mailto:<?= htmlspecialchars($membre_bureau['email']) ?>"><?= htmlspecialchars($membre_bureau['email']) ?></a></p>
                                 <?php endif; ?>
                                 <?php if (!empty($membre_bureau['telephone'])): ?>
                                 <p><strong>Téléphone :</strong> <a href="tel:<?= htmlspecialchars($membre_bureau['telephone']) ?>"><?= htmlspecialchars($membre_bureau['telephone']) ?></a></p>
                                 <?php endif; ?>
                                 <p><strong>Date de création :</strong> <?= date('d/m/Y H:i', strtotime($membre_bureau['date_creation'])) ?></p>
                                 <?php if (!empty($membre_bureau['date_modification'])): ?>
                                 <p><strong>Dernière modification :</strong> <?= date('d/m/Y H:i', strtotime($membre_bureau['date_modification'])) ?></p>
                                 <?php endif; ?>
                             </div>
                                        </div>
                                        
                         <?php if (!empty($membre_bureau['adresse'])): ?>
                                        <div class="mt-3">
                             <h6><strong>Adresse :</strong></h6>
                             <p class="text-muted"><?= nl2br(htmlspecialchars($membre_bureau['adresse'])) ?></p>
                                        </div>
                                        <?php endif; ?>
                                        
                         <?php if (!empty($membre_bureau['biographie'])): ?>
                         <div class="mt-3">
                             <h6><strong>Biographie :</strong></h6>
                             <p class="text-muted"><?= nl2br(htmlspecialchars($membre_bureau['biographie'])) ?></p>
                         </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button class="gmail-btn" onclick="loadSection('bureau', 'modifier', <?= $membre_bureau['id'] ?>)">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </button>
                    <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('bureau')">
                        <i class="fas fa-list"></i>
                        Retour à la liste
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'ajouter'): ?>
        <!-- Formulaire d'ajout -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Nouveau membre du bureau</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Bureau&action=ajouter" enctype="multipart/form-data">
                                
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="nom" class="form-label">Nom *</label>
                                 <input type="text" class="form-control" id="nom" name="nom" required>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="prenom" class="form-label">Prénom *</label>
                                 <input type="text" class="form-control" id="prenom" name="prenom" required>
                             </div>
                         </div>
                     </div>
                     
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="poste" class="form-label">Poste *</label>
                                 <input type="text" class="form-control" id="poste" name="poste" required>
                             </div>
                         </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profession" class="form-label">Profession</label>
                                 <input type="text" class="form-control" id="profession" name="profession">
                             </div>
                         </div>
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
                                    
                                    <div class="mb-3">
                         <label for="biographie" class="form-label">Biographie</label>
                         <textarea class="form-control" id="biographie" name="biographie" rows="4"></textarea>
                                    </div>
                                    
                     <div class="row">
                         <div class="col-md-6">
                                    <div class="mb-3">
                                 <label for="actif" class="form-label">Statut</label>
                                 <select class="form-control" id="actif" name="actif">
                                     <option value="1" selected>Actif</option>
                                     <option value="0">Inactif</option>
                                        </select>
                                    </div>
                                </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="photo" class="form-label">Photo</label>
                                 <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            
                     <div class="alert alert-info">
                         <i class="fas fa-info-circle me-2"></i>
                         <strong>Note :</strong> L'ordre d'affichage sera automatiquement calculé. Le nouveau membre sera placé à la fin de la liste.
                            </div>
                            
                            <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('bureau')">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </button>
                        <button type="submit" class="gmail-btn">
                            <i class="fas fa-plus"></i>
                            Ajouter
                                </button>
                            </div>
                        </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'list' || empty($action)): ?>
        <!-- Liste des membres du bureau -->
                <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Organigramme du bureau</h5>   
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <?php foreach ($membres_bureau as $membre): ?>
                    <div class="col-md-4 mb-4">
                        <div class="gmail-card">
                            <div class="gmail-card-body text-center">
                                 <?php if ($membre['photo'] && file_exists('../images/bureau/' . $membre['photo'])): ?>
                                     <img src="../images/bureau/<?= htmlspecialchars($membre['photo']) ?>" 
                                          alt="<?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?>" 
                                          class="img-fluid rounded-circle mb-3" 
                                          style="width: 120px; height: 120px; object-fit: cover;">
                                 <?php else: ?>
                                     <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" 
                                          style="width: 120px; height: 120px;">
                                         <i class="fas fa-user fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>
            
                                <h5><?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($membre['poste']) ?></p>

                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="loadSection('bureau', 'voir', <?= $membre['id'] ?>)"
                                            title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary"
                                            onclick="loadSection('bureau', 'modifier', <?= $membre['id'] ?>)"
                                            title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="mvc_router.php?controller=Bureau&action=supprimer&id=<?= $membre['id'] ?>" style="display: inline;">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce membre du bureau ?')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
            