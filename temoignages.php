<?php
// Page des Témoignages - ASOD ACADEMIE
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les témoignages publiés
    $stmt = $pdo->prepare("
        SELECT 
            id, nom, prenom, fonction, entreprise, photo, 
            temoignage, note, ordre_affichage, date_creation
        FROM temoignages 
        WHERE statut = 'publie' 
        ORDER BY ordre_affichage ASC, date_creation DESC
    ");
    $stmt->execute();
    $temoignages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer les statistiques
    $total = count($temoignages);
    $moyenne = 0;
    if ($total > 0) {
        $somme_notes = array_sum(array_column($temoignages, 'note'));
        $moyenne = round($somme_notes / $total, 1);
    }
    
} catch (Exception $e) {
    $temoignages = [];
    $total = 0;
    $moyenne = 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Témoignages - ASOD ACADEMIE</title>
    <meta name="description" content="Découvrez les témoignages de nos membres, parents et partenaires sur leur expérience avec ASOD ACADEMIE.">
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
        
        .stats-section {
            background: var(--asod-light);
            padding: 3rem 0;
        }
        
        .stat-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--asod-primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6b7280;
            font-weight: 500;
        }
        
        .temoignage-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #e5e7eb;
            position: relative;
        }
        
        .temoignage-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .temoignage-card::before {
            content: '"';
            position: absolute;
            top: -10px;
            left: 20px;
            font-size: 4rem;
            color: var(--asod-secondary);
            font-family: serif;
            line-height: 1;
        }
        
        .temoignage-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1.5rem;
            display: block;
            border: 4px solid var(--asod-secondary);
        }
        
        .temoignage-text {
            color: #4b5563;
            margin-bottom: 1.5rem;
            font-style: italic;
            line-height: 1.7;
        }
        
        .temoignage-author {
            text-align: center;
        }
        
        .author-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--asod-primary);
            margin-bottom: 0.25rem;
        }
        
        .author-title {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .stars {
            color: var(--asod-secondary);
            font-size: 1.2rem;
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
        
        /* Styles pour le formulaire de témoignage */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .star-input {
            display: none;
        }
        
        .star-label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .star-label:hover,
        .star-label:hover ~ .star-label {
            color: var(--asod-secondary);
        }
        
        .star-input:checked ~ .star-label,
        .star-input:checked ~ .star-label ~ .star-label {
            color: var(--asod-secondary);
        }
        
        .form-control:focus {
            border-color: var(--asod-primary);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.25);
        }
        
        #formMessage {
            min-height: 50px;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 2rem !important;
            }
            
            .star-label {
                font-size: 1.5rem;
            }
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
                        <a class="nav-link" href="partenaires.php">Partenaires & Sponsors</a>
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
                    <h1 class="section-title" data-aos="fade-up">Témoignages</h1>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Découvrez les témoignages de nos membres, parents et partenaires sur leur expérience avec ASOD ACADEMIE
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques -->
    <?php if ($total > 0): ?>
    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total ?></div>
                        <div class="stat-label">Témoignages</div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-number"><?= $moyenne ?></div>
                        <div class="stat-label">Note Moyenne</div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Formulaire de Témoignage -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0" data-aos="fade-up">
                        <div class="card-body p-5">
                            <h2 class="text-center mb-4" style="color: var(--asod-primary);">
                                <i class="fas fa-comment-dots me-2"></i>
                                Partagez votre expérience
                            </h2>
                            <p class="text-center text-muted mb-4">
                                Votre avis compte ! Partagez votre expérience avec ASOD ACADEMIE et aidez d'autres personnes à nous découvrir.
                            </p>
                            
                            <form id="temoignageForm" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="prenom" class="form-label">Prénom *</label>
                                        <input type="text" class="form-control" id="prenom" name="prenom" required 
                                               minlength="2" maxlength="50" placeholder="Votre prénom">
                                        <div class="invalid-feedback">
                                            Le prénom doit contenir entre 2 et 50 caractères.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nom" class="form-label">Nom *</label>
                                        <input type="text" class="form-control" id="nom" name="nom" required 
                                               minlength="2" maxlength="50" placeholder="Votre nom">
                                        <div class="invalid-feedback">
                                            Le nom doit contenir entre 2 et 50 caractères.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="temoignage" class="form-label">Votre témoignage *</label>
                                    <textarea class="form-control" id="temoignage" name="temoignage" rows="5" required 
                                              minlength="10" maxlength="1000" 
                                              placeholder="Partagez votre expérience avec ASOD ACADEMIE..."></textarea>
                                    <div class="form-text">
                                        <span id="charCount">0</span> / 1000 caractères
                                    </div>
                                    <div class="invalid-feedback">
                                        Le témoignage doit contenir entre 10 et 1000 caractères.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Note (optionnelle)</label>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <span class="text-muted me-2">1</span>
                                        <div class="star-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <input type="radio" name="note" id="star<?= $i ?>" value="<?= $i ?>" 
                                                   class="star-input" <?= $i === 5 ? 'checked' : '' ?>>
                                            <label for="star<?= $i ?>" class="star-label">
                                                <i class="fas fa-star"></i>
                                            </label>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-muted ms-2">5</span>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Envoyer mon témoignage
                                    </button>
                                </div>
                                
                                <div id="formMessage" class="mt-3"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Témoignages Section -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($temoignages)): ?>
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="text-center mb-5" data-aos="fade-up">Ce qu'ils disent de nous</h2>
                </div>
            </div>
            <div class="row g-4">
                <?php foreach ($temoignages as $index => $temoignage): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="temoignage-card">
                        <?php if (!empty($temoignage['photo'])): ?>
                        <img src="<?= htmlspecialchars($temoignage['photo']) ?>" 
                             alt="<?= htmlspecialchars($temoignage['nom'] . ' ' . $temoignage['prenom']) ?>" 
                             class="temoignage-photo"
                             onerror="this.style.display='none'">
                        <?php else: ?>
                        <div class="temoignage-photo" style="background: var(--asod-light); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 2rem; color: var(--asod-primary);"></i>
                        </div>
                        <?php endif; ?>
                        
                        <p class="temoignage-text"><?= htmlspecialchars($temoignage['temoignage']) ?></p>
                        
                        <div class="temoignage-author">
                            <div class="author-name"><?= htmlspecialchars($temoignage['prenom'] . ' ' . $temoignage['nom']) ?></div>
                            
                            <?php if (!empty($temoignage['fonction']) || !empty($temoignage['entreprise'])): ?>
                            <div class="author-title">
                                <?php if (!empty($temoignage['fonction'])): ?>
                                    <?= htmlspecialchars($temoignage['fonction']) ?>
                                <?php endif; ?>
                                <?php if (!empty($temoignage['fonction']) && !empty($temoignage['entreprise'])): ?>
                                    chez
                                <?php endif; ?>
                                <?php if (!empty($temoignage['entreprise'])): ?>
                                    <?= htmlspecialchars($temoignage['entreprise']) ?>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($temoignage['note'])): ?>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= $temoignage['note'] ? '' : '-o' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-comments"></i>
                <h3>Aucun témoignage pour le moment</h3>
                <p>Nous travaillons à collecter les témoignages de nos membres. Revenez bientôt pour découvrir leurs expériences !</p>
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
        
        // Gestion du formulaire de témoignage
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('temoignageForm');
            const textarea = document.getElementById('temoignage');
            const charCount = document.getElementById('charCount');
            const submitBtn = document.getElementById('submitBtn');
            const formMessage = document.getElementById('formMessage');
            
            // Compteur de caractères
            if (textarea && charCount) {
                textarea.addEventListener('input', function() {
                    const length = this.value.length;
                    charCount.textContent = length;
                    
                    if (length > 1000) {
                        charCount.style.color = '#dc3545';
                    } else if (length > 800) {
                        charCount.style.color = '#ffc107';
                    } else {
                        charCount.style.color = '#6c757d';
                    }
                });
            }
            
            // Soumission du formulaire
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Validation HTML5
                    if (!form.checkValidity()) {
                        form.classList.add('was-validated');
                        return;
                    }
                    
                    // Désactiver le bouton pendant l'envoi
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
                    formMessage.innerHTML = '';
                    
                    // Récupérer la note sélectionnée
                    const noteInput = form.querySelector('input[name="note"]:checked');
                    const note = noteInput ? noteInput.value : null;
                    
                    // Préparer les données
                    const formData = new FormData();
                    formData.append('nom', document.getElementById('nom').value.trim());
                    formData.append('prenom', document.getElementById('prenom').value.trim());
                    formData.append('temoignage', document.getElementById('temoignage').value.trim());
                    if (note) {
                        formData.append('note', note);
                    }
                    
                    try {
                        const response = await fetch('php/temoignage.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            formMessage.innerHTML = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    ${data.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                            
                            // Réinitialiser le formulaire
                            form.reset();
                            form.classList.remove('was-validated');
                            charCount.textContent = '0';
                            
                            // Réinitialiser les étoiles à 5
                            document.getElementById('star5').checked = true;
                            
                            // Scroll vers le message
                            formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            
                            // Recharger la page après 3 secondes pour voir le nouveau témoignage (s'il est publié)
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        } else {
                            formMessage.innerHTML = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ${data.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                            formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    } catch (error) {
                        formMessage.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Une erreur est survenue lors de l'envoi. Veuillez réessayer plus tard.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } finally {
                        // Réactiver le bouton
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer mon témoignage';
                    }
                });
            }
        });
    </script>
</body>
</html>
