<?php
// Page Guide des Formateurs - ASOD ACADEMIE

require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
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
    <title>Guide des Formateurs - ASOD ACADEMIE</title>
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
            margin-bottom: 2rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
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
        
        .formateur-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .principle-item {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        
        .do-item {
            background: rgba(25, 135, 84, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #198754;
        }
        
        .dont-item {
            background: rgba(220, 53, 69, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #dc3545;
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .coaching-evolution {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
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
                        <a class="nav-link" href="index.php#about">ASOD Académie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formation.php">Engagements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="organigramme.php">Organigramme</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formation.php">Formation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guide-parents.php">Guide Parents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="guide-formateurs.php">Guide Formateurs</a>
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
        <!-- En-tête -->
        <div class="text-center mb-5">
            <div class="mb-4" data-aos="fade-up">
                <img src="images/logo.png" alt="ASOD ACADEMIE" class="logo-page" style="height: 100px; width: auto; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));">
            </div>
            <h1 class="section-title" data-aos="fade-up" data-aos-delay="100">Guide des Formateurs</h1>
            <p class="lead text-white" data-aos="fade-up" data-aos-delay="200">
                L'art de former les jeunes footballeurs
            </p>
        </div>

        <!-- Le Rôle du Formateur -->
        <div class="card" data-aos="fade-up" data-aos-delay="300">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-user-tie formateur-icon text-primary"></i>
                    <h3 class="text-primary">Le Rôle du Formateur</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-compass text-primary me-2"></i>Guide, pas Dirige</h5>
                            <p>Le formateur « guide » au lieu de « dirige ». Il accompagne le joueur dans son développement plutôt que de le contraindre.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-user-graduate text-primary me-2"></i>Développement Individuel</h5>
                            <p>Le formateur accorde toute son attention au développement individuel de chaque joueur pour lui permettre d'atteindre son plus haut niveau.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-shield-alt text-primary me-2"></i>Protection du Joueur</h5>
                            <p>Le formateur ne met jamais le jeune joueur en péril au profit du résultat. La sécurité et le bien-être priment.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-lightbulb text-primary me-2"></i>Environnement Stimulant</h5>
                            <p>Le formateur crée un environnement stimulant dans lequel le joueur rencontre un maximum d'expériences et de défis.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coaching et Comportement -->
        <div class="card" data-aos="fade-up" data-aos-delay="400">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-heart formateur-icon text-success"></i>
                    <h3 class="text-primary">Coaching et Comportement</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-thumbs-up text-success me-2"></i>Coaching Positif</h5>
                            <p>Le formateur coache chaque joueur d'une manière adéquate et positive, en adaptant la matière à leurs possibilités réelles.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-star text-success me-2"></i>Comportement Exemplaire</h5>
                            <p>Le formateur a un comportement exemplaire (politesse, hygiène de vie, ponctualité, etc.) pour servir de modèle.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Évolution du Coaching -->
                <div class="coaching-evolution" data-aos="fade-up" data-aos-delay="500">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-arrow-right me-2"></i>
                        Évolution du Coaching
                    </h4>
                    <div class="row">
                        <div class="col-md-6 text-center" col-12>
                            <h5><i class="fas fa-user-tie me-2"></i>Coaching Directif</h5>
                            <p>Le formateur coache directement les joueurs</p>
                        </div>
                        <div class="col-md-6 text-center" col-12>
                            <h5><i class="fas fa-users me-2"></i>Coaching Mutuel</h5>
                            <p>Les joueurs se coachent entre eux</p>
                        </div>
                    </div>
                    <p class="text-center mt-3 mb-0">
                        <strong>Le formateur évolue d'un coaching directif vers un coaching mutuel entre les joueurs</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- État d'Esprit -->
        <div class="card" data-aos="fade-up" data-aos-delay="600">
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="fas fa-brain formateur-icon text-warning"></i>
                    <h3 class="text-primary">ÊTRE FORMATEUR, C'EST AVANT TOUT UN ÉTAT D'ESPRIT</h3>
                </div>
                
                <div class="alert alert-warning alert-custom">
                    <h5><i class="fas fa-lightbulb text-warning me-2"></i>Philosophie de Formation</h5>
                    <p class="mb-0">La formation des jeunes footballeurs nécessite une approche pédagogique adaptée, une patience infinie et une passion pour le développement humain autant que sportif.</p>
                </div>
            </div>
        </div>

        <!-- À FAIRE -->
        <div class="card" data-aos="fade-up" data-aos-delay="700">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle formateur-icon text-success"></i>
                    <h3 class="text-primary">À FAIRE par le Formateur</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-user-check text-success me-2"></i>Autonomie du Joueur</h5>
                            <p><strong>Laisser le joueur, par lui-même :</strong></p>
                            <ul>
                                <li>Prendre ses décisions</li>
                                <li>Découvrir (expérience personnelle)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-hands-helping text-success me-2"></i>Soutien et Patience</h5>
                            <p>Soutenir le joueur, être patient avec lui et lui donner confiance à court et à long terme.</p>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-lightbulb text-success me-2"></i>Aide à la Décision</h5>
                            <p><strong>Aider le joueur :</strong></p>
                            <ul>
                                <li>À prendre la bonne décision</li>
                                <li>À trouver une solution</li>
                                <li>Avec un coaching positif</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-clock text-success me-2"></i>Équité du Temps de Jeu</h5>
                            <p>Donner à chaque joueur un temps de jeu équivalent pour assurer une progression équitable.</p>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-search text-success me-2"></i>Collaboration Détection</h5>
                            <p>Collaborer aux activités de détection organisées par ASOD Académie.</p>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-question-circle text-success me-2"></i>Stimulation par Questionnement</h5>
                            <p>Stimuler les joueurs via le questionnement et se concentrer sur leur développement.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- À NE PAS FAIRE -->
        <div class="card" data-aos="fade-up" data-aos-delay="800">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-times-circle formateur-icon text-danger"></i>
                    <h3 class="text-primary">À NE PAS FAIRE par le Formateur</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-user-times text-danger me-2"></i>Entraîner comme des Adultes</h5>
                            <p>Ne pas entraîner comme des adultes, avec un esprit de «championnite» démesuré.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-clock text-danger me-2"></i>Inégalité du Temps de Jeu</h5>
                            <p>Ne pas accorder à tous le même temps de jeu.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-comment-slash text-danger me-2"></i>Être Directif</h5>
                            <p>Ne pas être directif, sans échange avec les joueurs, du début à la fin de la séance.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-exclamation-triangle text-danger me-2"></i>Reproches Réguliers</h5>
                            <p>Ne pas faire des reproches réguliers aux jeunes joueurs moins doués.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-user-minus text-danger me-2"></i>Remplacer pour une Erreur</h5>
                            <p>Ne pas remplacer un enfant pour une erreur commise.</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-volume-up text-danger me-2"></i>Empêcher la Décision</h5>
                            <p>Ne pas empêcher un jeune de décider en lui criant sans cesse à l'avance ce qu'il doit faire.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directeur Technique -->
        <?php if ($directeurTechnique): ?>
        <div class="card" data-aos="fade-up" data-aos-delay="900">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-whistle text-primary" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-primary">Directeur Technique</h4>
                <h5 class="text-muted"><?= htmlspecialchars($directeurTechnique['nom'] . ' ' . $directeurTechnique['prenom']) ?></h5>
                <?php if (!empty($directeurTechnique['diplome'])): ?>
                    <p class="text-muted"><?= htmlspecialchars($directeurTechnique['diplome']) ?></p>
                <?php endif; ?>
                <p class="small">Votre guide dans la formation des jeunes joueurs</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Boutons d'action -->
        <div class="text-center mt-5">
            <a href="formation.php" class="btn btn-primary btn-lg me-3">
                <i class="fas fa-book me-2"></i>Document de Formation
            </a>
            <a href="index.php" class="btn btn-outline-light btn-lg">
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
