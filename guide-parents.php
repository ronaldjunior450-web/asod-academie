<?php
// Page Guide des Parents - ASOD ACADEMIE

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
    <title>Guide des Parents - ASOD ACADEMIE</title>
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
        
        .parent-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
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
        
        .principle-item {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link" href="organigramme.php">Organigramme</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formation.php">Formation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="guide-parents.php">Guide Parents</a>
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
            <h1 class="section-title" data-aos="fade-up" data-aos-delay="100">Guide des Parents</h1>
            <p class="lead text-white" data-aos="fade-up" data-aos-delay="200">
                Accompagner votre enfant dans sa formation footballistique
            </p>
        </div>

        <!-- Introduction -->
        <div class="card" data-aos="fade-up" data-aos-delay="300">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-heart parent-icon text-primary"></i>
                    <h3 class="text-primary">Le Rôle des Parents</h3>
                </div>
                
                <div class="alert alert-primary alert-custom">
                    <h5><i class="fas fa-lightbulb text-primary me-2"></i>Notre Message aux Parents</h5>
                    <p class="mb-0">Le rôle du parent doit être de guider, accompagner et soutenir son enfant à se former au football, tout en résistant à la tentation de s'immiscer dans le rôle de l'encadrant durant les entraînements et/ou les matchs.</p>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-child text-primary me-2"></i>Laissons les enfants être des enfants</h5>
                            <p>C'est important pour les parents de comprendre que l'enfant perdra sa motivation s'il ne se sent plus capable d'assouvir les attentes de ses parents dans leur implication sportive, en particulier en matière de sélections et/ou de résultats (compétition).</p>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-balance-scale text-primary me-2"></i>Équilibre et Flexibilité</h5>
                            <p>Être flexible/tolérant/s'adapter aux attentes des joueurs à chaque étape de leur développement permettra aux parents et à l'enfant d'aligner leurs attentes et leurs besoins. C'est essentiel dans la fixation d'objectifs réalistes.</p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning alert-custom mt-4">
                    <h5><i class="fas fa-shield-alt text-warning me-2"></i>Valeurs du Sport</h5>
                    <p class="mb-0">Par contre, lorsqu'il s'agit de défendre les valeurs du sport telles que le respect, le fair-play et le vivre-ensemble, les parents peuvent se montrer exigeants.</p>
                </div>
            </div>
        </div>

        <!-- À FAIRE -->
        <div class="card" data-aos="fade-up" data-aos-delay="400">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle parent-icon text-success"></i>
                    <h3 class="text-primary">À FAIRE par les Parents</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-hands-helping text-success me-2"></i>Soutenir et Encourager</h5>
                            <p>Soutenir son enfant, être patient avec lui et lui donner confiance à court et à long terme.</p>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-chart-line text-success me-2"></i>Focus sur les Progrès</h5>
                            <p>Encourager son enfant sur ses progrès et non sur le résultat du match.</p>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-balance-scale text-success me-2"></i>Équilibre de Vie</h5>
                            <p>Aider son enfant à trouver un équilibre entre le football et les autres activités telles que les études, les sorties, etc.</p>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="do-item">
                            <h5><i class="fas fa-users text-success me-2"></i>Encourager l'Équipe</h5>
                            <p>Encourager son enfant et ses équipiers, sans s'immiscer dans le rôle du formateur.</p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-success alert-custom mt-4">
                    <h5><i class="fas fa-star text-success me-2"></i>Comportement Exemplaire</h5>
                    <p class="mb-0">Avoir un comportement exemplaire en bord de terrain, tant envers l'arbitre que les adversaires.</p>
                </div>
            </div>
        </div>

        <!-- À NE PAS FAIRE -->
        <div class="card" data-aos="fade-up" data-aos-delay="500">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-times-circle parent-icon text-danger"></i>
                    <h3 class="text-primary">À NE PAS FAIRE par les Parents</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-4" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-trophy text-danger me-2"></i>Survaloriser la Victoire</h5>
                            <p>Placer trop d'importance dans la victoire, dans le résultat immédiat.</p>
                        </div>
                    </div>
                    <div class="col-md-4" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-user-times text-danger me-2"></i>Vivre ses Rêves</h5>
                            <p>Vivre ses propres rêves sportifs à travers ses enfants.</p>
                        </div>
                    </div>
                    <div class="col-md-4" col-12>
                        <div class="dont-item">
                            <h5><i class="fas fa-hand-paper text-danger me-2"></i>Exercer Trop d'Influence</h5>
                            <p>Exercer trop d'influence dans le développement sportif de son enfant.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conseils Pratiques -->
        <div class="card" data-aos="fade-up" data-aos-delay="600">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-lightbulb parent-icon text-warning"></i>
                    <h3 class="text-primary">Conseils Pratiques</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-comments text-primary me-2"></i>Communication</h5>
                            <ul>
                                <li>Écoutez votre enfant après les entraînements</li>
                                <li>Posez des questions sur son plaisir, pas seulement sur les résultats</li>
                                <li>Respectez ses choix et ses préférences</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-clock text-primary me-2"></i>Patience</h5>
                            <ul>
                                <li>Le développement sportif prend du temps</li>
                                <li>Chaque enfant progresse à son rythme</li>
                                <li>Les échecs font partie de l'apprentissage</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-graduation-cap text-primary me-2"></i>Équilibre</h5>
                            <ul>
                                <li>Le football ne doit pas prendre toute la place</li>
                                <li>Les études restent prioritaires</li>
                                <li>Encouragez d'autres activités</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-heart text-primary me-2"></i>Amour Inconditionnel</h5>
                            <ul>
                                <li>Aimez votre enfant pour ce qu'il est</li>
                                <li>Pas seulement pour ses performances</li>
                                <li>Votre soutien ne dépend pas des résultats</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directeur Technique -->
        <?php if ($directeurTechnique): ?>
        <div class="card" data-aos="fade-up" data-aos-delay="700">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-whistle text-primary" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-primary">Directeur Technique</h4>
                <h5 class="text-muted"><?= htmlspecialchars($directeurTechnique['nom'] . ' ' . $directeurTechnique['prenom']) ?></h5>
                <?php if (!empty($directeurTechnique['diplome'])): ?>
                    <p class="text-muted"><?= htmlspecialchars($directeurTechnique['diplome']) ?></p>
                <?php endif; ?>
                <p class="small">Votre partenaire dans la formation de votre enfant</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Boutons d'action -->
        <div class="text-center mt-5">
            <a href="index.php#inscription" class="btn btn-primary btn-lg me-3">
                <i class="fas fa-user-plus me-2"></i>Inscrire mon enfant
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


