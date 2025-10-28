<?php
require_once 'php/config.php';

// Récupérer les formations actives avec statistiques
try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->query("
        SELECT f.*, 
               e.nom as formateur_nom, 
               e.prenom as formateur_prenom,
               COUNT(DISTINCT fs.id) as nombre_sessions,
               COUNT(DISTINCT fi.id) as nombre_inscrits
        FROM formations f 
        LEFT JOIN entraineurs e ON f.formateur_id = e.id 
        LEFT JOIN formation_sessions fs ON f.id = fs.formation_id AND fs.date_session >= CURDATE()
        LEFT JOIN formation_inscriptions fi ON f.id = fi.formation_id AND fi.statut IN ('inscrit', 'present')
        WHERE f.statut = 'actif'
        GROUP BY f.id
        ORDER BY f.cout_formation DESC, f.type_formation, f.age_min
    ");
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Statistiques globales
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_formations,
            COUNT(DISTINCT f.type_formation) as types_formation,
            MIN(f.cout_formation) as prix_min,
            MAX(f.cout_formation) as prix_max,
            AVG(f.cout_formation) as prix_moyen,
            SUM(f.places_disponibles) as places_totales
        FROM formations f 
        WHERE f.statut = 'actif' AND f.cout_formation > 1000
    ");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $formations = [];
    $stats = ['total_formations' => 0, 'types_formation' => 0, 'prix_min' => 0, 'prix_max' => 0, 'prix_moyen' => 0, 'places_totales' => 0];
    error_log("Erreur formations publiques: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formations Professionnelles - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --gold-color: #ffd700;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-section .lead {
            font-size: 1.5rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto 30px;
            font-weight: 300;
        }

        .stats-hero {
            margin-top: 50px;
            padding: 30px 0;
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .stat-hero {
            text-align: center;
            color: white;
        }

        .stat-hero .number {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
        }

        .stat-hero .label {
            font-size: 0.9rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title {
            text-align: center;
            margin: 80px 0 60px;
            position: relative;
        }

        .section-title h2 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .section-title .subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            margin: 30px auto;
            border-radius: 2px;
        }

        .formation-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
            overflow: hidden;
            height: 100%;
            position: relative;
        }

        .formation-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }

        .formation-card.premium {
            border: 3px solid var(--gold-color);
            box-shadow: 0 15px 35px rgba(255, 215, 0, 0.3);
        }

        .formation-card.premium::before {
            content: '⭐ PREMIUM';
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--gold-color);
            color: #333;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }

        .formation-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .formation-header.premium {
            background: linear-gradient(135deg, var(--gold-color), #ffed4e);
            color: #333;
        }

        .formation-header h5 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .type-badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .age-range {
            margin-top: 10px;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .formation-body {
            padding: 40px 30px;
            background: white;
        }

        .price-section {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--success-color);
            margin-bottom: 5px;
        }

        .price-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .formation-features {
            list-style: none;
            padding: 0;
            margin: 25px 0;
        }

        .formation-features li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .formation-features li:last-child {
            border-bottom: none;
        }

        .formation-features i {
            color: var(--success-color);
            margin-right: 10px;
            width: 20px;
        }

        .formation-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #eee;
        }

        .btn-formation {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-formation:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .comparison-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 80px 0;
            margin: 80px 0;
        }

        .comparison-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .comparison-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            text-align: center;
            font-weight: 600;
        }

        .comparison-table td {
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .testimonial-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
            margin: 80px 0;
        }

        .testimonial-card {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .cta-section {
            background: #fff;
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .hero-section .lead {
                font-size: 1.2rem;
            }
            
            .formation-header {
                padding: 25px 20px;
            }
            
            .formation-body {
                padding: 25px 20px;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="fas fa-futbol me-2"></i>ASOD ACADEMIE
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
                        <a class="nav-link active fw-bold" href="formation.php">Formations</a>
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
            <div class="hero-content">
                <h1><i class="fas fa-graduation-cap me-3"></i>Formations Professionnelles</h1>
                <p class="lead">L'excellence footballistique à votre portée avec les formations ASOD ACADEMIE</p>
                <p class="mb-4">Inspirées des meilleures académies africaines • Entraîneurs qualifiés • Méthodes modernes</p>
                
                <div class="stats-hero">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="stat-hero">
                                <span class="number"><?= $stats['total_formations'] ?></span>
                                <span class="label">Formations</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-hero">
                                <span class="number"><?= $stats['types_formation'] ?></span>
                                <span class="label">Spécialités</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-hero">
                                <span class="number"><?= number_format($stats['prix_min']/1000) ?>k</span>
                                <span class="label">Dès (FCFA)</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-hero">
                                <span class="number"><?= $stats['places_totales'] ?></span>
                                <span class="label">Places</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formations Section -->
    <section class="py-5" style="margin-top: 100px;">
        <div class="container">
            <div class="section-title">
                <h2>Nos Formations d'Excellence</h2>
                <p class="subtitle">Inspirées des meilleures académies africaines : JMG Academy, Diambars, Right to Dream, ASEC Mimosas</p>
            </div>

            <?php if (empty($formations)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-graduation-cap text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">Formations en préparation</h4>
                    <p class="text-muted">Nos programmes d'excellence seront bientôt disponibles</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($formations as $index => $formation): ?>
                        <?php 
                        $isPremium = $formation['cout_formation'] >= 60000;
                        $typeColors = [
                            'complete' => 'var(--gold-color)',
                            'technique' => 'var(--primary-color)',
                            'physique' => 'var(--success-color)',
                            'mentale' => 'var(--info-color)',
                            'tactique' => 'var(--warning-color)'
                        ];
                        $typeIcons = [
                            'complete' => 'fa-trophy',
                            'technique' => 'fa-futbol',
                            'physique' => 'fa-dumbbell',
                            'mentale' => 'fa-brain',
                            'tactique' => 'fa-chess'
                        ];
                        ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="formation-card <?= $isPremium ? 'premium' : '' ?>">
                                <div class="formation-header <?= $isPremium ? 'premium' : '' ?>">
                                    <h5><?= htmlspecialchars($formation['titre']) ?></h5>
                                    <span class="type-badge">
                                        <i class="fas <?= $typeIcons[$formation['type_formation']] ?? 'fa-star' ?> me-2"></i>
                                        <?= ucfirst($formation['type_formation'] ?? 'formation') ?>
                                    </span>
                                    <div class="age-range">
                                        <i class="fas fa-users me-1"></i>
                                        <?= $formation['age_min'] ?>-<?= $formation['age_max'] ?> ans
                                    </div>
                                </div>
                                
                                <div class="formation-body">
                                    <div class="price-section">
                                        <div class="price">
                                            <?= number_format($formation['cout_formation']) ?> <small>FCFA</small>
                                        </div>
                                        <div class="price-label">Formation complète</div>
                                    </div>

                                    <?php if ($formation['description']): ?>
                                        <p class="text-muted mb-3">
                                            <?= htmlspecialchars(substr($formation['description'], 0, 120)) ?>
                                            <?= strlen($formation['description']) > 120 ? '...' : '' ?>
                                        </p>
                                    <?php endif; ?>

                                    <ul class="formation-features">
                                        <li>
                                            <i class="fas fa-clock"></i>
                                            <span><?= htmlspecialchars($formation['duree']) ?></span>
                                        </li>
                                        <li>
                                            <i class="fas fa-signal"></i>
                                            <span>Niveau <?= ucfirst($formation['niveau'] ?? 'adapté') ?></span>
                                        </li>
                                        <?php if ($formation['formateur_nom']): ?>
                                        <li>
                                            <i class="fas fa-user-tie"></i>
                                            <span><?= htmlspecialchars($formation['formateur_nom'] . ' ' . $formation['formateur_prenom']) ?></span>
                                        </li>
                                        <?php endif; ?>
                                        <li>
                                            <i class="fas fa-users"></i>
                                            <span><?= $formation['places_disponibles'] ?> places disponibles</span>
                                        </li>
                                        <?php if ($formation['nombre_sessions'] > 0): ?>
                                        <li>
                                            <i class="fas fa-calendar-check"></i>
                                            <span><?= $formation['nombre_sessions'] ?> session(s) programmée(s)</span>
                                        </li>
                                        <?php endif; ?>
                                    </ul>

                                    <div class="formation-info">
                                        <button class="btn btn-formation w-100" onclick="showFormationDetails(<?= htmlspecialchars(json_encode($formation)) ?>)">
                                            <i class="fas fa-info-circle me-2"></i>Découvrir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Comparison Section -->
    <section class="comparison-section">
        <div class="container">
            <div class="section-title">
                <h2>Pourquoi Choisir ASOD ACADEMIE ?</h2>
                <p class="subtitle">Comparaison avec les grandes académies africaines</p>
            </div>

            <div class="table-responsive">
                <table class="table comparison-table">
                    <thead>
                        <tr>
                            <th>Critères</th>
                            <th>ASOD ACADEMIE</th>
                            <th>JMG Academy</th>
                            <th>Diambars</th>
                            <th>Right to Dream</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Prix Formation</strong></td>
                            <td class="text-success"><strong>30k - 75k FCFA</strong></td>
                            <td>200k FCFA</td>
                            <td>150k FCFA</td>
                            <td>300k FCFA</td>
                        </tr>
                        <tr>
                            <td><strong>Localisation</strong></td>
                            <td class="text-success"><strong>Bénin (Local)</strong></td>
                            <td>Côte d'Ivoire</td>
                            <td>Sénégal</td>
                            <td>Ghana</td>
                        </tr>
                        <tr>
                            <td><strong>Formations</strong></td>
                            <td class="text-success"><strong><?= $stats['total_formations'] ?> programmes</strong></td>
                            <td>4 programmes</td>
                            <td>3 programmes</td>
                            <td>3 programmes</td>
                        </tr>
                        <tr>
                            <td><strong>Approche</strong></td>
                            <td class="text-success"><strong>Holistique + Local</strong></td>
                            <td>Technique</td>
                            <td>Excellence</td>
                            <td>Développement</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="mb-4">Prêt à Rejoindre l'Excellence ?</h2>
            <p class="lead mb-4">Rejoignez la première académie de football du Bénin avec des formations inspirées des meilleures académies africaines</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <a href="index.php#contact" class="btn btn-formation btn-lg me-3 mb-3">
                        <i class="fas fa-envelope me-2"></i>Nous Contacter
                    </a>
                    <a href="index.php#inscription" class="btn btn-outline-primary btn-lg mb-3">
                        <i class="fas fa-user-plus me-2"></i>S'inscrire
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-futbol me-2"></i>ASOD ACADEMIE
                    </h5>
                    <p>L'académie de football de référence au Bénin. Formations professionnelles inspirées des meilleures académies africaines.</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Contact</h6>
                    <p class="mb-1"><i class="fas fa-envelope me-2"></i>contact@asodacademie.bj</p>
                    <p class="mb-1"><i class="fas fa-phone me-2"></i>+229 XX XX XX XX</p>
                    <p><i class="fas fa-map-marker-alt me-2"></i>Cotonou, Bénin</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 ASOD ACADEMIE. Tous droits réservés. | Formations professionnelles de football</p>
            </div>
        </div>
    </footer>

    <!-- Modal Détails Formation -->
    <div class="modal fade" id="formationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Détails de la formation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Contenu dynamique -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <a href="index.php#contact" class="btn btn-primary">
                        <i class="fas fa-envelope me-1"></i>S'inscrire
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Afficher les détails d'une formation
        function showFormationDetails(formation) {
            const modal = document.getElementById('formationModal');
            const title = document.getElementById('modalTitle');
            const content = document.getElementById('modalContent');
            
            title.innerHTML = `<i class="fas fa-graduation-cap me-2"></i>${formation.titre}`;
            
            const typeLabels = {
                'complete': 'Formation Complète',
                'technique': 'Formation Technique',
                'physique': 'Formation Physique',
                'mentale': 'Formation Mentale',
                'tactique': 'Formation Tactique'
            };
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Type :</strong> ${typeLabels[formation.type_formation] || 'Formation'}</p>
                                <p><strong>Niveau :</strong> ${formation.niveau.charAt(0).toUpperCase() + formation.niveau.slice(1)}</p>
                                <p><strong>Durée :</strong> ${formation.duree}</p>
                                <p><strong>Ages :</strong> ${formation.age_min} - ${formation.age_max} ans</p>
                                <p><strong>Places :</strong> ${formation.places_disponibles} disponibles</p>
                                ${formation.formateur_nom ? `<p><strong>Formateur :</strong> ${formation.formateur_nom} ${formation.formateur_prenom}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-euro-sign me-2"></i>Tarification</h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="display-4 text-success fw-bold">${new Intl.NumberFormat('fr-FR').format(formation.cout_formation)}</div>
                                <div class="text-muted">FCFA</div>
                                <div class="mt-2">
                                    <span class="badge bg-success">Formation complète</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${formation.description ? `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Description</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">${formation.description}</p>
                        </div>
                    </div>
                ` : ''}
                
                ${formation.objectifs ? `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-target me-2"></i>Objectifs</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">${formation.objectifs}</p>
                        </div>
                    </div>
                ` : ''}
                
                ${formation.contenu ? `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Programme</h6>
                        </div>
                        <div class="card-body">
                            <div style="max-height: 200px; overflow-y: auto;">
                                <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;">${formation.contenu}</pre>
                            </div>
                        </div>
                    </div>
                ` : ''}
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Inscription :</strong> Contactez-nous pour vous inscrire à cette formation d'excellence.
                </div>
            `;
            
            new bootstrap.Modal(modal).show();
        }

        // Animation au scroll
        window.addEventListener('scroll', function() {
            const cards = document.querySelectorAll('.formation-card');
            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }
            });
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter les animations d'entrée
            const cards = document.querySelectorAll('.formation-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(50px)';
                card.style.transition = 'all 0.6s ease';
                card.style.transitionDelay = `${index * 0.1}s`;
            });
            
            // Déclencher les animations
            setTimeout(() => {
                cards.forEach(card => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            }, 500);
        });
    </script>
</body>
</html>












