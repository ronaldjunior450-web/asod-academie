<?php
// Formulaire d'ajout de membre avec liste optimisée pour le Bénin
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Ajouter un nouveau membre</h5>
        <button class="btn btn-sm btn-outline-secondary" onclick="loadSection('membres')">
            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
        </button>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="mvc_router.php?controller=Membres&action=ajouter">
            
            <!-- Photo -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo du membre</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="form-text text-muted">Formats acceptés : JPG, PNG, GIF (max 2MB)</small>
                    </div>
                </div>
            </div>
            
            <!-- Informations personnelles obligatoires -->
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Informations personnelles (obligatoires)</h6>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance *</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone *</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Adresse (obligatoire)</h6>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse *</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="3" required></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Informations complémentaires optionnelles -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="text-secondary mb-3">Informations complémentaires (optionnelles)</h6>
                    <div class="mb-3">
                        <label for="lieu_naissance" class="form-label">Lieu de naissance</label>
                        <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance">
                    </div>
                    <div class="mb-3">
                        <label for="pays" class="form-label">Pays</label>
                        <select class="form-select" id="pays" name="pays" onchange="updateVilles()">
                            <option value="">Sélectionner un pays</option>
                            <!-- Pays principal -->
                            <option value="Bénin">Bénin</option>
                            <!-- Pays voisins -->
                            <option value="Nigeria">Nigeria</option>
                            <option value="Togo">Togo</option>
                            <option value="Burkina Faso">Burkina Faso</option>
                            <option value="Niger">Niger</option>
                            <!-- Pays francophones -->
                            <option value="France">France</option>
                            <option value="Sénégal">Sénégal</option>
                            <!-- Autre -->
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ville" class="form-label">Ville</label>
                        <select class="form-select" id="ville" name="ville" onchange="updatePays()">
                            <option value="">Sélectionner une ville</option>
                            <!-- Villes béninoises (pays principal) -->
                            <optgroup label="Bénin">
                                <option value="Cotonou">Cotonou</option>
                                <option value="Porto-Novo">Porto-Novo</option>
                                <option value="Parakou">Parakou</option>
                                <option value="Djougou">Djougou</option>
                                <option value="Abomey-Calavi">Abomey-Calavi</option>
                                <option value="Natitingou">Natitingou</option>
                                <option value="Lokossa">Lokossa</option>
                                <option value="Ouidah">Ouidah</option>
                                <option value="Kandi">Kandi</option>
                                <option value="Savalou">Savalou</option>
                                <option value="Bohicon">Bohicon</option>
                                <option value="Comé">Comé</option>
                                <option value="Malanville">Malanville</option>
                                <option value="Pobè">Pobè</option>
                                <option value="Sakété">Sakété</option>
                            </optgroup>
                            <!-- Villes nigérianes (pays voisin) -->
                            <optgroup label="Nigeria">
                                <option value="Lagos">Lagos</option>
                                <option value="Abuja">Abuja</option>
                                <option value="Kano">Kano</option>
                            </optgroup>
                            <!-- Villes togolaises (pays voisin) -->
                            <optgroup label="Togo">
                                <option value="Lomé">Lomé</option>
                                <option value="Sokodé">Sokodé</option>
                                <option value="Kara">Kara</option>
                            </optgroup>
                            <!-- Villes burkinabées (pays voisin) -->
                            <optgroup label="Burkina Faso">
                                <option value="Ouagadougou">Ouagadougou</option>
                                <option value="Bobo-Dioulasso">Bobo-Dioulasso</option>
                            </optgroup>
                            <!-- Villes nigériennes (pays voisin) -->
                            <optgroup label="Niger">
                                <option value="Niamey">Niamey</option>
                                <option value="Zinder">Zinder</option>
                            </optgroup>
                            <!-- Villes françaises (pays francophone) -->
                            <optgroup label="France">
                                <option value="Paris">Paris</option>
                                <option value="Lyon">Lyon</option>
                                <option value="Marseille">Marseille</option>
                            </optgroup>
                            <!-- Villes sénégalaises (pays francophone) -->
                            <optgroup label="Sénégal">
                                <option value="Dakar">Dakar</option>
                                <option value="Thiès">Thiès</option>
                            </optgroup>
                            <!-- Autres villes -->
                            <optgroup label="Autres">
                                <option value="Autre">Autre</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="code_postal" class="form-label">Code postal</label>
                        <input type="text" class="form-control" id="code_postal" name="code_postal">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-secondary mb-3">Informations sportives (optionnelles)</h6>
                    <div class="mb-3">
                        <label for="poste" class="form-label">Poste de jeu</label>
                        <select class="form-select" id="poste" name="poste">
                            <option value="">Sélectionner un poste</option>
                            <option value="Gardien">Gardien</option>
                            <option value="Défenseur">Défenseur</option>
                            <option value="Milieu">Milieu</option>
                            <option value="Attaquant">Attaquant</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="numero_licence" class="form-label">Numéro de licence FFF</label>
                        <input type="text" class="form-control" id="numero_licence" name="numero_licence">
                    </div>
                    <div class="mb-3">
                        <label for="equipe_id" class="form-label">Équipe</label>
                        <select class="form-select" id="equipe_id" name="equipe_id">
                            <option value="">Sélectionner une équipe</option>
                            <?php
                            try {
                                $pdo = getDBConnection();
                                $stmt = $pdo->query("SELECT * FROM equipes ORDER BY nom");
                                $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($equipes as $equipe) {
                                    echo '<option value="' . $equipe['id'] . '">' . htmlspecialchars($equipe['nom']) . ' (' . $equipe['genre'] . ')</option>';
                                }
                            } catch (Exception $e) {
                                echo '<option value="">Erreur lors du chargement des équipes</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select" id="statut" name="statut">
                            <option value="actif">Actif</option>
                            <option value="suspendu">Suspendu</option>
                            <option value="radie">Radié</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_adhesion" class="form-label">Date d'adhésion</label>
                        <input type="text" class="form-control" id="date_adhesion" name="date_adhesion" value="<?= date('Y-m-d H:i:s') ?>" readonly style="background-color: #f8f9fa;">
                        <small class="form-text text-muted">Attribuée automatiquement à la date et heure actuelles</small>
                    </div>
                    <div class="mb-3">
                        <label for="numero_cip" class="form-label">Numéro CIP</label>
                        <input type="text" class="form-control" id="numero_cip" name="numero_cip">
                    </div>
                </div>
            </div>
            
            <!-- Informations parents optionnelles -->
            <div class="row mt-4">
                <div class="col-12"><h6 class="text-secondary mb-3">Informations parents (optionnelles)</h6></div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom_parent" class="form-label">Nom du parent</label>
                        <input type="text" class="form-control" id="nom_parent" name="nom_parent">
                    </div>
                    <div class="mb-3">
                        <label for="prenom_parent" class="form-label">Prénom du parent</label>
                        <input type="text" class="form-control" id="prenom_parent" name="prenom_parent">
                    </div>
                    <div class="mb-3">
                        <label for="telephone_parent" class="form-label">Téléphone du parent</label>
                        <input type="tel" class="form-control" id="telephone_parent" name="telephone_parent">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email_parent" class="form-label">Email du parent</label>
                        <input type="email" class="form-control" id="email_parent" name="email_parent">
                    </div>
                    <div class="mb-3">
                        <label for="profession_parent" class="form-label">Profession du parent</label>
                        <input type="text" class="form-control" id="profession_parent" name="profession_parent">
                    </div>
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="loadSection('membres')">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Enregistrer le membre
                </button>
            </div>
            
        </form>
    </div>
</div>

<!-- JavaScript pour l'interaction dynamique pays/ville -->
<script>
function updateVilles() {
    const pays = document.getElementById("pays").value;
    const ville = document.getElementById("ville");
    const options = ville.querySelectorAll("option");
    
    // Masquer toutes les options sauf la première
    options.forEach(option => {
        if (option.value === "") {
            option.style.display = "block";
        } else {
            option.style.display = "none";
        }
    });
    
    // Afficher les villes du pays sélectionné
    if (pays) {
        const optgroups = ville.querySelectorAll("optgroup");
        optgroups.forEach(group => {
            if (group.label === pays) {
                const groupOptions = group.querySelectorAll("option");
                groupOptions.forEach(option => {
                    option.style.display = "block";
                });
            }
        });
    }
    
    // Réinitialiser la sélection de ville
    ville.value = "";
}

function updatePays() {
    const ville = document.getElementById("ville").value;
    const pays = document.getElementById("pays");
    
    // Mapping ville -> pays (optimisé pour le Bénin)
    const villePays = {
        // Bénin (pays principal)
        "Cotonou": "Bénin", "Porto-Novo": "Bénin", "Parakou": "Bénin", "Djougou": "Bénin", "Abomey-Calavi": "Bénin", "Natitingou": "Bénin", "Lokossa": "Bénin", "Ouidah": "Bénin", "Kandi": "Bénin", "Savalou": "Bénin", "Bohicon": "Bénin", "Comé": "Bénin", "Malanville": "Bénin", "Pobè": "Bénin", "Sakété": "Bénin",
        // Nigeria (pays voisin)
        "Lagos": "Nigeria", "Abuja": "Nigeria", "Kano": "Nigeria",
        // Togo (pays voisin)
        "Lomé": "Togo", "Sokodé": "Togo", "Kara": "Togo",
        // Burkina Faso (pays voisin)
        "Ouagadougou": "Burkina Faso", "Bobo-Dioulasso": "Burkina Faso",
        // Niger (pays voisin)
        "Niamey": "Niger", "Zinder": "Niger",
        // France (pays francophone)
        "Paris": "France", "Lyon": "France", "Marseille": "France",
        // Sénégal (pays francophone)
        "Dakar": "Sénégal", "Thiès": "Sénégal"
    };
    
    if (ville && villePays[ville]) {
        pays.value = villePays[ville];
    }
}

// Initialiser les sélections au chargement
document.addEventListener("DOMContentLoaded", function() {
    updateVilles();
});
</script>
