<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .event-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .event-image {
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
        }
        
        .event-status {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
        }
        
        .event-date {
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
            color: white;
            padding: 10px 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .filter-btn {
            border-radius: 25px;
            padding: 8px 20px;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
            border-color: #0d6efd;
            color: white;
        }
        
        .loading-spinner {
            display: none;
        }
        
        .no-events {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .event-description {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="ASOD ACADEMIE" class="me-2" style="height: 40px; width: auto;">
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
                        <a class="nav-link" href="nos-joueurs.php">Nos Joueurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="entraineurs.php">Nos Entraîneurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nos-equipes.php">Nos Équipes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="evenements.php">Événements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5" style="margin-top: 76px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-calendar-alt me-3"></i>Nos Événements
                    </h1>
                    <p class="lead mb-4">
                        Découvrez tous les événements, matchs, tournois et activités de l'ASOD ACADEMIE
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <img src="images/logo.png" alt="ASOD ACADEMIE" class="mb-3" style="height: 80px; width: auto; opacity: 0.9;">
                    <i class="fas fa-calendar-check fa-4x opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtres -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="text-center">
                <h3 class="mb-4">Filtrer les événements</h3>
                <div class="d-flex flex-wrap justify-content-center">
                    <button class="btn btn-outline-primary filter-btn active" data-filter="all">
                        <i class="fas fa-list me-2"></i>Tous
                    </button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="a_venir">
                        <i class="fas fa-clock me-2"></i>À venir
                    </button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="en_cours">
                        <i class="fas fa-play me-2"></i>En cours
                    </button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="termine">
                        <i class="fas fa-check me-2"></i>Terminés
                    </button>
                    <button class="btn btn-outline-primary filter-btn" data-filter="annule">
                        <i class="fas fa-times me-2"></i>Annulés
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenu des événements -->
    <section class="py-5">
        <div class="container">
            <!-- Loading -->
            <div class="loading-spinner text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3">Chargement des événements...</p>
            </div>

            <!-- Événements -->
            <div id="evenements-container">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>

            <!-- Message si aucun événement -->
            <div id="no-events" class="no-events" style="display: none;">
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <h4>Aucun événement trouvé</h4>
                <p>Il n'y a actuellement aucun événement correspondant à votre filtre.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="d-flex align-items-center">
                        <img src="images/logo.png" alt="ASOD ACADEMIE" class="me-2" style="height: 30px; width: auto;">
                        ASOD ACADEMIE
                    </h5>
                    <p class="mb-0">Votre académie de football de référence</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2024 ASOD ACADEMIE. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allEvents = [];
        let currentFilter = 'all';

        // Charger les événements
        async function loadEvenements() {
            const loadingSpinner = document.querySelector('.loading-spinner');
            const container = document.getElementById('evenements-container');
            const noEvents = document.getElementById('no-events');
            
            loadingSpinner.style.display = 'block';
            container.innerHTML = '';
            noEvents.style.display = 'none';

            try {
                const response = await fetch('php/api_evenements.php');
                const data = await response.json();
                
                if (data.success) {
                    allEvents = data.data;
                    displayEvents();
                } else {
                    throw new Error(data.error || 'Erreur lors du chargement');
                }
            } catch (error) {
                console.error('Erreur:', error);
                container.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Erreur lors du chargement des événements: ${error.message}
                    </div>
                `;
            } finally {
                loadingSpinner.style.display = 'none';
            }
        }

        // Afficher les événements
        function displayEvents() {
            const container = document.getElementById('evenements-container');
            const noEvents = document.getElementById('no-events');
            
            let eventsToShow = [];
            
            if (currentFilter === 'all') {
                eventsToShow = [
                    ...allEvents.a_venir,
                    ...allEvents.en_cours,
                    ...allEvents.termine,
                    ...allEvents.annule
                ];
            } else {
                eventsToShow = allEvents[currentFilter] || [];
            }

            if (eventsToShow.length === 0) {
                noEvents.style.display = 'block';
                container.innerHTML = '';
                return;
            }

            noEvents.style.display = 'none';
            
            const eventsHtml = eventsToShow.map(event => `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card event-card h-100">
                        <div class="position-relative">
                            ${event.image_url ? 
                                `<img src="${event.image_url}" class="card-img-top event-image" alt="${event.titre}">` :
                                `<div class="event-image d-flex align-items-center justify-content-center">
                                    <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                </div>`
                            }
                            <span class="badge bg-${event.statut_class} event-status">
                                ${event.statut_label}
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${event.titre}</h5>
                            <div class="event-date">
                                <i class="fas fa-calendar me-2"></i>
                                <strong>${event.date_affichage}</strong>
                                ${event.heure_affichage ? `<br><i class="fas fa-clock me-2"></i>${event.heure_affichage}` : ''}
                            </div>
                            ${event.lieu ? `<p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>${event.lieu}</p>` : ''}
                            <p class="card-text event-description">${event.description_courte}</p>
                            <div class="mt-auto">
                                <button class="btn btn-outline-primary btn-sm" onclick="showEventDetails(${event.id})">
                                    <i class="fas fa-eye me-2"></i>Voir détails
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = `<div class="row">${eventsHtml}</div>`;
        }

        // Gestion des filtres
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Retirer la classe active de tous les boutons
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');
                
                currentFilter = this.getAttribute('data-filter');
                displayEvents();
            });
        });

        // Afficher les détails d'un événement
        function showEventDetails(eventId) {
            // Pour l'instant, on affiche juste une alerte
            // Plus tard, on pourra créer une modal ou rediriger vers une page détail
            alert('Fonctionnalité de détails à implémenter pour l\'événement ID: ' + eventId);
        }

        // Charger les événements au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            loadEvenements();
        });
    </script>
</body>
</html>
