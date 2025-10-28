<?php
// Page de formation ASOD ACADEMIE - Document "La Formation des jeunes joueurs ASOD"

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
    <title>Méthode Pédagogique - ASOD ACADEMIE</title>
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
        
        .formation-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        /* Justification des textes */
        .card-body p,
        .card-body li,
        .alert p,
        .lead,
        .text-justify {
            text-align: justify;
        }
        
        .card-body h1,
        .card-body h2,
        .card-body h3,
        .card-body h4,
        .card-body h5,
        .card-body h6 {
            text-align: center;
        }
        
        .format-card {
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .format-card:hover {
            border-left-color: #ffc107;
            transform: translateX(10px);
        }
        
        .age-badge {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .principle-item {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        
        .toc {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .toc ul {
            list-style: none;
            padding-left: 0;
        }
        
        .toc li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .toc li:last-child {
            border-bottom: none;
        }
        
        .toc a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .toc a:hover {
            color: #ffc107;
            transform: translateX(5px);
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
                        <a class="nav-link" href="organigramme.php">Organigramme</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="formation.php">Formation</a>
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
        <!-- En-tête -->
        <div class="text-center mb-5">
            <div class="mb-4" data-aos="fade-up">
                <img src="images/logo.png" alt="ASOD ACADEMIE" class="logo-page" style="height: 100px; width: auto; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));">
            </div>
            <h1 class="section-title" data-aos="fade-up" data-aos-delay="100">Méthode Pédagogique ASOD</h1>
            <p class="lead text-white" data-aos="fade-up" data-aos-delay="200">
                Notre approche de formation des jeunes footballeurs - Philosophie et méthodes d'entraînement ASOD
            </p>
        </div>

        <!-- Navigation par onglets -->
        <div class="card mb-4" data-aos="fade-up" data-aos-delay="250">
            <div class="card-body">
                <ul class="nav nav-tabs nav-justified" id="formationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab" aria-controls="content" aria-selected="true">
                            <i class="fas fa-book me-2"></i>Contenu Formation
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="evaluations-tab" data-bs-toggle="tab" data-bs-target="#evaluations" type="button" role="tab" aria-controls="evaluations" aria-selected="false">
                            <i class="fas fa-chart-line me-2"></i>Évaluations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button" role="tab" aria-controls="sessions" aria-selected="false">
                            <i class="fas fa-calendar-alt me-2"></i>Sessions
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="formationTabsContent">
            <!-- Onglet Contenu Formation -->
            <div class="tab-pane fade show active" id="content" role="tabpanel" aria-labelledby="content-tab">
                <!-- Table des matières -->
        <div class="toc" data-aos="fade-up" data-aos-delay="200">
            <h3 class="text-primary mb-4"><i class="fas fa-list me-2"></i>Sommaire</h3>
            <ul>
                <li><a href="#introduction">1. Introduction</a></li>
                <li><a href="#formation-ecole-vie">2. Formation = Le football est une école de la vie</a></li>
                <li><a href="#climat-environnement">3. Climat d'environnement et formation</a></li>
                <li><a href="#apprendre-en-jouant">4. Apprendre en jouant</a></li>
                <li><a href="#zone-philosophie">5. La zone comme philosophie de formation</a></li>
                <li><a href="#seance-entrainement">6. La séance d'entraînement : méthodes progressive et GAG</a></li>
                <li><a href="#plan-apprentissage">7. Le plan d'apprentissage et les formats de jeu</a></li>
                <li><a href="#principes-tactiques">8. Principes tactiques ASOD ACADEMIE</a></li>
                <li><a href="#principes-jeu">9. Les principes de jeu qui peuvent être développés quel que soit le dispositif tactique</a></li>
            </ul>
        </div>

        <!-- Introduction -->
        <div class="card" id="introduction" data-aos="fade-up" data-aos-delay="300">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-play-circle formation-icon text-primary"></i>
                    <h3 class="text-primary">1. Introduction</h3>
                </div>
                <p class="lead">Au travers des évolutions des méthodes d'entraînement, des réformes des modes de jeu, et des confrontations des conceptions de formation du jeune footballeur et footballeuse, ASOD veut maintenir un cap quant à une vision de formation qu'elle juge optimale.</p>
                
                <div class="row mt-4">
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-bullseye text-primary me-2"></i>Notre Vision</h5>
                            <p>Tout en intégrant dorénavant l'ensemble des différentes tendances d'apprentissage du football en zone, elle garde comme fil conducteur les principes de jeu prônés par la Direction Technique Nationale (DTN).</p>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-bridge text-primary me-2"></i>Notre Approche</h5>
                            <p>Former un jeune footballeur ou une jeune footballeuse pour qu'il devienne un joueur ou une joueuse de football adulte équivaut à ériger un pont solide soutenu par des piliers de FORMATION.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ASOD Académie -->
        <div class="card" data-aos="fade-up" data-aos-delay="300">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-graduation-cap formation-icon text-primary"></i>
                    <h3 class="text-primary">ASOD Académie</h3>
                </div>
                
                <div class="alert alert-primary mb-4">
                    <h5><i class="fas fa-star text-primary me-2"></i>Notre Objectif</h5>
                    <p class="mb-0">ASOD Académie a pour objectif de <strong>rendre le football plus attractif pour les jeunes</strong> en leur proposant des <strong>formats de jeu et un plan d'apprentissage adaptés</strong> à leur développement.</p>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4" col-12>
                        <div class="card border-success h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-heart text-success mb-3" style="font-size: 2.5rem;"></i>
                                <h5 class="text-primary">Attractivité</h5>
                                <p class="mb-0">Rendre le football <strong>plus attractif</strong> pour les jeunes joueurs et joueuses.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4" col-12>
                        <div class="card border-info h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-cogs text-info mb-3" style="font-size: 2.5rem;"></i>
                                <h5 class="text-primary">Adaptation</h5>
                                <p class="mb-0">Formats de jeu et plan d'apprentissage <strong>adaptés à leur développement</strong> psychomoteur et cognitif.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4" col-12>
                        <div class="card border-warning h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-trophy text-warning mb-3" style="font-size: 2.5rem;"></i>
                                <h5 class="text-primary">Excellence</h5>
                                <p class="mb-0">Un meilleur <strong>environnement et une meilleure éducation sportive</strong> pour une expérience optimale.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-4" col-12>
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <h5 class="text-primary">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    Environnement de qualité
                                </h5>
                                <p class="mb-0">Un <strong>meilleur environnement</strong> d'apprentissage qui favorise l'épanouissement et le développement des jeunes joueurs dans un cadre sécurisé et stimulant.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4" col-12>
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h5 class="text-primary">
                                    <i class="fas fa-book text-success me-2"></i>
                                    Éducation sportive
                                </h5>
                                <p class="mb-0">Une <strong>meilleure éducation sportive</strong> qui transmet les valeurs du sport, le respect, la discipline et l'esprit d'équipe.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-info mt-4">
                    <div class="card-body text-center">
                        <h5 class="text-primary">
                            <i class="fas fa-target text-info me-2"></i>
                            Résultat attendu
                        </h5>
                        <p class="mb-0">Une <strong>meilleure expérience</strong> qui <strong>fidélisera les jeunes sur le long terme</strong> et développera leur passion pour le football.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Les 4 Piliers de Formation -->
        <div class="card" data-aos="fade-up" data-aos-delay="400">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-layer-group formation-icon text-primary"></i>
                    <h3 class="text-primary">Les 4 Piliers de Formation</h3>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-map text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5>1. La philosophie de la zone</h5>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-graduation-cap text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5>2. Le plan d'apprentissage</h5>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-futbol text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5>3. Les formes de match et intermédiaires</h5>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-user text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5>4. Le joueur(se) acteur de sa formation</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formation = École de la vie -->
        <div class="card" id="formation-ecole-vie" data-aos="fade-up" data-aos-delay="500">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-graduation-cap formation-icon text-primary"></i>
                    <h3 class="text-primary">FORMATION = LE FOOTBALL EST UNE ÉCOLE DE LA VIE → ÉDUCATION</h3>
                </div>
                
                <div class="alert alert-primary">
                    <h5><i class="fas fa-lightbulb text-primary me-2"></i>Notre Philosophie</h5>
                    <p class="mb-0">La FORMATION d'un joueur de football repose sur un processus d'apprentissage dirigé vers un objectif final, orienté vers le match et basé sur une planification rigoureuse.</p>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-4" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-bullseye text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Objectif Final</h5>
                            <p>Que doit « savoir-faire » un(e) joueur(se) à la fin de sa formation</p>
                        </div>
                    </div>
                    <div class="col-md-4" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-futbol text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Orientation Match</h5>
                            <p>Que doit savoir faire le joueur ou la joueuse en match</p>
                        </div>
                    </div>
                    <div class="col-md-4" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-route text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Planification</h5>
                            <p>Que doit savoir faire un joueur ou une joueuse à chaque étape de sa formation</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5">
                    <h4 class="text-primary mb-4"><i class="fas fa-heart text-primary me-2"></i>Valeurs Éducatives du Football</h4>
                    
                    <div class="row">
                        <div class="col-md-6" col-12>
                            <div class="principle-item">
                                <h5><i class="fas fa-handshake text-success me-2"></i>Respect des Autres</h5>
                                <ul>
                                    <li>Formateurs, partenaires, adversaires</li>
                                    <li>Arbitres, spectateurs</li>
                                    <li>Respect de soi-même : repos, alimentation, écoute de son corps</li>
                                    <li>Respect du matériel, de l'infrastructure</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6" col-12>
                            <div class="principle-item">
                                <h5><i class="fas fa-shield-alt text-success me-2"></i>Respect des Principes</h5>
                                <ul>
                                    <li>Respect des lois du jeu (= fair-play)</li>
                                    <li>Respect des principes élémentaires de vie (= discipline)</li>
                                    <li>Éducation face à la corruption, aux drogues</li>
                                    <li>Lutte contre le racisme et la violence</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-4">
                        <h5><i class="fas fa-trophy text-warning me-2"></i>Apprentissage de la Compétition</h5>
                        <p class="mb-0">Le joueur ou la joueuse joue pour gagner mais apprend à perdre. La compétition est un outil d'apprentissage et de développement personnel.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Climat d'environnement et formation -->
        <div class="card" id="climat-environnement" data-aos="fade-up" data-aos-delay="600">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-leaf formation-icon text-success"></i>
                    <h3 class="text-primary">2. Climat d'environnement et formation</h3>
                </div>
                <p class="lead">L'environnement de formation joue un rôle crucial dans le développement des jeunes joueurs. ASOD ACADEMIE s'engage à créer un climat propice à l'apprentissage et à l'épanouissement.</p>
                
                <!-- Introduction sur la motivation -->
                <div class="alert alert-info mt-4">
                    <h5><i class="fas fa-lightbulb text-primary me-2"></i>La Motivation : Clé du Succès</h5>
                    <p class="mb-0">La motivation d'un joueur ou d'une joueuse à apprendre va dépendre de la confiance qu'il a dans sa capacité à effectuer l'activité. Si le joueur ou la joueuse réussit régulièrement la tâche, il se sentira compétent, sera motivé à poursuivre l'activité et y prendra beaucoup de plaisir.</p>
                    <p class="mt-2 mb-0"><strong>Il est essentiel de créer un climat d'environnement motivant s'appuyant sur la FORMATION.</strong></p>
                </div>
                
                <div class="alert alert-success mt-4">
                    <h5><i class="fas fa-users text-success me-2"></i>LE CLIMAT D'ENVIRONNEMENT = L'AFFAIRE DE TOUS</h5>
                    <p class="mb-0">Instaurer un climat d'environnement favorable autour du joueur est l'affaire de tous : parents, entraîneur ou formateur, club et fédération.</p>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-3" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-home text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Parents</h5>
                            <p>Soutien, encouragement et suivi du développement de leur enfant</p>
                        </div>
                    </div>
                    <div class="col-md-3" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-whistle text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Entraîneurs</h5>
                            <p>Formation technique, tactique et éducative des joueurs</p>
                        </div>
                    </div>
                    <div class="col-md-3" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-building text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Club</h5>
                            <p>Infrastructure, organisation et valeurs de l'association</p>
                        </div>
                    </div>
                    <div class="col-md-3" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-flag text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Fédération</h5>
                            <p>Règlementation, formation des éducateurs et développement du football</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apprendre en jouant -->
        <div class="card" id="apprendre-en-jouant" data-aos="fade-up" data-aos-delay="600">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-gamepad formation-icon text-warning"></i>
                    <h3 class="text-primary">Apprendre en Jouant</h3>
                </div>
                
                <div class="row">
                    <div class="col-md-8 mx-auto" col-12>
                        <div class="principle-item text-center">
                            <h5><i class="fas fa-star text-warning me-2"></i>Notre Approche Pédagogique</h5>
                            <p class="lead">Il faut offrir beaucoup de moments « amusants » dans la formation : du plaisir du jeu via le plaisir de l'entraînement vers le plaisir de compétition</p>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-4" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-futbol text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Plaisir du Jeu</h5>
                            <p>Découverte et exploration du football dans un cadre ludique et sécurisé.</p>
                        </div>
                    </div>
                    <div class="col-md-4" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-dumbbell text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Plaisir de l'Entraînement</h5>
                            <p>Développement des compétences techniques et tactiques dans la joie.</p>
                        </div>
                    </div>
                    <div class="col-md-4" col-12>
                        <div class="principle-item text-center">
                            <i class="fas fa-trophy text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5>Plaisir de Compétition</h5>
                            <p>Application des acquis dans un contexte compétitif motivant.</p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-4">
                    <h5><i class="fas fa-heart text-warning me-2"></i>Le Plaisir : Fondement de la Formation</h5>
                    <p class="mb-0">La sensation du plaisir est essentielle pour la confiance en soi, pour le développement du joueur et pour mieux jouer au football.</p>
                </div>
            </div>
        </div>

        <!-- La zone comme philosophie de formation -->
        <div class="card" id="zone-philosophie" data-aos="fade-up" data-aos-delay="600">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-chess-board formation-icon text-info"></i>
                    <h3 class="text-primary">3. La zone comme philosophie de formation</h3>
                </div>
                <p class="lead">La philosophie de la zone constitue le fondement de notre approche pédagogique. Elle permet aux jeunes joueurs de comprendre et d'appliquer les principes tactiques fondamentaux du football moderne.</p>
                
                <div class="alert alert-info">
                    <h5><i class="fas fa-lightbulb me-2"></i>Principe Clé</h5>
                    <p class="mb-0">L'apprentissage par zones favorise la compréhension du jeu, l'anticipation et la prise de décision rapide, éléments essentiels du football contemporain.</p>
                </div>
            </div>
        </div>

        <!-- La séance d'entraînement -->
        <div class="card" id="seance-entrainement" data-aos="fade-up" data-aos-delay="700">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-stopwatch formation-icon text-warning"></i>
                    <h3 class="text-primary">6. La séance d'entraînement : méthodes progressive et GAG</h3>
                </div>
                
                <div class="alert alert-info">
                    <h5><i class="fas fa-lightbulb text-info me-2"></i>Le Football des Rues</h5>
                    <p class="mb-0">LE FOOTBALL DES RUES a été pendant longtemps une des meilleures formes naturelles de formation au jeu de football.</p>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-8" col-md-12 col-12>
                        <h4 class="text-primary mb-3">
                            <i class="fas fa-graduation-cap text-warning me-2"></i>
                            Apprentissage basé sur la réalité du jeu
                        </h4>
                        <p class="lead">L'apprentissage doit être basé sur la réalité du jeu : les joueurs doivent être confrontés à des unités d'entraînement comprenant les ingrédients du véritable jeu de football afin de développer les qualités footballistiques nécessaires dans le 2 c 2, 3 c 3, 5 c 5, 8 c 8 et le 11 c 11.</p>
                    </div>
                    <div class="col-lg-4" col-md-12 col-12>
                        <img src="images/tactique/Image5.png" alt="Méthode Progressive et GAG" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                    </div>
                </div>

                <!-- Les 2 notions importantes -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-4" col-12>
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h5 class="text-primary">
                                    <i class="fas fa-futbol text-success me-2"></i>
                                    La Forme de Match (FM)
                                </h5>
                                <p>Tâche/exercice qui se rapproche le plus du match :</p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Terrain délimité</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Goal disposé sur la ligne de but + gardien</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Un ou plusieurs goals (petits ou grands)</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Un ballon</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Joueur(s) en possession du ballon</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Joueur(s) en perte de balle</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Application des lois réelles du jeu</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4" col-12>
                        <div class="card border-warning h-100">
                            <div class="card-body">
                                <h5 class="text-primary">
                                    <i class="fas fa-cogs text-warning me-2"></i>
                                    La Forme Intermédiaire (FI)
                                </h5>
                                <p>Tâche/exercice qui ne répond pas spécifiquement aux règles d'un match :</p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-warning me-2"></i>Échauffement</li>
                                    <li><i class="fas fa-check text-warning me-2"></i>Jeu</li>
                                    <li><i class="fas fa-check text-warning me-2"></i>Exercice de coordination</li>
                                    <li><i class="fas fa-check text-warning me-2"></i>Exercice de finition</li>
                                    <li><i class="fas fa-check text-warning me-2"></i>Etc.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Les 3 objectifs -->
                <div class="card border-primary">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-target text-primary me-2"></i>
                            CHAQUE SÉANCE VISE 3 OBJECTIFS
                        </h4>
                        <div class="row">
                            <div class="col-md-4 mb-4" col-12>
                                <div class="text-center p-3 border rounded h-100">
                                    <i class="fas fa-exchange-alt text-success" style="font-size: 3rem;"></i>
                                    <h5 class="text-primary mt-3">Loi de Transfert</h5>
                                    <p class="mb-0">Proposer des situations de jeu identiques au match</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4" col-12>
                                <div class="text-center p-3 border rounded h-100">
                                    <i class="fas fa-chart-line text-warning" style="font-size: 3rem;"></i>
                                    <h5 class="text-primary mt-3">Loi de la Progressivité</h5>
                                    <ul class="list-unstyled text-start">
                                        <li><i class="fas fa-arrow-right text-warning me-2"></i>Proposer des Formes de Match + ou - complexes que dans le match</li>
                                        <li><i class="fas fa-arrow-right text-warning me-2"></i>Proposer des Formes Intermédiaires plus simples</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4" col-12>
                                <div class="text-center p-3 border rounded h-100">
                                    <i class="fas fa-redo text-info" style="font-size: 3rem;"></i>
                                    <h5 class="text-primary mt-3">Loi de la Répétition</h5>
                                    <ul class="list-unstyled text-start">
                                        <li><i class="fas fa-arrow-right text-info me-2"></i>Répéter régulièrement des Formes de Match identiques au match</li>
                                        <li><i class="fas fa-arrow-right text-info me-2"></i>Répéter des Formes Intermédiaires</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Séance d'entraînement U10-U13 -->
                <div class="card border-info mt-4">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-child text-info me-2"></i>
                            LA SÉANCE D'ENTRAÎNEMENT U10-U13
                        </h4>
                        
                        <div class="row mb-4">
                            <div class="col-lg-8" col-md-12 col-12>
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-users text-warning me-2"></i>Caractéristiques Spécifiques</h5>
                                    <p class="mb-0">Tenant compte des caractéristiques spécifiques de l'enfant U10-U13, il y a lieu de lui proposer dans la semaine des activités adaptées à ses moyens et ses besoins, tant sur le plan cognitif, technique, mental, physique que tactique.</p>
                                </div>
                                
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-clock text-success me-2"></i>Structure de la Séance</h5>
                                    <p class="mb-0">La séance d'entraînement U10-U13 s'articulera autour de <strong>6 ateliers d'une durée de 10 minutes</strong> et d'un <strong>retour au calme de 5 minutes</strong>. La durée maximale de la séance est de <strong>1h15</strong>.</p>
                                </div>
                            </div>
                            <div class="col-lg-4" col-md-12 col-12>
                                <img src="images/tactique/Image6.png?v=2" alt="Séance d'entraînement U10-U13" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                            </div>
                        </div>

                        <div class="alert alert-primary">
                            <h5><i class="fas fa-star text-primary me-2"></i>Le Programme ASOD</h5>
                            <p class="mb-0">Le « PROGRAMME » de ASOD regorge de situations ludiques adaptées aux U10-U13 qui doivent être proposées sans modération tant lors du « FESTIFOOT » du week-end que durant l'entraînement de la semaine.</p>
                        </div>

                        <!-- Les 6 ateliers -->
                        <div class="row">
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-running text-success me-2"></i>
                                            1. Coordination Générale
                                        </h5>
                                        <p>Sous forme de jeu : se déplacer, courir, sauter, pousser, tirer, lancer, se tenir en équilibre, frapper, jeter, attraper, œil-main, œil-pied, vitesse de réaction.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-users text-info me-2"></i>
                                            2. Jeu Collectif
                                        </h5>
                                        <p>Avec ou sans ballon : style « chasseur », course-relais, mouvements, coopération, stratégie, perception de l'espace, anticiper, décider, réagir, accélérer, freiner, etc.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-warning h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-fist-raised text-warning me-2"></i>
                                            3. Duel
                                        </h5>
                                        <p>Du « 1c1 » sous différentes organisations avec évolution vers « 2c2 ».</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-danger h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-futbol text-danger me-2"></i>
                                            4. Conduite de Balle/Dribble
                                        </h5>
                                        <p>Sous forme de jeu pour développer la maîtrise du ballon.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-bullseye text-primary me-2"></i>
                                            5. Tir au But
                                        </h5>
                                        <p>Dans un grand but pour la réussite de l'enfant et développer la confiance.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-secondary h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-trophy text-secondary me-2"></i>
                                            6. Match
                                        </h5>
                                        <p><strong>2c2 (en U10)</strong> et <strong>3c3 (en U13)</strong> selon le format du Festifoot.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Retour au calme -->
                        <div class="card border-success mt-4">
                            <div class="card-body text-center">
                                <h5 class="text-primary">
                                    <i class="fas fa-heart text-success me-2"></i>
                                    Retour au Calme
                                </h5>
                                <p class="mb-0">Dès les U10, on apprend à aider à ranger le matériel en fin de séance (chacun a sa petite tâche).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Séance d'entraînement U15-U17 -->
                <div class="card border-warning mt-4">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-user-graduate text-warning me-2"></i>
                            LA SÉANCE D'ENTRAÎNEMENT U15-U17
                        </h4>
                        
                        <div class="row mb-4">
                            <div class="col-lg-8" col-md-12 col-12>
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-users text-info me-2"></i>Caractéristiques Spécifiques</h5>
                                    <p class="mb-0">La séance d'entraînement U15-U17 se composera <strong>6-7 ateliers d'une durée de 10 minutes</strong> et d'un <strong>retour au calme de 5 à 10 minutes</strong>. La durée maximale de la séance est de <strong>1h30</strong>.</p>
                                </div>
                            </div>
                            <div class="col-lg-4" col-md-12 col-12>
                                <img src="images/tactique/image7.png?v=1" alt="Séance d'entraînement U15-U17" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                            </div>
                        </div>

                        <!-- Les 6-7 ateliers -->
                        <div class="row">
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-running text-success me-2"></i>
                                            1. Coordination Générale
                                        </h5>
                                        <p>Avec ou sans ballon, sous forme de jeu : se déplacer, courir, sauter, pousser, tirer, lancer, se tenir en équilibre, frapper, jeter, attraper, œil-main, œil-pied, vitesse de réaction, échelle de rythme, cerceaux (appuis contrastés/différés), haies basses (max 20cm), mobilité : changements de direction, demi-tour ou tour complet sur soi-même.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-futbol text-primary me-2"></i>
                                            2. Maîtrise Technique
                                        </h5>
                                        <p>Conduite de balle, passe courte, dribble-feinte, contrôle sur balle basse.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-danger h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-bullseye text-danger me-2"></i>
                                            3. Tir au But
                                        </h5>
                                        <p>Tir à 10-15m dans un grand but, tir sur centre au sol.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-users text-info me-2"></i>
                                            4. Forme de Jeu Collectif
                                        </h5>
                                        <p>Avec ou sans ballon, style « chasseur », course-relais, mouvements, coopération, stratégie, perception de l'espace, anticiper, décider, réagir, accélérer, freiner, etc.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-warning h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-fist-raised text-warning me-2"></i>
                                            5. Duel / Match Réduit
                                        </h5>
                                        <p>Sur petit espace du « 1c1 » sous différentes organisations avec évolution vers 2c1, 2c2, 3c1, 3c2, 3c3, 4c2, 4c3, avec ou sans appuis, avec petits ou grands buts pour chaque équipe. Se rendre jouable, se démarquer, utilisation de l'espace et des partenaires. Voir les teamtactics.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-secondary h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-stopwatch text-secondary me-2"></i>
                                            6. Course Relais
                                        </h5>
                                        <p>Réagir à un signal visuel, auditif, tactile, cognitif, sous forme de course relais (distance courte, changement de direction, de déplacement, évitement d'obstacles).</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Retour au calme -->
                        <div class="card border-success mt-4">
                            <div class="card-body text-center">
                                <h5 class="text-primary">
                                    <i class="fas fa-heart text-success me-2"></i>
                                    Retour au Calme
                                </h5>
                                <p class="mb-0">Initiation au jonglage avec ou sans rebond, fixez des objectifs réalisables et différents à chacun des joueurs (= défis), avec « soccerpal » ou « sense-ball ».</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Séance d'entraînement U20+ -->
                <div class="card border-danger mt-4">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-trophy text-danger me-2"></i>
                            LA SÉANCE D'ENTRAÎNEMENT À PARTIR DES U20
                        </h4>
                        
                        <div class="row mb-4">
                            <div class="col-lg-8" col-md-12 col-12>
                                <div class="alert alert-danger">
                                    <h5><i class="fas fa-chart-line text-danger me-2"></i>Structure de la Séance</h5>
                                    <p class="mb-0">La séance est composée d'un <strong>échauffement</strong>, <strong>corps de séance</strong> et d'un <strong>retour au calme</strong>. Elle contient <strong>minimum 50% de formes de match</strong>.</p>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-target text-warning me-2"></i>Thème de la Séance</h5>
                                    <p class="mb-0">Le thème de la séance doit être en lien direct avec un des <strong>principes de jeu</strong>. Les tâches/exercices doivent intégrer les <strong>teamtactics</strong>, les <strong>basics</strong>, les aspects <strong>physiques</strong> et <strong>mentaux</strong>.</p>
                                </div>
                            </div>
                            <div class="col-lg-4" col-md-12 col-12>
                                <img src="images/tactique/image8.png?v=1" alt="Séance d'entraînement U20+" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                            </div>
                        </div>

                        <!-- Exemple de thème -->
                        <div class="alert alert-info mb-4">
                            <h5><i class="fas fa-lightbulb text-info me-2"></i>Exemple de Thème</h5>
                            <p class="mb-0">Le thème (teamtactic) <strong>« Verticaliser ou diagonaliser le plus précisément par une bonne circulation de balle »</strong> est en lien direct avec les teamtactics des U10-U13 et fait appel aux basics (enchaînement contrôle orienté +...), aux aspects mentaux (relations d'équipe) ou physiques (la durée de la séance ou d'une forme de match).</p>
                        </div>

                        <!-- Structure détaillée -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-4" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-fire text-success" style="font-size: 3rem;"></i>
                                        <h5 class="text-primary mt-3">Échauffement</h5>
                                        <p class="mb-0">Sous forme d'une geste technique et un exercice de coordination en lien avec le thème.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-futbol text-primary" style="font-size: 3rem;"></i>
                                        <h5 class="text-primary mt-3">Corps de Séance</h5>
                                        <p class="mb-0">Composé de forme(s) de match et forme(s) intermédiaire(s) en lien avec le thème.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4" col-12>
                                <div class="card border-warning h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-heart text-warning" style="font-size: 3rem;"></i>
                                        <h5 class="text-primary mt-3">Retour au Calme</h5>
                                        <p class="mb-0">Se fera sous forme d'un exercice de jonglage (U10-U13).</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Méthodologie -->
                        <div class="row">
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-repeat text-info me-2"></i>
                                            Répétition et Évolution
                                        </h5>
                                        <p>Une même séance doit/peut être répétée à travers un cycle (4 semaines) à condition de prévoir des évolutions dans les tâches/exercices proposés.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-graduation-cap text-success me-2"></i>
                                            Approche Pédagogique
                                        </h5>
                                        <p>Il est essentiel d'enseigner le bon geste et faire preuve de patience. Stimuler les joueurs via le questionnement et se concentrer sur le développement plutôt que sur les résultats.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Collaboration ASOD -->
                        <div class="card border-primary mt-4">
                            <div class="card-body text-center">
                                <h5 class="text-primary">
                                    <i class="fas fa-handshake text-primary me-2"></i>
                                    Collaboration ASOD
                                </h5>
                                <p class="mb-0">Collaborer aux activités de détection organisées par <strong>ASOD</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Le plan d'apprentissage et les formats de jeu -->
        <div class="card" id="plan-apprentissage" data-aos="fade-up" data-aos-delay="800">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-route formation-icon text-danger"></i>
                    <h3 class="text-primary">5. Le plan d'apprentissage et les formats de jeu</h3>
                </div>
                
                <div class="row mb-4">
                    <div class="col-lg-8" col-md-12 col-12>
                        <div class="alert alert-primary">
                            <h5><i class="fas fa-graduation-cap text-primary me-2"></i>Définition du Plan d'Apprentissage</h5>
                            <p class="mb-0">Le plan d'apprentissage définit les <strong>compétences techniques, tactiques, physiques et mentales</strong> à développer par format de jeu. Tenant compte qu'apprendre, c'est <strong>cumuler les acquis d'un format de jeu vers l'autre</strong>.</p>
                        </div>
                    </div>
                    <div class="col-lg-4" col-md-12 col-12>
                        <img src="images/tactique/image9.png?v=1" alt="Plan d'apprentissage et formats de jeu" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                    </div>
                </div>

                <!-- BASICS et TEAMTACTICS -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-4" col-12>
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-3">
                                    <i class="fas fa-cogs text-success me-2"></i>
                                    BASICS
                                </h4>
                                <p class="mb-0">Ensemble des <strong>compétences techniques et tactiques individuelles</strong> (compétences de base) dont un joueur a besoin pour exécuter l'action juste après avoir pris la bonne décision dans le <strong>11c11</strong>, indépendamment du système de jeu et de l'animation de jeu.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4" col-12>
                        <div class="card border-info h-100">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-3">
                                    <i class="fas fa-users text-info me-2"></i>
                                    TEAMTACTICS
                                </h4>
                                <p class="mb-0">Ensemble des <strong>actes pour mieux fonctionner comme individu dans une équipe</strong>, indépendamment du système de jeu et de l'animation de jeu, tout en utilisant les <strong>« basics »</strong> ainsi que les <strong>aptitudes physiques et mentales</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Format de jeu à 2 - U6 et jeu à 3 - U7 -->
                <div class="card border-success mt-4">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-child text-success me-2"></i>
                            FORMAT DE JEU À 2 - U6 ET JEU À 3 - U7
                        </h4>
                        
                        <div class="row mb-4">
                            <div class="col-lg-8" col-md-12 col-12>
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-futbol text-success me-2"></i>Formats de Jeu</h5>
                                    <p class="mb-0"><strong>U6-U7</strong> : <strong>2c2</strong> et <strong>3c3</strong></p>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-user-graduate text-info me-2"></i>Qui est l'enfant U6-U7 ?</h5>
                                    <p class="mb-0">L'enfant U6-U7 est en <strong>3e maternelle ou peut-être en 1ère primaire</strong> et découvre le football. Il se situe dans une <strong>phase d'exploration</strong> et veut découvrir de nouvelles choses. Ce n'est pas un adulte en miniature, il nécessite une <strong>approche spécifique</strong> afin d'éviter un abandon rapide de la pratique du football.</p>
                                </div>
                            </div>
                            <div class="col-lg-4" col-md-12 col-12>
                                <img src="images/tactique/image10.png?v=1" alt="Format de jeu U6-U7" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                            </div>
                        </div>

                        <!-- Caractéristiques du joueur U6-U7 -->
                        <div class="alert alert-warning mb-4">
                            <h5><i class="fas fa-list text-warning me-2"></i>Les caractéristiques d'un joueur U6-U7 :</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-copy text-primary me-2"></i>
                                            En période d'imitation
                                        </h6>
                                        <p class="mb-0">L'enfant s'attache à imiter l'adulte.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-running text-success me-2"></i>
                                            Besoin naturel de bouger
                                        </h6>
                                        <p class="mb-0">Besoin naturel de bouger, de jouer et de s'amuser.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-book text-info me-2"></i>
                                            Goût pour les histoires
                                        </h6>
                                        <p class="mb-0">Possède un goût prononcé pour les histoires mais aussi une belle disponibilité affective pour les apprentissages.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-warning h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-clock text-warning me-2"></i>
                                            Concentration faible
                                        </h6>
                                        <p class="mb-0">Capacité de concentration faible et des changements fréquents d'activité sont nécessaires pour le tenir en alerte.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-danger h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-gamepad text-danger me-2"></i>
                                            Très jouette
                                        </h6>
                                        <p class="mb-0">Très jouette, il s'intéresse à beaucoup de jeux et éprouve le besoin de varier et de renouveler des expériences.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-secondary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-home text-secondary me-2"></i>
                                            Découvre le football
                                        </h6>
                                        <p class="mb-0">Il découvre le football, fait ses premiers pas dans le club et y découvre un nouvel environnement.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-users text-primary me-2"></i>
                                            Apprend à vivre avec les autres
                                        </h6>
                                        <p class="mb-0">Apprend progressivement à vivre et à jouer avec les autres.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-expand-arrows-alt text-info me-2"></i>
                                            Perception de l'espace
                                        </h6>
                                        <p class="mb-0">Sa perception de l'espace est encore peu développée.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Habilités à développer -->
                        <div class="card border-success mt-4">
                            <div class="card-body text-center">
                                <h5 class="text-primary">
                                    <i class="fas fa-star text-success me-2"></i>
                                    Quelles sont les habilités à développer en U6-U7 ?
                                </h5>
                                <p class="mb-0">Voir l'image ci-dessus pour les détails des habilités à développer.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Festifoot U6-U7 -->
                <div class="card border-warning mt-4">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-star text-warning me-2"></i>
                            LES U6 JOUENT À 2 CONTRE 2 EN FESTIFOOT - LES U7 JOUENT À 3 CONTRE 3 EN FESTIFOOT
                        </h4>
                        
                        <div class="row mb-4">
                            <div class="col-lg-8" col-md-12 col-12>
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-chart-line text-warning me-2"></i>Réduction des Abandons</h5>
                                    <p class="mb-0">Plusieurs années d'observations, de réflexions et d'échanges ont été nécessaires pour comprendre et <strong>réduire le taux important d'abandons</strong> constaté auprès de nos plus jeunes enfants.</p>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-heart text-info me-2"></i>Environnement Stimulant</h5>
                                    <p class="mb-0">L'enfant de cinq ou six ans n'étant pas prêt pour notre football, nous avons voulu lui offrir un <strong>environnement plus stimulant</strong> dans lequel il puisse se développer harmonieusement et découvrir sereinement le monde du football.</p>
                                </div>
                            </div>
                            <div class="col-lg-4" col-md-12 col-12>
                                <img src="images/tactique/image11.png?v=1" alt="Festifoot U6-U7" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                            </div>
                        </div>

                        <!-- Philosophie du Festifoot -->
                        <div class="alert alert-success mb-4">
                            <h5><i class="fas fa-lightbulb text-success me-2"></i>Philosophie du Festifoot</h5>
                            <p class="mb-0">L'environnement « <strong>Festifoot</strong> » est idéal pour l'épanouissement des plus jeunes car il évite tout esprit de compétition et de rivalité entre les enfants, les parents, les formateurs et les clubs : <strong>on ne joue pas contre les autres mais avec les autres</strong>.</p>
                        </div>

                        <!-- Les 3 avantages du Festifoot -->
                        <div class="row">
                            <div class="col-md-4 mb-4" col-12>
                                <div class="card border-danger h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-heart text-danger" style="font-size: 3rem;"></i>
                                        <h5 class="text-primary mt-3">Évite la Compétition</h5>
                                        <p class="mb-0">Évite tout esprit de compétition et de rivalité entre les enfants, leurs parents et les clubs.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-running text-success" style="font-size: 3rem;"></i>
                                        <h5 class="text-primary mt-3">Stimule les Habilités</h5>
                                        <p class="mb-0">Stimule les habilités motrices fondamentales des enfants au travers de situations ludiques et psychomotoriques.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-futbol text-primary" style="font-size: 3rem;"></i>
                                        <h5 class="text-primary mt-3">Procure des Opportunités</h5>
                                        <p class="mb-0">Procure à chaque enfant beaucoup d'opportunités de toucher, de conduire et bloquer le ballon, ainsi que dribbler, shooter et marquer des buts.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Format de jeu à 5 - U8 à U9 -->
                <div class="card border-info mt-4">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-users text-info me-2"></i>
                            FORMAT DE JEU À 5 - U8 À U9
                        </h4>
                        
                        <div class="row mb-4">
                            <div class="col-lg-8" col-md-12 col-12>
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-futbol text-info me-2"></i>Format de Jeu</h5>
                                    <p class="mb-0"><strong>U8-U9</strong> : <strong>5c5</strong></p>
                                </div>
                                
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-passport text-success me-2"></i>Passe Idéale</h5>
                                    <p class="mb-0">Le format de jeu à <strong>5c5 est idéal pour la passe jusque 10 mètres</strong>.</p>
                                </div>
                            </div>
                            <div class="col-lg-4" col-md-12 col-12>
                                <img src="images/tactique/image12.png?v=1" alt="Format de jeu U8-U9" class="img-fluid mx-auto d-block mb-3" style="max-height: 300px;">
                                <img src="images/tactique/image13.png?v=1" alt="Format de jeu U8-U9 - Habilités" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                            </div>
                        </div>

                        <!-- Caractéristiques du joueur U8-U9 -->
                        <div class="alert alert-warning mb-4">
                            <h5><i class="fas fa-list text-warning me-2"></i>Quelles sont les caractéristiques d'un joueur U8-U9 ?</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-graduation-cap text-primary me-2"></i>
                                            Premier stade scolaire
                                        </h6>
                                        <p class="mb-0">Se situe dans le premier stade scolaire (2ème ou 3ème primaire).</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-heart text-success me-2"></i>
                                            Intérêt pour les activités sportives
                                        </h6>
                                        <p class="mb-0">S'intéresse aux activités sportives qu'il rencontre dans son milieu de vie : école, famille et quartier.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-fire text-info me-2"></i>
                                            Motricité débordante
                                        </h6>
                                        <p class="mb-0">Cette étape du développement se distingue par une motricité débordante et un réel enthousiasme.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-warning h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-futbol text-warning me-2"></i>
                                            Engagement dans la discipline
                                        </h6>
                                        <p class="mb-0">Les enfants commencent à s'engager véritablement dans une discipline sportive et partent à la découverte des habiletés motrices spécialisées et des mouvements techniques.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-danger h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-cogs text-danger me-2"></i>
                                            Maîtrise des comportements moteurs
                                        </h6>
                                        <p class="mb-0">L'engagement sera d'autant plus réussi que l'enfant maîtrise les comportements moteurs fondamentaux impliqués dans l'activité spécialisée.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-secondary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-chart-line text-secondary me-2"></i>
                                            Taux d'inscription maximum
                                        </h6>
                                        <p class="mb-0">Le taux d'inscription dans les clubs de football atteint son maximum durant cette période (Groupes hétérogènes : débutants et joueurs expérimentés).</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-lightbulb text-primary me-2"></i>
                                            Grande envie d'apprendre
                                        </h6>
                                        <p class="mb-0">Grande envie d'apprendre de nouvelles choses.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-brain text-success me-2"></i>
                                            Concentration augmentée
                                        </h6>
                                        <p class="mb-0">La concentration augmente.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Habilités à développer -->
                        <div class="card border-info mt-4">
                            <div class="card-body text-center">
                                <h5 class="text-primary">
                                    <i class="fas fa-star text-info me-2"></i>
                                    Quelles sont les habilités à développer en U8-U9 ?
                                </h5>
                                <p class="mb-0">Voir l'image ci-dessus pour les détails des habilités à développer.</p>
                            </div>
                        </div>

                        <!-- Recommandations pour le match 5 contre 5 -->
                        <div class="card border-warning mt-4">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-4">
                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                    Recommandations pour le match 5 contre 5
                                </h4>
                                
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-book text-warning me-2"></i>Les bonnes pratiques en école de foot : jeu à 5</h5>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-clock text-primary me-2"></i>
                                                    Temps de jeu équitable
                                                </h6>
                                                <p class="mb-0">Accorder au moins <strong>50% du temps de jeu à chaque joueur</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-sync text-success me-2"></i>
                                                    Polyvalence des joueurs
                                                </h6>
                                                <p class="mb-0">Favoriser la polyvalence de ses joueurs : <strong>rotation des positions</strong> (latérale, axiale, défensive, offensive).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user-shield text-info me-2"></i>
                                                    Gardien de but - Jeu haut
                                                </h6>
                                                <p class="mb-0">Inciter le gardien de but à <strong>jouer haut et à développer son jeu au pied</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-futbol text-warning me-2"></i>
                                                    Reconstruction au sol
                                                </h6>
                                                <p class="mb-0">Tenter au maximum de <strong>reconstruire le jeu au sol à partir du gardien de but</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-feet text-danger me-2"></i>
                                                    Relance courte
                                                </h6>
                                                <p class="mb-0">Pousser le gardien de but à jouer haut et à développer son jeu au pied (<strong>relance courte des deux pieds</strong>).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-brain text-secondary me-2"></i>
                                                    Prise de décision
                                                </h6>
                                                <p class="mb-0">Stimuler la prise de décision de l'enfant : exemple lors de la rentrée de touche, l'enfant rentre en conduite de balle et défie l'adversaire.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-primary me-2"></i>
                                                    Fixer et dribbler
                                                </h6>
                                                <p class="mb-0">Encourager l'enfant à <strong>fixer, défier et dribbler l'adversaire</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-heart text-success me-2"></i>
                                                    Influence positive
                                                </h6>
                                                <p class="mb-0">Influencer positivement la formation des adversaires : <strong>pas de pression abusive sur le gardien de but</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-comments text-info me-2"></i>
                                                    Coaching positif
                                                </h6>
                                                <p class="mb-0">Coacher positivement et <strong>s'accorder des périodes de silence dans son coaching</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-trophy text-warning me-2"></i>
                                                    Challenge au lieu de pénalty
                                                </h6>
                                                <p class="mb-0">Remplacer le pénalty d'après match par le <strong>« challenge » (=shoot-out)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dispositifs principaux -->
                                <div class="alert alert-primary mt-4">
                                    <h5><i class="fas fa-chess-board text-primary me-2"></i>Dispositifs principaux dans le jeu à 5</h5>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <img src="images/tactique/image14.png?v=1" alt="Dispositifs principaux dans le jeu à 5" class="img-fluid mx-auto d-block" style="max-height: 500px;">
                                </div>

                                <!-- Explication des dispositifs -->
                                <div class="row mt-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h5 class="text-primary text-center mb-3">
                                                    <i class="fas fa-diamond text-success me-2"></i>
                                                    Dispositif 1-1-2-1 (Losange)
                                                </h5>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check text-success me-2"></i><strong>Répartition équitable</strong> des espaces à exploiter ou à défendre</li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Favorise le <strong>jeu en triangle</strong></li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Initie les enfants au <strong>jeu en zone</strong></li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Favorise les <strong>premières coopérations</strong> entre les enfants</li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Structure leurs <strong>déplacements</strong></li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Relativement <strong>facile à comprendre</strong> pour les enfants</li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Éduque à la <strong>flexibilité des tâches</strong> offensives et défensives</li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Position <strong>latérale et position axiale</strong></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h5 class="text-primary text-center mb-3">
                                                    <i class="fas fa-square text-warning me-2"></i>
                                                    Dispositif 1-2-2 (Carré)
                                                </h5>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-exclamation-triangle text-warning me-2"></i><strong>N'est indiqué que pour des joueurs aguerris</strong></li>
                                                    <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>La compréhension des tâches est <strong>beaucoup plus complexe</strong> pour les enfants</li>
                                                    <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>Positionnement de joueurs sur une <strong>même ligne</strong></li>
                                                    <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>Favorise le <strong>passing latéral</strong></li>
                                                </ul>
                                                <div class="alert alert-warning mt-3">
                                                    <small><i class="fas fa-info-circle me-2"></i><strong>Attention :</strong> Ce dispositif nécessite une meilleure compréhension tactique et n'est pas recommandé pour les débutants.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Format de jeu à 8 - U10 à U13 -->
                <div class="card border-primary mt-4">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-users text-primary me-2"></i>
                            FORMAT DE JEU À 8 - U10 À U13
                        </h4>
                        
                        <div class="row mb-4">
                            <div class="col-lg-8" col-md-12 col-12>
                                <div class="alert alert-primary">
                                    <h5><i class="fas fa-futbol text-primary me-2"></i>Format de Jeu</h5>
                                    <p class="mb-0"><strong>U10-U13</strong> : <strong>8c8</strong></p>
                                </div>
                                
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-passport text-success me-2"></i>Passe Idéale</h5>
                                    <p class="mb-0">Le format de jeu à <strong>8c8 est idéal pour la passe de 15 mètres (U10-U11) et 20 mètres (U12-U13)</strong>.</p>
                                </div>
                            </div>
                            <div class="col-lg-4" col-md-12 col-12>
                                <img src="images/tactique/image15.png?v=1" alt="Format de jeu U10-U13" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                            </div>
                        </div>

                        <!-- Caractéristiques du joueur U10-U13 -->
                        <div class="alert alert-warning mb-4">
                            <h5><i class="fas fa-list text-warning me-2"></i>Quelles sont les caractéristiques d'un joueur U10-U13 ?</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-graduation-cap text-primary me-2"></i>
                                            Second âge scolaire
                                        </h6>
                                        <p class="mb-0">Le second âge scolaire débute vers 9 ans et se poursuit jusqu'à la puberté (12-13 ans).</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-cogs text-success me-2"></i>
                                            Apprentissage technique
                                        </h6>
                                        <p class="mb-0">Cette période est particulièrement <strong>bénéfique pour l'apprentissage technique</strong>.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-dumbbell text-info me-2"></i>
                                            Force et puissance
                                        </h6>
                                        <p class="mb-0">La force et la puissance augmentent plus rapidement que la taille et le poids, ce qui favorise la <strong>maîtrise corporelle et l'agilité</strong>.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-warning h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-balance-scale text-warning me-2"></i>
                                            Maturation vestibulaire
                                        </h6>
                                        <p class="mb-0">Vers l'âge de 10-11 ans, l'appareil vestibulaire (organe de l'équilibre) et les autres organes sensoriels atteignent leur <strong>maturation morphologique et fonctionnelle</strong>.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-danger h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-star text-danger me-2"></i>
                                            Âge d'or de l'apprentissage
                                        </h6>
                                        <p class="mb-0">L'enfant a envie d'apprendre : <strong>âge d'or de l'apprentissage</strong>.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-secondary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-trophy text-secondary me-2"></i>
                                            Se mesurer aux autres
                                        </h6>
                                        <p class="mb-0">Veut se mesurer aux autres et cherche à <strong>atteindre un but en équipe</strong>.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-search text-info me-2"></i>
                                            Esprit critique
                                        </h6>
                                        <p class="mb-0">Est critique vis-à-vis de ses propres prestations et de celles des autres.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Habilités à développer -->
                        <div class="card border-primary mt-4">
                            <div class="card-body text-center">
                                <h5 class="text-primary">
                                    <i class="fas fa-star text-primary me-2"></i>
                                    Quelles sont les habilités à développer en U10-U13 ?
                                </h5>
                                <p class="mb-0">Voir l'image ci-dessus pour les détails des habilités à développer.</p>
                            </div>
                        </div>

                        <!-- Recommandations pour le match 8 contre 8 -->
                        <div class="card border-warning mt-4">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-4">
                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                    Recommandations pour le match 8 contre 8
                                </h4>
                                
                                <div class="text-center mb-4">
                                    <img src="images/tactique/image16.png?v=1" alt="Recommandations pour le match 8 contre 8" class="img-fluid mx-auto d-block" style="max-height: 400px;">
                                </div>

                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-book text-warning me-2"></i>Les bonnes pratiques en école de foot : jeu à 8</h5>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-clock text-primary me-2"></i>
                                                    Temps de jeu équitable
                                                </h6>
                                                <p class="mb-0">Accorder au moins <strong>50% du temps de jeu à chaque joueur</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-sync text-success me-2"></i>
                                                    Polyvalence des joueurs
                                                </h6>
                                                <p class="mb-0">Favoriser la polyvalence des joueurs : <strong>rotation des positions</strong> (axiale, latérale, offensive, défensive).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-brain text-info me-2"></i>
                                                    Prise de décision
                                                </h6>
                                                <p class="mb-0">Stimuler la prise de décision du joueur : exemple lors de la <strong>relance courte ou mi-longue du gardien de but</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-chess text-warning me-2"></i>
                                                    Construction du jeu
                                                </h6>
                                                <p class="mb-0">Construire le jeu de l'arrière avec le <strong>gardien de but et les défenseurs comme point de départ</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-futbol text-danger me-2"></i>
                                                    Possession de balle
                                                </h6>
                                                <p class="mb-0">Prioriser la possession de balle par une <strong>circulation de balle progressive et précise</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-running text-secondary me-2"></i>
                                                    Notion de démarquage
                                                </h6>
                                                <p class="mb-0">Sensibiliser les enfants à la <strong>notion de démarquage</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user-shield text-primary me-2"></i>
                                                    Gardien de but - Jeu haut
                                                </h6>
                                                <p class="mb-0">Inciter le gardien de but à <strong>jouer haut et à développer son jeu au pied</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-layer-group text-success me-2"></i>
                                                    Notion de bloc
                                                </h6>
                                                <p class="mb-0">Développer la notion de bloc en conservant des distances homogènes entre les lignes : l'attaquant de pointe ne reste pas « planté » devant le but adverse (hors-jeu virtuel).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-info me-2"></i>
                                                    Prise d'initiative défensive
                                                </h6>
                                                <p class="mb-0">Induire la prise d'initiative des joueurs défensifs : <strong>infiltration des défenseurs avec et sans ballon</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-users text-warning me-2"></i>
                                                    Réflexion et coopération
                                                </h6>
                                                <p class="mb-0">Favoriser la réflexion, la coopération et la stratégie entre les joueurs : exemple lors de combinaison courte sur corner, coup-franc, coup de pied but et rentrée en touche.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body text-center">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-heart text-danger me-2"></i>
                                                    Philosophie du jeu
                                                </h6>
                                                <p class="mb-0"><strong>Le jeu en soi est plus important que la victoire</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dispositifs principaux dans le jeu à 8 -->
                        <div class="card border-info mt-4">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-4">
                                    <i class="fas fa-chess-board text-info me-2"></i>
                                    Dispositifs principaux dans le jeu à 8
                                </h4>
                                
                                <div class="text-center mb-4">
                                    <img src="images/tactique/image17.png?v=1" alt="Dispositifs principaux dans le jeu à 8" class="img-fluid mx-auto d-block" style="max-height: 500px;">
                                </div>

                                <!-- Les 3 dispositifs -->
                                <div class="row">
                                    <div class="col-md-4 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h5 class="text-primary text-center mb-3">
                                                    <i class="fas fa-diamond text-success me-2"></i>
                                                    Dispositif 1-3-1-3 (Double Losange)
                                                </h5>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check text-success me-2"></i><strong>Répartition équitable</strong> des espaces tant en perte de balle qu'en possession de balle (largeur et profondeur)</li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Favorise au maximum le <strong>jeu en triangle</strong></li>
                                                    <li><i class="fas fa-check text-success me-2"></i><strong>Construction à partir du gardien de but</strong> (plusieurs possibilités)</li>
                                                    <li><i class="fas fa-check text-success me-2"></i>La <strong>créativité des joueurs offensifs</strong> est de mise</li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Possibilité de se <strong>projeter rapidement vers l'avant</strong> = circulation de balle progressive</li>
                                                    <li><i class="fas fa-check text-success me-2"></i>Autorise des <strong>reconversions rapides</strong> de la perte de balle vers la possession de balle et inversement</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h5 class="text-primary text-center mb-3">
                                                    <i class="fas fa-square text-warning me-2"></i>
                                                    Dispositif 1-2-3-2
                                                </h5>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check text-warning me-2"></i>Offre une <strong>densité axiale</strong></li>
                                                    <li><i class="fas fa-check text-warning me-2"></i>Possibilité de <strong>sortir rapidement le ballon sur les 2 attaquants</strong></li>
                                                    <li><i class="fas fa-check text-warning me-2"></i>Favorise l'<strong>alternance jeu court/jeu long</strong></li>
                                                    <li><i class="fas fa-check text-warning me-2"></i>Développe la <strong>relation des 2 attaquants</strong></li>
                                                </ul>
                                                <div class="alert alert-warning mt-3">
                                                    <small><i class="fas fa-exclamation-triangle me-2"></i><strong>Inconvénient :</strong> Impose des courses intensives des latéraux pour gérer la profondeur dans les couloirs au risque de voir dézoner les défenseurs.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h5 class="text-primary text-center mb-3">
                                                    <i class="fas fa-layer-group text-info me-2"></i>
                                                    Dispositif 1-2-4-1
                                                </h5>
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check text-info me-2"></i>Assure une <strong>base défensive solide</strong></li>
                                                    <li><i class="fas fa-check text-info me-2"></i>Le <strong>jeu combiné dans l'axe</strong></li>
                                                </ul>
                                                <div class="alert alert-warning mt-3">
                                                    <small><i class="fas fa-exclamation-triangle me-2"></i><strong>Inconvénient :</strong> Une gestion déficiente de la profondeur sur les flancs peut déstabiliser l'équipe. Les deux joueurs excentrés doivent couvrir tout le couloir.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remarque sur la polyvalence -->
                                <div class="alert alert-primary mt-4">
                                    <h5><i class="fas fa-info-circle text-primary me-2"></i>Remarque importante</h5>
                                    <p class="mb-0">La <strong>polyvalence du joueur dans le jeu</strong> est encore d'application à ce stade car chaque poste peut enrichir le bagage technico-tactique du joueur (y compris le gardien de but).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Format de jeu à 11 - U13 à U20 -->
                <div class="card border-danger mt-4">
                    <div class="card-body">
                        <h4 class="text-primary text-center mb-4">
                            <i class="fas fa-trophy text-danger me-2"></i>
                            FORMAT DE JEU À 11 - U13 À U20
                        </h4>
                        
                        <div class="row mb-4">
                            <div class="col-lg-8" col-md-12 col-12>
                                <div class="alert alert-danger">
                                    <h5><i class="fas fa-futbol text-danger me-2"></i>Format de Jeu</h5>
                                    <p class="mb-0"><strong>U13-U20</strong> : <strong>11c11</strong></p>
                                </div>
                                
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-passport text-success me-2"></i>Passe Idéale</h5>
                                    <p class="mb-0">Le format de jeu à <strong>11c11 est idéal pour la passe d'environ 30 mètres et plus</strong> (avec l'application de la règle du hors-jeu et en tenant compte de l'espace qui s'est créé entre les défenseurs et le but).</p>
                                </div>
                            </div>
                            <div class="col-lg-4" col-md-12 col-12>
                                <div class="text-center">
                                    <i class="fas fa-futbol text-danger" style="font-size: 8rem;"></i>
                                    <h5 class="text-primary mt-3">11 contre 11</h5>
                                    <p class="text-muted">Format complet du football</p>
                                </div>
                            </div>
                        </div>

                        <!-- Caractéristiques du joueur U13-U17 -->
                        <div class="alert alert-warning mb-4">
                            <h5><i class="fas fa-list text-warning me-2"></i>Quelles sont les caractéristiques d'un joueur U13-U17 ?</h5>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-brain text-primary me-2"></i>
                                            Jugement et opinion (U13-U15)
                                        </h6>
                                        <p class="mb-0">Devient capable de juger, a sa propre opinion et éprouve le besoin de se faire valoir.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-user-times text-info me-2"></i>
                                            Confiance en soi (U16-U17)
                                        </h6>
                                        <p class="mb-0">Montre moins de confiance en soi et recherche son égo, s'oppose aux valeurs traditionnelles.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3" col-12>
                                <div class="card border-warning h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-child text-warning me-2"></i>
                                            La Puberté - 2 Phases
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6" col-12>
                                                <h6 class="text-warning">Phase 1 :</h6>
                                                <ul class="list-unstyled">
                                                    <li><strong>Filles :</strong> 11-12 ans → 13-14 ans</li>
                                                    <li><strong>Garçons :</strong> 12-13 ans → 14-15 ans</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6" col-12>
                                                <h6 class="text-warning">Phase 2 :</h6>
                                                <ul class="list-unstyled">
                                                    <li><strong>Filles :</strong> 13-14 ans → 17-18 ans</li>
                                                    <li><strong>Garçons :</strong> 14-15 ans → 18-19 ans</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Maturation et compétition -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-chart-line text-success me-2"></i>
                                            Maturation et Développement
                                        </h5>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-arrow-up text-success me-2"></i><strong>Décalage possible :</strong> 4-5 ans entre âge biologique et chronologique</li>
                                            <li><i class="fas fa-star text-success me-2"></i><strong>Tôt matures :</strong> Avantage physique précoce</li>
                                            <li><i class="fas fa-trophy text-success me-2"></i><strong>Tard matures :</strong> Plus de chances d'atteindre le haut niveau</li>
                                        </ul>
                                        <div class="alert alert-warning mt-3">
                                            <small><i class="fas fa-exclamation-triangle me-2"></i><strong>Attention :</strong> Prétendre qu'un sportif est promis au haut niveau à un âge précoce constitue une fausse représentation.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h5 class="text-primary">
                                            <i class="fas fa-flag text-info me-2"></i>
                                            La Compétition (U14+)
                                        </h5>
                                        <p class="mb-2"><strong>U14 = Début de la compétition !</strong></p>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-info me-2"></i>Ne doit pas nuire au développement</li>
                                            <li><i class="fas fa-check text-info me-2"></i>Source de plaisir, non de stress</li>
                                            <li><i class="fas fa-check text-info me-2"></i>Répondre aux motivations du sportif</li>
                                            <li><i class="fas fa-check text-info me-2"></i>Respecter la santé et le bien-être</li>
                                            <li><i class="fas fa-check text-info me-2"></i>Ne jamais compromettre la sécurité</li>
                                            <li><i class="fas fa-check text-info me-2"></i>Gouvernée par les principes éducatifs</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Caractéristiques du joueur U20 -->
                        <div class="alert alert-primary mb-4">
                            <h5><i class="fas fa-user-graduate text-primary me-2"></i>Quelles sont les caractéristiques d'un joueur U20 ?</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            Phase terminale de l'adolescence
                                        </h6>
                                        <p class="mb-0">Ce stade correspond à la phase terminale de l'adolescence.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-expand-arrows-alt text-success me-2"></i>
                                            Croissance en largeur
                                        </h6>
                                        <p class="mb-0">La croissance en longueur ralentit au profit de la croissance en largeur.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-info h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-cogs text-info me-2"></i>
                                            Morphologie favorable
                                        </h6>
                                        <p class="mb-0">La morphologie devient favorable à l'amélioration des coordinations.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" col-12>
                                <div class="card border-warning h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-star text-warning me-2"></i>
                                            Deuxième âge d'or
                                        </h6>
                                        <p class="mb-0">La seconde phase de la puberté est souvent considérée comme le <strong>« deuxième âge d'or » de l'apprentissage</strong>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Améliorations de performance -->
                        <div class="card border-success mt-4">
                            <div class="card-body text-center">
                                <h5 class="text-primary">
                                    <i class="fas fa-chart-line text-success me-2"></i>
                                    Améliorations de Performance
                                </h5>
                                <p class="mb-0">L'augmentation de la force musculaire et l'importante capacité d'apprentissage et de fixation des schémas moteurs expliquent les <strong>améliorations de performance très nettes</strong> observées durant l'adolescence.</p>
                            </div>
                        </div>

                        <!-- Habilités à développer en U13-U17 -->
                        <div class="card border-warning mt-4">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-4">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    Quelles sont les habilités à développer en U13-U17 ?
                                </h4>
                                
                                <div class="text-center mb-4">
                                    <img src="images/tactique/image18.png?v=1" alt="Habilités à développer en U13-U17" class="img-fluid mx-auto d-block" style="max-height: 400px;">
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h5 class="text-primary text-center mb-3">
                                                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                                                    U14-U15
                                                </h5>
                                                <p class="mb-0">Le joueur doit <strong>maîtriser les Basics & Teamtactics</strong> vus dans le format de jeu à 2, à 5 et à 8 et en <strong>développer de nouveaux</strong> aux caractéristiques du jeu à 11.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h5 class="text-primary text-center mb-3">
                                                    <i class="fas fa-cogs text-success me-2"></i>
                                                    U16-U17
                                                </h5>
                                                <p class="mb-0">Le joueur doit <strong>maîtriser l'ensemble des Basics & Teamtactics</strong> (perfectionnement via l'entraînement individuel). Le joueur les <strong>applique dans le projet de jeu de l'équipe</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h5 class="text-primary text-center mb-3">
                                                    <i class="fas fa-trophy text-info me-2"></i>
                                                    U18-U19
                                                </h5>
                                                <p class="mb-0">Le joueur entre dans sa phase dite de <strong>« Postformation »</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progression des habilités -->
                                <div class="alert alert-info mt-4">
                                    <h5><i class="fas fa-route text-info me-2"></i>Progression des Habilités</h5>
                                    <p class="mb-0">Cette progression montre l'évolution naturelle des compétences du joueur, de la maîtrise des bases à l'application dans le projet d'équipe, jusqu'à la phase de postformation où le joueur devient autonome dans son développement.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Remarques importantes et recommandations 11c11 -->
                        <div class="card border-danger mt-4">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-4">
                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                    Remarques importantes et recommandations 11c11
                                </h4>
                                
                                <div class="text-center mb-4">
                                    <img src="images/tactique/img19.png?v=1" alt="Remarques importantes et recommandations 11c11" class="img-fluid mx-auto d-block" style="max-height: 400px;">
                                </div>

                                <!-- Remarques importantes -->
                                <div class="alert alert-warning mb-4">
                                    <h5><i class="fas fa-lightbulb text-warning me-2"></i>Remarques importantes</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                                    Période de restructuration
                                                </h6>
                                                <p class="mb-0">La phase de la puberté est une période de restructuration. Les erreurs commises dans la programmation des charges des séances et dans les rapports avec les adolescents sont les <strong>premières causes d'abandon</strong> de l'activité sportive.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-balance-scale text-info me-2"></i>
                                                    Âge biologique vs calendrier
                                                </h6>
                                                <p class="mb-0">Il est important de doser les charges d'entraînement en tenant compte de l'<strong>âge biologique (degré de maturité physique et mental)</strong> du joueur plutôt que son âge calendrier.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user-graduate text-success me-2"></i>
                                                    Tâche complexe du formateur
                                                </h6>
                                                <p class="mb-0">C'est à ce niveau qu'entre en jeu la tâche complexe du formateur qui sera de <strong>maintenir intacte la motivation</strong> de « ses protégés », de la stabiliser et de <strong>résoudre les situations de conflit</strong> par une intervention pédagogique appropriée.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Points à veiller -->
                                <div class="alert alert-primary mb-4">
                                    <h5><i class="fas fa-list text-primary me-2"></i>Veiller à :</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user-cog text-primary me-2"></i>
                                                    Adapter/individualiser
                                                </h6>
                                                <p class="mb-0">Adapter/individualiser certaines séances en fonction des capacités de chacun.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-cogs text-success me-2"></i>
                                                    Maintenir la technique
                                                </h6>
                                                <p class="mb-0">Maintenir et perfectionner la technique spécifique.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exclamation-circle text-info me-2"></i>
                                                    Ne pas insister
                                                </h6>
                                                <p class="mb-0">Ne pas insister sur l'acquisition d'habilités trop complexes.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-chart-line text-warning me-2"></i>
                                                    Augmenter progressivement
                                                </h6>
                                                <p class="mb-0">Augmenter progressivement la charge des séances.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-ear-listen text-danger me-2"></i>
                                                    Être à l'écoute
                                                </h6>
                                                <p class="mb-0">Être à l'écoute des besoins du sportif.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-dumbbell text-secondary me-2"></i>
                                                    Développer les qualités physiques
                                                </h6>
                                                <p class="mb-0">Développer les différentes qualités physiques.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recommandations pour le match 11c11 -->
                                <div class="alert alert-success mb-4">
                                    <h5><i class="fas fa-futbol text-success me-2"></i>Recommandations pour le match 11 contre 11</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-chess-board text-primary me-2"></i>
                                                    Systèmes vs Idées
                                                </h6>
                                                <p class="mb-0">Les systèmes/dispositifs ont bien moins d'importance que les <strong>idées qui les animent</strong> : développer des principes de jeu en phase avec la catégorie d'âge.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-map text-success me-2"></i>
                                                    Jeu en zone
                                                </h6>
                                                <p class="mb-0">Le <strong>jeu en zone comme fil conducteur</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-brain text-info me-2"></i>
                                                    Comprendre le jeu
                                                </h6>
                                                <p class="mb-0">Aider les joueurs à <strong>comprendre le jeu</strong> : jouer ne suffit plus.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-clock text-warning me-2"></i>
                                                    Temps de jeu équitable
                                                </h6>
                                                <p class="mb-0">Viser un <strong>temps de jeu équitable</strong> pour chacun.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-target text-danger me-2"></i>
                                                    Vision long terme
                                                </h6>
                                                <p class="mb-0">Guider le joueur pour le <strong>long terme et non pour l'instant du match</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Compétences mentales -->
                                <div class="alert alert-info mb-4">
                                    <h5><i class="fas fa-brain text-info me-2"></i>Accompagner chaque joueur à développer ses compétences mentales :</h5>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-primary me-2"></i>
                                                    Concentration & motivation
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-trophy text-success me-2"></i>
                                                    Mentalité de gagneur
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-balance-scale text-info me-2"></i>
                                                    Stabilité émotionnelle
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-shield-alt text-warning me-2"></i>
                                                    Contrôle de soi-même et discipline
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-heart text-danger me-2"></i>
                                                    Confiance en soi et force mentale
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-users text-secondary me-2"></i>
                                                    Relations interpersonnelles et d'équipe
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-calendar text-primary me-2"></i>
                                                    Gestion du style de vie
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Philosophie finale -->
                                <div class="card border-success mt-4">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary">
                                            <i class="fas fa-heart text-success me-2"></i>
                                            Philosophie du jeu
                                        </h5>
                                        <p class="mb-0"><strong>Le jeu est plus important que le résultat</strong>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dispositifs principaux dans le jeu à 11 -->
                        <div class="card border-primary mt-4">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-4">
                                    <i class="fas fa-chess-board text-primary me-2"></i>
                                    Dispositifs principaux dans le jeu à 11
                                </h4>
                                
                                <div class="text-center mb-4">
                                    <img src="images/tactique/img20.png?v=1" alt="Dispositifs principaux dans le jeu à 11" class="img-fluid mx-auto d-block" style="max-height: 400px;">
                                </div>

                                <!-- Introduction du système 1-4-3-3 -->
                                <div class="alert alert-primary mb-4">
                                    <h5><i class="fas fa-futbol text-primary me-2"></i>Le système 1-4-3-3</h5>
                                    <p class="mb-0">Le 1-4-3-3 permet une <strong>répartition équitable des espaces</strong> à exploiter (TT+) ou à défendre (TT-) avec un maximum de jeu en triangle et des <strong>reconversions rapides</strong> de la perte de balle vers la possession de balle et inversement.</p>
                                </div>

                                <!-- Défense de 4 arrières -->
                                <div class="alert alert-info mb-4">
                                    <h5><i class="fas fa-shield-alt text-info me-2"></i>Une défense de 4 arrières</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-expand-arrows-alt text-info me-2"></i>
                                                    Couvrir la largeur
                                                </h6>
                                                <p class="mb-0">Permet de <strong>couvrir plus facilement la largeur</strong> du terrain.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-users text-success me-2"></i>
                                                    Supériorité numérique
                                                </h6>
                                                <p class="mb-0">Offre une <strong>supériorité numérique (4 c 3)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-warning me-2"></i>
                                                    Montée et construction
                                                </h6>
                                                <p class="mb-0">Favorise la <strong>montée d'un arrière</strong> et la <strong>construction du gardien</strong> vers les défenseurs.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Milieu de terrain de 3 joueurs -->
                                <div class="alert alert-success mb-4">
                                    <h5><i class="fas fa-triangle text-success me-2"></i>Milieu de terrain de 3 joueurs qui forment un triangle offensif</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-sync-alt text-primary me-2"></i>
                                                    Circulation de balle
                                                </h6>
                                                <p class="mb-0">Offre une <strong>meilleure circulation de balle</strong> dans la construction.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-danger me-2"></i>
                                                    Plus de joueurs devant le but
                                                </h6>
                                                <p class="mb-0">Permet <strong>plus de joueurs devant le but</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-running text-info me-2"></i>
                                                    Infiltrations des défenseurs
                                                </h6>
                                                <p class="mb-0">Permet l'<strong>infiltration des défenseurs centraux</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attaque de 3 joueurs -->
                                <div class="alert alert-warning mb-4">
                                    <h5><i class="fas fa-fire text-warning me-2"></i>Une attaque de 3 joueurs</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-passport text-success me-2"></i>
                                                    Plus de possibilités de passes
                                                </h6>
                                                <p class="mb-0">Offre <strong>plus de possibilités de passes</strong> dans les pieds.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-magic text-danger me-2"></i>
                                                    Audace et créativité
                                                </h6>
                                                <p class="mb-0">Stimule l'<strong>audace des dribbles</strong> et la <strong>créativité</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user-tie text-primary me-2"></i>
                                                    Le 9 comme target-man
                                                </h6>
                                                <p class="mb-0">Forme le 9 comme <strong>« target-man »</strong> (appui, remiseur), comme attaquant plongeant en profondeur (dans le dos des défenseurs) et comme <strong>finisseur devant le but</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recommandations et flexibilité -->
                                <div class="alert alert-secondary mb-4">
                                    <h5><i class="fas fa-lightbulb text-secondary me-2"></i>Recommandations et flexibilité tactique</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                                    Passage U14-U15
                                                </h6>
                                                <p class="mb-0">Pour ces différentes raisons, il est <strong>conseillé lors du passage dans le jeu à 11 (U14-U15)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-cogs text-success me-2"></i>
                                                    Flexibilité tactique
                                                </h6>
                                                <p class="mb-0">Par la suite, d'<strong>autres dispositifs sont envisagés</strong> pour développer la <strong>FLEXIBILITE TACTIQUE</strong> du joueur.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-balance-scale text-info me-2"></i>
                                                    Condition d'application
                                                </h6>
                                                <p class="mb-0">À la condition d'appliquer certains <strong>PRINCIPES DE JEU en possession et en perte de balle</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Résumé des avantages -->
                                <div class="card border-success mt-4">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary">
                                            <i class="fas fa-trophy text-success me-2"></i>
                                            Avantages du système 1-4-3-3
                                        </h5>
                                        <div class="row mt-3">
                                            <div class="col-md-4" col-12>
                                                <p class="mb-0"><strong>Défense :</strong> 3 avantages</p>
                                            </div>
                                            <div class="col-md-4" col-12>
                                                <p class="mb-0"><strong>Milieu :</strong> 3 avantages</p>
                                            </div>
                                            <div class="col-md-4" col-12>
                                                <p class="mb-0"><strong>Attaque :</strong> 3 avantages</p>
                                            </div>
                                        </div>
                                        <p class="mt-3 mb-0"><strong>Total : 9 avantages tactiques majeurs</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Principes de jeu en possession de balle -->
                        <div class="card border-success mt-4">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-4">
                                    <i class="fas fa-futbol text-success me-2"></i>
                                    Les principes de jeu qui peuvent être développés quel que soit le dispositif tactique
                                </h4>
                                
                                <div class="alert alert-info mb-4">
                                    <h5><i class="fas fa-lightbulb text-info me-2"></i>EN POSSESSION DE BALLE</h5>
                                    <p class="mb-0">Ci-après des principes de jeu basés sur la <strong>vision de formation</strong>, sa <strong>philosophie (jeu en zone)</strong>, son <strong>plan d'apprentissage (basics et teamtactics)</strong>, et à développer par la <strong>méthode MIM</strong>.</p>
                                </div>

                                <!-- Principe 1 : Disponibilité -->
                                <div class="alert alert-primary mb-4">
                                    <h5><i class="fas fa-user-check text-primary me-2"></i>DISPONIBILITÉ : SE DÉMARQUER ET ÊTRE JOUABLE</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-running text-primary me-2"></i>
                                                    Se libérer du marquage
                                                </h6>
                                                <p class="mb-0">S'écarter, décrocher (hors du champ de vision de l'adversaire).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye-slash text-success me-2"></i>
                                                    Être jouable dans le dos
                                                </h6>
                                                <p class="mb-0">Ne pas venir vers le ballon mais être jouable dans le <strong>dos de l'adversaire</strong>, entre les lignes de l'équipe adverse.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exchange-alt text-info me-2"></i>
                                                    Mouvements sans ballons
                                                </h6>
                                                <p class="mb-0">Créer beaucoup de <strong>mouvements sans ballons</strong>, permuter les positions (switches), réaliser des <strong>courses opposées</strong> entre joueurs.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrows-alt text-warning me-2"></i>
                                                    Exemples de courses opposées
                                                </h6>
                                                <p class="mb-0">Ex : intérieur ⭠⭢ extérieur, montée ⭠⭢ descente. <strong>Coordonner les courses et les déplacements</strong> (utilisation rationnelle de l'espace).</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 1 et 2 : Disponibilité -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                DISPONIBLE DANS LE DOS DE L'ADVERSAIRE
                                            </h6>
                                            <img src="images/tactique/Img21.png?v=1" alt="Disponible dans le dos de l'adversaire" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                COURSES OPPOSÉES : LIBÉRER ET EXPLOITER L'ESPACE
                                            </h6>
                                            <img src="images/tactique/Img22.png?v=1" alt="Courses opposées : libérer et exploiter l'espace" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 2 : Construction -->
                                <div class="alert alert-success mb-4">
                                    <h5><i class="fas fa-building text-success me-2"></i>CONSTRUCTION AVEC LE GARDIEN DE BUT ET LES DÉFENSEURS COMME POINT DE DÉPART</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-expand-arrows-alt text-primary me-2"></i>
                                                    Créer de l'espace
                                                </h6>
                                                <p class="mb-0">Créer de l'espace en <strong>largeur et en profondeur</strong> : mouvements coordonnés (disponibilité).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-sync-alt text-success me-2"></i>
                                                    Circulation de balle rapide
                                                </h6>
                                                <p class="mb-0">Progresser par une <strong>circulation de balle rapide</strong> jusqu'à la création d'une occasion de but.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exclamation-triangle text-info me-2"></i>
                                                    Prise de risque calculée
                                                </h6>
                                                <p class="mb-0">Prise de risque calculée : <strong>passing diagonal ou vertical</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user text-warning me-2"></i>
                                                    Position du corps adaptée
                                                </h6>
                                                <p class="mb-0">Position du corps adaptée lors de la réception du ballon pour continuer à jouer vers l'avant : <strong>ouverte et orientée</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-cogs text-danger me-2"></i>
                                                    Pas de construction stéréotypée
                                                </h6>
                                                <p class="mb-0">Pas de construction stéréotypée : <strong>flexibilité (K + 2, 3 ou plus de joueurs)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-secondary me-2"></i>
                                                    Surmonter un joueur ou une ligne
                                                </h6>
                                                <p class="mb-0">Surmonter un joueur ou une ligne.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 3 et 4 : Construction -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                CRÉER DE L'ESPACE + JEU DE POSITION EN LARGEUR ET PROFONDEUR
                                            </h6>
                                            <img src="images/tactique/Img23.png?v=1" alt="Créer de l'espace + jeu de position en largeur et profondeur" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                RELANCE COURTE, MI-LONGUE VOIRE LONGUE
                                            </h6>
                                            <img src="images/tactique/Img24.png?v=1" alt="Relance courte, mi-longue voire longue" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 3 : Circulation progressive -->
                                <div class="alert alert-warning mb-4">
                                    <h5><i class="fas fa-sync-alt text-warning me-2"></i>CIRCULATION DE BALLE PROGRESSIVE (VERS L'AVANT) RAPIDE ET SOIGNÉE</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-primary me-2"></i>
                                                    Passing rapide vers la zone faible
                                                </h6>
                                                <p class="mb-0">Passing rapide vers la <strong>zone faible de l'adversaire</strong> (centre ou flanc ou profondeur).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-triangle text-success me-2"></i>
                                                    Jeu en triangle
                                                </h6>
                                                <p class="mb-0">Rechercher le <strong>jeu en triangle</strong> : utiliser le troisième homme.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-expand-arrows-alt text-info me-2"></i>
                                                    Créer le décalage
                                                </h6>
                                                <p class="mb-0">Créer le <strong>décalage en variant dans la largeur</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                                    Éviter les pertes stupides
                                                </h6>
                                                <p class="mb-0">Éviter les <strong>pertes de balle stupides</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-warning me-2"></i>
                                                    Éliminer l'adversaire
                                                </h6>
                                                <p class="mb-0">Éliminer l'adversaire ou une ligne par un <strong>passing diagonal & vertical</strong> ou par <strong>un-deux</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-tachometer-alt text-secondary me-2"></i>
                                                    Varier la vitesse
                                                </h6>
                                                <p class="mb-0">Varier la <strong>vitesse du ballon (temporiser)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 1-2 : Circulation progressive -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                FORMER DES TRIANGLES
                                            </h6>
                                            <img src="images/tactique/Img25.png?v=1" alt="Former des triangles" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                CRÉER LE DÉCALAGE : OUT-IN-OUT
                                            </h6>
                                            <img src="images/tactique/Img26.png?v=1" alt="Créer le décalage : out-in-out" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 4 : Variation dans la longueur -->
                                <div class="alert alert-info mb-4">
                                    <h5><i class="fas fa-arrows-alt-v text-info me-2"></i>VARIATION DANS LA LONGUEUR (PROFONDEUR)</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-primary me-2"></i>
                                                    Verticaliser/diagonaliser
                                                </h6>
                                                <p class="mb-0">Verticaliser/diagonaliser vers le <strong>joueur le plus haut</strong> + joueur en soutien (appui-soutien).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-layer-group text-success me-2"></i>
                                                    Surmonter une ligne
                                                </h6>
                                                <p class="mb-0">Surmonter une ligne.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exchange-alt text-info me-2"></i>
                                                    Alterner les jeux
                                                </h6>
                                                <p class="mb-0">Alterner entre <strong>jeu dans les pieds et jeu en profondeur</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-clock text-warning me-2"></i>
                                                    Ne pas jouer systématiquement
                                                </h6>
                                                <p class="mb-0">Ne pas jouer systématiquement en première intention : chercher le <strong>second ou troisième déplacement</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 3-4 : Variation dans la longueur -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                COURT- COURT – LONG
                                            </h6>
                                            <img src="images/tactique/Img27.png?v=1" alt="Court-court-long" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                VISER LE JOUEUR LE PLUS HAUT + SOUTENIR
                                            </h6>
                                            <img src="images/tactique/Img28.png?v=1" alt="Viser le joueur le plus haut + soutenir" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 5 : Changement de côté -->
                                <div class="alert alert-danger mb-4">
                                    <h5><i class="fas fa-exchange-alt text-danger me-2"></i>CHANGEMENT DE CÔTÉ (TOURNER LE JEU) ET GAIN DE TERRAIN</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-undo text-primary me-2"></i>
                                                    Contourner le bloc
                                                </h6>
                                                <p class="mb-0">Lorsque l'adversaire ferme les espaces sur un flanc, jouer en soutien vers un partenaire disponible pour <strong>contourner le bloc en renversant le jeu</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-success me-2"></i>
                                                    Passing rapide vers la zone faible
                                                </h6>
                                                <p class="mb-0">Passing rapide vers la <strong>zone faible de l'adversaire</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-expand-arrows-alt text-info me-2"></i>
                                                    Créer de l'espace
                                                </h6>
                                                <p class="mb-0">Créer de l'espace pour soi-même et l'exploiter.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-warning me-2"></i>
                                                    Gagner du terrain
                                                </h6>
                                                <p class="mb-0">Gagner du terrain dès que possible : si pas de marquage lors de la prise de balle (prise d'information) → se retourner rapidement (turn-up) + <strong>accélérer balle au pied</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 5-6 : Changement de côté -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                CONTOURNER LE BLOC ADVERSE
                                            </h6>
                                            <img src="images/tactique/Img29.png?v=1" alt="Contourner le bloc adverse" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                ACCÉLÉRER LE JEU GRÂCE À LA PRISE D'INFORMATION
                                            </h6>
                                            <img src="images/tactique/Img30.png?v=1" alt="Accélérer le jeu grâce à la prise d'information" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 6 : Infiltration -->
                                <div class="alert alert-primary mb-4">
                                    <h5><i class="fas fa-running text-primary me-2"></i>INFILTRATION AVEC ET SANS BALLON</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-clock text-primary me-2"></i>
                                                    Infiltrer au bon moment
                                                </h6>
                                                <p class="mb-0">Infiltrer dans le <strong>bon espace et au bon moment</strong> (timing et prise d'information).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-success me-2"></i>
                                                    Gagner du terrain balle au pied
                                                </h6>
                                                <p class="mb-0">Gagner du terrain <strong>balle au pied</strong>, fixer et faire sortir l'adversaire de sa position.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exclamation-triangle text-info me-2"></i>
                                                    Prise de risque calculée
                                                </h6>
                                                <p class="mb-0">Prise de risque calculée.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-layer-group text-warning me-2"></i>
                                                    Surmonter une ligne
                                                </h6>
                                                <p class="mb-0">Pouvoir <strong>surmonter une ligne</strong> pour joindre les joueurs lancés.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 1-2 : Infiltration -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                INFILTRATION DU DÉFENSEUR CENTRAL SI ESPACE LIBRE
                                            </h6>
                                            <img src="images/tactique/Img31.png?v=1" alt="Infiltration du défenseur central si espace libre" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                INFILTRATION DU DÉF. LATÉRAL SI ESPACE LIBRE
                                            </h6>
                                            <img src="images/tactique/Img32.png?v=1" alt="Infiltration du défenseur latéral si espace libre" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 7 : Création d'occasions de but -->
                                <div class="alert alert-danger mb-4">
                                    <h5><i class="fas fa-bullseye text-danger me-2"></i>CRÉATION D'OCCASIONS DE BUT PAR UNE PASSE DANS LE DOS DE LA DÉFENSE</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-expand-arrows-alt text-primary me-2"></i>
                                                    Créer des espaces
                                                </h6>
                                                <p class="mb-0">Créer des <strong>espaces dans le dos des défenseurs</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye-slash text-success me-2"></i>
                                                    Plonger dans le dos
                                                </h6>
                                                <p class="mb-0">Plonger dans le <strong>dos de la ligne défensive</strong> (hors du champ de vision des défenseurs) suivi par une <strong>passe subtile en profondeur</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-flag text-info me-2"></i>
                                                    Prendre la profondeur
                                                </h6>
                                                <p class="mb-0">Prendre la <strong>profondeur tout en évitant le hors-jeu</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrows-alt text-warning me-2"></i>
                                                    Jouer dans l'intervalle
                                                </h6>
                                                <p class="mb-0">Jouer dans l'<strong>intervalle pour éliminer les adversaires</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-running text-danger me-2"></i>
                                                    Courir avec et sans ballon
                                                </h6>
                                                <p class="mb-0">Courir <strong>avec et sans ballon</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-secondary me-2"></i>
                                                    Passing rapide et précis
                                                </h6>
                                                <p class="mb-0">Passing <strong>rapide et précis</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 3-5 : Création d'occasions -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                COURSE EXTÉRIEURE/INTÉRIEURE
                                            </h6>
                                            <img src="images/tactique/Img33.png?v=1" alt="Course extérieure/intérieure" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                DONNER ET PARTIR (GIVE&GO)
                                            </h6>
                                            <img src="images/tactique/Img34.png?v=1" alt="Donner et partir (Give&Go)" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 5 : Img35 -->
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                TECHNIQUES D'INFILTRATION AVANCÉES
                                            </h6>
                                            <img src="images/tactique/Img35.png?v=1" alt="Techniques d'infiltration avancées" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 8 : Création et conclusion sur centre -->
                                <div class="alert alert-warning mb-4">
                                    <h5><i class="fas fa-crosshairs text-warning me-2"></i>CRÉATION ET CONCLUSION D'OCCASIONS DE BUT SUR CENTRE</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-primary me-2"></i>
                                                    Qualité et timing du centre
                                                </h6>
                                                <p class="mb-0">Qualité et <strong>timing du centre</strong> (position adversaires + partenaires).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-users text-success me-2"></i>
                                                    Occupation efficace dans les 16m
                                                </h6>
                                                <p class="mb-0">Mouvements rapides et <strong>occupation efficace dans les 16m</strong> (1er, 2ème, 11m et 16m) par au moins 4 joueurs.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-running text-info me-2"></i>
                                                    Course diagonale
                                                </h6>
                                                <p class="mb-0">Course <strong>diagonale pour échapper à l'adversaire</strong> (hors du champ de vision) : timing.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exchange-alt text-warning me-2"></i>
                                                    Varier les centres
                                                </h6>
                                                <p class="mb-0">Varier les centres : de loin (fuyant derrière la défense), de près (au sol/lob) et en retrait (45°).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye text-danger me-2"></i>
                                                    Anticiper et réagir
                                                </h6>
                                                <p class="mb-0">Anticiper et <strong>réagir sur le rebond</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 1-2 : Création et conclusion sur centre -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                POSITION ET MOUVEMENT DANS LES 16M
                                            </h6>
                                            <img src="images/tactique/Img36.png?v=1" alt="Position et mouvement dans les 16m" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                CENTRE DE LOIN, DE PRÈS ET EN RETRAIT
                                            </h6>
                                            <img src="images/tactique/Img37.png?v=1" alt="Centre de loin, de près et en retrait" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 9 : Prévention contre-attaque -->
                                <div class="alert alert-danger mb-4">
                                    <h5><i class="fas fa-shield-alt text-danger me-2"></i>PRÉVENTION DE LA CONTRE-ATTAQUE ADVERSE</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-exclamation-triangle text-primary me-2"></i>
                                                    Pas de perte de balle stupide
                                                </h6>
                                                <p class="mb-0">Pas de <strong>perte de balle stupide</strong> : éviter le passing latéral.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                                    Formation du T-défensif
                                                </h6>
                                                <p class="mb-0">Anticipation défensive par la <strong>formation du T-défensif</strong> (2+1, 3+1 tenant compte de la disposition adverse).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user-friends text-info me-2"></i>
                                                    Marquage offensif
                                                </h6>
                                                <p class="mb-0">Marquage <strong>offensif sur les attaquants adverses</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-warning me-2"></i>
                                                    Position haute du gardien
                                                </h6>
                                                <p class="mb-0">Position <strong>haute du gardien de but</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-comments text-secondary me-2"></i>
                                                    Communication
                                                </h6>
                                                <p class="mb-0">Communication.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images 3-4 : Prévention contre-attaque -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                FORMER UN T-DÉFENSIF + POSITION HAUTE DU K
                                            </h6>
                                            <img src="images/tactique/Img38.png?v=1" alt="Former un T-défensif + position haute du K" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                MARQUER LES ATTAQUANTS ADVERSES
                                            </h6>
                                            <img src="images/tactique/Img39.png?v=1" alt="Marquer les attaquants adverses" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 10 : Lancement contre-attaque -->
                                <div class="alert alert-info mb-4">
                                    <h5><i class="fas fa-bolt text-info me-2"></i>LANCEMENT DE LA CONTRE-ATTAQUE DANGEREUSE</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye text-primary me-2"></i>
                                                    Anticipation offensive
                                                </h6>
                                                <p class="mb-0">Anticipation <strong>offensive</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-success me-2"></i>
                                                    Dès la récupération
                                                </h6>
                                                <p class="mb-0">Dès la <strong>récupération de balle</strong> : penser et jouer en profondeur (avec et sans ballon).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user text-info me-2"></i>
                                                    Joindre le joueur libre le plus haut
                                                </h6>
                                                <p class="mb-0">Joindre le <strong>joueur libre le plus haut</strong> (celui-ci décroche) et continuer à jouer en profondeur.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-running text-warning me-2"></i>
                                                    Plusieurs joueurs accompagnent
                                                </h6>
                                                <p class="mb-0">Plusieurs joueurs <strong>accompagnent la contre-attaque</strong> : sprint.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 5 : Lancement contre-attaque -->
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                DÈS RÉCUPÉRATION, VISER LA PROFONDEUR
                                            </h6>
                                            <img src="images/tactique/Img40.png?v=1" alt="Dès récupération, viser la profondeur" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Résumé des principes -->
                                <div class="card border-success mt-4">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary">
                                            <i class="fas fa-brain text-success me-2"></i>
                                            Résumé des principes de jeu en possession
                                        </h5>
                                        <div class="row mt-3">
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>DISPONIBILITÉ :</strong> Se démarquer et être jouable</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>CONSTRUCTION :</strong> Gardien et défenseurs comme point de départ</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>CIRCULATION :</strong> Progressive, variation, changement de côté</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>INFILTRATION :</strong> Avec/sans ballon et création d'occasions</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>CENTRES :</strong> Création et conclusion d'occasions</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>CONTRE-ATTAQUE :</strong> Prévention et lancement</p>
                                            </div>
                                        </div>
                                        <p class="mt-3 mb-0"><strong>Développés par la méthode MIM</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Principes de jeu en perte de balle -->
                        <div class="card border-danger mt-4">
                            <div class="card-body">
                                <h4 class="text-primary text-center mb-4">
                                    <i class="fas fa-shield-alt text-danger me-2"></i>
                                    Les principes de jeu en perte de balle
                                </h4>
                                
                                <div class="alert alert-danger mb-4">
                                    <h5><i class="fas fa-exclamation-triangle text-danger me-2"></i>EN PERTE DE BALLE</h5>
                                    <p class="mb-0">Ci-après des principes de jeu basés sur la <strong>vision de formation</strong>, sa <strong>philosophie (jeu en zone)</strong>, son <strong>plan d'apprentissage (basics et teamtactics)</strong>, et à développer par la <strong>méthode MIM</strong>.</p>
                                </div>

                                <!-- Principe 1 : Formation d'un bloc compact -->
                                <div class="alert alert-primary mb-4">
                                    <h5><i class="fas fa-layer-group text-primary me-2"></i>FORMATION D'UN BLOC COMPACT (EN LARGEUR ET EN LONGUEUR)</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-compress-arrows-alt text-primary me-2"></i>
                                                    Réduire l'espace de jeu
                                                </h6>
                                                <p class="mb-0">Réduire l'<strong>espace de jeu de l'adversaire</strong> et l'espace entre les lignes.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-ruler text-success me-2"></i>
                                                    Respecter les distances
                                                </h6>
                                                <p class="mb-0">Respecter les <strong>distances entre les lignes (10-15m)</strong> et les distances entre joueurs d'une même ligne (7-10m).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrows-alt-h text-info me-2"></i>
                                                    Coulisser et basculer
                                                </h6>
                                                <p class="mb-0">Coulisser et <strong>basculer vers le ballon</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-times text-warning me-2"></i>
                                                    Pas de courses croisées
                                                </h6>
                                                <p class="mb-0">Pas de <strong>courses croisées entre les joueurs</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-up text-danger me-2"></i>
                                                    Définir la hauteur du bloc
                                                </h6>
                                                <p class="mb-0">Définir la <strong>hauteur du bloc (bas – médian – haut)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-comments text-secondary me-2"></i>
                                                    Communication fondamentale
                                                </h6>
                                                <p class="mb-0">Communication <strong>fondamentale entre joueurs et lignes de joueurs</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-flag text-info me-2"></i>
                                                    Ligne de hors-jeu
                                                </h6>
                                                <p class="mb-0">Le <strong>défenseur central le plus près de la balle</strong> détermine la ligne de hors-jeu.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user-shield text-success me-2"></i>
                                                    Le gardien couvre le bloc
                                                </h6>
                                                <p class="mb-0">Le <strong>gardien de but couvre le bloc</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 1 : Formation d'un bloc compact -->
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                LES JOUEURS DE COULOIRS GLISSENT VERS L'AXE
                                            </h6>
                                            <img src="images/tactique/Img41.png?v=1" alt="Les joueurs de couloirs glissent vers l'axe" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 2 : Pression agressive -->
                                <div class="alert alert-warning mb-4">
                                    <h5><i class="fas fa-bullseye text-warning me-2"></i>PRESSION AGRESSIVE SUR LE PORTEUR DU BALLON</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    Pression individuelle
                                                </h6>
                                                <p class="mb-0">Pression <strong>individuelle du joueur (pressing positif)</strong> qui se trouve le plus proche du ballon, les autres couvrent.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-ban text-success me-2"></i>
                                                    Empêcher les passes en profondeur
                                                </h6>
                                                <p class="mb-0">Empêcher les <strong>passes en profondeur</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye text-info me-2"></i>
                                                    Si pas de pression
                                                </h6>
                                                <p class="mb-0">Si pas de pression sur le ballon <strong>anticiper (la passe en profondeur) et reculer</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-users text-warning me-2"></i>
                                                    Pressing collectif
                                                </h6>
                                                <p class="mb-0">Actionner un <strong>pressing collectif</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 2 : Pression agressive -->
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                PRESSING POSITIF SUR LE PORTEUR DU BALLON
                                            </h6>
                                            <img src="images/tactique/Img42.png?v=1" alt="Pressing positif sur le porteur du ballon" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 3 : Récupération du ballon -->
                                <div class="alert alert-danger mb-4">
                                    <h5><i class="fas fa-futbol text-danger me-2"></i>RÉCUPÉRATION DU BALLON</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-fist-raised text-primary me-2"></i>
                                                    Jouer et gagner le duel
                                                </h6>
                                                <p class="mb-0">Jouer et <strong>gagner le duel (position du corps)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye text-success me-2"></i>
                                                    Anticiper et intercepter
                                                </h6>
                                                <p class="mb-0">Anticiper et <strong>intercepter : couper les lignes/angles de passe</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-running text-info me-2"></i>
                                                    Pressing négatif
                                                </h6>
                                                <p class="mb-0">Pressing <strong>négatif (= sprint)</strong> : travail défensif des joueurs du bloc offensif.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 3 : Récupération du ballon -->
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                PRESSING NÉGATIF SUR LE PORTEUR DU BALLON
                                            </h6>
                                            <img src="images/tactique/Img43.png?v=1" alt="Pressing négatif sur le porteur du ballon" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 4 : Fermeture de l'axe -->
                                <div class="alert alert-info mb-4">
                                    <h5><i class="fas fa-compress-arrows-alt text-info me-2"></i>FERMETURE DE L'AXE</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-shield-alt text-primary me-2"></i>
                                                    Formation du T-défensif
                                                </h6>
                                                <p class="mb-0">Formation du <strong>T-défensif orienté axe ballon-but</strong> (assez de joueurs dans l'axe).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-ban text-success me-2"></i>
                                                    Empêcher la profondeur
                                                </h6>
                                                <p class="mb-0">Empêcher l'adversaire de <strong>trouver la profondeur</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-compress text-info me-2"></i>
                                                    Réduire l'espace dans le dos
                                                </h6>
                                                <p class="mb-0">Réduire l'<strong>espace dans le dos de la défense</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-times text-warning me-2"></i>
                                                    Fermer les angles de passe
                                                </h6>
                                                <p class="mb-0">Fermer les <strong>angles de passe</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 1 : Fermeture de l'axe -->
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                RESSERRER ET FERMER L'AXE
                                            </h6>
                                            <img src="images/tactique/Img44.png?v=1" alt="Resserrer et fermer l'axe" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 5 : Défense efficace de la zone de vérité -->
                                <div class="alert alert-warning mb-4">
                                    <h5><i class="fas fa-shield-alt text-warning me-2"></i>DÉFENSE EFFICACE DE LA ZONE DE VÉRITÉ</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-user-friends text-primary me-2"></i>
                                                    Marquage plus strict
                                                </h6>
                                                <p class="mb-0">Marquage <strong>plus strict dans la zone de vérité</strong> (agressivité positive).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye text-success me-2"></i>
                                                    Ne pas se laisser surprendre
                                                </h6>
                                                <p class="mb-0">Ne pas se laisser surprendre par les adversaires lancés dans les 16m (split-vision).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-fist-raised text-info me-2"></i>
                                                    Ne pas se faire éliminer
                                                </h6>
                                                <p class="mb-0">Ne pas se faire éliminer en 1c1, <strong>gagner le duel, ne pas laisser tirer</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-ban text-warning me-2"></i>
                                                    Bloquer les tirs et centres
                                                </h6>
                                                <p class="mb-0">Bloquer les <strong>tirs au but et les centres</strong> (attention aux mains).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-users text-danger me-2"></i>
                                                    Occupation efficace devant le but
                                                </h6>
                                                <p class="mb-0">Occupation efficace devant le but sur les centres : <strong>formation du T (couvrir 1er, 2ème et 11m)</strong> + position du corps (de trois-quarts).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-secondary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-secondary me-2"></i>
                                                    Premier sur le ballon
                                                </h6>
                                                <p class="mb-0">Premier sur le <strong>ballon dans sa zone (agressivité)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-sync-alt text-primary me-2"></i>
                                                    Suivre et réagir sur le rebond
                                                </h6>
                                                <p class="mb-0">Suivre et <strong>réagir sur le rebond (2ème et 3ème ballon)</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-comments text-success me-2"></i>
                                                    Communication du gardien
                                                </h6>
                                                <p class="mb-0">Communication du <strong>gardien de but et des défenseurs près du but</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 2 : Défense efficace de la zone de vérité -->
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                FORMATION EN T DEVANT NOTRE BUT SUR CENTRE
                                            </h6>
                                            <img src="images/tactique/Img45.png?v=1" alt="Formation en T devant notre but sur centre" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Principe 6 : Déjouer la contre-attaque -->
                                <div class="alert alert-danger mb-4">
                                    <h5><i class="fas fa-bolt text-danger me-2"></i>DÉJOUER LA CONTRE-ATTAQUE</h5>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-bullseye text-primary me-2"></i>
                                                    Pression immédiate
                                                </h6>
                                                <p class="mb-0">Dès la perte de balle, <strong>pression immédiate sur le porteur du ballon</strong> (contre-pressing).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye text-success me-2"></i>
                                                    Anticiper la passe en profondeur
                                                </h6>
                                                <p class="mb-0">Anticiper la <strong>passe en profondeur</strong> : si pas de pression sur le ballon → reculer.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-info h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-arrow-down text-info me-2"></i>
                                                    Recul-frein
                                                </h6>
                                                <p class="mb-0">Recul-frein pour les <strong>joueurs du T-défensif</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4" col-12>
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-running text-warning me-2"></i>
                                                    Retour rapide dans le bloc
                                                </h6>
                                                <p class="mb-0">Retour <strong>rapide dans le bloc des joueurs hors du T-défensif</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 3 : Déjouer la contre-attaque -->
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-4" col-12>
                                        <div class="text-center">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                RETOUR RAPIDE DANS LE BLOC DES JOUEURS HORS DU T
                                            </h6>
                                            <img src="images/tactique/Img46.png?v=1" alt="Retour rapide dans le bloc des joueurs hors du T" class="img-fluid mx-auto d-block" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Résumé des principes en perte de balle -->
                                <div class="card border-danger mt-4">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary">
                                            <i class="fas fa-shield-alt text-danger me-2"></i>
                                            Résumé des principes de jeu en perte de balle
                                        </h5>
                                        <div class="row mt-3">
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>BLOC COMPACT :</strong> Formation en largeur et longueur</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>PRESSION AGRESSIVE :</strong> Sur le porteur du ballon</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>RÉCUPÉRATION :</strong> Du ballon par duel et interception</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>FERMETURE AXE :</strong> T-défensif et angles de passe</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>ZONE DE VÉRITÉ :</strong> Défense efficace et marquage strict</p>
                                            </div>
                                            <div class="col-md-2" col-12>
                                                <p class="mb-0"><strong>CONTRE-ATTAQUE :</strong> Déjouer par pression et anticipation</p>
                                            </div>
                                        </div>
                                        <p class="mt-3 mb-0"><strong>Développés par la méthode MIM</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <p class="lead">Chaque catégorie d'âge bénéficie d'un format de jeu adapté à son développement psychomoteur et cognitif.</p>
                
                <!-- Format U6-U7 -->
                <div class="format-card card mb-3">
                    <div class="card-body">
                        <div class="age-badge">U6 à U7</div>
                        <h5><i class="fas fa-futbol text-primary me-2"></i>Format de jeu à 2 - U6 à jeu à 3 - U7</h5>
                        <p>Initiation au football avec des formats réduits permettant une approche ludique et progressive des fondamentaux.</p>
                        <ul>
                            <li>Développement de la coordination</li>
                            <li>Premiers contacts avec le ballon</li>
                            <li>Apprentissage des règles de base</li>
                        </ul>
                    </div>
                </div>

                <!-- Format U8-U9 -->
                <div class="format-card card mb-3">
                    <div class="card-body">
                        <div class="age-badge">U8 à U9</div>
                        <h5><i class="fas fa-futbol text-primary me-2"></i>Format de jeu à 5</h5>
                        <p>Développement des compétences techniques de base dans un contexte de jeu simplifié.</p>
                        <ul>
                            <li>Technique individuelle</li>
                            <li>Premières notions tactiques</li>
                            <li>Esprit d'équipe</li>
                        </ul>
                    </div>
                </div>

                <!-- Format U10-U13 -->
                <div class="format-card card mb-3">
                    <div class="card-body">
                        <div class="age-badge">U10 à U13</div>
                        <h5><i class="fas fa-futbol text-primary me-2"></i>Format de jeu à 8</h5>
                        <p>Transition vers un football plus structuré avec l'introduction des concepts tactiques avancés.</p>
                        <ul>
                            <li>Perfectionnement technique</li>
                            <li>Développement tactique</li>
                            <li>Préparation physique</li>
                        </ul>
                    </div>
                </div>

                <!-- Format U14-U19 -->
                <div class="format-card card mb-3">
                    <div class="card-body">
                        <div class="age-badge">U14 à U19</div>
                        <h5><i class="fas fa-futbol text-primary me-2"></i>Format de jeu à 11</h5>
                        <p>Football à 11 complet avec tous les aspects du jeu moderne.</p>
                        <ul>
                            <li>Maîtrise technique complète</li>
                            <li>Stratégie et tactique avancées</li>
                            <li>Préparation compétitive</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Principes tactiques ASOD ACADEMIE -->
        <div class="card" id="principes-tactiques" data-aos="fade-up" data-aos-delay="900">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-chess-board text-primary" style="font-size: 3rem;"></i>
                    <h3 class="text-primary">Principes Tactiques ASOD ACADEMIE</h3>
                </div>
                
                <div class="alert alert-info">
                    <h5><i class="fas fa-lightbulb text-info me-2"></i>Formation Progressive</h5>
                    <p class="mb-0">Les principes de jeu en zone se travaillent de manière cumulative tout au long de la formation du jeune joueur en fonction de ses capacités techniques – tactiques – physiques et mentales.</p>
                </div>

                <!-- Formation 1-4-3-3 -->
                <div class="row mb-5">
                    <div class="col-lg-4 mb-4" col-md-12 col-12>
                        <div class="card border-primary h-100">
                            <div class="card-body text-center">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-star text-warning me-2"></i>
                                    Le Clin d'Œil de ASOD
                                </h4>
                                <h5 class="text-muted">Formation 1-4-3-3 en Zone</h5>
                                <img src="images/tactique/le clin d'oeil.png" alt="Formation 1-4-3-3 en Zone" class="img-fluid mb-3 mx-auto d-block" style="max-height: 300px;">
                                <p class="small text-muted">
                                    <strong>Structure :</strong> Gardien (1) | Défense (2-5) | Milieu (6-8-10) | Attaque (7-9-11)
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4" col-md-12 col-12>
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-futbol text-success me-2"></i>
                                    EN POSSESSION DE BALLE
                                </h4>
                                <img src="images/tactique/communication.png" alt="Principes de jeu en possession de balle" class="img-fluid mb-3 mx-auto d-block" style="max-height: 250px;">
                                <p class="mb-3">
                                    <strong>Occupation rationnelle des positions</strong> de chaque joueur sur le terrain en possession de balle pour se créer des occasions de buts par du mouvement avec et sans ballon.
                                </p>
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-check text-success me-2"></i>Occupation rationnelle</li>
                                            <li><i class="fas fa-check text-success me-2"></i>100% de possession</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Diagonaliser/Verticaliser</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4" col-md-12 col-12>
                        <div class="card border-danger h-100">
                            <div class="card-body">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-shield-alt text-danger me-2"></i>
                                    EN PERTE DE BALLE
                                </h4>
                                <img src="images/tactique/perte.png" alt="Principes de jeu en perte de balle" class="img-fluid mb-3 mx-auto d-block" style="max-height: 250px;">
                                <p class="mb-3">
                                    <strong>Occupation rationnelle des positions</strong> de chaque joueur sur le terrain en perte de balle pour récupérer le ballon et empêcher l'adversaire de se créer des occasions de buts.
                                </p>
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-check text-danger me-2"></i>Bloc compact medium-haut</li>
                                            <li><i class="fas fa-check text-danger me-2"></i>Pression individuelle</li>
                                            <li><i class="fas fa-check text-danger me-2"></i>Fermer les angles</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Détail des principes -->
                <div class="row">
                    <div class="col-md-4 mb-4" col-12>
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h5 class="text-primary">
                                    <i class="fas fa-bullseye text-success me-2"></i>
                                    Objectifs Offensifs
                                </h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Créer des occasions</strong> par le mouvement avec et sans ballon
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Conserver le ballon</strong> avec une possession de 100%
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Étendre l'espace</strong> de jeu pour déséquilibrer
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Varier les angles</strong> de jeu (diagonal/vertical)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4" col-12>
                        <div class="card border-danger h-100">
                            <div class="card-body">
                                <h5 class="text-primary">
                                    <i class="fas fa-shield-alt text-danger me-2"></i>
                                    Objectifs Défensifs
                                </h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-danger me-2"></i>
                                        <strong>Récupération immédiate</strong> du ballon
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-danger me-2"></i>
                                        <strong>Bloc compact</strong> medium-haut avec distance limitée
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-danger me-2"></i>
                                        <strong>Pression individuelle</strong> du joueur le plus proche
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-danger me-2"></i>
                                        <strong>Fermer les angles</strong> de jeu directs
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4" col-12>
                        <div class="card border-info h-100">
                            <div class="card-body">
                                <h5 class="text-primary">
                                    <i class="fas fa-comments text-info me-2"></i>
                                    Communication et Anticipation
                                </h5>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-info me-2"></i>
                                        <strong>Communication</strong> constante entre tous les joueurs
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-info me-2"></i>
                                        <strong>Anticiper</strong> la perte de balle pour la transition
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-info me-2"></i>
                                        <strong>Assez de joueurs</strong> dans l'axe central
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-info me-2"></i>
                                        <strong>Pas de courses croisées</strong> désorganisées
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Méthodes offensives et défensives -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-4" col-12>
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h5 class="text-primary text-center mb-4">
                                    <i class="fas fa-trophy text-success me-2"></i>
                                    Méthodes de Création d'Occasions
                                </h5>
                                <div class="row">
                                    <div class="col-12 text-center mb-3">
                                        <div class="p-3 border rounded">
                                            <i class="fas fa-passport text-success" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">Passe dans le Dos</h6>
                                            <p class="small mb-0">Passer derrière la défense adverse pour créer des occasions de but</p>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center mb-3">
                                        <div class="p-3 border rounded">
                                            <i class="fas fa-crosshairs text-warning" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">Occupation Efficace</h6>
                                            <p class="small mb-0">Être bien placé devant le but pour marquer</p>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center mb-3">
                                        <div class="p-3 border rounded">
                                            <i class="fas fa-bolt text-danger" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">Toujours Tenter</h6>
                                            <p class="small mb-0">Prendre l'initiative et tenter sa chance</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4" col-12>
                        <div class="card border-danger h-100">
                            <div class="card-body">
                                <h5 class="text-primary text-center mb-4">
                                    <i class="fas fa-shield-alt text-danger me-2"></i>
                                    Méthodes de Protection du But
                                </h5>
                                <div class="row">
                                    <div class="col-12 text-center mb-3">
                                        <div class="p-3 border rounded">
                                            <i class="fas fa-users text-danger" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">Bloc Compact</h6>
                                            <p class="small mb-0">Distance mutuelle limitée au sein du bloc medium-haut</p>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center mb-3">
                                        <div class="p-3 border rounded">
                                            <i class="fas fa-crosshairs text-warning" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">Zone de Vérité</h6>
                                            <p class="small mb-0">Marquage strict dans la zone critique devant le but</p>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center mb-3">
                                        <div class="p-3 border rounded">
                                            <i class="fas fa-user-shield text-info" style="font-size: 2rem;"></i>
                                            <h6 class="mt-2">Position Haute Gardien</h6>
                                            <p class="small mb-0">Pas de hors-jeu systématique, gardien en position haute</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Les principes de jeu -->
        <div class="card" id="principes-jeu" data-aos="fade-up" data-aos-delay="1000">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-cogs formation-icon text-secondary"></i>
                    <h3 class="text-primary">6. Les principes de jeu qui peuvent être développés quel que soit le dispositif tactique</h3>
                </div>
                <p class="lead">Ces principes fondamentaux constituent la base de notre philosophie de jeu et sont développés à tous les niveaux de formation.</p>
                
                <div class="row mt-4">
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-shield-alt text-primary me-2"></i>Principes Défensifs</h5>
                            <ul>
                                <li>Récupération du ballon</li>
                                <li>Compactité défensive</li>
                                <li>Pression collective</li>
                                <li>Transitions défensives</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6" col-12>
                        <div class="principle-item">
                            <h5><i class="fas fa-rocket text-primary me-2"></i>Principes Offensifs</h5>
                            <ul>
                                <li>Conservation du ballon</li>
                                <li>Progression collective</li>
                                <li>Finalisation</li>
                                <li>Transitions offensives</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directeur Technique -->
        <?php if ($directeurTechnique): ?>
        <div class="card" data-aos="fade-up" data-aos-delay="1000">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-whistle text-primary" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-primary">Directeur Technique</h4>
                <h5 class="text-muted"><?= htmlspecialchars($directeurTechnique['nom'] . ' ' . $directeurTechnique['prenom']) ?></h5>
                <?php if (!empty($directeurTechnique['diplome'])): ?>
                    <p class="text-muted"><?= htmlspecialchars($directeurTechnique['diplome']) ?></p>
                <?php endif; ?>
                <p class="small">Responsable de la mise en œuvre de cette vision de formation selon les principes de la DTN</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Boutons d'action -->
        <div class="text-center mt-5">
            <a href="index.php#inscription" class="btn btn-primary btn-lg me-3">
                <i class="fas fa-user-plus me-2"></i>Inscription
            </a>
            <a href="formations_publiques.php" class="btn btn-warning btn-lg me-3">
                <i class="fas fa-graduation-cap me-2"></i>Nos Formations Pratiques
            </a>
            <a href="index.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Suivez-nous sur les réseaux sociaux -->
    <div class="container mt-5 mb-5">
        <div class="card border-primary" data-aos="fade-up" data-aos-delay="100">
            <div class="card-body text-center">
                <h3 class="text-primary mb-4">
                    <i class="fas fa-share-alt text-primary me-2"></i>
                    Suivez-nous sur les réseaux sociaux
                </h3>
                
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4 mb-4" col-12>
                        <div class="card border-primary h-100">
                            <div class="card-body text-center">
                                <i class="fab fa-facebook text-primary mb-3" style="font-size: 3rem;"></i>
                                <h5 class="text-primary">Facebook</h5>
                                <p class="mb-3">Suivez l'actualité d'ASOD ACADEMIE et de l'Académie sur notre page Facebook officielle.</p>
                                <a href="https://www.facebook.com/share/1FAoftp4RW/" target="_blank" class="btn btn-primary btn-lg">
                                    <i class="fab fa-facebook me-2"></i>
                                    Suivre sur Facebook
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <h5><i class="fas fa-info-circle text-info me-2"></i>Restez connectés !</h5>
                    <p class="mb-0">Rejoignez notre communauté sur Facebook pour ne rien manquer de l'actualité du club, des résultats des matchs, des photos des entraînements et des événements de l'ASOD Académie.</p>
                </div>
            </div>
        </div>
            </div>

            <!-- Onglet Évaluations -->
            <div class="tab-pane fade" id="evaluations" role="tabpanel" aria-labelledby="evaluations-tab">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary mb-4"><i class="fas fa-chart-line me-2"></i>Évaluations de Formation</h3>
                        <div id="evaluations-content">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p class="mt-2">Chargement des évaluations...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Sessions -->
            <div class="tab-pane fade" id="sessions" role="tabpanel" aria-labelledby="sessions-tab">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary mb-4"><i class="fas fa-calendar-alt me-2"></i>Sessions de Formation</h3>
                        <div id="sessions-content">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p class="mt-2">Chargement des sessions...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact ASOD Académie -->
    <div class="container mt-5 mb-5">
        <div class="card border-success" data-aos="fade-up" data-aos-delay="100">
            <div class="card-body text-center">
                <h3 class="text-success mb-4">
                    <i class="fas fa-envelope text-success me-2"></i>
                    Contactez l'ASOD Académie
                </h3>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6" col-12>
                        <div class="card border-success h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-envelope text-success mb-3" style="font-size: 3rem;"></i>
                                <h5 class="text-success">Email ASOD Académie</h5>
                                <p class="mb-3">Pour toute question concernant l'ASOD Académie, les formations, les inscriptions ou les programmes éducatifs.</p>
                                <a href="mailto:asodacedemie@gmail.com" class="btn btn-success btn-lg">
                                    <i class="fas fa-envelope me-2"></i>
                                    asodacedemie@gmail.com
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success mt-4">
                    <h5><i class="fas fa-graduation-cap text-success me-2"></i>Une question sur la formation ?</h5>
                    <p class="mb-0">Notre équipe pédagogique est à votre disposition pour répondre à toutes vos questions sur nos programmes de formation, nos méthodes d'apprentissage et nos objectifs éducatifs.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Smooth scrolling pour les liens de la table des matières
        document.querySelectorAll('.toc a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Chargement du contenu des onglets
        document.addEventListener('DOMContentLoaded', function() {
            // Charger les évaluations
            document.getElementById('evaluations-tab').addEventListener('shown.bs.tab', function() {
                loadEvaluations();
            });

            // Charger les sessions
            document.getElementById('sessions-tab').addEventListener('shown.bs.tab', function() {
                loadSessions();
            });
        });

        function loadEvaluations() {
            const content = document.getElementById('evaluations-content');
            if (content.innerHTML.includes('Chargement')) {
                fetch('php/api_formation_evaluations.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayEvaluations(data.data);
                        } else {
                            content.innerHTML = '<div class="alert alert-warning">Aucune évaluation disponible pour le moment.</div>';
                        }
                    })
                    .catch(error => {
                        content.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des évaluations.</div>';
                    });
            }
        }

        function loadSessions() {
            const content = document.getElementById('sessions-content');
            if (content.innerHTML.includes('Chargement')) {
                fetch('php/api_formation_sessions.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displaySessions(data.data);
                        } else {
                            content.innerHTML = '<div class="alert alert-warning">Aucune session disponible pour le moment.</div>';
                        }
                    })
                    .catch(error => {
                        content.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des sessions.</div>';
                    });
            }
        }

        function displayEvaluations(evaluations) {
            const content = document.getElementById('evaluations-content');
            
            if (evaluations.length === 0) {
                content.innerHTML = '<div class="alert alert-info">Aucune évaluation disponible pour le moment.</div>';
                return;
            }

            let html = '<div class="row">';
            evaluations.forEach(eval => {
                const date = new Date(eval.date_evaluation).toLocaleDateString('fr-FR');
                const noteGenerale = eval.note_generale || 'N/A';
                
                html += `
                    <div class="col-md-6 col-lg-4 mb-4" col-12>
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">${eval.joueur_nom}</h5>
                                <p class="card-text">
                                    <strong>Session:</strong> ${eval.session_titre || 'N/A'}<br>
                                    <strong>Date:</strong> ${date}<br>
                                    <strong>Note générale:</strong> ${noteGenerale}/20
                                </p>
                                <div class="row text-center">
                                    <div class="col-3">
                                        <small class="text-muted">Technique</small><br>
                                        <span class="badge bg-primary">${eval.note_technique || 'N/A'}</span>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted">Tactique</small><br>
                                        <span class="badge bg-info">${eval.note_tactique || 'N/A'}</span>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted">Physique</small><br>
                                        <span class="badge bg-success">${eval.note_physique || 'N/A'}</span>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted">Mentale</small><br>
                                        <span class="badge bg-warning">${eval.note_mentale || 'N/A'}</span>
                                    </div>
                                </div>
                                ${eval.commentaires ? `<p class="mt-2"><small class="text-muted">${eval.commentaires}</small></p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            content.innerHTML = html;
        }

        function displaySessions(sessions) {
            const content = document.getElementById('sessions-content');
            
            if (sessions.length === 0) {
                content.innerHTML = '<div class="alert alert-info">Aucune session disponible pour le moment.</div>';
                return;
            }

            let html = '<div class="row">';
            sessions.forEach(session => {
                const date = new Date(session.date_session).toLocaleDateString('fr-FR');
                const statutClass = session.statut === 'active' ? 'success' : 
                                  session.statut === 'completed' ? 'primary' : 'secondary';
                
                html += `
                    <div class="col-md-6 col-lg-4 mb-4" col-12>
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">${session.titre}</h5>
                                <p class="card-text">
                                    <strong>Date:</strong> ${date}<br>
                                    <strong>Heure:</strong> ${session.heure_debut} - ${session.heure_fin}<br>
                                    <strong>Lieu:</strong> ${session.lieu || 'À définir'}<br>
                                    <strong>Type:</strong> ${session.type_session}<br>
                                    <strong>Niveau:</strong> ${session.niveau_requis}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-${statutClass}">${session.statut}</span>
                                    <small class="text-muted">${session.nombre_inscrits}/${session.nombre_places} places</small>
                                </div>
                                ${session.description ? `<p class="mt-2"><small>${session.description}</small></p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            content.innerHTML = html;
        }
    </script>
</body>
</html>
