<?php
require_once 'php/config.php';
require_once 'php/entraineurs_data.php';

// Récupérer les données des entraîneurs
$entraineurs = getEntraineursData();
$stats = getEntraineursStats();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Entraîneurs - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        .entraineur-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .entraineur-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .specialite-badge {
            font-size: 0.9em;
            padding: 8px 16px;
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        .avatar-large {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="ASOD ACADEMIE" style="height: 40px; margin-right: 10px;">
                ASOD ACADEMIE
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
                        <a class="nav-link" href="organigramme.php">Organigramme</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="entraineurs.php">Entraîneurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="actualites.php">Actualités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formation.php">Formation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nos-joueurs.php">Nos Joueurs</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-whistle me-3"></i>
                        Nos Entraîneurs
                    </h1>
                    <p class="lead mb-4">
                        Découvrez notre équipe d'entraîneurs qualifiés et passionnés qui accompagnent nos joueurs vers l'excellence.
                    </p>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3 class="fw-bold"><?= $stats['actifs'] ?></h3>
                            <p class="mb-0">Entraîneurs Actifs</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="fw-bold"><?= $stats['specialites'] ?></h3>
                            <p class="mb-0">Spécialités</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="fw-bold"><?= $stats['total'] ?></h3>
                            <p class="mb-0">Total</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Liste des Entraîneurs -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2 class="text-center mb-5">
                        <i class="fas fa-users me-2"></i>
                        Notre Équipe d'Encadrement
                    </h2>
                    
                    <?php if (!empty($entraineurs)): ?>
                        <div class="row">
                            <?php foreach ($entraineurs as $entraineur): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card entraineur-card h-100">
                                        <div class="card-body text-center">
                                            <?php 
                                            $photoPath = $entraineur['photo'];
                                            $fullPhotoPath = '';
                                            $displayPath = '';
                                            
                                            if ($photoPath && strpos($photoPath, 'uploads/entraineurs/') === 0) {
                                                $fullPhotoPath = __DIR__ . '/' . $photoPath;
                                                $displayPath = $photoPath;
                                            } elseif ($photoPath && strpos($photoPath, 'images/entraineurs/') === 0) {
                                                $fullPhotoPath = __DIR__ . '/' . $photoPath;
                                                $displayPath = $photoPath;
                                            } elseif ($photoPath) {
                                                $fullPhotoPath = __DIR__ . '/images/entraineurs/' . $photoPath;
                                                $displayPath = 'images/entraineurs/' . $photoPath;
                                            } else {
                                                $fullPhotoPath = __DIR__ . '/images/entraineurs/default-avatar.svg';
                                                $displayPath = 'images/entraineurs/default-avatar.svg';
                                            }
                                            
                                            if (file_exists($fullPhotoPath)): ?>
                                                <img src="<?= htmlspecialchars($displayPath) ?>" 
                                                     alt="<?= htmlspecialchars($entraineur['prenom'] . ' ' . $entraineur['nom']) ?>" 
                                                     class="avatar-large rounded-circle mx-auto mb-3" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="avatar-large rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                                    <i class="fas fa-user text-white fa-2x"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <h5 class="card-title mb-2">
                                                <?= htmlspecialchars($entraineur['prenom'] . ' ' . $entraineur['nom']) ?>
                                            </h5>
                                            
                                            <span class="badge specialite-badge bg-primary mb-3">
                                                <?= htmlspecialchars(ucfirst($entraineur['specialite'])) ?>
                                            </span>
                                            
                                            <?php if (!empty($entraineur['equipes_noms'])): ?>
                                                <p class="text-muted small mb-0">
                                                    <i class="fas fa-futbol me-1"></i>
                                                    Équipes: <?= htmlspecialchars($entraineur['equipes_noms']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucun entraîneur disponible</h4>
                            <p class="text-muted">Nos entraîneurs seront bientôt présentés ici.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> ASOD ACADEMIE - Tous droits réservés</p>
            <p class="mb-0 text-muted">Association Sportive Oeil du Défi</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





