<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Joueurs - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #fd7e14;
            --info-color: #0dcaf0;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--dark-color) 0%, #16213e 100%);
            color: white;
            padding: 80px 0 60px;
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
            background: url('images/tactique/img19.png') center/cover;
            opacity: 0.1;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stat-label {
            color: var(--secondary-color);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .filters-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .joueur-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            border: none;
        }

        .joueur-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .joueur-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }

        .joueur-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .badge-genre {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .badge-garcon {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }

        .badge-fille {
            background: linear-gradient(135deg, #e91e63, #ad1457);
            color: white;
        }

        .badge-equipe {
            background: linear-gradient(135deg, var(--success-color), #146c43);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .search-box {
            position: relative;
        }

        .search-box .form-control {
            border-radius: 25px;
            padding: 12px 50px 12px 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .search-box .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        .section-title {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            border-radius: 2px;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 60px 0;
        }

        .loading-spinner .spinner-border {
            width: 3rem;
            height: 3rem;
            color: var(--primary-color);
        }

        .no-results {
            text-align: center;
            padding: 60px 0;
            color: var(--secondary-color);
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .pagination-custom .page-link {
            border-radius: 10px;
            margin: 0 5px;
            border: none;
            color: var(--primary-color);
            font-weight: 500;
        }

        .pagination-custom .page-link:hover,
        .pagination-custom .page-item.active .page-link {
            background: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0 40px;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .filters-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-users me-3"></i>
                        Nos Joueurs
                    </h1>
                    <p class="lead mb-4">
                        Découvrez tous les membres de notre académie de football. 
                        Des jeunes talents aux joueurs confirmés, chacun contribue à l'esprit d'équipe de l'ASOD ACADEMIE.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <span class="badge bg-light text-dark fs-6 px-3 py-2">
                            <i class="fas fa-futbol me-2"></i>Tous niveaux
                        </span>
                        <span class="badge bg-light text-dark fs-6 px-3 py-2">
                            <i class="fas fa-trophy me-2"></i>Toutes catégories
                        </span>
                        <span class="badge bg-light text-dark fs-6 px-3 py-2">
                            <i class="fas fa-heart me-2"></i>Une famille
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="hero-stats">
                        <div class="stat-circle">
                            <i class="fas fa-users fa-4x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques -->
    <section class="py-5">
        <div class="container">
            <div class="stats-card">
                <div class="row" id="statistiques-container">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number" id="stat-total">0</div>
                            <div class="stat-label">Total Joueurs</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number text-info" id="stat-garcons">0</div>
                            <div class="stat-label">Garçons</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number text-danger" id="stat-filles">0</div>
                            <div class="stat-label">Filles</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number text-success" id="stat-age">0</div>
                            <div class="stat-label">Âge Moyen</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtres et Recherche -->
    <section class="pb-3">
        <div class="container">
            <div class="filters-section">
                <h3 class="mb-4">
                    <i class="fas fa-filter me-2"></i>
                    Rechercher et Filtrer
                </h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <input type="text" class="form-control" id="recherche" placeholder="Rechercher un joueur...">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filtre-genre">
                            <option value="tous">Tous les genres</option>
                            <option value="garcon">Garçons</option>
                            <option value="fille">Filles</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filtre-equipe">
                            <option value="">Toutes les équipes</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" id="btn-reset-filtres">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Liste des Joueurs -->
    <section class="pb-5">
        <div class="container">
            <!-- Chargement -->
            <div class="loading-spinner" id="loading">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3">Chargement des joueurs...</p>
            </div>

            <!-- Pas de résultats -->
            <div class="no-results d-none" id="no-results">
                <i class="fas fa-search"></i>
                <h4>Aucun joueur trouvé</h4>
                <p>Essayez de modifier vos critères de recherche.</p>
            </div>

            <!-- Garçons -->
            <div id="section-garcons" class="d-none">
                <h2 class="section-title">
                    <i class="fas fa-male me-2 text-primary"></i>
                    Garçons <span class="badge bg-primary" id="count-garcons">0</span>
                </h2>
                <div class="row" id="liste-garcons">
                    <!-- Les joueurs garçons seront ajoutés ici dynamiquement -->
                </div>
            </div>

            <!-- Filles -->
            <div id="section-filles" class="d-none">
                <h2 class="section-title">
                    <i class="fas fa-female me-2 text-danger"></i>
                    Filles <span class="badge bg-danger" id="count-filles">0</span>
                </h2>
                <div class="row" id="liste-filles">
                    <!-- Les joueuses seront ajoutées ici dynamiquement -->
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Navigation des joueurs" class="mt-5">
                <ul class="pagination pagination-custom justify-content-center" id="pagination">
                    <!-- La pagination sera générée dynamiquement -->
                </ul>
            </nav>
        </div>
    </section>

    <!-- Retour au site -->
    <section class="py-4 bg-dark">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-home me-2"></i>
                        Découvrez notre académie
                    </h5>
                    <p class="text-light mb-0">Retournez sur notre site principal pour plus d'informations</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="index.php" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour au site
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Redirection vers la page joueurs.php qui contient la vraie liste
        window.location.href = 'joueurs.php';
    </script>
</body>
</html>
