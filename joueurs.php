<?php
http_response_code(404);
exit('Page non disponible.');
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer tous les joueurs avec leurs équipes
    $stmt = $pdo->query("
        SELECT m.*, 
               COALESCE(i.date_naissance, m.date_naissance) as date_naissance,
               i.email as email_inscription,
               i.telephone as telephone_inscription,
               e.nom as equipe_nom
        FROM membres m 
        LEFT JOIN inscriptions i ON m.inscription_id = i.id
        LEFT JOIN equipes e ON m.equipe_id = e.id 
        WHERE m.statut = 'actif' 
        ORDER BY m.genre ASC, m.categorie ASC, m.nom ASC, m.prenom ASC
    ");
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organiser les joueurs par genre puis par catégorie
    $joueursOrganises = [
        'garcons' => [
            'U8-U10' => [],
            'U12-U14' => [],
            'U16-U18' => [],
            'Seniors' => [],
            'Sans_categorie' => []
        ],
        'filles' => [
            'U8-U10' => [],
            'U12-U14' => [],
            'U16-U18' => [],
            'Seniors' => [],
            'Sans_categorie' => []
        ]
    ];
    
    $statistiques = [
        'total_joueurs' => 0,
        'total_garcons' => 0,
        'total_filles' => 0,
        'par_categorie' => [
            'garcons' => ['U8-U10' => 0, 'U12-U14' => 0, 'U16-U18' => 0, 'Seniors' => 0, 'Sans_categorie' => 0],
            'filles' => ['U8-U10' => 0, 'U12-U14' => 0, 'U16-U18' => 0, 'Seniors' => 0, 'Sans_categorie' => 0]
        ]
    ];
    
    foreach ($joueurs as $joueur) {
        $genre = $joueur['genre'] === 'fille' ? 'filles' : 'garcons';
        $categorie = $joueur['categorie'] ?? '';
        
        // Mapper les catégories de la base vers les catégories du code
        $categorieMap = [
            'U11' => 'U8-U10',
            'U13' => 'U12-U14', 
            'U15' => 'U16-U18',
            'U17' => 'U16-U18',
            'U20' => 'U16-U18',  // Ajouté pour U20
            'U19' => 'Seniors',
            'Senior' => 'Seniors',
            'Seniors' => 'Seniors',  // Ajouté pour Seniors
            'U8-U10' => 'U8-U10',    // Ajouté pour les catégories déjà mappées
            'U12-U14' => 'U12-U14',
            'U16-U18' => 'U16-U18',
            '' => 'Sans_categorie'
        ];
        
        $categorie = $categorieMap[$categorie] ?? 'Sans_categorie';
        
        $joueursOrganises[$genre][$categorie][] = $joueur;
        $statistiques['par_categorie'][$genre][$categorie]++;
        
        $statistiques['total_joueurs']++;
        if ($genre === 'filles') {
            $statistiques['total_filles']++;
        } else {
            $statistiques['total_garcons']++;
        }
    }
    
} catch (Exception $e) {
    $joueurs = [];
    $joueursOrganises = [
        'garcons' => ['U8-U10' => [], 'U12-U14' => [], 'U16-U18' => [], 'Seniors' => [], 'Sans_categorie' => []],
        'filles' => ['U8-U10' => [], 'U12-U14' => [], 'U16-U18' => [], 'Seniors' => [], 'Sans_categorie' => []]
    ];
    $statistiques = [
        'total_joueurs' => 0,
        'total_garcons' => 0,
        'total_filles' => 0,
        'par_categorie' => [
            'garcons' => ['U8-U10' => 0, 'U12-U14' => 0, 'U16-U18' => 0, 'Seniors' => 0, 'Sans_categorie' => 0],
            'filles' => ['U8-U10' => 0, 'U12-U14' => 0, 'U16-U18' => 0, 'Seniors' => 0, 'Sans_categorie' => 0]
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Joueurs - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
            --success-color: #198754;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --border-color: #dee2e6;
            --boys-color: #007bff;
            --girls-color: #e91e63;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }

        .club-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
        }

        .stats-row {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .back-link {
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            color: var(--accent-color);
        }

        /* Onglets principaux (Garçons/Filles) */
        .main-tabs {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .main-tabs .nav-tabs {
            border-bottom: none;
            background: #f8f9fa;
        }

        .main-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 20px 40px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .main-tabs .nav-link:not(.active) {
            background: #f8f9fa;
            color: var(--secondary-color);
        }

        .main-tabs .nav-link.boys {
            color: var(--boys-color);
        }

        .main-tabs .nav-link.boys.active {
            background: var(--boys-color);
            color: white;
        }

        .main-tabs .nav-link.girls {
            color: var(--girls-color);
        }

        .main-tabs .nav-link.girls.active {
            background: var(--girls-color);
            color: white;
        }

        /* Sous-onglets (Catégories) */
        .sub-tabs {
            background: #f8f9fa;
            border-bottom: 2px solid var(--border-color);
            padding: 0 30px;
        }

        .sub-tabs .nav-tabs {
            border-bottom: none;
            margin-bottom: 0;
        }

        .sub-tabs .nav-link {
            border: none;
            border-radius: 8px 8px 0 0;
            padding: 12px 20px;
            font-weight: 500;
            color: var(--secondary-color);
            transition: all 0.3s ease;
            margin-right: 5px;
        }

        .sub-tabs .nav-link:hover {
            background: rgba(13, 110, 253, 0.1);
            color: var(--primary-color);
        }

        .sub-tabs .nav-link.active {
            background: white;
            color: var(--primary-color);
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
        }

        /* Contenu des équipes */
        .team-content {
            background: white;
            padding: 30px;
        }

        .category-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 20px 30px;
            margin: -30px -30px 30px -30px;
            border-radius: 0;
        }

        .category-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .category-info {
            margin-top: 8px;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .players-table {
            margin: 0;
        }

        .players-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: var(--dark-color);
            padding: 15px 20px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .players-table td {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .players-table tbody tr {
            transition: all 0.2s ease;
        }

        .players-table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .player-number {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary-color);
            min-width: 40px;
            text-align: center;
        }

        .player-photo {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
        }

        .player-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .player-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark-color);
            margin: 0;
        }

        .player-details {
            font-size: 0.9rem;
            color: var(--secondary-color);
            margin: 3px 0 0 0;
        }

        .gender-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .gender-male {
            background: var(--boys-color);
            color: white;
        }

        .gender-female {
            background: var(--girls-color);
            color: white;
        }

        .age-info {
            font-size: 0.85rem;
            color: var(--secondary-color);
        }

        .license-info {
            font-size: 0.8rem;
            color: var(--secondary-color);
            font-family: 'Courier New', monospace;
        }

        .status-active {
            color: var(--success-color);
            font-weight: 500;
        }

        .status-pending {
            color: var(--accent-color);
            font-weight: 500;
        }

        .no-players {
            text-align: center;
            padding: 60px;
            color: var(--secondary-color);
        }

        .no-players i {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .tab-badge {
            background: var(--accent-color);
            color: var(--dark-color);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .nav-link.active .tab-badge {
            background: rgba(255,255,255,0.3);
            color: white;
        }

        .sub-tab-badge {
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 6px;
        }

        .sub-tabs .nav-link.active .sub-tab-badge {
            background: var(--accent-color);
            color: var(--dark-color);
        }

        @media (max-width: 768px) {
            .header-section {
                padding: 40px 0;
            }
            
            .main-tabs .nav-link {
                padding: 15px 25px;
                font-size: 1rem;
            }
            
            .sub-tabs .nav-link {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .category-header {
                padding: 15px 20px;
                margin: -30px -20px 20px -20px;
            }
            
            .team-content {
                padding: 20px;
            }
            
            .players-table th,
            .players-table td {
                padding: 12px 10px;
                font-size: 0.9rem;
            }
            
            /* Affichage optimisé sur mobile - tous les champs visibles */
            .players-table th,
            .players-table td {
                font-size: 0.8rem;
                padding: 8px 4px;
                text-align: center;
            }
            
            .players-table .player-name {
                font-size: 0.75rem;
                font-weight: 600;
            }
            
            .players-table .badge {
                font-size: 0.7rem;
                padding: 2px 6px;
            }
            
            .age-info, .position-info {
                font-size: 0.7rem;
            }
            
            .player-photo {
                width: 30px;
                height: 30px;
            }
            
            .player-avatar {
                width: 30px;
                height: 30px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .main-tabs,
            .sub-tabs {
                overflow-x: auto;
                scrollbar-width: thin;
            }
            
            .main-tabs .nav-tabs,
            .sub-tabs .nav-tabs {
                flex-wrap: nowrap;
            }
            
            .main-tabs .nav-link,
            .sub-tabs .nav-link {
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <section class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <img src="images/logo.png" alt="ASOD ACADEMIE" class="club-logo">
                </div>
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">NOS JOUEURS</h1>
                    <p class="lead mb-0">ASOD ACADEMIE - Effectif par genre et catégories</p>
                    
                    <div class="stats-row">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="fw-bold fs-4"><?= $statistiques['total_joueurs'] ?></div>
                                <small>Joueurs</small>
                            </div>
                            <div class="col-3">
                                <div class="fw-bold fs-4"><?= $statistiques['total_garcons'] ?></div>
                                <small>Garçons</small>
                            </div>
                            <div class="col-3">
                                <div class="fw-bold fs-4"><?= $statistiques['total_filles'] ?></div>
                                <small>Filles</small>
                            </div>
                            <div class="col-3">
                                <div class="fw-bold fs-4"><?= count(array_filter(array_merge($statistiques['par_categorie']['garcons'], $statistiques['par_categorie']['filles']))) ?></div>
                                <small>Catégories</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <a href="index.php" class="back-link">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour au site
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Onglets principaux : Garçons / Filles -->
        <div class="main-tabs">
            <ul class="nav nav-tabs" id="genreTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link boys active" 
                            id="garcons-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#garcons" 
                            type="button" 
                            role="tab">
                        <i class="fas fa-male me-2"></i>
                        GARÇONS
                        <span class="tab-badge"><?= $statistiques['total_garcons'] ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link girls" 
                            id="filles-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#filles" 
                            type="button" 
                            role="tab">
                        <i class="fas fa-female me-2"></i>
                        FILLES
                        <span class="tab-badge"><?= $statistiques['total_filles'] ?></span>
                    </button>
                </li>
            </ul>

            <!-- Contenu des onglets principaux -->
            <div class="tab-content" id="genreTabContent">
                <!-- GARÇONS -->
                <div class="tab-pane fade show active" id="garcons" role="tabpanel" aria-labelledby="garcons-tab">
                    <!-- Sous-onglets pour les catégories des garçons -->
                    <div class="sub-tabs">
                        <ul class="nav nav-tabs" id="garconsCategories" role="tablist">
                            <?php foreach ($joueursOrganises['garcons'] as $categorie => $joueurs_cat): ?>
                                <?php if (!empty($joueurs_cat)): ?>
                                    <?php $isFirstBoys = !isset($firstBoysSet); $firstBoysSet = true; ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= $isFirstBoys ? 'active' : '' ?>" 
                                                id="garcons-<?= $categorie ?>-tab" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#garcons-<?= $categorie ?>" 
                                                type="button" 
                                                role="tab">
                                            <?= $categorie === 'Sans_categorie' ? 'Non classés' : $categorie ?>
                                            <span class="sub-tab-badge"><?= count($joueurs_cat) ?></span>
                                        </button>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Contenu des catégories garçons -->
                    <div class="tab-content">
                        <?php foreach ($joueursOrganises['garcons'] as $categorie => $joueurs_cat): ?>
                            <?php if (!empty($joueurs_cat)): ?>
                                <?php $isFirstBoysCat = !isset($firstBoysCatSet); $firstBoysCatSet = true; ?>
                                <div class="tab-pane fade <?= $isFirstBoysCat ? 'show active' : '' ?>" 
                                     id="garcons-<?= $categorie ?>" 
                                     role="tabpanel">
                                    
                                    <div class="team-content">
                                        <div class="category-header">
                                            <h3 class="category-title">
                                                <i class="fas fa-male"></i>
                                                GARÇONS <?= $categorie === 'Sans_categorie' ? 'NON CLASSÉS' : $categorie ?>
                                                <span class="team-badge"><?= count($joueurs_cat) ?> joueur<?= count($joueurs_cat) > 1 ? 's' : '' ?></span>
                                            </h3>
                                            <div class="category-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Catégorie <?= $categorie === 'Sans_categorie' ? 'non définie' : $categorie ?> - Équipe masculine
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table players-table">
                                                <thead>
                                                    <tr>
                                                        <th width="80">N°</th>
                                                        <th width="80">Photo</th>
                                                        <th>Joueur</th>
                                                        <th>Équipe</th>
                                                        <th>Âge</th>
                                                        <th>Poste</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($joueurs_cat as $index => $joueur): ?>
                                                        <?php
                                                        $age = null;
                                                        if ($joueur['date_naissance']) {
                                                            $age = date_diff(date_create($joueur['date_naissance']), date_create('today'))->y;
                                                        }
                                                        
                                                        $photoHtml = '';
                                                        // Convertir le chemin uploads/membres vers images/membres
                                                        $photoPath = $joueur['photo'];
                                                        if ($photoPath && strpos($photoPath, 'uploads/membres/') === 0) {
                                                            $photoPath = str_replace('uploads/membres/', 'images/membres/', $photoPath);
                                                        }
                                                        
                                                        if ($joueur['photo'] && file_exists(__DIR__ . '/' . $photoPath)) {
                                                            $photoHtml = '<img src="serve_image.php?file=' . urlencode(basename($photoPath)) . '" alt="' . htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) . '" class="player-photo">';
                                                        } else {
                                                            $photoHtml = '<div class="player-avatar">' . strtoupper(substr($joueur['prenom'], 0, 1) . substr($joueur['nom'], 0, 1)) . '</div>';
                                                        }
                                                        
                                                        $statusHtml = '';
                                                        switch($joueur['statut']) {
                                                            case 'actif':
                                                                $statusHtml = '<span class="status-active"><i class="fas fa-check-circle me-1"></i>Actif</span>';
                                                                break;
                                                            case 'suspendu':
                                                                $statusHtml = '<span class="status-pending"><i class="fas fa-pause me-1"></i>Suspendu</span>';
                                                                break;
                                                            case 'radie':
                                                                $statusHtml = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Radié</span>';
                                                                break;
                                                            default:
                                                                $statusHtml = '<span class="text-muted"><i class="fas fa-question me-1"></i>Inconnu</span>';
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="player-number"><?= $index + 1 ?></td>
                                                            <td><?= $photoHtml ?></td>
                                                            <td>
                                                                <div class="player-name"><?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?></div>
                                                            </td>
                                                            <td >
                                                                <span class="badge bg-primary">
                                                                    <?= $joueur['equipe_nom'] ?: 'Non assigné' ?>
                                                                </span>
                                                            </td>
                                                            <td >
                                                                <div class="age-info"><?= $age ? $age . ' ans' : 'N/A' ?></div>
                                                            </td>
                                                            <td >
                                                                <div class="position-info"><?= $joueur['poste'] ?: 'À définir' ?></div>
                                                            </td>
                                                            <td><?= $statusHtml ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- FILLES -->
                <div class="tab-pane fade" id="filles" role="tabpanel" aria-labelledby="filles-tab">
                    <!-- Sous-onglets pour les catégories des filles -->
                    <div class="sub-tabs">
                        <ul class="nav nav-tabs" id="fillesCategories" role="tablist">
                            <?php foreach ($joueursOrganises['filles'] as $categorie => $joueurs_cat): ?>
                                <?php if (!empty($joueurs_cat)): ?>
                                    <?php $isFirstGirls = !isset($firstGirlsSet); $firstGirlsSet = true; ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= $isFirstGirls ? 'active' : '' ?>" 
                                                id="filles-<?= $categorie ?>-tab" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#filles-<?= $categorie ?>" 
                                                type="button" 
                                                role="tab">
                                            <?= $categorie === 'Sans_categorie' ? 'Non classées' : $categorie ?>
                                            <span class="sub-tab-badge"><?= count($joueurs_cat) ?></span>
                                        </button>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Contenu des catégories filles -->
                    <div class="tab-content">
                        <?php foreach ($joueursOrganises['filles'] as $categorie => $joueurs_cat): ?>
                            <?php if (!empty($joueurs_cat)): ?>
                                <?php $isFirstGirlsCat = !isset($firstGirlsCatSet); $firstGirlsCatSet = true; ?>
                                <div class="tab-pane fade <?= $isFirstGirlsCat ? 'show active' : '' ?>" 
                                     id="filles-<?= $categorie ?>" 
                                     role="tabpanel">
                                    
                                    <div class="team-content">
                                        <div class="category-header" style="background: linear-gradient(135deg, var(--girls-color), #ad1457);">
                                            <h3 class="category-title">
                                                <i class="fas fa-female"></i>
                                                FILLES <?= $categorie === 'Sans_categorie' ? 'NON CLASSÉES' : $categorie ?>
                                                <span class="team-badge"><?= count($joueurs_cat) ?> joueuse<?= count($joueurs_cat) > 1 ? 's' : '' ?></span>
                                            </h3>
                                            <div class="category-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Catégorie <?= $categorie === 'Sans_categorie' ? 'non définie' : $categorie ?> - Équipe féminine
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table players-table">
                                                <thead>
                                                    <tr>
                                                        <th width="80">N°</th>
                                                        <th width="80">Photo</th>
                                                        <th>Joueuse</th>
                                                        <th>Équipe</th>
                                                        <th>Âge</th>
                                                        <th>Poste</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($joueurs_cat as $index => $joueur): ?>
                                                        <?php
                                                        $age = null;
                                                        if ($joueur['date_naissance']) {
                                                            $age = date_diff(date_create($joueur['date_naissance']), date_create('today'))->y;
                                                        }
                                                        
                                                        $photoHtml = '';
                                                        // Convertir le chemin uploads/membres vers images/membres
                                                        $photoPath = $joueur['photo'];
                                                        if ($photoPath && strpos($photoPath, 'uploads/membres/') === 0) {
                                                            $photoPath = str_replace('uploads/membres/', 'images/membres/', $photoPath);
                                                        }
                                                        
                                                        if ($joueur['photo'] && file_exists(__DIR__ . '/' . $photoPath)) {
                                                            $photoHtml = '<img src="serve_image.php?file=' . urlencode(basename($photoPath)) . '" alt="' . htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) . '" class="player-photo">';
                                                        } else {
                                                            $photoHtml = '<div class="player-avatar">' . strtoupper(substr($joueur['prenom'], 0, 1) . substr($joueur['nom'], 0, 1)) . '</div>';
                                                        }
                                                        
                                                        $statusHtml = '';
                                                        switch($joueur['statut']) {
                                                            case 'actif':
                                                                $statusHtml = '<span class="status-active"><i class="fas fa-check-circle me-1"></i>Actif</span>';
                                                                break;
                                                            case 'suspendu':
                                                                $statusHtml = '<span class="status-pending"><i class="fas fa-pause me-1"></i>Suspendu</span>';
                                                                break;
                                                            case 'radie':
                                                                $statusHtml = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Radié</span>';
                                                                break;
                                                            default:
                                                                $statusHtml = '<span class="text-muted"><i class="fas fa-question me-1"></i>Inconnu</span>';
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="player-number"><?= $index + 1 ?></td>
                                                            <td><?= $photoHtml ?></td>
                                                            <td>
                                                                <div class="player-name"><?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?></div>
                                                            </td>
                                                            <td >
                                                                <span class="badge bg-primary">
                                                                    <?= $joueur['equipe_nom'] ?: 'Non assigné' ?>
                                                                </span>
                                                            </td>
                                                            <td >
                                                                <div class="age-info"><?= $age ? $age . ' ans' : 'N/A' ?></div>
                                                            </td>
                                                            <td >
                                                                <div class="position-info"><?= $joueur['poste'] ?: 'À définir' ?></div>
                                                            </td>
                                                            <td><?= $statusHtml ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- FILLES - Structure identique -->
                <div class="tab-pane fade" id="filles" role="tabpanel" aria-labelledby="filles-tab">
                    <!-- Contenu identique pour les filles avec les bonnes données -->
                    <?php if (array_sum(array_map('count', $joueursOrganises['filles'])) === 0): ?>
                        <div class="no-players">
                            <i class="fas fa-female fa-3x mb-3"></i>
                            <h5>Aucune joueuse inscrite</h5>
                            <p class="mb-0">Les joueuses apparaîtront ici une fois inscrites.</p>
                        </div>
                    <?php else: ?>
                        <!-- Sous-onglets pour les catégories des filles -->
                        <div class="sub-tabs">
                            <ul class="nav nav-tabs" id="fillesCategories" role="tablist">
                                <?php foreach ($joueursOrganises['filles'] as $categorie => $joueurs_cat): ?>
                                    <?php if (!empty($joueurs_cat)): ?>
                                        <?php $isFirstGirlsSub = !isset($firstGirlsSubSet); $firstGirlsSubSet = true; ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?= $isFirstGirlsSub ? 'active' : '' ?>" 
                                                    id="filles-sub-<?= $categorie ?>-tab" 
                                                    data-bs-toggle="tab" 
                                                    data-bs-target="#filles-sub-<?= $categorie ?>" 
                                                    type="button" 
                                                    role="tab">
                                                <?= $categorie === 'Sans_categorie' ? 'Non classées' : $categorie ?>
                                                <span class="sub-tab-badge"><?= count($joueurs_cat) ?></span>
                                            </button>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Contenu des catégories filles -->
                        <div class="tab-content">
                            <?php foreach ($joueursOrganises['filles'] as $categorie => $joueurs_cat): ?>
                                <?php if (!empty($joueurs_cat)): ?>
                                    <?php $isFirstGirlsSubCat = !isset($firstGirlsSubCatSet); $firstGirlsSubCatSet = true; ?>
                                    <div class="tab-pane fade <?= $isFirstGirlsSubCat ? 'show active' : '' ?>" 
                                         id="filles-sub-<?= $categorie ?>" 
                                         role="tabpanel">
                                        
                                        <div class="team-content">
                                            <div class="category-header" style="background: linear-gradient(135deg, var(--girls-color), #ad1457);">
                                                <h3 class="category-title">
                                                    <i class="fas fa-female"></i>
                                                    FILLES <?= $categorie === 'Sans_categorie' ? 'NON CLASSÉES' : $categorie ?>
                                                    <span class="team-badge"><?= count($joueurs_cat) ?> joueuse<?= count($joueurs_cat) > 1 ? 's' : '' ?></span>
                                                </h3>
                                                <div class="category-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Catégorie <?= $categorie === 'Sans_categorie' ? 'non définie' : $categorie ?> - Équipe féminine
                                                </div>
                                            </div>
                                            
                                            <!-- Tableau identique pour les filles -->
                                            <div class="table-responsive">
                                                <table class="table players-table">
                                                    <thead>
                                                        <tr>
                                                            <th width="80">N°</th>
                                                            <th width="80">Photo</th>
                                                            <th>Joueuse</th>
                                                            <th >Équipe</th>
                                                            <th >Âge</th>
                                                            <th >Licence</th>
                                                            <th>Statut</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($joueurs_cat as $index => $joueur): ?>
                                                            <?php
                                                            $age = null;
                                                            if ($joueur['date_naissance']) {
                                                                $age = date_diff(date_create($joueur['date_naissance']), date_create('today'))->y;
                                                            }
                                                            
                                                            // Convertir le chemin uploads/membres vers images/membres
                                                            $photoPath = $joueur['photo'];
                                                            if ($photoPath && strpos($photoPath, 'uploads/membres/') === 0) {
                                                                $photoPath = str_replace('uploads/membres/', 'images/membres/', $photoPath);
                                                            }
                                                            
                                                            $photoHtml = $joueur['photo'] && file_exists(__DIR__ . '/' . $photoPath)
                                                                ? '<img src="serve_image.php?file=' . urlencode(basename($photoPath)) . '" alt="' . htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) . '" class="player-photo">'
                                                                : '<div class="player-avatar">' . strtoupper(substr($joueur['prenom'], 0, 1) . substr($joueur['nom'], 0, 1)) . '</div>';
                                                            
                                                            $statusHtml = $joueur['statut'] === 'actif' 
                                                                ? '<span class="status-active"><i class="fas fa-check-circle me-1"></i>Actif</span>'
                                                                : '<span class="status-pending"><i class="fas fa-clock me-1"></i>En attente</span>';
                                                            ?>
                                                            <tr>
                                                                <td class="player-number"><?= $index + 1 ?></td>
                                                                <td><?= $photoHtml ?></td>
                                                                <td>
                                                                    <div class="player-name"><?= htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']) ?></div>
                                                                </td>
                                                                <td >
                                                                    <span class="badge" style="background: var(--girls-color); color: white;">
                                                                        <?= $joueur['equipe_nom'] ?: 'Non assignée' ?>
                                                                    </span>
                                                                </td>
                                                                <td >
                                                                    <div class="age-info"><?= $age ? $age . ' ans' : 'N/A' ?></div>
                                                                </td>
                                                                <td >
                                                                    <div class="position-info"><?= $joueur['poste'] ?: 'À définir' ?></div>
                                                                </td>
                                                                <td><?= $statusHtml ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gérer l'activation des sous-onglets lors du changement d'onglet principal
        document.addEventListener('DOMContentLoaded', function() {
            const mainTabs = document.querySelectorAll('#genreTab [data-bs-toggle="tab"]');
            
            mainTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    const targetId = e.target.getAttribute('data-bs-target');
                    
                    // Activer le premier sous-onglet de la section
                    setTimeout(() => {
                        const firstSubTab = document.querySelector(`${targetId} .sub-tabs .nav-link:first-child`);
                        if (firstSubTab && !firstSubTab.classList.contains('active')) {
                            firstSubTab.click();
                        }
                    }, 100);
                });
            });
            
            // Animation d'entrée pour les tableaux
            const allTabs = document.querySelectorAll('[data-bs-toggle="tab"]');
            allTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    const target = e.target.getAttribute('data-bs-target');
                    const pane = document.querySelector(target);
                    
                    if (pane) {
                        const table = pane.querySelector('.players-table');
                        if (table) {
                            table.style.opacity = '0';
                            table.style.transform = 'translateY(20px)';
                            
                            setTimeout(() => {
                                table.style.transition = 'all 0.3s ease';
                                table.style.opacity = '1';
                                table.style.transform = 'translateY(0)';
                            }, 50);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
