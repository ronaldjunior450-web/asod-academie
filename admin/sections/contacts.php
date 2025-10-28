<?php
// Section Contacts - Gestion des contacts Gmail Style
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$message = '';
$error = '';
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'reply') {
        try {
            // Récupérer le contact original
            $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            $contact = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($contact) {
                // Envoyer la réponse par email (simulation)
                $email_sent = mail(
                    $contact['email'],
                    'Re: ' . $contact['sujet'],
                    "Bonjour " . $contact['nom'] . ",\n\n" . $_POST['reponse'] . "\n\nCordialement,\nL'équipe ASOD ACADEMIE"
                );
                
                // Marquer comme lu et répondre
                $stmt = $pdo->prepare("UPDATE contacts SET statut = 'repondu', date_reponse = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                
                $message = 'Réponse envoyée avec succès' . ($email_sent ? '' : ' (simulation)');
            }
            
        } catch (Exception $e) {
            $error = "Erreur lors de l'envoi de la réponse : " . $e->getMessage();
        }
    }
    
    if ($action === 'mark_read') {
        try {
            $stmt = $pdo->prepare("UPDATE contacts SET statut = 'lu' WHERE id = ?");
            $stmt->execute([$id]);
            
            $message = 'Message marqué comme lu';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            
            $message = 'Message supprimé avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

// Récupérer un contact spécifique pour visualisation
$contact = null;
if ($id && $action === 'view') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement du message : " . $e->getMessage();
    }
}

// Récupérer tous les contacts
try {
    $stmt = $pdo->query("
        SELECT * FROM contacts 
        ORDER BY date_envoi DESC
    ");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des messages : " . $e->getMessage();
    $contacts = [];
}

// Statistiques des contacts
$stats = [
    'total' => count($contacts),
    'non_lus' => count(array_filter($contacts, fn($c) => $c['statut'] === 'non_lu')),
    'lus' => count(array_filter($contacts, fn($c) => $c['statut'] === 'lu')),
    'repondus' => count(array_filter($contacts, fn($c) => $c['statut'] === 'repondu'))
];
?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
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
            <i class="fas fa-envelope me-2"></i>
            Boîte de Réception
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
                            <i class="fas fa-inbox fa-2x text-primary"></i>
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
                            <i class="fas fa-envelope fa-2x text-warning"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['non_lus'] ?></h3>
                        <p class="text-muted mb-0">Non lus</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-envelope-open fa-2x text-info"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['lus'] ?></h3>
                        <p class="text-muted mb-0">Lus</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="gmail-card">
                    <div class="gmail-card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-reply fa-2x text-success"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['repondus'] ?></h3>
                        <p class="text-muted mb-0">Répondus</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($action === 'view' && $contact): ?>
        <!-- Modal Voir Contact -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Message de <?= htmlspecialchars($contact['nom'] . ' ' . $contact['prenom']) ?></h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('contacts')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <h4><?= htmlspecialchars($contact['sujet']) ?></h4>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?= $contact['statut'] === 'non_lu' ? 'warning' : ($contact['statut'] === 'repondu' ? 'success' : 'info') ?> me-2">
                                    <?= ucfirst($contact['statut']) ?>
                                </span>
                                <small class="text-muted">
                                    Reçu le <?= date('d/m/Y à H:i', strtotime($contact['date_envoi'])) ?>
                                </small>
                            </div>
                        </div>
                        
                        <div class="content mb-4">
                            <p><strong>De :</strong> <?= htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']) ?> (<?= htmlspecialchars($contact['email']) ?>)</p>
                            <p><strong>Téléphone :</strong> <?= htmlspecialchars($contact['telephone']) ?></p>
                            <hr>
                            <p><?= nl2br(htmlspecialchars($contact['message'])) ?></p>
                            
                            <?php if ($contact['reponse']): ?>
                            <hr>
                            <div class="alert alert-success">
                                <h6><i class="fas fa-reply me-2"></i>Réponse envoyée</h6>
                                <p><strong>Répondu par :</strong> <?= htmlspecialchars($contact['repondu_par'] ?? 'Admin') ?></p>
                                <p><strong>Date de réponse :</strong> <?= date('d/m/Y à H:i', strtotime($contact['date_reponse'])) ?></p>
                                <p><?= nl2br(htmlspecialchars($contact['reponse'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($contact['statut'] !== 'repondu'): ?>
                        <!-- Formulaire de réponse -->
                        <div class="gmail-card">
                            <div class="gmail-card-header">
                                <h6 class="gmail-card-title mb-0">Répondre</h6>
                            </div>
                            <div class="gmail-card-body">
                                <!-- Options de réponse -->
                                <div class="mb-3">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary active" id="replyViaAdmin">
                                            <i class="fas fa-desktop me-2"></i>Via l'interface admin
                                        </button>
                                        <button type="button" class="btn btn-outline-success" id="replyViaEmail">
                                            <i class="fas fa-envelope me-2"></i>Via email
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Formulaire de réponse via interface admin -->
                                <div id="adminReplyForm">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="reply">
                                        <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label for="reponse" class="form-label">Votre réponse</label>
                                            <textarea class="form-control" id="reponse" name="reponse" rows="6" required></textarea>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('contacts')">
                                                <i class="fas fa-arrow-left"></i>
                                                Annuler
                                            </button>
                                            <button type="submit" class="gmail-btn">
                                                <i class="fas fa-reply"></i>
                                                Envoyer la réponse
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Instructions pour réponse par email -->
                                <div id="emailReplyInstructions" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-envelope me-2"></i>Répondre par email</h6>
                                        <p>Pour répondre directement par email :</p>
                                        <ol>
                                            <li>Copiez l'email du visiteur : <strong><?= htmlspecialchars($contact['email']) ?></strong></li>
                                            <li>Créez un nouveau message dans votre client email</li>
                                            <li>Envoyez votre réponse</li>
                                            <li>Le système détectera automatiquement votre réponse</li>
                                        </ol>
                                        <p><small class="text-muted">💡 Conseil : Utilisez le même sujet que le message original pour faciliter la détection automatique.</small></p>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('contacts')">
                                            <i class="fas fa-arrow-left"></i>
                                            Retour
                                        </button>
                                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="copyEmail('<?= htmlspecialchars($contact['email']) ?>')">
                                            <i class="fas fa-copy"></i>
                                            Copier l'email
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <?php if ($contact['statut'] === 'non_lu'): ?>
                            <button class="gmail-btn gmail-btn-secondary" onclick="markAsRead(<?= $contact['id'] ?>)">
                                <i class="fas fa-envelope-open"></i>
                                Marquer comme lu
                            </button>
                            <?php endif; ?>
                            
                            <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('contacts')">
                                <i class="fas fa-list"></i>
                                Voir tous les messages
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Liste des contacts -->
        <?php if ($action === 'list' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Messages reçus</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterContacts(this.value)">
                        <option value="">Tous les statuts</option>
                        <option value="non_lu">Non lus</option>
                        <option value="lu">Lus</option>
                        <option value="repondu">Répondus</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Expéditeur</th>
                                <th>Sujet</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                            <tr class="<?= $contact['statut'] === 'non_lu' ? 'table-warning' : '' ?>">
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($contact['email']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($contact['sujet']) ?></strong>
                                </td>
                                <td>
                                    <?= htmlspecialchars(substr($contact['message'], 0, 100)) ?><?= strlen($contact['message']) > 100 ? '...' : '' ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($contact['date_envoi'])) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $contact['statut'] === 'non_lu' ? 'warning' : ($contact['statut'] === 'repondu' ? 'success' : 'info') ?>">
                                        <?= ucfirst($contact['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('contacts', 'view', <?= $contact['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($contact['statut'] === 'non_lu'): ?>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="markAsRead(<?= $contact['id'] ?>)" 
                                                title="Marquer comme lu">
                                            <i class="fas fa-envelope-open"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) { loadSection('contacts', 'delete', <?= $contact['id'] ?>) }" 
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
// Fonction de filtrage des contacts
function filterContacts(statut) {
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

// Fonction pour marquer comme lu
function markAsRead(id) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="mark_read">
        <input type="hidden" name="id" value="${id}">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Gestion des options de réponse
document.addEventListener('DOMContentLoaded', function() {
    const replyViaAdmin = document.getElementById('replyViaAdmin');
    const replyViaEmail = document.getElementById('replyViaEmail');
    const adminReplyForm = document.getElementById('adminReplyForm');
    const emailReplyInstructions = document.getElementById('emailReplyInstructions');
    
    if (replyViaAdmin && replyViaEmail) {
        replyViaAdmin.addEventListener('click', function() {
            this.classList.add('active');
            replyViaEmail.classList.remove('active');
            adminReplyForm.style.display = 'block';
            emailReplyInstructions.style.display = 'none';
        });
        
        replyViaEmail.addEventListener('click', function() {
            this.classList.add('active');
            replyViaAdmin.classList.remove('active');
            adminReplyForm.style.display = 'none';
            emailReplyInstructions.style.display = 'block';
        });
    }
});

// Fonction pour copier l'email
function copyEmail(email) {
    navigator.clipboard.writeText(email).then(function() {
        // Afficher une notification de succès
        const notification = document.createElement('div');
        notification.className = 'alert alert-success position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        notification.innerHTML = '<i class="fas fa-check me-2"></i>Email copié dans le presse-papiers';
        document.body.appendChild(notification);
        
        setTimeout(function() {
            notification.remove();
        }, 3000);
    }).catch(function(err) {
        console.error('Erreur lors de la copie: ', err);
    });
}
</script>
