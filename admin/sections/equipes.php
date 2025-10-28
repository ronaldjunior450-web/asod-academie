<?php
// Section Équipes - Gestion des équipes Gmail Style
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
$success = $_GET['success'] ?? '';
if ($success) {
    $success_messages = [
        'add' => 'Équipe ajoutée avec succès !',
        'update' => 'Équipe mise à jour avec succès !',
        'delete' => 'Équipe supprimée avec succès !'
    ];
    $message = $success_messages[$success] ?? 'Action réussie';
    
    // Stocker le message en session pour affichage unique
    if (!isset($_SESSION['message_displayed_' . $success])) {
        $_SESSION['message_displayed_' . $success] = true;
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
                INSERT INTO equipes (nom, categorie, genre, age_min, age_max, entraineur, 
                    couleur_maillot, couleur_short, couleur_chaussettes, actif, ordre_affichage, horaires_entrainement) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['nom'],
                $_POST['categorie'],
                $_POST['genre'],
                $_POST['age_min'],
                $_POST['age_max'],
                $_POST['entraineur'] ?? '',
                $_POST['couleur_maillot'] ?? '',
                $_POST['couleur_short'] ?? '',
                $_POST['couleur_chaussettes'] ?? '',
                $_POST['actif'] ?? 1,
                $_POST['ordre_affichage'] ?? 0,
                $_POST['horaires_entrainement'] ?? ''
            ]);
            
            $message = 'Équipe ajoutée avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
    
    if ($action === 'update') {
        try {
            $stmt = $pdo->prepare("
                UPDATE equipes 
                SET nom = ?, categorie = ?, genre = ?, age_min = ?, age_max = ?, 
                    entraineur = ?, couleur_maillot = ?, couleur_short = ?, couleur_chaussettes = ?, 
                    actif = ?, ordre_affichage = ?, horaires_entrainement = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['nom'],
                $_POST['categorie'],
                $_POST['genre'],
                $_POST['age_min'],
                $_POST['age_max'],
                $_POST['entraineur'] ?? '',
                $_POST['couleur_maillot'] ?? '',
                $_POST['couleur_short'] ?? '',
                $_POST['couleur_chaussettes'] ?? '',
                $_POST['actif'],
                $_POST['ordre_affichage'] ?? 0,
                $_POST['horaires_entrainement'] ?? '',
                $id
            ]);
            
            $message = 'Équipe mise à jour avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
    
    if ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM equipes WHERE id = ?");
            $stmt->execute([$id]);
            
            $message = 'Équipe supprimée avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
}

// Récupérer une équipe spécifique pour modification ou visualisation
$equipe = null;
if ($id && ($action === 'edit' || $action === 'view')) {
    try {
        $stmt = $pdo->prepare("
            SELECT e.*, ent.nom as entraineur_nom, ent.prenom as entraineur_prenom, ent.specialite as entraineur_specialite
            FROM equipes e
            LEFT JOIN entraineurs ent ON e.entraineur_id = ent.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        $equipe = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors du chargement de l'équipe : " . $e->getMessage();
    }
}

// Récupérer toutes les équipes avec leurs effectifs
try {
    $stmt = $pdo->query("
        SELECT e.*, 
               COUNT(m.id) as effectif_total,
               COUNT(CASE WHEN m.statut = 'actif' THEN 1 END) as effectif_actif
        FROM equipes e
        LEFT JOIN membres m ON e.id = m.equipe_id
        GROUP BY e.id
        ORDER BY e.genre, e.age_min
    ");
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors du chargement des équipes : " . $e->getMessage();
    $equipes = [];
}

// Récupérer tous les entraîneurs actifs
try {
    $stmt = $pdo->query("
        SELECT id, nom, prenom, specialite 
        FROM entraineurs 
        WHERE statut = 'actif'
        ORDER BY nom, prenom
    ");
    $entraineurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $entraineurs = [];
}
?>

<!-- Messages -->
<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-equipes">
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
            const successMessage = document.getElementById('success-message-equipes');
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


<?php 
// Messages d'erreur depuis l'URL
$errorParam = $_GET['error'] ?? '';
if ($errorParam === 'has_members'): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>Impossible de supprimer cette équipe car elle contient des membres. Veuillez d'abord transférer ou supprimer les membres.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($errorParam === 'delete_failed'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>Erreur lors de la suppression de l'équipe.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Header de la section -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-futbol me-2"></i>
            Gestion des Équipes
        </h2>
        <div>
            <button class="gmail-btn gmail-btn-secondary me-2">
                <i class="fas fa-download"></i>
                Exporter
            </button>
            <button class="gmail-btn" onclick="loadSection('equipes', 'ajouter')">
                <i class="fas fa-plus"></i>
                Nouvelle équipe
            </button>
        </div>
    </div>
    <div class="gmail-card-body">
        <?php if ($action === 'view' && $equipe): ?>
        <!-- Modal Voir Équipe -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Détails de l'équipe</h5>
                <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('equipes')">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
            </div>
            <div class="gmail-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3><?= htmlspecialchars($equipe['nom']) ?></h3>
                        <p><strong>Catégorie :</strong> <?= htmlspecialchars($equipe['categorie']) ?></p>
                        <p><strong>Genre :</strong> <?= ucfirst($equipe['genre']) ?></p>
                        <p><strong>Âge :</strong> <?= $equipe['age_min'] ?> - <?= $equipe['age_max'] ?> ans</p>
                        <p><strong>Entraîneur :</strong> 
                            <?php if ($equipe['entraineur_nom']): ?>
                                <?= htmlspecialchars($equipe['entraineur_nom'] . ' ' . $equipe['entraineur_prenom']) ?>
                                <?php if ($equipe['entraineur_specialite'] !== 'general'): ?>
                                    <small class="text-muted">(<?= ucfirst($equipe['entraineur_specialite']) ?>)</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Non défini</span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Horaires d'entraînement :</strong><br>
                            <small class="text-muted"><?= nl2br(htmlspecialchars($equipe['horaires_entrainement'] ?? 'Non définis')) ?></small>
                        </p>
                        <p><strong>Statut :</strong> 
                            <span class="badge bg-<?= $equipe['actif'] ? 'success' : 'secondary' ?>">
                                <?= $equipe['actif'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Couleurs de l'équipe</h5>
                        <div class="row">
                            <div class="col-4">
                                <div class="text-center">
                                    <?php
                                    $couleur_maillot = $equipe['couleur_maillot'] ?? '';
                                    $couleur_hex = '';
                                    switch(strtolower($couleur_maillot)) {
                                        // Couleurs de base
                                        case 'bleu': $couleur_hex = '#007bff'; break;
                                        case 'blanc': $couleur_hex = '#ffffff'; break;
                                        case 'rouge': $couleur_hex = '#dc3545'; break;
                                        case 'vert': $couleur_hex = '#28a745'; break;
                                        case 'jaune': $couleur_hex = '#ffc107'; break;
                                        case 'noir': $couleur_hex = '#000000'; break;
                                        case 'orange': $couleur_hex = '#fd7e14'; break;
                                        case 'violet': $couleur_hex = '#6f42c1'; break;
                                        case 'rose': $couleur_hex = '#e83e8c'; break;
                                        case 'gris': $couleur_hex = '#6c757d'; break;
                                        case 'marron': $couleur_hex = '#8b4513'; break;
                                        
                                        // Nuances de bleu
                                        case 'bleu ciel': $couleur_hex = '#87ceeb'; break;
                                        case 'bleu marine': $couleur_hex = '#000080'; break;
                                        case 'bleu foncé': $couleur_hex = '#0000cd'; break;
                                        case 'bleu clair': $couleur_hex = '#add8e6'; break;
                                        case 'cyan': $couleur_hex = '#00ffff'; break;
                                        case 'turquoise': $couleur_hex = '#40e0d0'; break;
                                        
                                        // Nuances de rouge
                                        case 'rouge foncé': $couleur_hex = '#8b0000'; break;
                                        case 'rouge clair': $couleur_hex = '#ffcccb'; break;
                                        case 'cramoisi': $couleur_hex = '#dc143c'; break;
                                        case 'bordeaux': $couleur_hex = '#800020'; break;
                                        case 'magenta': $couleur_hex = '#ff00ff'; break;
                                        
                                        // Nuances de vert
                                        case 'vert foncé': $couleur_hex = '#006400'; break;
                                        case 'vert clair': $couleur_hex = '#90ee90'; break;
                                        case 'vert lime': $couleur_hex = '#32cd32'; break;
                                        case 'vert olive': $couleur_hex = '#808000'; break;
                                        case 'vert menthe': $couleur_hex = '#98fb98'; break;
                                        case 'vert émeraude': $couleur_hex = '#50c878'; break;
                                        
                                        // Nuances de jaune
                                        case 'jaune foncé': $couleur_hex = '#b8860b'; break;
                                        case 'jaune clair': $couleur_hex = '#ffffe0'; break;
                                        case 'doré': $couleur_hex = '#ffd700'; break;
                                        case 'crème': $couleur_hex = '#f5f5dc'; break;
                                        case 'beige': $couleur_hex = '#f5f5dc'; break;
                                        
                                        // Nuances de violet
                                        case 'violet foncé': $couleur_hex = '#4b0082'; break;
                                        case 'violet clair': $couleur_hex = '#dda0dd'; break;
                                        case 'lavande': $couleur_hex = '#e6e6fa'; break;
                                        case 'indigo': $couleur_hex = '#4b0082'; break;
                                        case 'pourpre': $couleur_hex = '#800080'; break;
                                        
                                        // Nuances de rose
                                        case 'rose foncé': $couleur_hex = '#c71585'; break;
                                        case 'rose clair': $couleur_hex = '#ffb6c1'; break;
                                        case 'fuchsia': $couleur_hex = '#ff00ff'; break;
                                        case 'corail': $couleur_hex = '#ff7f50'; break;
                                        case 'saumon': $couleur_hex = '#fa8072'; break;
                                        
                                        // Nuances de gris
                                        case 'gris foncé': $couleur_hex = '#2f4f4f'; break;
                                        case 'gris clair': $couleur_hex = '#d3d3d3'; break;
                                        case 'argent': $couleur_hex = '#c0c0c0'; break;
                                        case 'charbon': $couleur_hex = '#36454f'; break;
                                        
                                        // Nuances de marron
                                        case 'marron foncé': $couleur_hex = '#654321'; break;
                                        case 'marron clair': $couleur_hex = '#d2b48c'; break;
                                        case 'châtain': $couleur_hex = '#8b4513'; break;
                                        case 'cuir': $couleur_hex = '#8b4513'; break;
                                        case 'bronze': $couleur_hex = '#cd7f32'; break;
                                        
                                        // Couleurs spéciales
                                        case 'or': $couleur_hex = '#ffd700'; break;
                                        case 'argent': $couleur_hex = '#c0c0c0'; break;
                                        case 'cuivre': $couleur_hex = '#b87333'; break;
                                        case 'platine': $couleur_hex = '#e5e4e2'; break;
                                        
                                        // Couleurs métalliques
                                        case 'métallique': $couleur_hex = '#c0c0c0'; break;
                                        case 'chrome': $couleur_hex = '#e5e5e5'; break;
                                        case 'acier': $couleur_hex = '#71797e'; break;
                                        
                                        // Couleurs naturelles
                                        case 'kaki': $couleur_hex = '#f0e68c'; break;
                                        case 'camouflage': $couleur_hex = '#78866b'; break;
                                        case 'terracotta': $couleur_hex = '#e2725b'; break;
                                        
                                        // Couleurs vives
                                        case 'néon': $couleur_hex = '#39ff14'; break;
                                        case 'fluo': $couleur_hex = '#ffff00'; break;
                                        case 'électrique': $couleur_hex = '#00ffff'; break;
                                        
                                        // Couleurs pastel
                                        case 'pastel': $couleur_hex = '#f8f8ff'; break;
                                        case 'pêche': $couleur_hex = '#ffcba4'; break;
                                        case 'lavande': $couleur_hex = '#e6e6fa'; break;
                                        
                                        // Couleurs sportives
                                        case 'maillot': $couleur_hex = '#007bff'; break;
                                        case 'domicile': $couleur_hex = '#007bff'; break;
                                        case 'extérieur': $couleur_hex = '#ffffff'; break;
                                        case 'troisième': $couleur_hex = '#ffc107'; break;
                                        
                                        // Couleurs par défaut
                                        default: $couleur_hex = '#ccc'; break;
                                    }
                                    ?>
                                    <div class="color-preview" style="width: 50px; height: 30px; background-color: <?= $couleur_hex ?> !important; border: 1px solid #ddd; margin: 0 auto; display: inline-block;"></div>
                                    <small>Maillot (<?= htmlspecialchars($couleur_maillot) ?>)</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <?php
                                    $couleur_short = $equipe['couleur_short'] ?? '';
                                    $couleur_hex_short = '';
                                    switch(strtolower($couleur_short)) {
                                        // Couleurs de base
                                        case 'bleu': $couleur_hex_short = '#007bff'; break;
                                        case 'blanc': $couleur_hex_short = '#ffffff'; break;
                                        case 'rouge': $couleur_hex_short = '#dc3545'; break;
                                        case 'vert': $couleur_hex_short = '#28a745'; break;
                                        case 'jaune': $couleur_hex_short = '#ffc107'; break;
                                        case 'noir': $couleur_hex_short = '#000000'; break;
                                        case 'orange': $couleur_hex_short = '#fd7e14'; break;
                                        case 'violet': $couleur_hex_short = '#6f42c1'; break;
                                        case 'rose': $couleur_hex_short = '#e83e8c'; break;
                                        case 'gris': $couleur_hex_short = '#6c757d'; break;
                                        case 'marron': $couleur_hex_short = '#8b4513'; break;
                                        
                                        // Nuances de bleu
                                        case 'bleu ciel': $couleur_hex_short = '#87ceeb'; break;
                                        case 'bleu marine': $couleur_hex_short = '#000080'; break;
                                        case 'bleu foncé': $couleur_hex_short = '#0000cd'; break;
                                        case 'bleu clair': $couleur_hex_short = '#add8e6'; break;
                                        case 'cyan': $couleur_hex_short = '#00ffff'; break;
                                        case 'turquoise': $couleur_hex_short = '#40e0d0'; break;
                                        
                                        // Nuances de rouge
                                        case 'rouge foncé': $couleur_hex_short = '#8b0000'; break;
                                        case 'rouge clair': $couleur_hex_short = '#ffcccb'; break;
                                        case 'cramoisi': $couleur_hex_short = '#dc143c'; break;
                                        case 'bordeaux': $couleur_hex_short = '#800020'; break;
                                        case 'magenta': $couleur_hex_short = '#ff00ff'; break;
                                        
                                        // Nuances de vert
                                        case 'vert foncé': $couleur_hex_short = '#006400'; break;
                                        case 'vert clair': $couleur_hex_short = '#90ee90'; break;
                                        case 'vert lime': $couleur_hex_short = '#32cd32'; break;
                                        case 'vert olive': $couleur_hex_short = '#808000'; break;
                                        case 'vert menthe': $couleur_hex_short = '#98fb98'; break;
                                        case 'vert émeraude': $couleur_hex_short = '#50c878'; break;
                                        
                                        // Nuances de jaune
                                        case 'jaune foncé': $couleur_hex_short = '#b8860b'; break;
                                        case 'jaune clair': $couleur_hex_short = '#ffffe0'; break;
                                        case 'doré': $couleur_hex_short = '#ffd700'; break;
                                        case 'crème': $couleur_hex_short = '#f5f5dc'; break;
                                        case 'beige': $couleur_hex_short = '#f5f5dc'; break;
                                        
                                        // Nuances de violet
                                        case 'violet foncé': $couleur_hex_short = '#4b0082'; break;
                                        case 'violet clair': $couleur_hex_short = '#dda0dd'; break;
                                        case 'lavande': $couleur_hex_short = '#e6e6fa'; break;
                                        case 'indigo': $couleur_hex_short = '#4b0082'; break;
                                        case 'pourpre': $couleur_hex_short = '#800080'; break;
                                        
                                        // Nuances de rose
                                        case 'rose foncé': $couleur_hex_short = '#c71585'; break;
                                        case 'rose clair': $couleur_hex_short = '#ffb6c1'; break;
                                        case 'fuchsia': $couleur_hex_short = '#ff00ff'; break;
                                        case 'corail': $couleur_hex_short = '#ff7f50'; break;
                                        case 'saumon': $couleur_hex_short = '#fa8072'; break;
                                        
                                        // Nuances de gris
                                        case 'gris foncé': $couleur_hex_short = '#2f4f4f'; break;
                                        case 'gris clair': $couleur_hex_short = '#d3d3d3'; break;
                                        case 'argent': $couleur_hex_short = '#c0c0c0'; break;
                                        case 'charbon': $couleur_hex_short = '#36454f'; break;
                                        
                                        // Nuances de marron
                                        case 'marron foncé': $couleur_hex_short = '#654321'; break;
                                        case 'marron clair': $couleur_hex_short = '#d2b48c'; break;
                                        case 'châtain': $couleur_hex_short = '#8b4513'; break;
                                        case 'cuir': $couleur_hex_short = '#8b4513'; break;
                                        case 'bronze': $couleur_hex_short = '#cd7f32'; break;
                                        
                                        // Couleurs spéciales
                                        case 'or': $couleur_hex_short = '#ffd700'; break;
                                        case 'argent': $couleur_hex_short = '#c0c0c0'; break;
                                        case 'cuivre': $couleur_hex_short = '#b87333'; break;
                                        case 'platine': $couleur_hex_short = '#e5e4e2'; break;
                                        
                                        // Couleurs métalliques
                                        case 'métallique': $couleur_hex_short = '#c0c0c0'; break;
                                        case 'chrome': $couleur_hex_short = '#e5e5e5'; break;
                                        case 'acier': $couleur_hex_short = '#71797e'; break;
                                        
                                        // Couleurs naturelles
                                        case 'kaki': $couleur_hex_short = '#f0e68c'; break;
                                        case 'camouflage': $couleur_hex_short = '#78866b'; break;
                                        case 'terracotta': $couleur_hex_short = '#e2725b'; break;
                                        
                                        // Couleurs vives
                                        case 'néon': $couleur_hex_short = '#39ff14'; break;
                                        case 'fluo': $couleur_hex_short = '#ffff00'; break;
                                        case 'électrique': $couleur_hex_short = '#00ffff'; break;
                                        
                                        // Couleurs pastel
                                        case 'pastel': $couleur_hex_short = '#f8f8ff'; break;
                                        case 'pêche': $couleur_hex_short = '#ffcba4'; break;
                                        case 'lavande': $couleur_hex_short = '#e6e6fa'; break;
                                        
                                        // Couleurs sportives
                                        case 'maillot': $couleur_hex_short = '#007bff'; break;
                                        case 'domicile': $couleur_hex_short = '#007bff'; break;
                                        case 'extérieur': $couleur_hex_short = '#ffffff'; break;
                                        case 'troisième': $couleur_hex_short = '#ffc107'; break;
                                        
                                        // Couleurs par défaut
                                        default: $couleur_hex_short = '#ccc'; break;
                                    }
                                    ?>
                                    <div class="color-preview" style="width: 50px; height: 30px; background-color: <?= $couleur_hex_short ?> !important; border: 1px solid #ddd; margin: 0 auto; display: inline-block;"></div>
                                    <small>Short (<?= htmlspecialchars($couleur_short) ?>)</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <?php
                                    $couleur_chaussettes = $equipe['couleur_chaussettes'] ?? '';
                                    $couleur_hex_chaussettes = '';
                                    switch(strtolower($couleur_chaussettes)) {
                                        // Couleurs de base
                                        case 'bleu': $couleur_hex_chaussettes = '#007bff'; break;
                                        case 'blanc': $couleur_hex_chaussettes = '#ffffff'; break;
                                        case 'rouge': $couleur_hex_chaussettes = '#dc3545'; break;
                                        case 'vert': $couleur_hex_chaussettes = '#28a745'; break;
                                        case 'jaune': $couleur_hex_chaussettes = '#ffc107'; break;
                                        case 'noir': $couleur_hex_chaussettes = '#000000'; break;
                                        case 'orange': $couleur_hex_chaussettes = '#fd7e14'; break;
                                        case 'violet': $couleur_hex_chaussettes = '#6f42c1'; break;
                                        case 'rose': $couleur_hex_chaussettes = '#e83e8c'; break;
                                        case 'gris': $couleur_hex_chaussettes = '#6c757d'; break;
                                        case 'marron': $couleur_hex_chaussettes = '#8b4513'; break;
                                        
                                        // Nuances de bleu
                                        case 'bleu ciel': $couleur_hex_chaussettes = '#87ceeb'; break;
                                        case 'bleu marine': $couleur_hex_chaussettes = '#000080'; break;
                                        case 'bleu foncé': $couleur_hex_chaussettes = '#0000cd'; break;
                                        case 'bleu clair': $couleur_hex_chaussettes = '#add8e6'; break;
                                        case 'cyan': $couleur_hex_chaussettes = '#00ffff'; break;
                                        case 'turquoise': $couleur_hex_chaussettes = '#40e0d0'; break;
                                        
                                        // Nuances de rouge
                                        case 'rouge foncé': $couleur_hex_chaussettes = '#8b0000'; break;
                                        case 'rouge clair': $couleur_hex_chaussettes = '#ffcccb'; break;
                                        case 'cramoisi': $couleur_hex_chaussettes = '#dc143c'; break;
                                        case 'bordeaux': $couleur_hex_chaussettes = '#800020'; break;
                                        case 'magenta': $couleur_hex_chaussettes = '#ff00ff'; break;
                                        
                                        // Nuances de vert
                                        case 'vert foncé': $couleur_hex_chaussettes = '#006400'; break;
                                        case 'vert clair': $couleur_hex_chaussettes = '#90ee90'; break;
                                        case 'vert lime': $couleur_hex_chaussettes = '#32cd32'; break;
                                        case 'vert olive': $couleur_hex_chaussettes = '#808000'; break;
                                        case 'vert menthe': $couleur_hex_chaussettes = '#98fb98'; break;
                                        case 'vert émeraude': $couleur_hex_chaussettes = '#50c878'; break;
                                        
                                        // Nuances de jaune
                                        case 'jaune foncé': $couleur_hex_chaussettes = '#b8860b'; break;
                                        case 'jaune clair': $couleur_hex_chaussettes = '#ffffe0'; break;
                                        case 'doré': $couleur_hex_chaussettes = '#ffd700'; break;
                                        case 'crème': $couleur_hex_chaussettes = '#f5f5dc'; break;
                                        case 'beige': $couleur_hex_chaussettes = '#f5f5dc'; break;
                                        
                                        // Nuances de violet
                                        case 'violet foncé': $couleur_hex_chaussettes = '#4b0082'; break;
                                        case 'violet clair': $couleur_hex_chaussettes = '#dda0dd'; break;
                                        case 'lavande': $couleur_hex_chaussettes = '#e6e6fa'; break;
                                        case 'indigo': $couleur_hex_chaussettes = '#4b0082'; break;
                                        case 'pourpre': $couleur_hex_chaussettes = '#800080'; break;
                                        
                                        // Nuances de rose
                                        case 'rose foncé': $couleur_hex_chaussettes = '#c71585'; break;
                                        case 'rose clair': $couleur_hex_chaussettes = '#ffb6c1'; break;
                                        case 'fuchsia': $couleur_hex_chaussettes = '#ff00ff'; break;
                                        case 'corail': $couleur_hex_chaussettes = '#ff7f50'; break;
                                        case 'saumon': $couleur_hex_chaussettes = '#fa8072'; break;
                                        
                                        // Nuances de gris
                                        case 'gris foncé': $couleur_hex_chaussettes = '#2f4f4f'; break;
                                        case 'gris clair': $couleur_hex_chaussettes = '#d3d3d3'; break;
                                        case 'argent': $couleur_hex_chaussettes = '#c0c0c0'; break;
                                        case 'charbon': $couleur_hex_chaussettes = '#36454f'; break;
                                        
                                        // Nuances de marron
                                        case 'marron foncé': $couleur_hex_chaussettes = '#654321'; break;
                                        case 'marron clair': $couleur_hex_chaussettes = '#d2b48c'; break;
                                        case 'châtain': $couleur_hex_chaussettes = '#8b4513'; break;
                                        case 'cuir': $couleur_hex_chaussettes = '#8b4513'; break;
                                        case 'bronze': $couleur_hex_chaussettes = '#cd7f32'; break;
                                        
                                        // Couleurs spéciales
                                        case 'or': $couleur_hex_chaussettes = '#ffd700'; break;
                                        case 'argent': $couleur_hex_chaussettes = '#c0c0c0'; break;
                                        case 'cuivre': $couleur_hex_chaussettes = '#b87333'; break;
                                        case 'platine': $couleur_hex_chaussettes = '#e5e4e2'; break;
                                        
                                        // Couleurs métalliques
                                        case 'métallique': $couleur_hex_chaussettes = '#c0c0c0'; break;
                                        case 'chrome': $couleur_hex_chaussettes = '#e5e5e5'; break;
                                        case 'acier': $couleur_hex_chaussettes = '#71797e'; break;
                                        
                                        // Couleurs naturelles
                                        case 'kaki': $couleur_hex_chaussettes = '#f0e68c'; break;
                                        case 'camouflage': $couleur_hex_chaussettes = '#78866b'; break;
                                        case 'terracotta': $couleur_hex_chaussettes = '#e2725b'; break;
                                        
                                        // Couleurs vives
                                        case 'néon': $couleur_hex_chaussettes = '#39ff14'; break;
                                        case 'fluo': $couleur_hex_chaussettes = '#ffff00'; break;
                                        case 'électrique': $couleur_hex_chaussettes = '#00ffff'; break;
                                        
                                        // Couleurs pastel
                                        case 'pastel': $couleur_hex_chaussettes = '#f8f8ff'; break;
                                        case 'pêche': $couleur_hex_chaussettes = '#ffcba4'; break;
                                        case 'lavande': $couleur_hex_chaussettes = '#e6e6fa'; break;
                                        
                                        // Couleurs sportives
                                        case 'maillot': $couleur_hex_chaussettes = '#007bff'; break;
                                        case 'domicile': $couleur_hex_chaussettes = '#007bff'; break;
                                        case 'extérieur': $couleur_hex_chaussettes = '#ffffff'; break;
                                        case 'troisième': $couleur_hex_chaussettes = '#ffc107'; break;
                                        
                                        // Couleurs par défaut
                                        default: $couleur_hex_chaussettes = '#ccc'; break;
                                    }
                                    ?>
                                    <div class="color-preview" style="width: 50px; height: 30px; background-color: <?= $couleur_hex_chaussettes ?> !important; border: 1px solid #ddd; margin: 0 auto; display: inline-block;"></div>
                                    <small>Chaussettes (<?= htmlspecialchars($couleur_chaussettes) ?>)</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <p><strong>Ordre d'affichage :</strong> <?= $equipe['ordre_affichage'] ?? 0 ?></p>
                        <p><strong>Date de création :</strong> <?= date('d/m/Y H:i', strtotime($equipe['date_creation'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <button class="gmail-btn" onclick="loadSection('equipes', 'modifier', <?= $equipe['id'] ?>)">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </button>
                            <button class="gmail-btn gmail-btn-secondary" onclick="loadSection('membres')">
                                <i class="fas fa-users"></i>
                                Voir les membres
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $equipe): ?>
        <!-- Formulaire de modification -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Modifier l'équipe</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Equipes&action=modifier&id=<?= $equipe['id'] ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $equipe['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom de l'équipe *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?= htmlspecialchars($equipe['nom']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorie" class="form-label">Catégorie *</label>
                                <select class="form-control" id="categorie" name="categorie" required>
                                    <option value="U11" <?= $equipe['categorie'] === 'U11' ? 'selected' : '' ?>>U11 (6-10 ans)</option>
                                    <option value="U13" <?= $equipe['categorie'] === 'U13' ? 'selected' : '' ?>>U13 (11-12 ans)</option>
                                    <option value="U15" <?= $equipe['categorie'] === 'U15' ? 'selected' : '' ?>>U15 (13-14 ans)</option>
                                    <option value="U17" <?= $equipe['categorie'] === 'U17' ? 'selected' : '' ?>>U17 (15-16 ans)</option>
                                    <option value="U20" <?= $equipe['categorie'] === 'U20' ? 'selected' : '' ?>>U20 (17-20 ans)</option>
                                    <option value="Seniors" <?= $equipe['categorie'] === 'Seniors' ? 'selected' : '' ?>>Seniors (20+ ans)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="genre" class="form-label">Genre *</label>
                                <select class="form-control" id="genre" name="genre" required>
                                    <option value="garcons" <?= $equipe['genre'] === 'garcons' ? 'selected' : '' ?>>Garçons</option>
                                    <option value="filles" <?= $equipe['genre'] === 'filles' ? 'selected' : '' ?>>Filles</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="age_min" class="form-label">Âge minimum *</label>
                                <input type="number" class="form-control" id="age_min" name="age_min" 
                                       value="<?= $equipe['age_min'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="age_max" class="form-label">Âge maximum *</label>
                                <input type="number" class="form-control" id="age_max" name="age_max" 
                                       value="<?= $equipe['age_max'] ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="entraineur_id" class="form-label">Entraîneur</label>
                                <select class="form-control" id="entraineur_id" name="entraineur_id">
                                    <option value="">Sélectionner un entraîneur</option>
                                    <?php foreach ($entraineurs as $entraineur): ?>
                                        <option value="<?= $entraineur['id'] ?>" <?= ($equipe['entraineur_id'] ?? '') == $entraineur['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($entraineur['nom'] . ' ' . $entraineur['prenom']) ?>
                                            <?php if ($entraineur['specialite'] !== 'general'): ?>
                                                (<?= ucfirst($entraineur['specialite']) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="horaires_entrainement" class="form-label">Horaires d'entraînement</label>
                                <textarea class="form-control" id="horaires_entrainement" name="horaires_entrainement" rows="2"><?= htmlspecialchars($equipe['horaires_entrainement'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="couleur_maillot" class="form-label">Couleur maillot</label>
                                <input type="text" class="form-control" id="couleur_maillot" name="couleur_maillot" 
                                       value="<?= htmlspecialchars($equipe['couleur_maillot'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="couleur_short" class="form-label">Couleur short</label>
                                <input type="text" class="form-control" id="couleur_short" name="couleur_short" 
                                       value="<?= htmlspecialchars($equipe['couleur_short'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="couleur_chaussettes" class="form-label">Couleur chaussettes</label>
                                <input type="text" class="form-control" id="couleur_chaussettes" name="couleur_chaussettes" 
                                       value="<?= htmlspecialchars($equipe['couleur_chaussettes'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ordre_affichage" class="form-label">Ordre d'affichage</label>
                                <input type="number" class="form-control" id="ordre_affichage" name="ordre_affichage" 
                                       value="<?= $equipe['ordre_affichage'] ?? 0 ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="actif" class="form-label">Statut</label>
                                <select class="form-control" id="actif" name="actif">
                                    <option value="1" <?= $equipe['actif'] ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= !$equipe['actif'] ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('equipes')">
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
                <h5 class="gmail-card-title mb-0">Nouvelle équipe</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" action="mvc_router.php?controller=Equipes&action=ajouter">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom de l'équipe *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorie" class="form-label">Catégorie *</label>
                                <select class="form-control" id="categorie" name="categorie" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="U11">U11 (6-10 ans)</option>
                                    <option value="U13">U13 (11-12 ans)</option>
                                    <option value="U15">U15 (13-14 ans)</option>
                                    <option value="U17">U17 (15-16 ans)</option>
                                    <option value="U20">U20 (17-20 ans)</option>
                                    <option value="Seniors">Seniors (20+ ans)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="genre" class="form-label">Genre *</label>
                                <select class="form-control" id="genre" name="genre" required>
                                    <option value="">Sélectionner un genre</option>
                                    <option value="garcons">Garçons</option>
                                    <option value="filles">Filles</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="age_min" class="form-label">Âge minimum *</label>
                                <input type="number" class="form-control" id="age_min" name="age_min" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="age_max" class="form-label">Âge maximum *</label>
                                <input type="number" class="form-control" id="age_max" name="age_max" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="entraineur_id" class="form-label">Entraîneur</label>
                                <select class="form-control" id="entraineur_id" name="entraineur_id">
                                    <option value="">Sélectionner un entraîneur</option>
                                    <?php foreach ($entraineurs as $entraineur): ?>
                                        <option value="<?= $entraineur['id'] ?>">
                                            <?= htmlspecialchars($entraineur['nom'] . ' ' . $entraineur['prenom']) ?>
                                            <?php if ($entraineur['specialite'] !== 'general'): ?>
                                                (<?= ucfirst($entraineur['specialite']) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="horaires_entrainement" class="form-label">Horaires d'entraînement</label>
                                <textarea class="form-control" id="horaires_entrainement" name="horaires_entrainement" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="couleur_maillot" class="form-label">Couleur maillot</label>
                                <input type="text" class="form-control" id="couleur_maillot" name="couleur_maillot">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="couleur_short" class="form-label">Couleur short</label>
                                <input type="text" class="form-control" id="couleur_short" name="couleur_short">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="couleur_chaussettes" class="form-label">Couleur chaussettes</label>
                                <input type="text" class="form-control" id="couleur_chaussettes" name="couleur_chaussettes">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ordre d'affichage</label>
                                <div class="form-control-plaintext text-muted">
                                    <i class="fas fa-info-circle"></i> Automatique (dernier + 1)
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="actif" class="form-label">Statut</label>
                                <select class="form-control" id="actif" name="actif">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('equipes')">
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

        <!-- Liste des équipes -->
        <?php if ($action === 'list' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Liste des équipes</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterEquipes(this.value)">
                        <option value="">Tous les genres</option>
                        <option value="garcons">Garçons</option>
                        <option value="filles">Filles</option>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Genre</th>
                                <th>Âge</th>
                                <th>Effectif</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equipes as $equipe): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($equipe['nom']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($equipe['categorie']) ?></span>
                                </td>
                                <td>
                                    <i class="fas fa-<?= $equipe['genre'] === 'garcons' ? 'male' : 'female' ?> me-1"></i>
                                    <?= ucfirst($equipe['genre']) ?>
                                </td>
                                <td>
                                    <?= $equipe['age_min'] ?> - <?= $equipe['age_max'] ?> ans
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $equipe['effectif_actif'] ?>/<?= $equipe['effectif_total'] ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $equipe['actif'] ? 'success' : 'secondary' ?>">
                                        <?= $equipe['actif'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="loadSection('equipes', 'voir', <?= $equipe['id'] ?>)" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="loadSection('equipes', 'modifier', <?= $equipe['id'] ?>)" 
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="mvc_router.php?controller=Equipes&action=supprimer&id=<?= $equipe['id'] ?>" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette équipe ?')">
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
// Fonction de filtrage des équipes
function filterEquipes(genre) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const genreCell = row.cells[2].textContent.toLowerCase();
        if (genre === '' || genreCell.includes(genre)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
