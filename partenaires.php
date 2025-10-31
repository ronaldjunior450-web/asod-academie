<?php
// Page des Partenaires - ASOD ACADEMIE
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les partenaires actifs
    $stmt = $pdo->prepare("
        SELECT 
            id, nom, description, logo, site_web, 
            contact_email, contact_telephone, adresse,
            type_partenaire, ordre_affichage
        FROM partenaires 
        WHERE statut = 'actif' 
        ORDER BY ordre_affichage ASC, nom ASC
    ");
    $stmt->execute();
    $partenaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les sponsors actifs
    $stmt = $pdo->prepare("
        SELECT 
            id, nom, description, logo, site_web, 
            contact_email, contact_telephone,
            type_sponsoring as type_partenaire, ordre_affichage
        FROM sponsors 
        WHERE statut = 'actif' 
        ORDER BY ordre_affichage ASC, nom ASC
    ");
    $stmt->execute();
    $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $partenaires = [];
    $sponsors = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partenaires & Sponsors - ASOD ACADEMIE</title>
    <meta name="description" content="Découvrez nos partenaires et sponsors qui nous accompagnent dans notre mission de formation sportive d'excellence.">
    <link rel="icon" type="image/png" href="images/logo.png">
    
    <!-- CDN pour les performances -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --asod-primary: #1e3a8a;
            --asod-secondary: #f59e0b;
            --asod-accent: #10b981;
            --asod-dark: #1f2937;
            --asod-light: #f8fafc;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--asod-dark);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--asod-primary) 0%, var(--asod-secondary) 100%);
            color: white;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,100 1000,0"/></svg>');
            background-size: cover;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }
        
        .partenaire-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #e5e7eb;
        }
        
        .partenaire-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .partenaire-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin: 0 auto 1.5rem;
            display: block;
            border-radius: 10px;
            background: #f8fafc;
            padding: 15px;
        }
        
        .partenaire-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--asod-primary);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .partenaire-description {
            color: #6b7280;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.95rem;
        }
        
        .partenaire-info {
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .partenaire-info i {
            color: var(--asod-secondary);
            margin-right: 0.5rem;
        }
        
        .type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .type-sponsor {
            background: #fef3c7;
            color: #92400e;
        }
        
        .type-partenaire_officiel {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .type-partenaire_media {
            background: #f3e8ff;
            color: #7c3aed;
        }
        
        .type-partenaire_technique {
            background: #d1fae5;
            color: #065f46;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        
        .btn-primary {
            background: var(--asod-primary);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }
        
        .navbar {
            background: rgba(255,255,255,0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--asod-primary) !important;
        }
        
        .nav-link {
            color: var(--asod-dark) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--asod-primary) !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="ASOD ACADEMIE" height="40" class="me-2">
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
                        <a class="nav-link" href="nos-equipes.php">Nos Équipes</a>
                    </li>
                    <li class="nav-item">
                        <!-- Lien vers Nos Joueurs retiré -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="actualites.php">Actualités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formation.php">Formations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="galerie.php">Galerie</a>
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
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="section-title" data-aos="fade-up">Partenaires & Sponsors</h1>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Découvrez nos partenaires et sponsors qui nous accompagnent dans notre mission de formation sportive d'excellence
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Partenaires Section -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($partenaires)): ?>
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="text-center mb-5" data-aos="fade-up">Nos Partenaires Officiels</h2>
                </div>
            </div>
            <div class="row g-4">
                <?php foreach ($partenaires as $index => $partenaire): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="partenaire-card">
                        <?php if (!empty($partenaire['logo'])): ?>
                        <img src="<?= htmlspecialchars($partenaire['logo']) ?>" 
                             alt="<?= htmlspecialchars($partenaire['nom']) ?>" 
                             class="partenaire-logo"
                             onerror="this.style.display='none'">
                        <?php endif; ?>
                        
                        <h3 class="partenaire-name"><?= htmlspecialchars($partenaire['nom']) ?></h3>
                        
                        <?php if (!empty($partenaire['type_partenaire'])): ?>
                        <div class="text-center">
                            <span class="type-badge type-<?= $partenaire['type_partenaire'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $partenaire['type_partenaire'])) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($partenaire['description'])): ?>
                        <p class="partenaire-description"><?= htmlspecialchars($partenaire['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="partenaire-info">
                            <?php if (!empty($partenaire['site_web'])): ?>
                            <p><i class="fas fa-globe"></i> 
                                <a href="<?= htmlspecialchars($partenaire['site_web']) ?>" target="_blank" class="text-decoration-none">
                                    Site Web
                                </a>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($partenaire['contact_email'])): ?>
                            <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($partenaire['contact_email']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($partenaire['contact_telephone'])): ?>
                            <p><i class="fas fa-phone"></i> <?= htmlspecialchars($partenaire['contact_telephone']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($partenaire['adresse'])): ?>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($partenaire['adresse']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($sponsors)): ?>
            <div class="row mb-5 mt-5">
                <div class="col-12">
                    <h2 class="text-center mb-5" data-aos="fade-up">Nos Sponsors</h2>
                </div>
            </div>
            <div class="row g-4">
                <?php foreach ($sponsors as $index => $sponsor): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="partenaire-card">
                        <?php if (!empty($sponsor['logo'])): ?>
                        <img src="<?= htmlspecialchars($sponsor['logo']) ?>" 
                             alt="<?= htmlspecialchars($sponsor['nom']) ?>" 
                             class="partenaire-logo"
                             onerror="this.style.display='none'">
                        <?php endif; ?>
                        
                        <h3 class="partenaire-name"><?= htmlspecialchars($sponsor['nom']) ?></h3>
                        
                        <?php if (!empty($sponsor['type_partenaire'])): ?>
                        <div class="text-center">
                            <span class="type-badge type-<?= $sponsor['type_partenaire'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $sponsor['type_partenaire'])) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($sponsor['description'])): ?>
                        <p class="partenaire-description"><?= htmlspecialchars($sponsor['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="partenaire-info">
                            <?php if (!empty($sponsor['site_web'])): ?>
                            <p><i class="fas fa-globe"></i> 
                                <a href="<?= htmlspecialchars($sponsor['site_web']) ?>" target="_blank" class="text-decoration-none">
                                    Site Web
                                </a>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($sponsor['contact_email'])): ?>
                            <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($sponsor['contact_email']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($sponsor['contact_telephone'])): ?>
                            <p><i class="fas fa-phone"></i> <?= htmlspecialchars($sponsor['contact_telephone']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if (empty($partenaires) && empty($sponsors)): ?>
            <div class="empty-state">
                <i class="fas fa-handshake"></i>
                <h3>Aucun partenaire pour le moment</h3>
                <p>Nous travaillons actuellement sur nos partenariats. Revenez bientôt pour découvrir nos partenaires officiels !</p>
                <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>ASOD ACADEMIE</h5>
                    <p>Association Sportive Oeil du Défi - Formation sportive d'excellence depuis 2018.</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Liens Rapides</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">Accueil</a></li>
                        <li><a href="index.php#about" class="text-light text-decoration-none">À Propos</a></li>
                        <li><a href="nos-equipes.php" class="text-light text-decoration-none">Nos Équipes</a></li>
                        <li><a href="formation.php" class="text-light text-decoration-none">Formations</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Contact</h5>
                    <p><i class="fas fa-envelope me-2"></i> contact@asodacademie.com</p>
                    <p><i class="fas fa-phone me-2"></i> +237 XXX XX XX XX</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 ASOD ACADEMIE. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>
