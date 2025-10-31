/**
 * Script pour charger et afficher les équipes de façon dynamique
 * Synchronisé avec l'administration
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ne charger les équipes que sur la page nos-equipes.php
    if (document.getElementById('equipes-content')) {
        loadEquipes();
    }
});

async function loadEquipes() {
    const container = document.getElementById('equipes-content');
    if (!container) return;
    
    try {
        // Afficher un indicateur de chargement
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3 text-muted">Chargement des équipes...</p>
            </div>
        `;
        
        const response = await fetch('php/api_equipes.php');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            console.log('Données reçues:', data.data);
            displayEquipes(data.data);
        } else {
            console.error('Erreur API:', data.error || data.message);
            showEquipesFallback();
        }
    } catch (error) {
        console.error('Erreur de chargement des équipes:', error);
        showEquipesFallback();
    }
}

function displayEquipes(data) {
    const container = document.getElementById('equipes-content');
    if (!container) return;
    
    const allTeams = [...data.garcons, ...data.filles];
    
    if (allTeams.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-futbol fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">Aucune équipe disponible</h3>
                <p class="text-muted">Les équipes seront affichées ici une fois configurées dans l'administration.</p>
            </div>
        `;
        return;
    }
    
    // Créer l'affichage avec onglets par genre
    let html = `
        <!-- Navigation par genre -->
        <div class="mb-4">
            <ul class="nav nav-pills justify-content-center" id="genreEquipesTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="garcons-equipes-tab" data-bs-toggle="pill" 
                            data-bs-target="#garcons-equipes" type="button" role="tab"
                            style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; border: none; margin: 0 10px;">
                        <i class="fas fa-male me-2"></i>
                        Équipes Garçons (${data.garcons.length})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="filles-equipes-tab" data-bs-toggle="pill" 
                            data-bs-target="#filles-equipes" type="button" role="tab"
                            style="background: linear-gradient(135deg, #e91e63, #ad1457); color: white; border: none; margin: 0 10px;">
                        <i class="fas fa-female me-2"></i>
                        Équipes Filles (${data.filles.length})
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- Contenu des onglets -->
        <div class="tab-content" id="genreEquipesTabContent">
            <!-- Équipes Garçons -->
            <div class="tab-pane fade show active" id="garcons-equipes" role="tabpanel">
                <div class="row">
                    ${(data.garcons || []).map((equipe, index) => createTeamCard(equipe, index, 'boys')).join('')}
                </div>
            </div>
            
            <!-- Équipes Filles -->
            <div class="tab-pane fade" id="filles-equipes" role="tabpanel">
                <div class="row">
                    ${(data.filles || []).map((equipe, index) => createTeamCard(equipe, index, 'girls')).join('')}
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

function createTeamCard(equipe, index, type) {
    // Vérifications de sécurité
    if (!equipe) {
        console.error('Équipe non définie:', equipe);
        return '';
    }
    
    const delay = (index + 1) * 100;
    const iconClass = getIconForCategorie(equipe.categorie || '');
    const gradientClass = type === 'boys' ? 
        'linear-gradient(135deg, #007bff, #0056b3)' : 
        'linear-gradient(135deg, #e91e63, #ad1457)';
    
    const coachesHtml = equipe.entraineurs_noms && equipe.entraineurs_noms !== null && equipe.entraineurs_noms.length > 0 
        ? `<span class="badge bg-success me-1 mb-1" style="font-size: 0.75rem;">${equipe.entraineurs_noms}</span>`
        : '<span class="text-muted small">Aucun entraîneur assigné</span>';
    
    const horaireHtml = equipe.horaires_entrainement 
        ? `<p class="text-muted small mb-0"><i class="fas fa-clock me-1"></i>${equipe.horaires_entrainement}</p>`
        : '<p class="text-muted small mb-0"><i class="fas fa-clock me-1"></i>Horaires non définis</p>';
    
    return `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="team-card">
                <div class="team-icon" style="background: ${gradientClass};">
                    <i class="${iconClass}"></i>
                </div>
                <h5 class="card-title">${equipe.nom}</h5>
                <p class="text-muted small mb-3">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Catégorie ${equipe.categorie} • ${equipe.age_min}-${equipe.age_max} ans
                </p>
                
                <div class="team-info">
                    <div class="mb-3">
                        <strong class="text-primary small d-block mb-2">
                            <i class="fas fa-user-tie me-1"></i>Encadrement:
                        </strong>
                        ${coachesHtml}
                    </div>
                    
                    <div class="mb-2">
                        <strong class="text-primary small d-block mb-1">
                            <i class="fas fa-clock me-1"></i>Horaires:
                        </strong>
                        ${horaireHtml}
                    </div>
                    
                    ${equipe.description ? `
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted">${equipe.description}</small>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

function getIconForCategorie(categorie) {
    const icons = {
        'U8-U10': 'fas fa-child',
        'U12-U14': 'fas fa-running',
        'U16-U18': 'fas fa-user-graduate',
        'Seniors': 'fas fa-trophy',
        'Feminines': 'fas fa-female'
    };
    return icons[categorie] || 'fas fa-futbol';
}

function getGradientForCategorie(categorie) {
    const gradients = {
        'U8-U10': 'linear-gradient(135deg, #ff9a9e, #fecfef)',
        'U12-U14': 'linear-gradient(135deg, #a8edea, #fed6e3)',
        'U16-U18': 'linear-gradient(135deg, #ffecd2, #fcb69f)',
        'Seniors': 'linear-gradient(135deg, #667eea, #764ba2)',
        'Feminines': 'linear-gradient(135deg, #e91e63, #ad1457)'
    };
    return gradients[categorie] || 'linear-gradient(135deg, #667eea, #764ba2)';
}

function showEquipesFallback() {
    const container = document.getElementById('equipes-content');
    if (!container) return;
    
    container.innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h3 class="text-muted">Erreur de chargement</h3>
            <p class="text-muted">Impossible de charger les équipes depuis l'administration.</p>
            <div class="mt-4">
                <button class="btn btn-primary me-2" onclick="loadEquipes()">
                    <i class="fas fa-refresh me-2"></i>Réessayer
                </button>
                <!-- Bouton vers joueurs retiré -->
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    Vérifiez que les équipes sont configurées dans l'administration
                </small>
            </div>
        </div>
    `;
}
