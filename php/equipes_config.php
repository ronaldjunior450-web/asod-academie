<?php
/**
 * Configuration centralisée des équipes ASOD ACADEMIE
 * Structure évolutive pour garçons et filles séparés
 */

// Configuration des équipes par genre et catégorie
$EQUIPES_CONFIG = [
    'garcons' => [
        'U8-U10' => [
            'nom' => 'U8-U10 Garçons',
            'age_min' => 7,
            'age_max' => 9,
            'description' => 'Initiation au football pour les garçons de 7 à 9 ans',
            'entraineur' => 'Coach Martin',
            'horaires' => 'Mercredi 14h-15h30',
            'couleur' => '#0d6efd', // Bleu
            'icone' => 'fas fa-futbol'
        ],
        'U12-U14' => [
            'nom' => 'U12-U14 Garçons',
            'age_min' => 11,
            'age_max' => 13,
            'description' => 'Développement des compétences techniques pour garçons de 11 à 13 ans',
            'entraineur' => 'Coach Dubois',
            'horaires' => 'Mercredi 16h-17h30',
            'couleur' => '#198754', // Vert
            'icone' => 'fas fa-futbol'
        ],
        'U16-U18' => [
            'nom' => 'U16-U18 Garçons',
            'age_min' => 15,
            'age_max' => 17,
            'description' => 'Préparation compétitive pour garçons de 15 à 17 ans',
            'entraineur' => 'Coach Laurent',
            'horaires' => 'Mardi & Jeudi 18h-19h30',
            'couleur' => '#fd7e14', // Orange
            'icone' => 'fas fa-futbol'
        ],
        'Seniors' => [
            'nom' => 'Seniors Garçons',
            'age_min' => 18,
            'age_max' => 99,
            'description' => 'Équipe principale masculine de l\'association',
            'entraineur' => 'Coach Moreau',
            'horaires' => 'Mardi & Jeudi 19h-21h',
            'couleur' => '#dc3545', // Rouge
            'icone' => 'fas fa-futbol'
        ]
    ],
    'filles' => [
        'U8-U10' => [
            'nom' => 'U8-U10 Filles',
            'age_min' => 7,
            'age_max' => 9,
            'description' => 'Initiation au football pour les filles de 7 à 9 ans',
            'entraineur' => 'Coach Sophie',
            'horaires' => 'Mercredi 14h-15h30',
            'couleur' => '#e83e8c', // Rose
            'icone' => 'fas fa-futbol'
        ],
        'U12-U14' => [
            'nom' => 'U12-U14 Filles',
            'age_min' => 11,
            'age_max' => 13,
            'description' => 'Développement des compétences techniques pour filles de 11 à 13 ans',
            'entraineur' => 'Coach Marie',
            'horaires' => 'Mercredi 16h-17h30',
            'couleur' => '#6f42c1', // Violet
            'icone' => 'fas fa-futbol'
        ],
        'U16-U18' => [
            'nom' => 'U16-U18 Filles',
            'age_min' => 15,
            'age_max' => 17,
            'description' => 'Préparation compétitive pour filles de 15 à 17 ans',
            'entraineur' => 'Coach Julie',
            'horaires' => 'Lundi & Mercredi 18h-19h30',
            'couleur' => '#20c997', // Teal
            'icone' => 'fas fa-futbol'
        ],
        'Seniors' => [
            'nom' => 'Seniors Filles',
            'age_min' => 18,
            'age_max' => 99,
            'description' => 'Équipe principale féminine de l\'association',
            'entraineur' => 'Coach Simon',
            'horaires' => 'Lundi & Mercredi 19h-21h',
            'couleur' => '#fd7e14', // Orange
            'icone' => 'fas fa-futbol'
        ]
    ]
];

/**
 * Obtenir toutes les équipes d'un genre
 */
function getEquipesByGenre($genre) {
    global $EQUIPES_CONFIG;
    return $EQUIPES_CONFIG[$genre] ?? [];
}

/**
 * Obtenir une équipe spécifique
 */
function getEquipe($genre, $categorie) {
    global $EQUIPES_CONFIG;
    return $EQUIPES_CONFIG[$genre][$categorie] ?? null;
}

/**
 * Obtenir toutes les catégories disponibles
 */
function getCategories() {
    return ['U8-U10', 'U12-U14', 'U16-U18', 'Seniors'];
}

/**
 * Obtenir les genres disponibles
 */
function getGenres() {
    return ['garcons', 'filles'];
}

/**
 * Obtenir le nom complet d'une équipe
 */
function getNomEquipe($genre, $categorie) {
    $equipe = getEquipe($genre, $categorie);
    return $equipe ? $equipe['nom'] : '';
}

/**
 * Obtenir les options pour les formulaires
 */
function getEquipesOptions() {
    global $EQUIPES_CONFIG;
    $options = [];
    
    foreach ($EQUIPES_CONFIG as $genre => $equipes) {
        $genreLabel = ($genre === 'garcons') ? 'Garçons' : 'Filles';
        $options[$genreLabel] = [];
        
        foreach ($equipes as $categorie => $equipe) {
            $options[$genreLabel][$categorie] = $equipe['nom'];
        }
    }
    
    return $options;
}

/**
 * Valider une équipe
 */
function validateEquipe($genre, $categorie) {
    $equipe = getEquipe($genre, $categorie);
    return $equipe !== null;
}

/**
 * Obtenir la couleur d'une équipe
 */
function getCouleurEquipe($genre, $categorie) {
    $equipe = getEquipe($genre, $categorie);
    return $equipe ? $equipe['couleur'] : '#6c757d';
}

/**
 * Obtenir l'icône d'une équipe
 */
function getIconeEquipe($genre, $categorie) {
    $equipe = getEquipe($genre, $categorie);
    return $equipe ? $equipe['icone'] : 'fas fa-futbol';
}
?>

