<?php
// Section Galerie - Gestion de la galerie Gmail Style
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/../php/config.php';

// Connexion à la base de données
$pdo = getDBConnection();

// Variables d'initialisation
$message = '';
$error = '';
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$id = $_GET['id'] ?? $_POST['id'] ?? null;
$categorieFilter = $_GET['categorie'] ?? '';


// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        try {
            // Gestion de l'image
            $image_path = null;
            
            // Créer le dossier uploads s'il n'existe pas
            $upload_dir = '../../uploads/galerie/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Traitement de l'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                // Dossier cible basé sur la catégorie sélectionnée
                $categorieChoisie = $_POST['categorie'] ?? 'joueurs';
                $targetDirFs = dirname(__DIR__, 2) . '/images/players/' . $categorieChoisie;
                if (!is_dir($targetDirFs)) { mkdir($targetDirFs, 0755, true); }
                // Nom standard
                $timestamp = time();
                $image_filename = $categorieChoisie . '_' . $timestamp . '.' . $image_extension;
                $targetPathFs = $targetDirFs . '/' . $image_filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPathFs)) {
                    $image_path = 'images/players/' . $categorieChoisie . '/' . $image_filename;
                } else {
                    $image_path = null;
                }
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO galerie (titre, description, image_path, categorie, actif, date_creation) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_POST['titre'],
                $_POST['description'],
                $image_path,
                $_POST['categorie'],
                $_POST['statut'] ?? 1
            ]);
            
            $message = 'Image ajoutée à la galerie avec succès';
            
        } catch (Exception $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    }
}

// Traitement des actions GET (suppression, etc.)
if ($action === 'delete') {
        try {
            // Récupérer les informations de l'image avant suppression
            $stmt = $pdo->prepare("SELECT image_path, categorie FROM galerie WHERE id = ?");
            $stmt->execute([$id]);
            $imageData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$imageData) {
                $error = "Aucune image trouvée avec l'ID $id";
            } else {
                // Supprimer le fichier physique si il existe
                if ($imageData['image_path'] && file_exists('../../' . $imageData['image_path'])) {
                    unlink('../../' . $imageData['image_path']);
                }
                
                // Supprimer de la base de données
                $stmt = $pdo->prepare("DELETE FROM galerie WHERE id = ?");
                $result = $stmt->execute([$id]);
                $rowCount = $stmt->rowCount();
                
                if ($result && $rowCount > 0) {
                    // Retourner une réponse JSON pour AJAX
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Image supprimée avec succès',
                        'categorie' => $imageData['categorie']
                    ]);
                    exit;
                } else {
                    $error = "Erreur lors de la suppression de la base de données";
                }
            }
            
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression : " . $e->getMessage();
        }
    }
    
    if ($action === 'delete_file') {
        try {
            $image_path = $_POST['image_path'] ?? '';
            $categorie = $_POST['categorie'] ?? '';
            
            if ($image_path && file_exists('../../' . $image_path)) {
                if (unlink('../../' . $image_path)) {
                    // Retourner une réponse JSON pour AJAX
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Fichier supprimé avec succès',
                        'categorie' => $categorie
                    ]);
                    exit;
                } else {
                    $error = 'Impossible de supprimer le fichier';
                }
            } else {
                $error = 'Fichier non trouvé';
            }
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression du fichier : " . $e->getMessage();
        }
    }


// Les images seront récupérées dans la section des statistiques

// Catégories disponibles
$categories = [
    'entrainements' => 'Entraînements',
    'matchs' => 'Matchs',
    'evenements' => 'Événements',
    'joueurs' => 'Joueurs'
];
?>

<!-- Messages -->
<?php if (isset($message) && !empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message-galerie">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        // Éviter les redéclarations de variables
        if (typeof window.galerieMessageHandled === 'undefined') {
            window.galerieMessageHandled = true;
            
            // Faire disparaître automatiquement le message de succès après 2 secondes
            setTimeout(function() {
                const successMessage = document.getElementById('success-message-galerie');
                if (successMessage) {
                    successMessage.style.transition = 'opacity 0.5s ease-out';
                    successMessage.style.opacity = '0';
                    setTimeout(function() {
                        successMessage.remove();
                    }, 500);
                }
            }, 2000);
        }
    </script>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Statistiques de la galerie -->
<?php
// Calculer les statistiques
$stats = [];
if (isset($usingFs) && $usingFs) {
    // Statistiques basées sur le système de fichiers
    $baseDir = dirname(__DIR__, 2) . '/images/players';
    $subDirs = ['entrainements', 'matchs', 'evenements', 'joueurs'];
    $total = 0;
    foreach ($subDirs as $sub) {
        $dirPath = $baseDir . '/' . $sub;
        if (!is_dir($dirPath)) { continue; }
        $files = glob($dirPath . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
        $total += count($files);
    }
    $rootFiles = glob($baseDir . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
    $total += count($rootFiles);
    $stats['total'] = $total;
    $stats['publie'] = $total; // tout est considéré publié côté fichiers
    $stats['brouillon'] = 0;
    $stats['aujourd_hui'] = 0; // non calculable sans dates
} else {
    // Statistiques basées sur la base de données
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM galerie");
    $stats['total'] = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) as publie FROM galerie WHERE actif = 1");
    $stats['publie'] = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) as brouillon FROM galerie WHERE actif = 0");
    $stats['brouillon'] = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) as aujourd_hui FROM galerie WHERE DATE(date_creation) = CURDATE()");
    $stats['aujourd_hui'] = $stmt->fetchColumn();
}

// Récupérer les images de la galerie
$stmt = $pdo->query("SELECT * FROM galerie ORDER BY date_creation DESC");
$images_galerie = $stmt->fetchAll(PDO::FETCH_ASSOC);
$usingFs = false;

// Si aucun enregistrement en base, parcourir images/players/*
if (empty($images_galerie)) {
    $images_galerie = [];
    $baseDir = dirname(__DIR__, 2) . '/images/players';
    $webBase = 'images/players';
    $subDirs = ['entrainements', 'matchs', 'evenements', 'joueurs'];
    $usingFs = true;
    foreach ($subDirs as $sub) {
        $dirPath = $baseDir . '/' . $sub;
        if (!is_dir($dirPath)) { continue; }
        $files = glob($dirPath . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
        foreach ($files as $filePath) {
            $images_galerie[] = [
                'id' => null,
                'titre' => pathinfo($filePath, PATHINFO_FILENAME),
                'description' => '',
                'image_path' => $webBase . '/' . $sub . '/' . basename($filePath),
                'categorie' => $sub,
                'actif' => 1,
                '__source' => 'fs'
            ];
        }
    }
    // Fichiers au niveau racine de players
    $rootFiles = glob($baseDir . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
    foreach ($rootFiles as $filePath) {
        $images_galerie[] = [
            'id' => null,
            'titre' => pathinfo($filePath, PATHINFO_FILENAME),
            'description' => '',
            'image_path' => $webBase . '/' . basename($filePath),
            'categorie' => 'joueurs',
            'actif' => 1,
            '__source' => 'fs'
        ];
    }
}
?>

<style>
/* Galerie - styles d'affichage harmonisés */
.galerie-grid { row-gap: 1.25rem; }
.thumb-wrapper { position: relative; border-top-left-radius: .5rem; border-top-right-radius: .5rem; overflow: hidden; background: #f8f9fa; }
.galerie-thumb { display: block; width: 100%; height: 250px; object-fit: cover; object-position: center top; }
.galerie-actions { position: absolute; right: .5rem; bottom: .5rem; display: flex; gap: .5rem; }
.galerie-actions .btn { padding: .25rem .5rem; border-radius: .375rem; backdrop-filter: blur(2px); }
.galerie-badge { position: absolute; left: .5rem; bottom: .5rem; }
.galerie-card .gmail-card-body { padding: 0; }
.galerie-meta { padding: .75rem; }
</style>

<!-- Section des statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="gmail-card stats-card">
            <div class="gmail-card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon primary">
                        <i class="fas fa-images fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 text-primary"><?= $stats['total'] ?></h3>
                        <small class="text-muted">Total des images</small>
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
                        <i class="fas fa-eye fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 text-success"><?= $stats['publie'] ?></h3>
                        <small class="text-muted">Images publiées</small>
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
                        <i class="fas fa-edit fa-lg"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 text-warning"><?= $stats['brouillon'] ?></h3>
                        <small class="text-muted">Brouillons</small>
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
                        <small class="text-muted">Ajoutées aujourd'hui</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Header de la section -->
<div class="gmail-card">
    <div class="gmail-card-header">
        <h2 class="gmail-card-title">
            <i class="fas fa-images me-2"></i>
            Gestion de la Galerie
        </h2>
        <div>
            <button class="gmail-btn" onclick="loadSection('galerie', 'add')">
                <i class="fas fa-plus"></i>
                Nouvelle image
            </button>
        </div>
    </div>
    <div class="gmail-card-body">
        <?php if ($action === 'add'): ?>
        <!-- Formulaire d'ajout -->
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Nouvelle image</h5>
            </div>
            <div class="gmail-card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
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
                                <label for="categorie" class="form-label">Catégorie *</label>
                                <select class="form-control" id="categorie" name="categorie" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image *</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="1">Publié</option>
                                    <option value="0">Brouillon</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="gmail-btn gmail-btn-secondary" onclick="loadSection('galerie')">
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

        <!-- Galerie d'images -->
        <?php if ($action === 'list' || empty($action)): ?>
        <div class="gmail-card">
            <div class="gmail-card-header">
                <h5 class="gmail-card-title mb-0">Galerie d'images</h5>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" onchange="filterGalerie(this.value)">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="gmail-card-body">
                <?php if (empty($images_galerie)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune image dans la galerie</h5>
                    <p class="text-muted">Commencez par ajouter votre première image.</p>
                    <button class="gmail-btn" onclick="loadSection('galerie', 'add')">
                        <i class="fas fa-plus"></i>
                        Ajouter une image
                    </button>
                </div>
                <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 row-cols-lg-5 galerie-grid">
                    <?php foreach ($images_galerie as $image): ?>
                    <div class="col galerie-item" data-categorie="<?= $image['categorie'] ?>">
                        <div class="gmail-card galerie-card">
                            <div class="gmail-card-body">
                                <div class="thumb-wrapper">
                                <?php 
                                $__fsPath = !empty($image['image_path']) ? dirname(__DIR__, 2) . '/' . $image['image_path'] : '';
                                if (!empty($image['image_path']) && file_exists($__fsPath)): ?>
                                <img src="/<?= htmlspecialchars($image['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($image['titre']) ?>" 
                                     class="galerie-thumb" data-fullsrc="/<?= htmlspecialchars($image['image_path']) ?>">
                                <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center rounded-top" 
                                     style="height: 220px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                                <?php endif; ?>
                                <div class="galerie-badge">
                                    <span class="badge bg-primary"><?= $categories[$image['categorie']] ?? ucfirst($image['categorie']) ?></span>
                                </div>
                                <div class="galerie-actions">
                                    <?php if (empty($image['__source'])): ?>
                                    <!-- Suppression depuis la base de données -->
                                    <button class="btn btn-danger btn-sm" 
                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) { loadSection('galerie', 'delete', <?= $image['id'] ?>) }" 
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php else: ?>
                                    <!-- Suppression depuis le système de fichiers -->
                                    <button class="btn btn-danger btn-sm" 
                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce fichier ? Cette action est irréversible.')) { deleteFile('<?= htmlspecialchars($image['image_path']) ?>', '<?= htmlspecialchars($image['categorie']) ?>') }" 
                                            title="Supprimer le fichier">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                                </div>
                                
                                <div class="galerie-meta">
                                    <h6 class="mb-1 text-truncate" title="<?= htmlspecialchars($image['titre']) ?>"><?= htmlspecialchars($image['titre']) ?></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted"><?= $categories[$image['categorie']] ?? ucfirst($image['categorie']) ?></small>
                                        <span class="badge bg-<?= $image['actif'] ? 'success' : 'warning' ?> badge-sm">
                                            <?= $image['actif'] ? '✓' : '✗' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Éviter les redéclarations de variables
if (typeof window.galerieScriptsLoaded === 'undefined') {
    window.galerieScriptsLoaded = true;
    
    // Fonction de filtrage de la galerie par catégorie
    window.filterGalerie = function(categorie) {
        const items = document.querySelectorAll('.galerie-item');
        items.forEach(item => {
            if (categorie === '' || item.dataset.categorie === categorie) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Mettre à jour le sélecteur de catégorie
        const selectElement = document.querySelector('select[onchange*="filterGalerie"]');
        if (selectElement) {
            selectElement.value = categorie;
        }
    };
    
    // Appliquer le filtre de catégorie au chargement de la page
    <?php if ($categorieFilter): ?>
    document.addEventListener('DOMContentLoaded', function() {
        window.filterGalerie('<?= htmlspecialchars($categorieFilter) ?>');
    });
    <?php endif; ?>
    
    // Nettoyer l'URL des paramètres de succès après affichage du message
    document.addEventListener('DOMContentLoaded', function() {
        const url = new URL(window.location);
        if (url.searchParams.has('success')) {
            // Attendre que le message soit affiché, puis nettoyer l'URL
            setTimeout(function() {
                url.searchParams.delete('success');
                window.history.replaceState({}, '', url);
            }, 100);
        }
        
        // Nettoyer aussi les paramètres d'erreur
        if (url.searchParams.has('error')) {
            setTimeout(function() {
                url.searchParams.delete('error');
                window.history.replaceState({}, '', url);
            }, 100);
        }
    });

    // Lightbox avec navigation gauche/droite
    window.currentIndex = 0;
    window.allImages = [];

document.addEventListener('click', function(e) {
    const img = e.target.closest('.galerie-thumb');
    if (!img) return;
    
    // Récupérer toutes les images visibles
    window.allImages = Array.from(document.querySelectorAll('.galerie-thumb')).map(el => ({
        src: el.getAttribute('data-fullsrc') || el.getAttribute('src'),
        alt: el.getAttribute('alt')
    }));
    
    window.currentIndex = window.allImages.findIndex(item => item.src === (img.getAttribute('data-fullsrc') || img.getAttribute('src')));
    if (window.currentIndex === -1) return;
    
    showLightbox();
});

function showLightbox() {
    let overlay = document.getElementById('galerie-lightbox');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'galerie-lightbox';
        overlay.style.cssText = `
            position: fixed; inset: 0; background: rgba(0,0,0,0.9); 
            display: flex; align-items: center; justify-content: center; 
            z-index: 1055; cursor: pointer;
        `;
        
        overlay.innerHTML = `
            <div style="position: relative; max-width: 90%; max-height: 90%; display: flex; align-items: center; justify-content: center;">
                <button id="lightbox-prev" style="position: absolute; left: -60px; background: rgba(255,255,255,0.2); border: none; color: white; width: 40px; height: 40px; border-radius: 50%; font-size: 18px; cursor: pointer; backdrop-filter: blur(10px);">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <img id="galerie-lightbox-img" style="max-width: 100%; max-height: 100%; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,.5);" alt="">
                <button id="lightbox-next" style="position: absolute; right: -60px; background: rgba(255,255,255,0.2); border: none; color: white; width: 40px; height: 40px; border-radius: 50%; font-size: 18px; cursor: pointer; backdrop-filter: blur(10px);">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div style="position: absolute; bottom: -50px; left: 50%; transform: translateX(-50%); color: white; font-size: 14px;">
                    <span id="lightbox-counter"></span>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Événements
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.remove();
        });
        
        document.getElementById('lightbox-prev').addEventListener('click', (e) => {
            e.stopPropagation();
            window.currentIndex = (window.currentIndex - 1 + window.allImages.length) % window.allImages.length;
            updateLightboxImage();
        });
        
        document.getElementById('lightbox-next').addEventListener('click', (e) => {
            e.stopPropagation();
            window.currentIndex = (window.currentIndex + 1) % window.allImages.length;
            updateLightboxImage();
        });
        
        // Navigation clavier
        document.addEventListener('keydown', handleKeydown);
    }
    
    updateLightboxImage();
}

function updateLightboxImage() {
    const img = document.getElementById('galerie-lightbox-img');
    const counter = document.getElementById('lightbox-counter');
    if (img && window.allImages[window.currentIndex]) {
        img.src = window.allImages[window.currentIndex].src;
        img.alt = window.allImages[window.currentIndex].alt;
        counter.textContent = `${window.currentIndex + 1} / ${window.allImages.length}`;
    }
}

function handleKeydown(e) {
    const overlay = document.getElementById('galerie-lightbox');
    if (!overlay) return;
    
    if (e.key === 'Escape') {
        overlay.remove();
        document.removeEventListener('keydown', handleKeydown);
    } else if (e.key === 'ArrowLeft') {
        window.currentIndex = (window.currentIndex - 1 + window.allImages.length) % window.allImages.length;
        updateLightboxImage();
    } else if (e.key === 'ArrowRight') {
        window.currentIndex = (window.currentIndex + 1) % window.allImages.length;
        updateLightboxImage();
    }
}

// Fonction pour supprimer un fichier du système de fichiers
async function deleteFile(imagePath, categorie) {
    try {
        // Créer les données POST
        const formData = new FormData();
        formData.append('action', 'delete_file');
        formData.append('image_path', imagePath);
        formData.append('categorie', categorie);
        
        // Envoyer la requête AJAX
        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                // Afficher le message de succès
                if (typeof showNotification === 'function') {
                    showNotification(data.message, 'success');
                } else {
                    alert(data.message);
                }
                
                // Recharger la galerie avec la catégorie
                if (data.categorie) {
                    loadSection('galerie', null, null, data.categorie);
                } else {
                    loadSection('galerie');
                }
            } else {
                if (typeof showNotification === 'function') {
                    showNotification(data.message || 'Erreur lors de la suppression', 'error');
                } else {
                    alert(data.message || 'Erreur lors de la suppression');
                }
            }
        } else {
            throw new Error('Erreur HTTP: ' + response.status);
        }
    } catch (error) {
        console.error('Erreur lors de la suppression:', error);
        if (typeof showNotification === 'function') {
            showNotification('Erreur lors de la suppression: ' + error.message, 'error');
        } else {
            alert('Erreur lors de la suppression: ' + error.message);
        }
    }
}
} // Fin de la condition window.galerieScriptsLoaded
</script>
