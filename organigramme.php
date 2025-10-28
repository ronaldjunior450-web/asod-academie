<?php
// Script pour afficher l'organigramme du bureau ASOD ACADEMIE depuis la base de données

require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les membres du bureau
    $stmt = $pdo->query("SELECT * FROM bureau WHERE actif = 1 ORDER BY ordre_affichage ASC");
    $membresBureau = $stmt->fetchAll();
    
    // Récupérer le directeur technique
    $stmt = $pdo->query("SELECT * FROM directeur_technique WHERE actif = 1 LIMIT 1");
    $directeurTechnique = $stmt->fetch();
    
} catch (Exception $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organigramme - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .section-title {
            color: white;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .text-primary {
            color: #0d6efd !important;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        .text-info {
            color: #17a2b8 !important;
        }
        
        .text-secondary {
            color: #6c757d !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #ffc107 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
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
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="ASOD ACADEMIE" class="logo-nav me-2" style="height: 40px; width: auto;">
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
                        <a class="nav-link" href="index.php#about">À Propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="organigramme.php">Organigramme</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nos-equipes.php">Nos Équipes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#inscription">Inscription</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container" style="margin-top: 100px; padding-bottom: 50px;">
        <div class="text-center mb-5">
            <h1 class="section-title" data-aos="fade-up">Organigramme du Bureau</h1>
            <p class="lead text-white" data-aos="fade-up" data-aos-delay="100">
                Découvrez les membres dirigeants de l'Association Sportive Oeil du Défi
            </p>
        </div>

        <!-- Membres du Bureau -->
        <div class="row">
            <?php foreach ($membresBureau as $index => $membre): ?>
                <div class="col-lg-4 col-md-6 mb-4" col-md-12 col-12 data-aos="fade-up" data-aos-delay="<?= ($index + 1) * 100 ?>">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <?php if (!empty($membre['photo']) && file_exists('images/bureau/' . $membre['photo'])): ?>
                                    <img src="images/bureau/<?= htmlspecialchars($membre['photo']) ?>" alt="Photo de <?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?>" 
                                         class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #0d6efd;">
                                <?php else: ?>
                                    <?php
                                    $icon = 'fas fa-user';
                                    $color = 'text-primary';
                                    
                                    switch($membre['poste']) {
                                    case 'Président':
                                        $icon = 'fas fa-crown';
                                        $color = 'text-warning';
                                        break;
                                    case '1er Vice-Président':
                                    case '2ème Vice-Président':
                                        $icon = 'fas fa-user-tie';
                                        $color = 'text-primary';
                                        break;
                                    case 'Trésorier Général':
                                    case 'Trésorière Adjointe':
                                        $icon = $membre['poste'] == 'Trésorier Général' ? 'fas fa-coins' : 'fas fa-piggy-bank';
                                        $color = 'text-success';
                                        break;
                                    case 'Secrétaire Général':
                                    case 'Secrétaire Adjoint':
                                        $icon = $membre['poste'] == 'Secrétaire Général' ? 'fas fa-file-alt' : 'fas fa-file-signature';
                                        $color = 'text-info';
                                        break;
                                    case 'Organisateur Général':
                                    case 'Organisateur Adjoint':
                                        $icon = $membre['poste'] == 'Organisateur Général' ? 'fas fa-calendar-alt' : 'fas fa-calendar-check';
                                        $color = 'text-warning';
                                        break;
                                }
                                    ?>
                                    <i class="<?= $icon ?> <?= $color ?>" style="font-size: 3rem;"></i>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title text-primary"><?= htmlspecialchars($membre['poste']) ?></h5>
                            <h6 class="text-muted"><?= htmlspecialchars($membre['nom'] . ' ' . $membre['prenom']) ?></h6>
                            <?php if (!empty($membre['profession'])): ?>
                                <p class="small text-muted"><?= prepareForForm($membre['profession']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($membre['email'])): ?>
                                <p class="small">
                                    <i class="fas fa-envelope me-1 text-primary"></i> 
                                    <a href="mailto:<?= htmlspecialchars($membre['email']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($membre['email']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($membre['telephone'])): ?>
                                <p class="small">
                                    <i class="fas fa-phone me-1 text-success"></i> 
                                    <a href="tel:<?= htmlspecialchars($membre['telephone']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($membre['telephone']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($membre['adresse'])): ?>
                                <p class="small"><i class="fas fa-map-marker-alt me-1 text-info"></i> <?= htmlspecialchars($membre['adresse']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($membre['biographie'])): ?>
                                <div class="mt-3">
                                    <p class="small text-muted fst-italic"><?= nl2br(htmlspecialchars($membre['biographie'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Directeur Technique -->
        <?php if ($directeurTechnique): ?>
        <div class="row mt-5">
            <div class="col-lg-6 mx-auto" col-md-12 col-12 data-aos="fade-up" data-aos-delay="1000">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-whistle text-primary" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="card-title text-primary">Directeur Technique</h4>
                        <h5 class="text-muted"><?= htmlspecialchars($directeurTechnique['nom'] . ' ' . $directeurTechnique['prenom']) ?></h5>
                        <?php if (!empty($directeurTechnique['diplome'])): ?>
                            <p class="text-muted"><?= htmlspecialchars($directeurTechnique['diplome']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($directeurTechnique['specialite'])): ?>
                            <p class="small"><?= htmlspecialchars($directeurTechnique['specialite']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Bouton retour -->
        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    </script>
</body>
</html>
