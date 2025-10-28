<?php
// Section Événements - Gestion des événements Gmail Style
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
    $success_messages = [
        'add' => 'Événement ajouté avec succès',
        'update' => 'Événement modifié avec succès',
        'delete' => 'Événement supprimé avec succès'
    ];
    $message = $success_messages[$_GET['success']] ?? 'Action réussie';
    
    // Stocker le message en session pour affichage unique
    if (!isset($_SESSION['message_displayed_' . $_GET['success']])) {
        $_SESSION['message_displayed_' . $_GET['success']] = true;
    } else {
        // Message déjà affiché, ne pas le réafficher
        $message = '';
    }
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO evenements (titre, description, type_evenement, date_debut, date_fin, lieu, statut) 
                VALUES (?, ?, ?, ?, ?, ?, 'planifie')
            ");
            $stmt->execute([
                $_POST['titre'],
                $_POST['description'],
                $_POST['type_evenement'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['lieu']
            ]);
            
            $message = 'Événement ajouté avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
    
    if ($action === 'update') {
        try {
            $stmt = $pdo->prepare("
                UPDATE evenements 
                SET titre = ?, description = ?, type_evenement = ?, date_debut = ?, date_fin = ?, lieu = ?, statut = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['titre'],
                $_POST['description'],
                $_POST['type_evenement'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['lieu'],
                $_POST['statut'],
                $id
            ]);
            
            $message = 'Événement mis à jour avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM evenements WHERE id = ?");
            $stmt->execute([$id]);
            
            $message = 'Événement supprimé avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

// Récupérer un événement spécifique pour modification ou visualisation
$evenement = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM evenements WHERE id = ?");
        $stmt->execute([$id]);
        $evenement = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement de l'événement : " . $e->getMessage();
    }
}

// Récupérer tous les événements
try {
    $stmt = $pdo->query("
        SELECT * FROM evenements 
        ORDER BY date_debut DESC
    ");
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des événements : " . $e->getMessage();
    $evenements = [];
}

// Statistiques des événements
try {
    $stmt = $pdo->query("SELECT type_evenement, COUNT(*) as count FROM evenements GROUP BY type_evenement");
    $type_stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $stmt = $pdo->query("SELECT statut, COUNT(*) as count FROM evenements GROUP BY statut");
    $status_stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $type_stats = $status_stats = [];
}
?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-evenements">
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
            const successMessage = document.getElementById('success-message-evenements');
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


<?php if (isset($_GET['error'])): ?>
    <?php
    $error_messages = [
        'delete_failed' => 'Erreur lors de la suppression de l\'événement',
        'not_found' => 'Événement non trouvé',
        'load_failed' => 'Erreur lors du chargement de l\'événement',
        'update_failed' => 'Erreur lors de la mise à jour de l\'événement',
        'add_failed' => 'Erreur lors de l\'ajout de l\'événement'
    ];
    $error_message = $error_messages[$_GET['error']] ?? 'Une erreur est survenue';
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Header de la section -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-calendar-alt me-2"></i>
            Gestion des Événements
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="gmail-btn" onclick="loadSection('evenements', 'add')">
                <i class="fas fa-plus"></i>
                Nouvel événement
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
                            <i class="fas fa-calendar-check fa-2x text-primary"></i>
                        </div>
                        <h3 class="mb-1"><?= $status_stats['planifie'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Planifiés</p>
        </div>
    </div>
</div>

            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-play fa-2x text-warning"></i>
                        </div>
                        <h3 class="mb-1"><?= $status_stats['en_cours'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">En cours</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-check fa-2x text-success"></i>
                        </div>
                        <h3 class="mb-1"><?= $status_stats['termine'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Terminés</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-times fa-2x text-danger"></i>
                        </div>
                        <h3 class="mb-1"><?= $status_stats['annule'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Annulés</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'view' && $evenement): ?>
        <!-- Modal Voir Événement -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails de l'événement</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('evenements')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($evenement['titre']) ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-<?= $evenement['statut'] === 'planifie' ? 'primary' : ($evenement['statut'] === 'programme' ? 'info' : ($evenement['statut'] === 'en_cours' ? 'warning' : ($evenement['statut'] === 'termine' ? 'success' : 'danger'))) ?> me-2">
                                <?= ucfirst($evenement['statut']) ?>
                            </span>
                            <span class="badge bg-info"><?= ucfirst($evenement['type_evenement']) ?></span>
                        </div>
                        <div class="content">
                            <?= nl2br(htmlspecialchars($evenement['description'])) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6><i class="fas fa-calendar me-2"></i>Dates</h6>
                            <p><strong>Début :</strong> <?= date('d/m/Y H:i', strtotime($evenement['date_debut'])) ?></p>
                            <p><strong>Fin :</strong> <?= date('d/m/Y H:i', strtotime($evenement['date_fin'])) ?></p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-map-marker-alt me-2"></i>Lieu</h6>
                            <p><?= htmlspecialchars($evenement['lieu']) ?></p>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('evenements', 'edit', <?= $evenement['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                            <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('evenements')">
                                <i class="fas fa-list"></i>
                                Voir tous les événements
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $evenement): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier l'événement</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Evenements&action=modifier&id=<?= $evenement['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" 
                               value="<?= htmlspecialchars($evenement['titre']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($evenement['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_evenement" class="form-label">Type d'événement *</label>
                                <select class="form-control" id="type_evenement" name="type_evenement" required>
                                    <option value="match" <?= $evenement['type_evenement'] === 'match' ? 'selected' : '' ?>>Match</option>
                                    <option value="entrainement" <?= $evenement['type_evenement'] === 'entrainement' ? 'selected' : '' ?>>Entraînement</option>
                                    <option value="tournoi" <?= $evenement['type_evenement'] === 'tournoi' ? 'selected' : '' ?>>Tournoi</option>
                                    <option value="formation" <?= $evenement['type_evenement'] === 'formation' ? 'selected' : '' ?>>Formation</option>
                                    <option value="autre" <?= $evenement['type_evenement'] === 'autre' ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="planifie" <?= $evenement['statut'] === 'planifie' ? 'selected' : '' ?>>Planifié</option>
                                    <option value="programme" <?= $evenement['statut'] === 'programme' ? 'selected' : '' ?>>Programmé</option>
                                    <option value="en_cours" <?= $evenement['statut'] === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                                    <option value="termine" <?= $evenement['statut'] === 'termine' ? 'selected' : '' ?>>Terminé</option>
                                    <option value="annule" <?= $evenement['statut'] === 'annule' ? 'selected' : '' ?>>Annulé</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_debut" class="form-label">Date de début *</label>
                                <input type="datetime-local" class="form-control" id="date_debut" name="date_debut" 
                                       value="<?= date('Y-m-d\TH:i', strtotime($evenement['date_debut'])) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_fin" class="form-label">Date de fin *</label>
                                <input type="datetime-local" class="form-control" id="date_fin" name="date_fin" 
                                       value="<?= date('Y-m-d\TH:i', strtotime($evenement['date_fin'])) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lieu" class="form-label">Lieu</label>
                        <input type="text" class="form-control" id="lieu" name="lieu" 
                               value="<?= htmlspecialchars($evenement['lieu']) ?>">
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('evenements')">
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
                <h5 class="gmail-card-title mb-0">Nouvel événement</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Evenements&action=ajouter">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_evenement" class="form-label">Type d'événement *</label>
                                <select class="form-control" id="type_evenement" name="type_evenement" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="match">Match</option>
                                    <option value="entrainement">Entraînement</option>
                                    <option value="tournoi">Tournoi</option>
                                    <option value="formation">Formation</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lieu" class="form-label">Lieu</label>
                                <input type="text" class="form-control" id="lieu" name="lieu">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_debut" class="form-label">Date de début *</label>
                                <input type="datetime-local" class="form-control" id="date_debut" name="date_debut" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_fin" class="form-label">Date de fin *</label>
                                <input type="datetime-local" class="form-control" id="date_fin" name="date_fin" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-control" id="statut" name="statut">
                            <option value="planifie">Planifié</option>
                            <option value="programme" selected>Programmé</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('evenements')">
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

        <!-- Liste des événements -->
        <?php if ($action === 'list' || empty($action) || $action === 'delete'): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des événements</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="console.log('🎯 Select changé:', this.value); filterEvenements(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="planifie">Planifiés</option>
                        <option value="programme">Programmés</option>
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminés</option>
                        <option value="annule">Annulés</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                                <tr>
                                <th>Titre</th>
                                    <th>Type</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                    <th>Lieu</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($evenements as $evenement): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($evenement['titre']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst($evenement['type_evenement']) ?></span>
                                        </td>
                                        <td>
                                    <?= date('d/m/Y H:i', strtotime($evenement['date_debut'])) ?>
                                        </td>
                                        <td>
                                    <?= date('d/m/Y H:i', strtotime($evenement['date_fin'])) ?>
                                        </td>
                                        <td>
                                    <?= htmlspecialchars($evenement['lieu']) ?>
                                        </td>
                                        <td>
                                    <span class="badge badge-statut bg-<?= $evenement['statut'] === 'planifie' ? 'primary' : ($evenement['statut'] === 'programme' ? 'info' : ($evenement['statut'] === 'en_cours' ? 'warning' : ($evenement['statut'] === 'termine' ? 'success' : 'danger'))) ?>">
                                        <?= ucfirst($evenement['statut']) ?>
                                            </span>
                                        </td>
                                        <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('evenements', 'view', <?= $evenement['id'] ?>)" 
                                                title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="loadSection('evenements', 'edit', <?= $evenement['id'] ?>)" 
                                                title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="mvc_router.php?controller=Evenements&action=supprimer&id=<?= $evenement['id'] ?>" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                        </form>
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
console.log('📜 Script evenements.php chargé');

// Fonction de filtrage des événements
function filterEvenements(statut) {
    console.log('🔍 Filtrage des événements avec statut:', statut);
    
    const rows = document.querySelectorAll('tbody tr');
    console.log('📊 Nombre de lignes trouvées:', rows.length);
    
    rows.forEach((row, index) => {
        const statusBadge = row.querySelector('.badge-statut');
        if (statusBadge) {
            const badgeText = statusBadge.textContent.toLowerCase().trim();
            console.log(`📋 Ligne ${index + 1}: Badge texte = "${badgeText}"`);
            
            let shouldShow = false;
            
            if (statut === '') {
                shouldShow = true;
                console.log(`✅ Ligne ${index + 1}: Affichée (tous les statuts)`);
            } else {
                // Mapping des statuts pour la comparaison
                const statutMapping = {
                    'planifie': 'planifie',
                    'programme': 'programme',
                    'en_cours': 'en_cours',
                    'termine': 'termine',
                    'annule': 'annule'
                };
                
                const expectedText = statutMapping[statut] || statut;
                shouldShow = badgeText.includes(expectedText);
                
                console.log(`🔍 Ligne ${index + 1}: Comparaison "${badgeText}" avec "${expectedText}" = ${shouldShow}`);
            }
            
            row.style.display = shouldShow ? '' : 'none';
        } else {
            console.log(`❌ Ligne ${index + 1}: Pas de badge trouvé`);
        }
    });
    
    console.log('✅ Filtrage terminé');
}
</script>
        