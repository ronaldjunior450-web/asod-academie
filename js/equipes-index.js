/**
 * Script pour afficher les équipes sur la page d'accueil
 * Version simplifiée sans chargement dynamique
 */

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('equipes-content-index');
    if (!container) return;
    
    // Afficher un message simple avec un lien vers la page dédiée
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-4">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h3 class="text-primary">Nos Équipes</h3>
                                <p class="lead text-muted">
                                    Découvrez l'organisation de notre académie avec nos équipes structurées par catégories d'âge.
                                </p>
                            </div>
                            
                            <div class="row text-center mb-4">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <i class="fas fa-male fa-2x text-primary mb-2"></i>
                                        <h5>Équipes Garçons</h5>
                                        <p class="text-muted small">U11, U13, U15, U17, U20</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <i class="fas fa-female fa-2x text-danger mb-2"></i>
                                        <h5>Équipes Filles</h5>
                                        <p class="text-muted small">U11, U13, U15, U17</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <i class="fas fa-whistle fa-2x text-success mb-2"></i>
                                        <h5>Entraîneurs Qualifiés</h5>
                                        <p class="text-muted small">Encadrement professionnel</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-center gap-3">
                                <a href="nos-equipes.php" class="btn btn-primary">
                                    <i class="fas fa-users me-2"></i>
                                    Voir toutes les équipes
                                </a>
                                <!-- Bouton Voir nos joueurs retiré -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
});





