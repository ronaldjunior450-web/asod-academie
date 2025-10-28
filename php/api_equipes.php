<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les équipes avec leurs entraîneurs
    $stmt = $pdo->query("
        SELECT e.*, 
               GROUP_CONCAT(
                   CONCAT(en.prenom, ' ', en.nom) 
                   SEPARATOR ', '
               ) as entraineurs_noms,
               COUNT(m.id) as nb_membres
        FROM equipes e
        LEFT JOIN equipe_entraineurs ee ON e.id = ee.equipe_id AND ee.actif = 1
        LEFT JOIN entraineurs en ON ee.entraineur_id = en.id AND en.actif = 1
        LEFT JOIN membres m ON e.id = m.equipe_id AND m.statut = 'actif'
        WHERE e.actif = 1
        GROUP BY e.id
        ORDER BY e.genre ASC, e.age_min ASC, e.nom ASC
    ");
    
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organiser par genre
    $equipesOrganisees = [
        'garcons' => [],
        'filles' => []
    ];
    
    foreach ($equipes as $equipe) {
        $genre = $equipe['genre'];
        
        // Normaliser le genre
        if (in_array($genre, ['garcons', 'garçon', 'garcon', 'male', 'm', 'masculin'])) {
            $genre = 'garcons';
        } elseif (in_array($genre, ['filles', 'fille', 'femelle', 'f', 'feminin', 'féminin'])) {
            $genre = 'filles';
        } else {
            $genre = 'garcons'; // Par défaut
        }
        
        // Utiliser la vraie catégorie de la base de données
        // La catégorie est déjà définie dans la requête SQL
        
        $equipesOrganisees[$genre][] = $equipe;
    }
    
    // Statistiques
    $stats = [
        'total' => count($equipes),
        'garcons' => count($equipesOrganisees['garcons']),
        'filles' => count($equipesOrganisees['filles']),
        'categories' => array_unique(array_column($equipes, 'categorie'))
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $equipesOrganisees,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de chargement des équipes',
        'message' => $e->getMessage()
    ]);
}
?>





