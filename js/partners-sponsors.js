/**
 * Script pour charger et afficher les partenaires et sponsors
 */

document.addEventListener('DOMContentLoaded', function() {
    loadPartnersAndSponsors();
});

async function loadPartnersAndSponsors() {
    try {
        const response = await fetch('php/api_partners_sponsors.php');
        const data = await response.json();
        
        if (data.success) {
            displayPartners(data.data.partenaires);
            displaySponsors(data.data.sponsors);
        } else {
            console.error('Erreur lors du chargement des données:', data.error);
            showFallbackContent();
        }
    } catch (error) {
        console.error('Erreur de connexion:', error);
        showFallbackContent();
    }
}

function displayPartners(partenaires) {
    const container = document.getElementById('partenaires-container');
    if (!container) return;
    
    if (partenaires.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Aucun partenaire à afficher pour le moment.</p>';
        return;
    }
    
    let html = '<div class="row">';
    
    partenaires.forEach(partenaire => {
        html += `
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 text-center border-0 shadow-sm" style="transition: transform 0.3s ease;">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            ${partenaire.logo ? 
                                `<img src="${partenaire.logo}" alt="${partenaire.nom}" class="img-fluid" style="max-height: 80px; object-fit: contain;">` :
                                `<div class="bg-light rounded p-3 mb-3">
                                    <i class="fas fa-handshake text-primary" style="font-size: 2rem;"></i>
                                </div>`
                            }
                        </div>
                        <h6 class="card-title text-primary mb-2">${partenaire.nom}</h6>
                        <p class="card-text text-muted small flex-grow-1">${partenaire.description || ''}</p>
                        <div class="mt-auto">
                            <span class="badge bg-info mb-2">${getTypeLabel(partenaire.type_partenaire)}</span>
                            ${partenaire.site_web ? 
                                `<a href="${partenaire.site_web}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> Site web
                                </a>` : ''
                            }
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function displaySponsors(sponsors) {
    const container = document.getElementById('sponsors-container');
    if (!container) return;
    
    if (sponsors.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Aucun sponsor à afficher pour le moment.</p>';
        return;
    }
    
    let html = '<div class="row">';
    
    sponsors.forEach(sponsor => {
        const montant = sponsor.montant_sponsoring ? 
            new Intl.NumberFormat('fr-FR').format(sponsor.montant_sponsoring) + ' FCFA' : '';
        
        html += `
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 text-center border-0 shadow-sm" style="transition: transform 0.3s ease;">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            ${sponsor.logo ? 
                                `<img src="${sponsor.logo}" alt="${sponsor.nom}" class="img-fluid" style="max-height: 80px; object-fit: contain;">` :
                                `<div class="bg-light rounded p-3 mb-3">
                                    <i class="fas fa-star text-warning" style="font-size: 2rem;"></i>
                                </div>`
                            }
                        </div>
                        <h6 class="card-title text-primary mb-2">${sponsor.nom}</h6>
                        <p class="card-text text-muted small flex-grow-1">${sponsor.description || ''}</p>
                        <div class="mt-auto">
                            <span class="badge ${getSponsorTypeClass(sponsor.type_sponsoring)} mb-2">${getSponsorTypeLabel(sponsor.type_sponsoring)}</span>
                            ${montant ? `<p class="text-success fw-bold small mb-2">${montant}</p>` : ''}
                            ${sponsor.site_web ? 
                                `<a href="${sponsor.site_web}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> Site web
                                </a>` : ''
                            }
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function getTypeLabel(type) {
    const labels = {
        'sponsor': 'Sponsor',
        'partenaire_officiel': 'Partenaire Officiel',
        'partenaire_media': 'Partenaire Média',
        'partenaire_technique': 'Partenaire Technique'
    };
    return labels[type] || type;
}

function getSponsorTypeLabel(type) {
    const labels = {
        'principal': 'Sponsor Principal',
        'secondaire': 'Sponsor Secondaire',
        'technique': 'Sponsor Technique',
        'media': 'Sponsor Média'
    };
    return labels[type] || type;
}

function getSponsorTypeClass(type) {
    const classes = {
        'principal': 'bg-danger',
        'secondaire': 'bg-warning',
        'technique': 'bg-info',
        'media': 'bg-purple'
    };
    return classes[type] || 'bg-secondary';
}

function showFallbackContent() {
    const partenairesContainer = document.getElementById('partenaires-container');
    const sponsorsContainer = document.getElementById('sponsors-container');
    
    if (partenairesContainer) {
        partenairesContainer.innerHTML = `
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <div class="bg-light rounded p-3 mb-3">
                                    <i class="fas fa-handshake text-primary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h6 class="card-title text-primary mb-2">Ville de Douala</h6>
                            <p class="card-text text-muted small flex-grow-1">Partenaire officiel de la ville</p>
                            <div class="mt-auto">
                                <span class="badge bg-info mb-2">Partenaire Officiel</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <div class="bg-light rounded p-3 mb-3">
                                    <i class="fas fa-handshake text-primary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h6 class="card-title text-primary mb-2">FECAFOOT</h6>
                            <p class="card-text text-muted small flex-grow-1">Fédération Camerounaise de Football</p>
                            <div class="mt-auto">
                                <span class="badge bg-info mb-2">Partenaire Technique</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    if (sponsorsContainer) {
        sponsorsContainer.innerHTML = `
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <div class="bg-light rounded p-3 mb-3">
                                    <i class="fas fa-star text-warning" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h6 class="card-title text-primary mb-2">Orange Cameroun</h6>
                            <p class="card-text text-muted small flex-grow-1">Sponsor principal du club</p>
                            <div class="mt-auto">
                                <span class="badge bg-danger mb-2">Sponsor Principal</span>
                                <p class="text-success fw-bold small mb-2">5 000 000 FCFA</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <div class="bg-light rounded p-3 mb-3">
                                    <i class="fas fa-star text-warning" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h6 class="card-title text-primary mb-2">MTN Cameroun</h6>
                            <p class="card-text text-muted small flex-grow-1">Sponsor secondaire</p>
                            <div class="mt-auto">
                                <span class="badge bg-warning mb-2">Sponsor Secondaire</span>
                                <p class="text-success fw-bold small mb-2">3 000 000 FCFA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}




