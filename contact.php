<?php
// Page Contact dédiée - ASOD ACADEMIE
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les informations de contact actives
    $stmt = $pdo->prepare("
        SELECT 
            type_contact, libelle, valeur, description, icone, couleur, ordre_affichage
        FROM contact_info 
        WHERE actif = 1 
        ORDER BY ordre_affichage ASC, type_contact ASC
    ");
    $stmt->execute();
    $contact_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $contact_infos = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - ASOD ACADEMIE</title>
    <meta name="description" content="Contactez ASOD ACADEMIE - Association Sportive Oeil du Défi. Adresse, email, téléphone et réseaux sociaux.">
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
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .contact-item:hover {
            transform: translateY(-5px);
        }
        
        .contact-item i {
            color: var(--asod-primary);
            font-size: 1.5rem;
            margin-top: 0.2rem;
            width: 30px;
        }
        
        .contact-item strong {
            color: var(--asod-dark);
            display: block;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .contact-item p {
            color: #6b7280;
            margin: 0;
        }
        
        .contact-item a {
            color: var(--asod-primary);
            text-decoration: none;
        }
        
        .contact-item a:hover {
            text-decoration: underline;
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
                        <a class="nav-link" href="partenaires.php">Partenaires & Sponsors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="temoignages.php">Témoignages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact.php">Contact</a>
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
                    <h1 class="display-4 fw-bold" data-aos="fade-up">Contactez-nous</h1>
                    <p class="lead" data-aos="fade-up" data-aos-delay="100">
                        Nous sommes là pour répondre à toutes vos questions sur ASOD ACADEMIE
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-5">
        <div class="container">
            
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="mb-4" data-aos="fade-up">Informations de Contact</h2>
                    
                    <?php if (!empty($contact_infos)): ?>
                        <?php foreach ($contact_infos as $info): ?>
                            <?php 
                            // Gérer les valeurs NULL et vides
                            $libelle = $info['libelle'] ?? ucfirst($info['type_contact']);
                            
                            // Icônes par défaut selon le type de contact
                            $icones_defaut = [
                                'whatsapp' => 'fab fa-whatsapp',
                                'email' => 'fas fa-envelope',
                                'telephone' => 'fas fa-phone',
                                'adresse' => 'fas fa-map-marker-alt',
                                'site_web' => 'fas fa-globe',
                                'facebook' => 'fab fa-facebook',
                                'instagram' => 'fab fa-instagram',
                                'twitter' => 'fab fa-twitter',
                                'youtube' => 'fab fa-youtube',
                                'linkedin' => 'fab fa-linkedin',
                                'tiktok' => 'fab fa-tiktok',
                                'telegram' => 'fab fa-telegram'
                            ];
                            
                            $icone = $info['icone'] ?? ($icones_defaut[$info['type_contact']] ?? 'fas fa-info-circle');
                            $valeur = $info['valeur'] ?? '';
                            $description = $info['description'] ?? '';
                            
                            // Ne pas afficher si la valeur est vide
                            if (empty($valeur)) continue;
                            ?>
                            <div class="contact-item" data-aos="fade-up" data-aos-delay="100">
                                <i class="<?= htmlspecialchars($icone) ?>"></i>
                                <div>
                                    <strong><?= htmlspecialchars($libelle) ?></strong>
                                    <?php if ($info['type_contact'] === 'site_web' || $info['type_contact'] === 'facebook' || $info['type_contact'] === 'whatsapp'): ?>
                                        <p><a href="<?= htmlspecialchars($valeur) ?>" target="_blank"><?= htmlspecialchars($valeur) ?></a></p>
                                    <?php else: ?>
                                        <p><?= htmlspecialchars($valeur) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($description)): ?>
                                        <small class="text-muted"><?= htmlspecialchars($description) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center">
                            <p>Aucune information de contact disponible pour le moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-6">
                    <h2 class="mb-4" data-aos="fade-up">Envoyez-nous un message</h2>
                    <form id="contactForm" action="php/contact.php" method="POST" data-aos="fade-up" data-aos-delay="200">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom *</label>
                            <input type="text" id="nom" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="sujet" class="form-label">Sujet *</label>
                            <input type="text" id="sujet" name="sujet" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Envoyer le message</button>
                    </form>
                </div>
            </div>
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
                    <p><i class="fas fa-envelope me-2"></i> asodacedemie@gmail.com</p>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Gbégamey Mifongou, Maison DAGBETO</p>
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
        
        // Gestion du formulaire de contact
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                // Afficher l'état de chargement
                submitBtn.textContent = 'Envoi en cours...';
                submitBtn.disabled = true;
                this.classList.add('loading');
                
                try {
                    const formData = new FormData(this);
                    
                    const response = await fetch('php/contact.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showMessage('Message envoyé avec succès ! Nous vous répondrons bientôt.', 'success');
                        this.reset();
                    } else {
                        showMessage(result.error || 'Erreur lors de l\'envoi du message. Veuillez réessayer.', 'error');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showMessage('Erreur de connexion. Veuillez réessayer plus tard.', 'error');
                } finally {
                    // Restaurer l'état du bouton
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    this.classList.remove('loading');
                }
            });
        }
        
        // Fonction pour afficher les messages
        function showMessage(message, type) {
            // Supprimer les messages existants
            const existingMessages = document.querySelectorAll('.message');
            existingMessages.forEach(msg => msg.remove());
            
            // Créer le nouveau message
            const messageDiv = document.createElement('div');
            messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show message`;
            messageDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insérer le message au début du formulaire
            if (contactForm) {
                contactForm.insertBefore(messageDiv, contactForm.firstChild);
                
                // Supprimer le message après 5 secondes
                setTimeout(() => {
                    messageDiv.remove();
                }, 5000);
            }
        }
        
        // Nettoyer l'URL après affichage des messages
        document.addEventListener('DOMContentLoaded', function() {
            const url = new URL(window.location);
            if (url.searchParams.has('success') || url.searchParams.has('error')) {
                setTimeout(function() {
                    url.searchParams.delete('success');
                    url.searchParams.delete('error');
                    window.history.replaceState({}, '', url);
                }, 5000); // Nettoyer après 5 secondes
            }
        });
    </script>
</body>
</html>
