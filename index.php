<?php
// ASOD ACADEMIE - Site Web Professionnel
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Statistiques réelles de la base de données
    $stmt = $pdo->query("SELECT COUNT(*) FROM membres WHERE statut = 'actif'");
    $stats_membres = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM equipes");
    $stats_equipes = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM entraineurs WHERE actif = 1");
    $stats_entraineurs = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM formations WHERE statut = 'actif'");
    $stats_formations = $stmt->fetchColumn();
    
    // Années d'expérience (depuis 2018)
    $stats_annees = date('Y') - 2018;
    
} catch (Exception $e) {
    // Valeurs par défaut en cas d'erreur
    $stats_membres = 16;
    $stats_equipes = 8;
    $stats_entraineurs = 6;
    $stats_formations = 8;
    $stats_annees = 7;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASOD ACADEMIE - Association Sportive Oeil du Défi</title>
    <meta name="description" content="ASOD ACADEMIE - Association Sportive Oeil du Défi. Formation sportive d'excellence depuis 2018. Rejoignez-nous pour une expérience sportive unique.">
    <link rel="icon" type="image/png" href="images/logo.png">
    
    <!-- CDN pour les performances -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* Variables CSS pour l'identité ASOD ACADEMIE */
        :root {
            --primary-blue: #0d6efd;
            --accent-yellow: #ffc107;
            --dark-blue: #1a1a2e;
            --light-gray: #f8f9fa;
            --text-dark: #333;
            --text-light: #666;
            --white: #ffffff;
            --border-radius: 0.625rem;  /* 10px */
            --shadow: 0 0.25rem 0.9375rem rgba(0, 0, 0, 0.1);  /* 0 4px 15px */
            --transition: all 0.3s ease;
            
            /* Unités relatives pour responsive */
            --spacing-xs: 0.25rem;   /* 4px */
            --spacing-sm: 0.5rem;    /* 8px */
            --spacing-md: 1rem;      /* 16px */
            --spacing-lg: 1.5rem;    /* 24px */
            --spacing-xl: 2rem;      /* 32px */
            --spacing-xxl: 3rem;     /* 48px */
            
            /* Tailles de police relatives */
            --font-xs: 0.75rem;      /* 12px */
            --font-sm: 0.875rem;     /* 14px */
            --font-base: 1rem;       /* 16px */
            --font-lg: 1.125rem;     /* 18px */
            --font-xl: 1.25rem;      /* 20px */
            --font-2xl: 1.5rem;      /* 24px */
            --font-3xl: 2rem;        /* 32px */
            --font-4xl: 2.5rem;      /* 40px */
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--white);
        }

        .container {
            max-width: 75rem;  /* 1200px */
            margin: 0 auto;
            padding: 0 var(--spacing-lg);  /* 0 24px */
        }

        /* Header */
        .header {
            background: var(--primary-blue);
            color: var(--white);
            padding: var(--spacing-md) 0;  /* 1rem 0 */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);  /* 1rem */
            font-size: var(--font-2xl);  /* 1.5rem */
            font-weight: bold;
        }

        /* Images responsives */
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        .logo img {
            width: 3.125rem;  /* 50px */
            height: 3.125rem;  /* 50px */
            border-radius: 50%;
            object-fit: cover;
        }

        .nav {
            display: flex;
            align-items: center;
        }

        .nav-list {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
        }

        .nav-list > li {
            position: relative;
        }

        .nav a {
            color: var(--white);
            text-decoration: none;
            font-weight: 600;  /* Plus gras */
            transition: var(--transition);
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            display: block;
        }

        .nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        /* Navigation Groups */
        .nav-group {
            position: relative;
        }

        .nav-group-title {
            color: var(--white);
            font-weight: 600;
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            display: block;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-group-title:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--accent-yellow);
        }

            .nav-group-title::after {
                content: '▼';
                margin-left: var(--spacing-sm);  /* 0.5rem */
                font-size: var(--font-sm);  /* 0.875rem */
                transition: var(--transition);
            }
            
            .nav-group-title.active::after {
                transform: rotate(180deg);
            }

        /* Submenu */
        .nav-submenu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
        }

        .nav-group:hover .nav-submenu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .nav-submenu li {
            list-style: none;
        }

        .nav-submenu a {
            color: var(--text-dark);
            padding: 0.8rem 1.5rem;
            border-radius: 0;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
            transition: var(--transition);
        }

        .nav-submenu a:hover {
            background: var(--light-gray);
            color: var(--primary-blue);
            border-left-color: var(--primary-blue);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 1001;
        }

        .mobile-menu-toggle span {
            width: 25px;
            height: 3px;
            background: var(--white);
            margin: 3px 0;
            transition: var(--transition);
            border-radius: 2px;
        }

        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-yellow));
            min-height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--white);
            position: relative;
            overflow: hidden;
            padding: var(--spacing-xxl) 0;  /* 3rem 0 */
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: var(--font-4xl);  /* 2.5rem */
            font-weight: 800;
            margin-bottom: var(--spacing-md);  /* 1rem */
            text-shadow: 0.125rem 0.125rem 0.25rem rgba(0, 0, 0, 0.3);  /* 2px 2px 4px */
        }
        
        .hero .subtitle {
            font-size: var(--font-xl);  /* 1.25rem */
            margin-bottom: var(--spacing-xl);  /* 2rem */
            opacity: 0.9;
        }

        .cta-button {
            display: inline-block;
            background: var(--accent-yellow);
            color: var(--dark-blue);
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
            color: var(--dark-blue);
        }

        /* Stats Section */
        .stats {
            background: var(--white);
            padding: var(--spacing-xxl) 0;  /* 3rem 0 */
            box-shadow: 0 -0.25rem 0.9375rem rgba(0, 0, 0, 0.1);  /* 0 -4px 15px */
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(12.5rem, 1fr));  /* minmax(200px, 1fr) */
            gap: var(--spacing-xl);  /* 2rem */
            text-align: center;
        }

        .stat-item {
            padding: 2rem;
            background: var(--light-gray);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-blue);
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-light);
            font-weight: 500;
        }

        /* About Section */
        .about {
            background: var(--light-gray);
            padding: var(--spacing-xxl) 0;  /* 3rem 0 */
        }

        .section-title {
            text-align: center;
            font-size: var(--font-3xl);  /* 2rem */
            font-weight: bold;
            color: var(--primary-blue);
            margin-bottom: var(--spacing-xxl);  /* 3rem */
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-xxl);  /* 3rem */
            align-items: center;
        }

        .about-text h3 {
            font-size: 1.8rem;
            color: var(--primary-blue);
                margin-bottom: 1rem;
            }
            
        .about-text p {
            color: var(--text-light);
                margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .about-image img {
            width: 100%;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }



        /* Inscription Section */
        .inscription {
            background: var(--light-gray);
            padding: 4rem 0;
        }

        .inscription-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        .inscription-info h3,
        .inscription-form h3 {
            color: var(--primary-blue);
            margin-bottom: 2rem;
                font-size: 1.5rem;
            }
            
        .inscription-form h4 {
            color: var(--primary-red);
            margin: 2rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-red);
            font-size: 1.2rem;
        }
        
        .form-note {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
                padding: 1rem;
            margin: 1.5rem 0;
        }
        
        .form-note p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .inscription-info p {
            color: var(--text-light);
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .inscription-benefits {
            margin-top: 2rem;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .benefit-item i {
            color: var(--accent-yellow);
            font-size: 1.2rem;
        }
        
        .benefit-item span {
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-family: inherit;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        .checkbox-group {
            margin-bottom: 1rem;
        }

        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            cursor: pointer;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .checkbox-label input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .checkmark {
            color: var(--primary-blue);
            font-weight: bold;
        }

        .submit-btn {
            background: var(--primary-blue);
            color: var(--white);
            padding: 1rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
        }

        .submit-btn:hover {
            background: var(--dark-blue);
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background: var(--dark-blue);
            color: var(--white);
            padding: 3rem 0 1rem;
            text-align: center;
        }

        .footer-content h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .footer-content p {
            opacity: 0.8;
            margin-bottom: 2rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-links a {
            color: var(--white);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--accent-yellow);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
            opacity: 0.7;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex;
            }
            
            .nav {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background: var(--primary-blue);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
                overflow-y: auto;
                display: flex;
            }
            
            .nav.active {
                transform: translateX(0);
            }
            
            .nav-list {
                flex-direction: column;
                gap: var(--spacing-lg);  /* 1.5rem */
                text-align: center;
                width: 100%;
                padding: var(--spacing-xl) 0;  /* 2rem 0 */
            }
        
            .nav-group-title {
                font-size: var(--font-xl);  /* 1.25rem - plus grand */
                padding: var(--spacing-lg) var(--spacing-xl);  /* 1.5rem 2rem - plus d'espace */
                margin-bottom: var(--spacing-md);  /* 1rem */
                color: var(--white);
                font-weight: 700;  /* Plus gras */
                text-shadow: 0.125rem 0.125rem 0.25rem rgba(0, 0, 0, 0.3);  /* Ombre pour contraste */
                background: rgba(255, 255, 255, 0.1);  /* Fond légèrement transparent */
                border-radius: var(--border-radius);
            }
            
            .nav-submenu {
                position: static;
                opacity: 0;
                visibility: hidden;
                transform: none;
                box-shadow: none;
                background: transparent;
                padding: 0;
                margin: 0;
                margin-top: var(--spacing-md);  /* 1rem */
                max-height: 0;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            
            .nav-submenu.active {
                opacity: 1;
                visibility: visible;
                max-height: 20rem;  /* Hauteur suffisante pour le contenu */
            }
            
            .nav-submenu a {
                color: var(--white);
                padding: var(--spacing-md) 0;  /* 1rem 0 - plus d'espace vertical */
                font-size: var(--font-lg);  /* 1.125rem - plus grand */
                font-weight: 600;  /* Plus gras */
                text-shadow: 0.125rem 0.125rem 0.25rem rgba(0, 0, 0, 0.3);  /* Ombre pour contraste */
                border-left: none;
                display: block;
                background: rgba(255, 255, 255, 0.05);  /* Fond très léger */
                margin: var(--spacing-xs) 0;  /* Petit espace entre les liens */
                border-radius: var(--border-radius);
                transition: var(--transition);
            }
            
            /* Amélioration de la lisibilité du menu mobile */
            .nav a {
                font-size: var(--font-xl);  /* 1.25rem - plus grand sur mobile */
                font-weight: 700;  /* Plus gras sur mobile */
                text-shadow: 0.125rem 0.125rem 0.25rem rgba(0, 0, 0, 0.5);  /* Ombre plus forte */
                background: rgba(255, 255, 255, 0.1);  /* Fond plus visible */
                margin: var(--spacing-xs) 0;  /* Espace entre les liens */
                padding: var(--spacing-lg) var(--spacing-xl);  /* Plus d'espace */
            }
            
            .nav a:hover {
                background: rgba(255, 255, 255, 0.2);  /* Fond plus visible au survol */
                color: var(--accent-yellow);
                transform: translateX(0.25rem);  /* Animation horizontale */
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero .subtitle {
                font-size: 1.1rem;
            }
            
            .about-content {
                grid-template-columns: 1fr;
                gap: var(--spacing-xl);  /* 2rem */
            }
            
            .inscription-content {
                grid-template-columns: 1fr;
                gap: var(--spacing-xl);  /* 2rem */
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .contact-content {
                grid-template-columns: 1fr;
                gap: var(--spacing-xl);  /* 2rem */
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: var(--spacing-md);  /* 1rem */
            }
            
            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="images/logo.png" alt="Logo ASOD ACADEMIE">
                    <span>ASOD ACADEMIE</span>
                </div>
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
            </button>
            
                <nav class="nav" id="mainNav">
                    <ul class="nav-list">
                        <li><a href="#accueil">Accueil</a></li>
                        <li class="nav-group">
                            <span class="nav-group-title">L'Académie</span>
                            <ul class="nav-submenu">
                                <li><a href="#about">À Propos</a></li>
                                <li><a href="organigramme.php">Organigramme</a></li>
                                <li><a href="nos-equipes.php">Nos Équipes</a></li>
                                <!-- Lien vers la liste des joueurs retiré à la demande du président -->
                                <li><a href="entraineurs.php">Entraîneurs</a></li>
                        </ul>
                    </li>
                        <li class="nav-group">
                            <span class="nav-group-title">Activités</span>
                            <ul class="nav-submenu">
                                <li><a href="actualites.php">Actualités</a></li>
                                <li><a href="evenements.php">Événements</a></li>
                                <li><a href="formation.php">Formations</a></li>
                                <li><a href="galerie.php">Galerie</a></li>
                        </ul>
                    </li>
                        <li><a href="partenaires.php">Partenaires & Sponsors</a></li>
                        <li><a href="temoignages.php">Témoignages</a></li>
                        <li class="nav-group">
                            <span class="nav-group-title">Rejoindre</span>
                            <ul class="nav-submenu">
                                <li><a href="#inscription">Inscription</a></li>
                                <li><a href="guide-parents.php">Guide Parents</a></li>
                                <li><a href="guide-formateurs.php">Guide Formateurs</a></li>
                        </ul>
                    </li>
                        <li><a href="contact.php">Contact</a></li>
                        </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="accueil" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 data-aos="fade-up">ASOD ACADEMIE</h1>
                <p class="subtitle" data-aos="fade-up" data-aos-delay="200">
                        Association Sportive Oeil du Défi<br>
                    Formation sportive d'excellence depuis 2018
                </p>
                <a href="#stats" class="cta-button" data-aos="fade-up" data-aos-delay="400">
                    Découvrir l'Académie
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item" data-aos="fade-up">
                    <span class="stat-number"><?php echo $stats_membres; ?></span>
                    <span class="stat-label">Membres Actifs</span>
                        </div>
                <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                    <span class="stat-number"><?php echo $stats_equipes; ?></span>
                    <span class="stat-label">Équipes</span>
                    </div>
                <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                    <span class="stat-number"><?php echo $stats_entraineurs; ?></span>
                    <span class="stat-label">Entraîneurs</span>
                </div>
                <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                    <span class="stat-number"><?php echo $stats_formations; ?></span>
                    <span class="stat-label">Formations</span>
                        </div>
                <div class="stat-item" data-aos="fade-up" data-aos-delay="400">
                    <span class="stat-number"><?php echo $stats_annees; ?></span>
                    <span class="stat-label">Années d'Expérience</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">À Propos de l'ASOD ACADEMIE</h2>
            <div class="about-content">
                <div class="about-text" data-aos="fade-right">
                    <h3>Notre Mission</h3>
                    <p>
                        L'ASOD ACADEMIE est une association sportive dédiée à la formation et au développement 
                        des jeunes talents du football. Depuis 2018, nous nous engageons à offrir une formation 
                        de qualité dans un environnement propice à l'épanouissement personnel et sportif.
                    </p>
                    <p>
                        Notre approche pédagogique allie excellence technique, valeurs humaines et développement 
                        personnel pour former des joueurs complets, sur et en dehors du terrain.
                    </p>
                    <a href="organigramme.php" class="cta-button">En savoir plus</a>
            </div>
                <div class="about-image" data-aos="fade-left">
                    <img src="images/players/joueurs/joueurs_001_1759846686.jpg" alt="Équipe ASOD ACADEMIE" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inscription Section -->
    <section id="inscription" class="inscription">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Inscription à l'ASOD ACADEMIE</h2>
            <div class="inscription-content">
                <div class="inscription-info" data-aos="fade-right">
                    <h3>Rejoignez l'ASOD ACADEMIE</h3>
                    <p>Inscrivez-vous à notre académie de football et développez votre passion pour le sport dans un environnement professionnel et bienveillant.</p>
                    
                    <div class="inscription-benefits">
                        <div class="benefit-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Formation technique de qualité</span>
                            </div>
                        <div class="benefit-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Entraîneurs diplômés</span>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Équipements modernes</span>
                    </div>
                        <div class="benefit-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Ambiance familiale</span>
                        </div>
                    </div>
                </div>
                
                <div class="inscription-form" data-aos="fade-left">
                    <h3>Formulaire d'Inscription</h3>
                    <form id="inscriptionForm" action="php/inscription.php" method="POST">
                        <!-- Informations personnelles -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" required>
                </div>
            </div>
            
                        <div class="form-row">
                            <div class="form-group">
                                <label for="date_naissance">Date de naissance *</label>
                                <input type="date" id="date_naissance" name="date_naissance" required>
                                    </div>
                            <div class="form-group">
                                <label for="sexe">Sexe *</label>
                                <select id="sexe" name="sexe" required>
                                    <option value="">Sélectionnez votre sexe</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                </div>
            </div>
            
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required>
                </div>
                            <div class="form-group">
                                <label for="telephone">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" required>
                    </div>
                </div>
                
                        <div class="form-group">
                            <label for="adresse">Adresse *</label>
                            <input type="text" id="adresse" name="adresse" required>
                </div>
                
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pays">Pays *</label>
                                <select id="pays" name="pays" required onchange="updateVillesList(this.value)">
                                    <option value="">Sélectionnez un pays</option>
                                            <option value="Bénin">Bénin</option>
                                    <option value="Nigeria">Nigeria</option>
                                            <option value="Togo">Togo</option>
                                            <option value="Burkina Faso">Burkina Faso</option>
                                            <option value="Niger">Niger</option>
                                    <option value="France">France</option>
                                    <option value="Sénégal">Sénégal</option>
                                    <option value="Cameroun">Cameroun</option>
                                    <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                    <option value="Mali">Mali</option>
                                            <option value="Autre">Autre</option>
                                        </select>
                                    </div>
                            <div class="form-group">
                                <label for="ville">Ville *</label>
                                <select id="ville" name="ville" required>
                                    <option value="">Sélectionnez une ville</option>
                                </select>
                                    </div>
                                </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="equipe">Équipe *</label>
                                <select id="equipe" name="equipe" required readonly disabled style="background-color: #f8f9fa; cursor: not-allowed;">
                                    <option value="">Sélectionnez d'abord votre date de naissance et sexe</option>
                                    <option value="U11">U11 (5-10 ans)</option>
                                    <option value="U13">U13 (11-12 ans)</option>
                                    <option value="U15">U15 (13-14 ans)</option>
                                    <option value="U17">U17 (15-16 ans)</option>
                                    <option value="U20">U20 (18-20 ans)</option>
                                    <option value="Seniors">Seniors (21+ ans)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="genre">Genre d'équipe *</label>
                                <select id="genre" name="genre" required readonly disabled style="background-color: #f8f9fa; cursor: not-allowed;">
                                    <option value="">Sélectionnez d'abord votre sexe</option>
                                    <option value="Garçons">Garçons</option>
                                    <option value="Filles">Filles</option>
                                </select>
                            </div>
                                </div>
                        
                        <!-- Champs cachés pour les données optionnelles -->
                        <input type="hidden" name="lieu_naissance" value="">
                        <input type="hidden" name="code_postal" value="">
                        <input type="hidden" name="poste" value="">
                        <input type="hidden" name="niveau" value="">
                        <input type="hidden" name="motivation" value="">
                        <input type="hidden" name="nom_parent" value="">
                        <input type="hidden" name="prenom_parent" value="">
                        <input type="hidden" name="telephone_parent" value="">
                        <input type="hidden" name="email_parent" value="">
                        <input type="hidden" name="profession_parent" value="">
                        <input type="hidden" name="adresse_parent" value="">
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="assurance" name="assurance" required>
                                <span class="checkmark"></span>
                                J'ai une assurance personnelle couvrant les activités sportives *
                                    </label>
                                </div>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="reglement" name="reglement" required>
                                <span class="checkmark"></span>
                                J'accepte le règlement intérieur de l'ASOD ACADEMIE *
                                    </label>
                                </div>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="newsletter" name="newsletter">
                                <span class="checkmark"></span>
                                Je souhaite recevoir les actualités de l'ASOD ACADEMIE
                                    </label>
                                </div>
                                
                        <button type="submit" class="submit-btn">S'inscrire maintenant</button>
                            </form>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <h3>🏆 ASOD ACADEMIE</h3>
                <p>Association Sportive Oeil du Défi</p>
                <div class="footer-links">
                    <a href="actualites.php">Actualités</a>
                    <a href="organigramme.php">Organigramme</a>
                    <a href="nos-equipes.php">Nos Équipes</a>
                    <a href="entraineurs.php">Entraîneurs</a>
                    <a href="formation.php">Formations</a>
                    <a href="partenaires.php">Partenaires & Sponsors</a>
                    <a href="temoignages.php">Témoignages</a>
                    <a href="contact.php">Contact</a>
                    </div>
                <div class="footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> ASOD ACADEMIE. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="js/villes_par_pays.js"></script>
    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mainNav = document.getElementById('mainNav');
            
            if (mobileMenuToggle && mainNav) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileMenuToggle.classList.toggle('active');
                    mainNav.classList.toggle('active');
                });
                
                // Gestion des sous-menus mobiles
                const navGroupTitles = mainNav.querySelectorAll('.nav-group-title');
                navGroupTitles.forEach(title => {
                    title.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const submenu = this.nextElementSibling;
                        const isActive = submenu.classList.contains('active');
                        
                        // Fermer tous les autres sous-menus et retirer la classe active
                        mainNav.querySelectorAll('.nav-submenu').forEach(sub => {
                            sub.classList.remove('active');
                        });
                        mainNav.querySelectorAll('.nav-group-title').forEach(t => {
                            t.classList.remove('active');
                        });
                        
                        // Ouvrir/fermer le sous-menu cliqué
                        if (!isActive) {
                            submenu.classList.add('active');
                            this.classList.add('active');
                        }
                    });
                });
                
                // Close menu when clicking on a link
                const navLinks = mainNav.querySelectorAll('a:not(.nav-group-title)');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenuToggle.classList.remove('active');
                        mainNav.classList.remove('active');
                    });
                });
            }
            
            // Les équipes sont maintenant codées en dur dans le HTML
        });
        
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        
        // Fonction pour calculer l'âge
        function calculateAge(dateOfBirth) {
            const today = new Date();
            const birthDate = new Date(dateOfBirth);
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            return age;
        }
        
        // Fonction pour déterminer l'équipe automatiquement (correspondant à l'admin)
        function determineEquipe(dateNaissance, sexe) {
            const age = calculateAge(dateNaissance);
            
            if (age >= 5 && age <= 10) {
                return 'U11';
            } else if (age >= 11 && age <= 12) {
                return 'U13';
            } else if (age >= 13 && age <= 14) {
                return 'U15';
            } else if (age >= 15 && age <= 16) {
                return 'U17';
            } else if (age >= 18 && age <= 20) {
                return 'U20';
            } else if (age >= 21) {
                return 'Seniors';
            } else {
                return 'U11'; // Par défaut pour les très jeunes
            }
        }
        
        // Fonction pour déterminer le genre automatiquement
        function determineGenre(sexe) {
            return sexe === 'M' ? 'Garçons' : 'Filles';
        }
        
        // Gestion de la sélection automatique de l'équipe
        const dateNaissanceInput = document.getElementById('date_naissance');
        const sexeSelect = document.getElementById('sexe');
        const equipeSelect = document.getElementById('equipe');
        const genreSelect = document.getElementById('genre');
        
        function updateEquipeAndGenre() {
            const dateNaissance = dateNaissanceInput.value;
            const sexe = sexeSelect.value;
            
            if (dateNaissance && sexe) {
                const equipe = determineEquipe(dateNaissance, sexe);
                const genre = determineGenre(sexe);
                
                // Mettre à jour l'équipe
                equipeSelect.value = equipe;
                equipeSelect.disabled = false;
                equipeSelect.style.backgroundColor = '#fff';
                equipeSelect.style.cursor = 'default';
                
                // Mettre à jour le genre
                genreSelect.value = genre;
                genreSelect.disabled = false;
                genreSelect.style.backgroundColor = '#fff';
                genreSelect.style.cursor = 'default';
                
                console.log(`Âge calculé: ${calculateAge(dateNaissance)} ans, Équipe: ${equipe}, Genre: ${genre}`);
            } else {
                // Réinitialiser les champs si les données ne sont pas complètes
                equipeSelect.value = '';
                equipeSelect.disabled = true;
                equipeSelect.style.backgroundColor = '#f8f9fa';
                equipeSelect.style.cursor = 'not-allowed';
                
                genreSelect.value = '';
                genreSelect.disabled = true;
                genreSelect.style.backgroundColor = '#f8f9fa';
                genreSelect.style.cursor = 'not-allowed';
            }
        }
        
        // Écouter les changements de date de naissance et sexe
        if (dateNaissanceInput && sexeSelect) {
            dateNaissanceInput.addEventListener('change', updateEquipeAndGenre);
            sexeSelect.addEventListener('change', updateEquipeAndGenre);
        }
        
        // Gestion du formulaire d'inscription
        const inscriptionForm = document.getElementById('inscriptionForm');
        if (inscriptionForm) {
            inscriptionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('.submit-btn');
                const originalText = submitBtn.textContent;
                
                submitBtn.textContent = 'Inscription en cours...';
                submitBtn.disabled = true;
                
                const formData = new FormData(this);
                
                fetch('php/inscription_simple.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Vérifier si la réponse est OK
                    if (!response.ok) {
                        throw new Error('Erreur réseau: ' + response.status);
                    }
                    
                    // Vérifier le type de contenu
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            console.error('Réponse non-JSON reçue:', text);
                            throw new Error('Réponse serveur invalide');
                        });
                    }
                    
                    return response.json();
                })
                .then(data => {
                    try {
                        if (data && data.success) {
                            // Afficher le message de succès en haut du formulaire
                            if (typeof showFormMessage === 'function') {
                                showFormMessage('success', 'Inscription réussie ! Votre demande a été enregistrée avec succès. Nous vous contacterons bientôt.');
                            } else {
                                console.error('showFormMessage function not found');
                            }
                            
                            // Réinitialiser le formulaire
                            this.reset();
                            
                            // Re-désactiver équipe/genre et remettre l'état initial
                            const equipeSelect = document.getElementById('equipe');
                            const genreSelect = document.getElementById('genre');
                            if (equipeSelect) {
                                equipeSelect.value = '';
                                equipeSelect.disabled = true;
                                equipeSelect.style.backgroundColor = '#f8f9fa';
                                equipeSelect.style.cursor = 'not-allowed';
                            }
                            if (genreSelect) {
                                genreSelect.value = '';
                                genreSelect.disabled = true;
                                genreSelect.style.backgroundColor = '#f8f9fa';
                                genreSelect.style.cursor = 'not-allowed';
                            }
                        } else {
                            if (typeof showFormMessage === 'function') {
                                showFormMessage('error', 'Erreur: ' + (data.error || 'Une erreur est survenue'));
                            } else {
                                console.error('Erreur: ' + (data.error || 'Une erreur est survenue'));
                            }
                        }
                    } catch (error) {
                        console.error('Erreur dans le traitement de la réponse:', error);
                        if (typeof showFormMessage === 'function') {
                            showFormMessage('error', 'Erreur lors du traitement de la réponse');
                        }
                    }
                })
                .catch(error => {
                    showFormMessage('error', 'Erreur lors de l\'inscription. Veuillez réessayer.');
                    console.error('Error:', error);
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
            });
        }
        
        // Fonction pour afficher les messages de formulaire
        function showFormMessage(type, message) {
            // Supprimer les anciens messages
            const existingMessages = document.querySelectorAll('.form-message');
            existingMessages.forEach(msg => msg.remove());
            
            // Créer le nouveau message
            const messageDiv = document.createElement('div');
            messageDiv.className = 'form-message';
            messageDiv.style.cssText = `
                padding: 16px 20px;
                margin-bottom: 20px;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 500;
                line-height: 1.5;
                animation: slideDown 0.3s ease-out;
                position: relative;
                ${type === 'success' ? `
                    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                    border: 1px solid #b8dacc;
                    color: #155724;
                ` : `
                    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                    border: 1px solid #f1b0b7;
                    color: #721c24;
                `}
            `;
            
            // Icône selon le type
            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
            
            messageDiv.innerHTML = `
                <i class="${icon}" style="margin-right: 8px;"></i>
                ${message}
                <button type="button" onclick="this.parentElement.remove()" style="
                    position: absolute;
                    top: 8px;
                    right: 12px;
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    color: inherit;
                    opacity: 0.7;
                ">&times;</button>
            `;
            
            // Insérer le message en haut du formulaire
            const form = document.getElementById('inscriptionForm');
            if (form) {
                form.parentNode.insertBefore(messageDiv, form);
                
                // Faire défiler vers le message
                messageDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Auto-suppression après 8 secondes pour les messages de succès
                if (type === 'success') {
                    setTimeout(() => {
                        if (messageDiv.parentNode) {
                            messageDiv.style.animation = 'slideUp 0.3s ease-out';
                            setTimeout(() => messageDiv.remove(), 300);
                        }
                    }, 8000);
                }
            }
        }
        
        // Gestion d'erreur globale pour éviter les alertes du navigateur
        window.addEventListener('error', function(e) {
            console.error('Erreur JavaScript:', e.error);
            // Empêcher l'affichage de l'alerte du navigateur
            e.preventDefault();
            return false;
        });
        
        // Gestion des erreurs de promesses non capturées
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Promesse rejetée non gérée:', e.reason);
            // Empêcher l'affichage de l'alerte du navigateur
            e.preventDefault();
            return false;
        });
    </script>
    
    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
        
        .form-message {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-message:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.2s ease;
        }
    </style>
</body>
</html>