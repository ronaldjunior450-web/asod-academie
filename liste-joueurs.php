<?php
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer tous les joueurs actifs avec leurs équipes
    $stmt = $pdo->query("
        SELECT m.*, e.nom as equipe_nom, e.description as equipe_description 
        FROM membres m 
        LEFT JOIN equipes e ON m.equipe_id = e.id 
        WHERE m.statut = 'actif' 
        ORDER BY m.genre ASC, e.id ASC, e.nom ASC, m.nom ASC, m.prenom ASC
    ");
    $joueurs = $stmt->fetchAll();
    
    // Séparer les joueurs par genre
    $garcons = array_filter($joueurs, fn($j) => ($j['genre'] ?? 'garcon') === 'garcon');
    $filles = array_filter($joueurs, fn($j) => ($j['genre'] ?? 'garcon') === 'fille');
    
} catch (Exception $e) {
    $joueurs = [];
    $garcons = [];
    $filles = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste Générale des Joueurs - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #fd7e14;
            --info-color: #0dcaf0;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            
            --gradient-primary: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            --gradient-secondary: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            --gradient-accent: linear-gradient(135deg, #0d6efd 0%, #ffc107 100%);
            --gradient-hero: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 50%, #ffc107 100%);
            
            --shadow-soft: 0 10px 30px rgba(13, 110, 253, 0.15);
            --shadow-hover: 0 20px 40px rgba(13, 110, 253, 0.25);
            --shadow-accent: 0 10px 30px rgba(255, 193, 7, 0.2);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .hero-section {
            background: var(--gradient-hero);
            color: white;
            padding: 60px 0;
        }
        
        .joueur-card {
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .joueur-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }
        
        .joueur-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
        }
        
        .joueur-photo-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
        }
        
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .equipe-badge {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
        }
        
        .genre-badge {
            font-size: 0.9em;
            padding: 6px 12px;
        }
        
        .bg-pink {
            background-color: #e91e63 !important;
            color: white;
        }
        
        .bg-blue {
            background-color: #2196F3 !important;
            color: white;
        }
    
        /* Mobile-First Responsive Design */
        @media (max-width: 768px) {
            /* Navigation Mobile */
            .navbar-brand {
                font-size: 1.4rem;
            }
            
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                font-size: 1rem;
            }
            
            /* Container Mobile */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            /* Sections Mobile */
            .py-5 {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }
            
            .mb-5 {
                margin-bottom: 2rem !important;
            }
            
            /* Cards Mobile */
            .card {
                margin-bottom: 1.5rem;
                border-radius: 15px;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            /* Tables Mobile */
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .table th,
            .table td {
                padding: 0.5rem;
            }
            
            /* Buttons Mobile */
            .btn {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
                width: 100%;
                margin-bottom: 1rem;
            }
            
            .btn-group .btn {
                width: auto;
                margin-bottom: 0;
            }
            
            /* Forms Mobile */
            .form-control, .form-select {
                padding: 0.75rem;
                font-size: 1rem;
                margin-bottom: 1rem;
            }
            
            /* Images Mobile */
            .img-fluid {
                border-radius: 10px;
                margin-bottom: 1rem;
            }
            
            /* Headers Mobile */
            h1 {
                font-size: 1.8rem;
            }
            
            h2 {
                font-size: 1.6rem;
            }
            
            h3 {
                font-size: 1.4rem;
            }
            
            h4 {
                font-size: 1.2rem;
            }
            
            /* Text Mobile */
            .lead {
                font-size: 1.1rem;
            }
            
            /* Grid Mobile */
            .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            .col-md-6,
            .col-md-4,
            .col-md-3,
            .col-lg-6,
            .col-lg-4,
            .col-lg-3 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
                margin-bottom: 1rem;
            }
            
            /* Player Cards Mobile */
            .player-card {
                margin-bottom: 1rem;
            }
            
            .player-info {
                padding: 1rem;
            }
            
            /* News Cards Mobile */
            .news-card {
                margin-bottom: 1.5rem;
            }
            
            .news-content {
                padding: 1rem;
            }
            
            /* Bureau Cards Mobile */
            .bureau-card {
                margin-bottom: 1.5rem;
            }
            
            /* Formation Cards Mobile */
            .formation-card {
                margin-bottom: 1.5rem;
            }
            
            /* Contact Form Mobile */
            .contact-form {
                padding: 1rem;
            }
            
            /* Footer Mobile */
            .footer {
                padding: 2rem 0;
                text-align: center;
            }
            
            .footer .row {
                text-align: center;
            }
            
            .footer .col-md-3,
            .footer .col-md-4 {
                margin-bottom: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            /* Extra Small Mobile */
            h1 {
                font-size: 1.6rem;
            }
            
            h2 {
                font-size: 1.4rem;
            }
            
            h3 {
                font-size: 1.2rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
        <div class="container">
            <a class="navbar-brand text-primary fw-bold d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="ASOD ACADEMIE" class="logo-nav me-2" style="height: 40px; width: auto;">
                ASOD ACADEMIE
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">À Propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">ASOD Académie</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Club
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="organigramme.php">Organigramme</a></li>
                            <li><a class="dropdown-item" href="formation.php">Formation</a></li>
                            <li><a class="dropdown-item" href="nos-joueurs.php">Nos Joueurs</a></li>
                            <li><a class="dropdown-item active" href="liste-joueurs.php">Liste Générale</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="guide-parents.php">Guide Parents</a></li>
                            <li><a class="dropdown-item" href="guide-formateurs.php">Guide Formateurs</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#inscription">Inscription</a>
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
        <div class="container text-center">
            <h1 class="display-4 mb-4">
                <i class="fas fa-list me-3"></i>Liste Générale des Joueurs
            </h1>
            <p class="lead">Tous nos joueurs organisés par genre</p>
        </div>
    </section>

    <!-- Statistiques -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4" col-12>
                    <div class="stats-card">
                        <div class="stats-number"><?php echo count($joueurs); ?></div>
                        <div class="stats-label">Total Joueurs</div>
                    </div>
                </div>
                <div class="col-md-4" col-12>
                    <div class="stats-card">
                        <div class="stats-number"><?php echo count($garcons); ?></div>
                        <div class="stats-label">Garçons</div>
                    </div>
                </div>
                <div class="col-md-4" col-12>
                    <div class="stats-card">
                        <div class="stats-number"><?php echo count($filles); ?></div>
                        <div class="stats-label">Filles</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Liste des joueurs -->
    <section class="py-5 bg-light">
        <div class="container">
            <?php if (empty($joueurs)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">Aucun joueur trouvé</h3>
                    <p class="text-muted">Les joueurs seront affichés ici une fois inscrits.</p>
                </div>
            <?php else: ?>
                <!-- Garçons -->
                <?php if (!empty($garcons)): ?>
                    <div class="section-header">
                        <h2><i class="fas fa-male me-2"></i>Garçons (<?php echo count($garcons); ?>)</h2>
                        <p class="mb-0">Tous nos joueurs masculins</p>
                    </div>
                    <div class="row">
                        <?php foreach ($garcons as $joueur): ?>
                            <div class="col-md-6 col-lg-4" col-12>
                                <div class="joueur-card">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <?php if ($joueur['photo'] && file_exists($joueur['photo'])): ?>
                                                <img src="<?php echo htmlspecialchars($joueur['photo']); ?>" 
                                                     alt="Photo de <?php echo htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']); ?>" 
                                                     class="joueur-photo">
                                            <?php else: ?>
                                                <div class="joueur-photo-placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">
                                                <?php echo htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']); ?>
                                            </h5>
                                            <p class="text-muted mb-2 small">
                                                <i class="fas fa-birthday-cake me-1"></i>
                                                Né le <?php echo date('d/m/Y', strtotime($joueur['date_naissance'])); ?>
                                            </p>
                                            <?php if ($joueur['equipe_nom']): ?>
                                                <span class="equipe-badge me-2">
                                                    <i class="fas fa-futbol me-1"></i><?php echo htmlspecialchars($joueur['equipe_nom']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="badge bg-blue genre-badge">
                                                <i class="fas fa-male me-1"></i>Garçon
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Filles -->
                <?php if (!empty($filles)): ?>
                    <div class="section-header">
                        <h2><i class="fas fa-female me-2"></i>Filles (<?php echo count($filles); ?>)</h2>
                        <p class="mb-0">Toutes nos joueuses</p>
                    </div>
                    <div class="row">
                        <?php foreach ($filles as $joueur): ?>
                            <div class="col-md-6 col-lg-4" col-12>
                                <div class="joueur-card">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <?php if ($joueur['photo'] && file_exists($joueur['photo'])): ?>
                                                <img src="<?php echo htmlspecialchars($joueur['photo']); ?>" 
                                                     alt="Photo de <?php echo htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']); ?>" 
                                                     class="joueur-photo">
                                            <?php else: ?>
                                                <div class="joueur-photo-placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">
                                                <?php echo htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']); ?>
                                            </h5>
                                            <p class="text-muted mb-2 small">
                                                <i class="fas fa-birthday-cake me-1"></i>
                                                Né le <?php echo date('d/m/Y', strtotime($joueur['date_naissance'])); ?>
                                            </p>
                                            <?php if ($joueur['equipe_nom']): ?>
                                                <span class="equipe-badge me-2">
                                                    <i class="fas fa-futbol me-1"></i><?php echo htmlspecialchars($joueur['equipe_nom']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="badge bg-pink genre-badge">
                                                <i class="fas fa-female me-1"></i>Fille
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6" col-12>
                    <h5><i class="fas fa-futbol me-2"></i>ASOD ACADEMIE</h5>
                    <p class="mb-0">Académie de football de Douala</p>
                </div>
                <div class="col-md-6 text-end" col-12>
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> ASOD ACADEMIE. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
