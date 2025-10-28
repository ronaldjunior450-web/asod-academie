<?php
// Page Galerie ASOD ACADEMIE
require_once 'php/config.php';

// Récupérer les images de la galerie depuis le système de fichiers
$images_galerie = [];
$baseDir = __DIR__ . '/images/players';
$webBase = 'images/players';
$subDirs = ['entrainements', 'matchs', 'evenements', 'joueurs'];

// Parcourir les sous-dossiers - Limiter à 9 photos par catégorie
foreach ($subDirs as $sub) {
    $dirPath = $baseDir . '/' . $sub;
    if (!is_dir($dirPath)) { continue; }
    
    $files = glob($dirPath . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
    // Limiter à 9 photos par catégorie
    $files = array_slice($files, 0, 9);
    
    foreach ($files as $filePath) {
        $images_galerie[] = [
            'titre' => pathinfo($filePath, PATHINFO_FILENAME),
            'image_path' => $webBase . '/' . $sub . '/' . basename($filePath),
            'categorie' => $sub,
            'actif' => 1
        ];
    }
}

// Pour les joueurs, privilégier les photos du sous-dossier
// Ne pas ajouter les fichiers racine pour les joueurs car ils sont des avatars

// Organiser par catégorie et compter le total
$categories = [
    'joueurs' => 'Joueurs',
    'entrainements' => 'Entraînements', 
    'matchs' => 'Matchs',
    'evenements' => 'Événements'
];

$images_par_categorie = [];
$total_par_categorie = [];

// Compter le total réel par catégorie (sans limite)
foreach ($subDirs as $sub) {
    $dirPath = $baseDir . '/' . $sub;
    if (!is_dir($dirPath)) { 
        $total_par_categorie[$sub] = 0;
        continue; 
    }
    $files = glob($dirPath . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
    $total_par_categorie[$sub] = count($files);
}

// Ne pas compter les fichiers racine pour les joueurs car ce sont des avatars

// Organiser les images affichées par catégorie
foreach ($images_galerie as $image) {
    $images_par_categorie[$image['categorie']][] = $image;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie Photos - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 50%, #ffc107 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .hero-section {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.9), rgba(26, 26, 46, 0.9));
            color: white;
            padding: 100px 0 60px;
            text-align: center;
        }
        
        .gallery-section {
            padding: 60px 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .filter-buttons {
            margin-bottom: 40px;
            text-align: center;
        }
        
        .filter-btn {
            margin: 5px;
            border-radius: 25px;
            padding: 10px 25px;
            border: 2px solid #0d6efd;
            background: transparent;
            color: #0d6efd;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: #0d6efd;
            color: white;
            transform: translateY(-2px);
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .gallery-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .gallery-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            object-position: center;
        }
        
        .gallery-content {
            padding: 20px;
        }
        
        .gallery-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .gallery-category {
            display: inline-block;
            background: #0d6efd;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .no-images {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-images i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        /* Modal pour l'image agrandie */
        .modal {
            background: rgba(0, 0, 0, 0.9);
        }
        
        .modal-content {
            background: transparent;
            border: none;
        }
        
        .modal-body {
            padding: 0;
            text-align: center;
        }
        
        .modal-image {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 10px;
        }
        
        .modal-title {
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="ASOD ACADEMIE" height="40">
                <span class="ms-2 fw-bold">ASOD ACADEMIE</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">À Propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nos-equipes.php">Nos Équipes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nos-joueurs.php">Nos Joueurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="entraineurs.php">Entraîneurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="actualites.php">Actualités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="evenements.php">Événements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="galerie.php">Galerie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formation.php">Formations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Galerie Photos</h1>
            <p class="lead">Découvrez nos moments forts, entraînements, matchs et événements</p>
        </div>
    </section>

    <!-- Galerie Section -->
    <section class="gallery-section">
        <div class="container">
            <!-- Filtres -->
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-th me-2"></i>Toutes les photos (<?= count($images_galerie) ?>)
                </button>
                <?php foreach ($categories as $key => $label): ?>
                    <button class="filter-btn" data-filter="<?= $key ?>">
                        <i class="fas fa-<?= $key === 'joueurs' ? 'users' : ($key === 'entrainements' ? 'dumbbell' : ($key === 'matchs' ? 'futbol' : 'calendar')) ?> me-2"></i>
                        <?= $label ?> (<?= $total_par_categorie[$key] ?? 0 ?>)
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Message informatif -->
            <div class="text-center mb-4">
                <p class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Affichage de 9 photos par catégorie (sur <?= array_sum($total_par_categorie) ?> photos au total)
                </p>
            </div>

            <!-- Grille des images -->
            <?php if (empty($images_galerie)): ?>
                <div class="no-images">
                    <i class="fas fa-images"></i>
                    <h3>Aucune photo disponible</h3>
                    <p>Les photos seront bientôt ajoutées à la galerie</p>
                </div>
            <?php else: ?>
                <div class="gallery-grid" id="galleryGrid">
                    <?php foreach ($images_galerie as $image): ?>
                        <div class="gallery-item" data-category="<?= $image['categorie'] ?>">
                            <img src="<?= htmlspecialchars($image['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($image['titre']) ?>" 
                                 class="gallery-image"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#imageModal"
                                 data-image="<?= htmlspecialchars($image['image_path']) ?>"
                                 data-title="<?= htmlspecialchars($image['titre']) ?>">
                            <div class="gallery-content">
                                <h4 class="gallery-title"><?= htmlspecialchars($image['titre']) ?></h4>
                                <span class="gallery-category">
                                    <?= $categories[$image['categorie']] ?? ucfirst($image['categorie']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal pour l'image agrandie -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <img id="modalImage" class="modal-image" src="" alt="">
                    <h5 id="modalTitle" class="modal-title"></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filtrage des images
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-item');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Retirer la classe active de tous les boutons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    galleryItems.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-category') === filter) {
                            item.style.display = 'block';
                            item.style.animation = 'fadeIn 0.5s ease-in-out';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
            
            // Modal pour l'image agrandie
            const imageModal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('modalTitle');
            
            imageModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imageSrc = button.getAttribute('data-image');
                const imageTitle = button.getAttribute('data-title');
                
                modalImage.src = imageSrc;
                modalTitle.textContent = imageTitle;
            });
        });
        
        // Animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
