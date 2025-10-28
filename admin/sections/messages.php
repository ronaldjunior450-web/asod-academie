<?php
/**
 * Page de gestion des messages de contact
 */

// Récupérer l'action depuis l'URL
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Variables pour les messages
$message = '';
$error = '';

// Gestion des messages de succès et d'erreur
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'deleted':
            $message = "Message supprimé avec succès !";
            break;
        case 'replied':
            $message = "Réponse envoyée avec succès !";
            break;
        case 'marked_read':
            $message = "Message marqué comme lu !";
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
    switch ($_GET['error']) {
        case 'delete_failed':
            $error = "Erreur lors de la suppression du message.";
            break;
        case 'message_not_found':
            $error = "Message non trouvé.";
            break;
        case 'reponse_vide':
            $error = "La réponse ne peut pas être vide.";
            break;
        case 'reponse_failed':
            $error = "Erreur lors de l'envoi de la réponse.";
            break;
        default:
            $error = "Erreur : " . htmlspecialchars($_GET['error']);
    }
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    
    if ($action && $id) {
        header("Location: index.php?section=messages&action={$action}&id={$id}");
        exit;
    }
}

try {
    $pdo = getDBConnection();
    
    // Actions GET (seulement pour voir et repondre)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action) {
        switch ($action) {
            case 'voir':
                if ($id) {
                    // Marquer comme lu
                    $stmt = $pdo->prepare("UPDATE contacts SET lu = 1, date_lecture = NOW() WHERE id = ?");
                    $stmt->execute([$id]);
                }
                break;
                
            case 'repondre':
                if ($id) {
                    // Rien à faire ici, juste afficher le formulaire
                }
                break;
        }
    }
    
    // Récupérer les messages (toujours à la fin, après toutes les actions)
    $stmt = $pdo->query("
        SELECT c.*
        FROM contacts c
        ORDER BY c.date_contact DESC
    ");
    $messages = $stmt->fetchAll();
    
    // Récupérer un message spécifique pour la vue
    $message_detail = null;
    if ($id && in_array($action, ['voir', 'repondre'])) {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        $message_detail = $stmt->fetch();
    }
    
} catch (Exception $e) {
    $error_message = "Erreur lors du chargement des messages : " . $e->getMessage();
}


if (isset($error_message)) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($error_message) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
}

// Calculer les statistiques (toujours affichées)
$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM contacts");
$stats['total'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as non_lus FROM contacts WHERE lu = 0");
$stats['non_lus'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as repondus FROM contacts WHERE repondu = 1");
$stats['repondus'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as aujourd_hui FROM contacts WHERE DATE(date_contact) = CURDATE()");
$stats['aujourd_hui'] = $stmt->fetchColumn();
?>

<!-- Section des statistiques (toujours affichée) -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="gmail-card stats-card">
            <div class="gmail-card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon primary">
                        <i class="fas fa-envelope fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 text-primary"><?= $stats['total'] ?></h3>
                        <small class="text-muted">Total des messages</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="gmail-card stats-card">
            <div class="gmail-card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon warning">
                        <i class="fas fa-envelope-open fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 text-warning"><?= $stats['non_lus'] ?></h3>
                        <small class="text-muted">Messages non lus</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="gmail-card stats-card">
            <div class="gmail-card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon success">
                        <i class="fas fa-reply fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 text-success"><?= $stats['repondus'] ?></h3>
                        <small class="text-muted">Messages répondus</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="gmail-card stats-card">
            <div class="gmail-card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon info">
                        <i class="fas fa-calendar-day fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 text-info"><?= $stats['aujourd_hui'] ?></h3>
                        <small class="text-muted">Messages d'aujourd'hui</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vue détaillée d'un message -->
<?php if ($action === 'voir' && $message_detail): ?>
<div class="gmail-card">
    <div class="gmail-card-header d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-envelope-open me-2"></i>Message de <?= htmlspecialchars($message_detail['nom'] . ' ' . $message_detail['prenom']) ?></h4>
        <button class="btn btn-outline-secondary" onclick="loadSection('messages', 'liste')">
            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
        </button>
    </div>
    <div class="gmail-card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <h5><?= htmlspecialchars($message_detail['sujet']) ?></h5>
                    <p class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Reçu le <?= date('d/m/Y à H:i', strtotime($message_detail['date_contact'])) ?>
                    </p>
                </div>
                
                <div class="message-content bg-light p-3 rounded mb-3">
                    <h6>Message :</h6>
                    <p><?= nl2br(htmlspecialchars($message_detail['message'])) ?></p>
                </div>
                
                <?php if ($message_detail['reponse']): ?>
                <div class="response-content bg-primary text-white p-3 rounded">
                    <h6><i class="fas fa-reply me-1"></i>Réponse :</h6>
                    <p><?= nl2br(htmlspecialchars($message_detail['reponse'])) ?></p>
                    <small>
                        Répondu le <?= date('d/m/Y à H:i', strtotime($message_detail['date_reponse'])) ?>
                        <?php if ($message_detail['repondu_par']): ?>
                        par l'administrateur (ID: <?= htmlspecialchars($message_detail['repondu_par']) ?>)
                        <?php endif; ?>
                    </small>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <div class="contact-info bg-light p-3 rounded">
                    <h6><i class="fas fa-user me-1"></i>Informations de contact</h6>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($message_detail['nom'] . ' ' . $message_detail['prenom']) ?></p>
                    <p><strong>Email :</strong> <a href="mailto:<?= htmlspecialchars($message_detail['email']) ?>"><?= htmlspecialchars($message_detail['email']) ?></a></p>
                    <?php if ($message_detail['telephone']): ?>
                    <p><strong>Téléphone :</strong> <a href="tel:<?= htmlspecialchars($message_detail['telephone']) ?>"><?= htmlspecialchars($message_detail['telephone']) ?></a></p>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <span class="badge bg-<?= $message_detail['lu'] ? 'success' : 'warning' ?> me-2">
                            <?= $message_detail['lu'] ? 'Lu' : 'Non lu' ?>
                        </span>
                        <span class="badge bg-<?= $message_detail['repondu'] ? 'info' : 'secondary' ?>">
                            <?= $message_detail['repondu'] ? 'Répondu' : 'Non répondu' ?>
                        </span>
                    </div>
                    
                    <div class="mt-3">
                        <?php if (!$message_detail['repondu']): ?>
                        <button class="btn btn-primary btn-sm" onclick="loadSection('messages', 'repondre', <?= $message_detail['id'] ?>)">
                            <i class="fas fa-reply me-1"></i>Répondre
                        </button>
                        <?php endif; ?>
                        <button class="btn btn-danger btn-sm" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) { loadSection('messages', 'supprimer', <?= $message_detail['id'] ?>) }">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire de réponse -->
<?php elseif ($action === 'repondre' && $message_detail): ?>
<div class="gmail-card">
    <div class="gmail-card-header d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-reply me-2"></i>Répondre à <?= htmlspecialchars($message_detail['nom'] . ' ' . $message_detail['prenom']) ?></h4>
        <button class="btn btn-outline-secondary" onclick="loadSection('messages', 'voir', <?= $message_detail['id'] ?>)">
            <i class="fas fa-arrow-left me-1"></i>Retour au message
        </button>
    </div>
    <div class="gmail-card-body">
        <form method="POST" action="mvc_router.php?controller=Messages&action=envoyerReponse&id=<?= $message_detail['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Message original :</label>
                <div class="bg-light p-3 rounded">
                    <strong><?= htmlspecialchars($message_detail['sujet']) ?></strong><br>
                    <small class="text-muted">De : <?= htmlspecialchars($message_detail['nom'] . ' ' . $message_detail['prenom']) ?> (<?= htmlspecialchars($message_detail['email']) ?>)</small><br>
                    <p class="mt-2"><?= nl2br(htmlspecialchars($message_detail['message'])) ?></p>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="reponse" class="form-label">Votre réponse :</label>
                <textarea class="form-control" id="reponse" name="reponse" rows="8" required placeholder="Tapez votre réponse ici..."></textarea>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i>Envoyer la réponse
                </button>
                <button type="button" class="btn btn-secondary" onclick="loadSection('messages', 'voir', <?= $message_detail['id'] ?>)">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Liste des messages -->
<?php else: ?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-messages">
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
            const successMessage = document.getElementById('success-message-messages');
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
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="gmail-card">
    <div class="gmail-card-header d-flex justify-content-between align-items-center">
        <h4><i class="fas fa-envelope me-2"></i>Messages de contact</h4>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" onchange="filterMessages(this.value)" style="width: auto;">
                <option value="">Tous les messages</option>
                <option value="non_lu">Non lus</option>
                <option value="lu">Lus</option>
                <option value="repondu">Répondus</option>
            </select>
        </div>
    </div>
    <div class="gmail-card-body">
        <?php if (empty($messages)): ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun message</h5>
            <p class="text-muted">Aucun message de contact n'a été reçu pour le moment.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Expéditeur</th>
                        <th>Sujet</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                    <tr class="message-row" data-status="<?= $message['statut'] ?>">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-placeholder me-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <strong><?= htmlspecialchars($message['nom'] . ' ' . $message['prenom']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($message['email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($message['sujet']) ?></strong>
                            <?php if (!$message['lu']): ?>
                            <span class="badge bg-warning ms-2">Nouveau</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= date('d/m/Y H:i', strtotime($message['date_contact'])) ?>
                        </td>
                        <td>
                            <span class="badge badge-statut bg-<?= $message['lu'] ? 'success' : 'warning' ?> me-1">
                                <?= $message['lu'] ? 'Lu' : 'Non lu' ?>
                            </span>
                            <span class="badge bg-<?= $message['repondu'] ? 'info' : 'secondary' ?>">
                                <?= $message['repondu'] ? 'Répondu' : 'Non répondu' ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-info" 
                                        onclick="loadSection('messages', 'voir', <?= $message['id'] ?>)" 
                                        title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if (!$message['repondu']): ?>
                                <button class="btn btn-sm btn-outline-success" 
                                        onclick="loadSection('messages', 'repondre', <?= $message['id'] ?>)" 
                                        title="Répondre">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <?php endif; ?>
                                <form method="POST" action="mvc_router.php?controller=Messages&action=supprimer&id=<?= $message['id'] ?>" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
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
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<style>
.avatar-placeholder {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #0d6efd, #6f42c1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.message-content {
    border-left: 4px solid #0d6efd;
}

.response-content {
    border-left: 4px solid #ffffff;
}

.contact-info {
    border: 1px solid #dee2e6;
}

.message-row:hover {
    background-color: #f8f9fa;
}

.message-row[data-status="non_lu"] {
    background-color: #fff3cd;
}

.message-row[data-status="non_lu"]:hover {
    background-color: #ffeaa7;
}

/* Styles pour les statistiques */
.stats-card {
    transition: transform 0.2s ease-in-out;
    border: 1px solid #e9ecef;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.stats-icon.primary {
    background: linear-gradient(135deg, #0d6efd, #6f42c1);
    color: white;
}

.stats-icon.warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
}

.stats-icon.success {
    background: linear-gradient(135deg, #198754, #20c997);
    color: white;
}

.stats-icon.info {
    background: linear-gradient(135deg, #0dcaf0, #6f42c1);
    color: white;
}
</style>

<script>
function filterMessages(status) {
    console.log('🔍 Filtrage des messages avec statut:', status);
    
    const rows = document.querySelectorAll('.message-row');
    console.log('📊 Nombre de lignes trouvées:', rows.length);
    
    rows.forEach((row, index) => {
        const statusBadge = row.querySelector('.badge-statut');
        const replyBadge = row.querySelector('.badge:not(.badge-statut)');
        
        if (statusBadge) {
            const statusText = statusBadge.textContent.toLowerCase().trim();
            const replyText = replyBadge ? replyBadge.textContent.toLowerCase().trim() : '';
            
            console.log(`📋 Ligne ${index + 1}: Status = "${statusText}", Reply = "${replyText}"`);
            
            let shouldShow = false;
            
            if (status === '') {
                shouldShow = true;
                console.log(`✅ Ligne ${index + 1}: Affichée (tous les statuts)`);
            } else {
                switch (status) {
                    case 'non_lu':
                        shouldShow = statusText.includes('non lu');
                        break;
                    case 'lu':
                        shouldShow = statusText.includes('lu') && !statusText.includes('non lu');
                        break;
                    case 'repondu':
                        shouldShow = replyText.includes('répondu');
                        break;
                }
                
                console.log(`🔍 Ligne ${index + 1}: Filtre "${status}" = ${shouldShow}`);
            }
            
            row.style.display = shouldShow ? '' : 'none';
        } else {
            console.log(`❌ Ligne ${index + 1}: Pas de badge trouvé`);
        }
    });
    
    console.log('✅ Filtrage terminé');
}
</script>
