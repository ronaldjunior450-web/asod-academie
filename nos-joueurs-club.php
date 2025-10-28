<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Effectif - ASOD ACADEMIE</title>
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

        .squad-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .squad-header {
            background: var(--primary-color);
            color: white;
            padding: 20px 30px;
            border-bottom: none;
        }

        .squad-title {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .squad-stats {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
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
            padding: 20px;
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
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
        }

        .player-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
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
            margin: 2px 0 0 0;
        }

        .position-badge {
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .position-gardien { background: #dc3545; }
        .position-defenseur { background: #198754; }
        .position-milieu { background: #fd7e14; }
        .position-attaquant { background: #6f42c1; }

        .team-badge {
            background: var(--accent-color);
            color: var(--dark-color);
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
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

        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group:last-child {
            margin-bottom: 0;
        }

        .filter-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark-color);
        }

        .form-select, .form-control {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.2s ease;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .search-box {
            position: relative;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .no-players {
            text-align: center;
            padding: 60px 20px;
            color: var(--secondary-color);
        }

        .no-players i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .back-link {
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .header-section {
                padding: 40px 0;
            }
            
            .squad-header {
                padding: 15px 20px;
            }
            
            .players-table th,
            .players-table td {
                padding: 15px 10px;
                font-size: 0.9rem;
            }
            
            .player-number {
                font-size: 1rem;
            }
            
            .player-photo,
            .player-avatar {
                width: 40px;
                height: 40px;
            }
        }

        /* Responsive table */
        @media (max-width: 576px) {
            .table-responsive {
                border: none;
            }
            
            .players-table {
                font-size: 0.8rem;
            }
            
            .d-none-mobile {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner"></div>
            <p class="mt-3">Chargement de l'effectif...</p>
        </div>
    </div>

    <!-- Header -->
    <section class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <img src="images/logo.png" alt="ASOD ACADEMIE" class="club-logo">
                </div>
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">EFFECTIF 2024/2025</h1>
                    <p class="lead mb-0">ASOD ACADEMIE - Association Sportive Oeil du Défi</p>
                    <div class="mt-3" id="globalStats">
                        <span class="badge bg-light text-dark me-2 px-3 py-2">
                            <i class="fas fa-users me-1"></i>
                            <span id="totalPlayers">0</span> Joueurs
                        </span>
                        <span class="badge bg-light text-dark me-2 px-3 py-2">
                            <i class="fas fa-male me-1"></i>
                            <span id="totalBoys">0</span> Garçons
                        </span>
                        <span class="badge bg-light text-dark px-3 py-2">
                            <i class="fas fa-female me-1"></i>
                            <span id="totalGirls">0</span> Filles
                        </span>
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
        <!-- Filtres -->
        <div class="filters-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="filter-group">
                        <label class="filter-label">Rechercher un joueur</label>
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput" placeholder="Nom ou prénom...">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="filter-group">
                        <label class="filter-label">Catégorie</label>
                        <select class="form-select" id="categoryFilter">
                            <option value="">Toutes les catégories</option>
                            <option value="garcon">Garçons</option>
                            <option value="fille">Filles</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="filter-group">
                        <label class="filter-label">Équipe</label>
                        <select class="form-select" id="teamFilter">
                            <option value="">Toutes les équipes</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="filter-group">
                        <label class="filter-label">&nbsp;</label>
                        <button class="btn btn-outline-primary w-100" id="resetFilters">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Équipe Masculine -->
        <div class="squad-container" id="boysSquad" style="display: none;">
            <div class="squad-header">
                <h2 class="squad-title">
                    <i class="fas fa-male me-2"></i>
                    ÉQUIPE MASCULINE
                </h2>
                <div class="squad-stats" id="boysStats">
                    <span id="boysCount">0</span> joueurs
                </div>
            </div>
            <div class="table-responsive">
                <table class="table players-table">
                    <thead>
                        <tr>
                            <th width="80">N°</th>
                            <th width="80">Photo</th>
                            <th>Joueur</th>
                            <th class="d-none-mobile">Position</th>
                            <th class="d-none-mobile">Équipe</th>
                            <th class="d-none-mobile">Âge</th>
                            <th class="d-none-mobile">Poste</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody id="boysTableBody">
                        <!-- Les joueurs seront ajoutés ici -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Équipe Féminine -->
        <div class="squad-container" id="girlsSquad" style="display: none;">
            <div class="squad-header">
                <h2 class="squad-title">
                    <i class="fas fa-female me-2"></i>
                    ÉQUIPE FÉMININE
                </h2>
                <div class="squad-stats" id="girlsStats">
                    <span id="girlsCount">0</span> joueuses
                </div>
            </div>
            <div class="table-responsive">
                <table class="table players-table">
                    <thead>
                        <tr>
                            <th width="80">N°</th>
                            <th width="80">Photo</th>
                            <th>Joueuse</th>
                            <th class="d-none-mobile">Position</th>
                            <th class="d-none-mobile">Équipe</th>
                            <th class="d-none-mobile">Âge</th>
                            <th class="d-none-mobile">Poste</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody id="girlsTableBody">
                        <!-- Les joueuses seront ajoutées ici -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Aucun joueur -->
        <div class="no-players" id="noPlayers" style="display: none;">
            <i class="fas fa-users"></i>
            <h4>Aucun joueur trouvé</h4>
            <p>Modifiez vos critères de recherche ou contactez l'administration.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        class SquadManager {
            constructor() {
                this.allPlayers = [];
                this.filteredPlayers = [];
                this.currentFilters = {
                    search: '',
                    category: '',
                    team: ''
                };
                this.init();
            }

            init() {
                this.bindEvents();
                this.loadTeams();
                this.loadPlayers();
            }

            bindEvents() {
                // Recherche
                document.getElementById('searchInput').addEventListener('input', (e) => {
                    this.currentFilters.search = e.target.value.toLowerCase();
                    this.filterPlayers();
                });

                // Filtres
                document.getElementById('categoryFilter').addEventListener('change', (e) => {
                    this.currentFilters.category = e.target.value;
                    this.filterPlayers();
                });

                document.getElementById('teamFilter').addEventListener('change', (e) => {
                    this.currentFilters.team = e.target.value;
                    this.filterPlayers();
                });

                // Reset
                document.getElementById('resetFilters').addEventListener('click', () => {
                    this.resetFilters();
                });
            }

            async loadTeams() {
                try {
                    const response = await fetch('php/api_membres_public.php?limite=1');
                    const data = await response.json();
                    
                    if (data.success && data.equipes_disponibles) {
                        const select = document.getElementById('teamFilter');
                        data.equipes_disponibles.forEach(team => {
                            const option = document.createElement('option');
                            option.value = team.id;
                            option.textContent = `${team.nom} (${team.nb_joueurs})`;
                            select.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Erreur chargement équipes:', error);
                }
            }

            async loadPlayers() {
                this.showLoading();
                
                try {
                    const response = await fetch('php/api_membres_public.php?limite=100');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.allPlayers = [...data.data.garcons, ...data.data.filles];
                        this.updateGlobalStats(data.data.statistiques);
                        this.filterPlayers();
                    } else {
                        this.showError('Erreur de chargement');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showError('Erreur de connexion');
                } finally {
                    this.hideLoading();
                }
            }

            filterPlayers() {
                this.filteredPlayers = this.allPlayers.filter(player => {
                    // Filtre recherche
                    if (this.currentFilters.search) {
                        const searchTerm = this.currentFilters.search;
                        const fullName = `${player.prenom} ${player.nom}`.toLowerCase();
                        if (!fullName.includes(searchTerm)) {
                            return false;
                        }
                    }

                    // Filtre catégorie
                    if (this.currentFilters.category && player.genre !== this.currentFilters.category) {
                        return false;
                    }

                    // Filtre équipe
                    if (this.currentFilters.team && player.equipe.id != this.currentFilters.team) {
                        return false;
                    }

                    return true;
                });

                this.displayPlayers();
            }

            displayPlayers() {
                const boys = this.filteredPlayers.filter(p => p.genre === 'garcon');
                const girls = this.filteredPlayers.filter(p => p.genre === 'fille');

                // Afficher/masquer les sections
                document.getElementById('boysSquad').style.display = boys.length > 0 ? 'block' : 'none';
                document.getElementById('girlsSquad').style.display = girls.length > 0 ? 'block' : 'none';
                document.getElementById('noPlayers').style.display = (boys.length === 0 && girls.length === 0) ? 'block' : 'none';

                // Mettre à jour les compteurs
                document.getElementById('boysCount').textContent = boys.length;
                document.getElementById('girlsCount').textContent = girls.length;

                // Remplir les tableaux
                this.fillTable('boysTableBody', boys);
                this.fillTable('girlsTableBody', girls);
            }

            fillTable(tableBodyId, players) {
                const tbody = document.getElementById(tableBodyId);
                tbody.innerHTML = '';

                players.forEach((player, index) => {
                    const row = this.createPlayerRow(player, index + 1);
                    tbody.appendChild(row);
                });
            }

            createPlayerRow(player, number) {
                const tr = document.createElement('tr');
                
                // Photo ou avatar
                const photoHtml = player.photo 
                    ? `<img src="${player.photo}" alt="${player.nom_complet}" class="player-photo">`
                    : `<div class="player-avatar">${player.prenom.charAt(0)}${player.nom.charAt(0)}</div>`;

                // Position (simulée basée sur l'âge ou l'équipe)
                const position = this.getPlayerPosition(player);
                const positionClass = this.getPositionClass(position);

                // Âge
                const ageText = player.age ? `${player.age} ans` : 'N/A';

                // Statut membre (pas cotisation)
                let statusHtml = '';
                switch(player.statut) {
                    case 'actif':
                        statusHtml = '<span class="status-active"><i class="fas fa-check-circle me-1"></i>Actif</span>';
                        break;
                    case 'suspendu':
                        statusHtml = '<span class="status-pending"><i class="fas fa-pause me-1"></i>Suspendu</span>';
                        break;
                    case 'radie':
                        statusHtml = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Radié</span>';
                        break;
                    default:
                        statusHtml = '<span class="text-muted"><i class="fas fa-question me-1"></i>Inconnu</span>';
                }

                tr.innerHTML = `
                    <td class="player-number">${number}</td>
                    <td>${photoHtml}</td>
                    <td>
                        <div class="player-name">${player.nom_complet}</div>
                        <div class="player-details d-md-none">
                            ${player.equipe.nom !== 'Sans équipe' ? player.equipe.nom : 'Non assigné'} • ${ageText}
                        </div>
                    </td>
                    <td class="d-none-mobile">
                        <span class="position-badge ${positionClass}">${position}</span>
                    </td>
                    <td class="d-none-mobile">
                        <span class="team-badge">${player.equipe.nom !== 'Sans équipe' ? player.equipe.nom : 'Non assigné'}</span>
                    </td>
                    <td class="d-none-mobile">
                        <div class="age-info">${ageText}</div>
                    </td>
                    <td class="d-none-mobile">
                        <div class="position-info">${player.poste || 'À définir'}</div>
                    </td>
                    <td>${statusHtml}</td>
                `;

                return tr;
            }

            getPlayerPosition(player) {
                // Logique simple pour assigner des positions basées sur l'âge
                if (!player.age) return 'Joueur';
                
                if (player.age <= 10) return 'Jeune';
                if (player.age <= 14) return 'Cadet';
                if (player.age <= 17) return 'Junior';
                return 'Senior';
            }

            getPositionClass(position) {
                const classes = {
                    'Gardien': 'position-gardien',
                    'Défenseur': 'position-defenseur',
                    'Milieu': 'position-milieu',
                    'Attaquant': 'position-attaquant'
                };
                return classes[position] || '';
            }

            updateGlobalStats(stats) {
                document.getElementById('totalPlayers').textContent = stats.total_joueurs;
                document.getElementById('totalBoys').textContent = stats.total_garcons;
                document.getElementById('totalGirls').textContent = stats.total_filles;
            }

            resetFilters() {
                this.currentFilters = { search: '', category: '', team: '' };
                document.getElementById('searchInput').value = '';
                document.getElementById('categoryFilter').value = '';
                document.getElementById('teamFilter').value = '';
                this.filterPlayers();
            }

            showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            showError(message) {
                console.error('Erreur:', message);
                // Vous pouvez ajouter une notification d'erreur ici
            }
        }

        // Initialiser l'application
        document.addEventListener('DOMContentLoaded', () => {
            new SquadManager();
        });
    </script>
</body>
</html>
