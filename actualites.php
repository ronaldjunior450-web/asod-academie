<?php
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer toutes les actualités publiées
    $stmt = $pdo->query("SELECT * FROM actualites WHERE statut = 'publie' ORDER BY date_publication DESC");
    $actualites = $stmt->fetchAll();
    
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
    <title>Actualités - ASOD ACADEMIE</title>
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
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .card-img-top {
            height: 250px;
            object-fit: cover;
        }
        
        .section-title {
            color: #fff;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .container {
            padding-top: 2rem;
        }
        
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #6610f2);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="index.html">
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
                        <a class="nav-link active" href="actualites.php">Actualités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formation.php">Formation</a>
                    </li>
                    <li class="nav-item">
                        <!-- Lien vers Nos Joueurs retiré -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container" style="margin-top: 100px;">
        <h1 class="section-title" data-aos="fade-up">
            <i class="fas fa-newspaper me-3"></i>Actualités ASOD ACADEMIE
        </h1>
        
        <?php if (!empty($actualites)): ?>
            <div class="row">
                <?php foreach ($actualites as $actualite): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100">
                            <?php if (!empty($actualite['image'])): ?>
                                <?php 
                                $images = explode(',', $actualite['image']);
                                $firstImage = trim($images[0]);
                                ?>
                                <img src="<?php echo htmlspecialchars($firstImage); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($actualite['titre']); ?>"
                                     style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fas fa-newspaper fa-3x text-white"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-primary mb-2">
                                        <?php echo ucfirst($actualite['statut']); ?>
                                    </span>
                                    <span class="text-muted small">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?php echo $actualite['date_publication'] ? date('d/m/Y', strtotime($actualite['date_publication'])) : date('d/m/Y', strtotime($actualite['date_creation'])); ?>
                                    </span>
                                </div>
                                
                                <h5 class="card-title text-primary mb-3">
                                    <?php echo htmlspecialchars($actualite['titre']); ?>
                                </h5>
                                
                                <p class="card-text flex-grow-1 text-muted">
                                    <?php 
                                    // Afficher le résumé s'il existe, sinon tronquer le contenu
                                    $text = !empty($actualite['resume']) ? $actualite['resume'] : strip_tags($actualite['contenu']);
                                    echo htmlspecialchars(substr($text, 0, 150)) . (strlen($text) > 150 ? '...' : ''); 
                                    ?>
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars($actualite['auteur'] ?? 'ASOD ACADEMIE'); ?>
                                        </small>
                                        <a href="news_detail.php?id=<?php echo $actualite['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-arrow-right me-1"></i>Lire plus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-5x text-white mb-4" data-aos="fade-up"></i>
                <h3 class="text-white mb-3" data-aos="fade-up" data-aos-delay="100">
                    Aucune actualité disponible
                </h3>
                <p class="text-white-50" data-aos="fade-up" data-aos-delay="200">
                    Les actualités seront bientôt publiées.
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>


