<?php
/**
 * Page de détail d'une actualité
 */

require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        header('Location: index.php');
        exit;
    }
    
    // Récupérer l'actualité
    $stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = ? AND statut = 'publie'");
    $stmt->execute([$id]);
    $actualite = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$actualite) {
        header('Location: index.php');
        exit;
    }
    
} catch (Exception $e) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($actualite['titre']); ?> - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        
        .article-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 100px auto 50px;
            max-width: 900px;
        }
        
        .article-header {
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .article-meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .article-meta span {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .article-content {
            padding: 40px;
        }
        
        .article-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .article-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }
        
        .article-text h1, .article-text h2, .article-text h3 {
            color: #0d6efd;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .article-text p {
            margin-bottom: 20px;
        }
        
        .article-text ul, .article-text ol {
            margin-bottom: 20px;
            padding-left: 30px;
        }
        
        .article-text li {
            margin-bottom: 8px;
        }
        
        .back-button {
            position: fixed;
            top: 120px;
            left: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50px;
            padding: 15px 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 30px 0;
        }
        
        .image-gallery img {
            flex: 1;
            min-width: 200px;
            max-width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .article-title {
                font-size: 2rem;
            }
            
            .article-header {
                padding: 30px 20px;
            }
            
            .article-content {
                padding: 30px 20px;
            }
            
            .back-button {
                top: 100px;
                left: 10px;
                padding: 12px 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-futbol text-primary me-2"></i>
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
                        <a class="nav-link" href="actualites.php">Actualités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formation.php">Formation</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bouton retour -->
    <a href="actualites.php" class="back-button">
        <i class="fas fa-arrow-left me-2"></i>Retour
    </a>

    <!-- Article -->
    <div class="container">
        <div class="article-container">
            <div class="article-header">
                <div class="article-meta">
                    <span><i class="fas fa-calendar-alt me-2"></i><?php echo date('d/m/Y', strtotime($actualite['date_publication'])); ?></span>
                    <span><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($actualite['auteur'] ?? 'ASOD ACADEMIE'); ?></span>
                    <span><i class="fas fa-tag me-2"></i><?php echo ucfirst($actualite['statut']); ?></span>
                </div>
                <h1 class="article-title"><?php echo htmlspecialchars($actualite['titre']); ?></h1>
            </div>
            
            <div class="article-content">
                <?php if (!empty($actualite['image'])): ?>
                    <?php 
                    $images = explode(',', $actualite['image']);
                    if (count($images) == 1): 
                        // Une seule image
                    ?>
                        <img src="<?php echo htmlspecialchars(trim($images[0])); ?>" 
                             alt="<?php echo htmlspecialchars($actualite['titre']); ?>" 
                             class="article-image">
                    <?php else: 
                        // Plusieurs images - galerie
                    ?>
                        <div class="image-gallery">
                            <?php foreach ($images as $image): 
                                if (trim($image)):
                            ?>
                                <img src="<?php echo htmlspecialchars(trim($image)); ?>" 
                                     alt="<?php echo htmlspecialchars($actualite['titre']); ?>">
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (!empty($actualite['resume'])): ?>
                    <div class="alert alert-info" style="border-radius: 15px; border: none; background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);">
                        <h5><i class="fas fa-info-circle me-2"></i>Résumé</h5>
                        <p class="mb-0"><?php echo htmlspecialchars($actualite['resume']); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="article-text">
                    <?php echo $actualite['contenu']; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>