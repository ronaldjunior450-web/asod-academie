/**
 * Script pour charger les actualités dynamiquement depuis la base de données
 */

document.addEventListener('DOMContentLoaded', function() {
    loadActualites();
});

async function loadActualites() {
    try {
        const response = await fetch('php/get_actualites.php');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            displayActualites(data.data);
        } else {
            displayNoActualites();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des actualités:', error);
        displayError();
    }
}

function displayActualites(actualites) {
    const container = document.getElementById('actualites-container');
    if (!container) return;
    
    container.innerHTML = '';
    
    actualites.forEach((actualite, index) => {
        const actualiteCard = createActualiteCard(actualite, index);
        container.appendChild(actualiteCard);
    });
    
    // Ajouter le bouton "Voir toutes les actualités"
    const viewAllBtn = document.createElement('div');
    viewAllBtn.className = 'row mt-5';
    viewAllBtn.innerHTML = `
        <div class="col-12 text-center">
            <a href="admin/gestion_actualites.php" class="btn btn-primary btn-lg px-4 py-2">
                <i class="fas fa-newspaper me-2"></i>Voir toutes les actualités
            </a>
        </div>
    `;
    container.appendChild(viewAllBtn);
}

function createActualiteCard(actualite, index) {
    const col = document.createElement('div');
    col.className = 'col-lg-4 col-md-6 mb-4';
    col.setAttribute('data-aos', 'fade-up');
    col.setAttribute('data-aos-delay', (index + 1) * 100);
    
    const gradientClass = getGradientClass(index);
    const iconClass = getIconClass(actualite.categorie);
    
    col.innerHTML = `
        <article class="news-card">
            <div class="news-image ${gradientClass}">
                ${actualite.image_url ? 
                    `<img src="${actualite.image_url}" alt="${actualite.titre}" style="width: 100%; height: 100%; object-fit: cover;">` :
                    `<i class="news-icon ${iconClass}"></i>`
                }
            </div>
            <div class="card-body d-flex flex-column">
                <div class="mb-2">
                    <span class="badge bg-primary">${actualite.categorie}</span>
                </div>
                <h5 class="card-title">${actualite.titre}</h5>
                <p class="card-text text-muted">${actualite.resume}</p>
                <div class="mt-auto">
                    <small class="text-muted">
                        <i class="fas fa-user"></i> ${actualite.auteur}
                        <br>
                        <i class="fas fa-calendar"></i> ${actualite.date_publication_formatted}
                    </small>
                    <a href="${actualite.detail_url}" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-arrow-right"></i> Lire plus
                    </a>
                </div>
            </div>
        </article>
    `;
    
    return col;
}

function getGradientClass(index) {
    const gradients = [
        'style="background: var(--gradient-primary);"',
        'style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);"',
        'style="background: var(--gradient-accent);"',
        'style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"',
        'style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"',
        'style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);"'
    ];
    return gradients[index % gradients.length];
}

function getIconClass(categorie) {
    const icons = {
        'general': 'fas fa-newspaper',
        'inscription': 'fas fa-user-plus',
        'equipe': 'fas fa-users',
        'evenement': 'fas fa-calendar',
        'formation': 'fas fa-graduation-cap'
    };
    return icons[categorie] || 'fas fa-newspaper';
}

function displayNoActualites() {
    const container = document.getElementById('actualites-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="col-12 text-center py-5">
            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Aucune actualité disponible</h4>
            <p class="text-muted">Revenez bientôt pour découvrir nos dernières actualités !</p>
        </div>
    `;
}

function displayError() {
    const container = document.getElementById('actualites-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="col-12 text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h4 class="text-warning">Erreur de chargement</h4>
            <p class="text-muted">Impossible de charger les actualités. Veuillez réessayer plus tard.</p>
            <button class="btn btn-primary" onclick="loadActualites()">
                <i class="fas fa-refresh me-2"></i>Réessayer
            </button>
        </div>
    `;
}


