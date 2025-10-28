<?php
/**
 * API pour récupérer les joueurs par sexe et catégorie
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Paramètres de la requête
    $genre = isset($_GET['genre']) ? sanitizeInput($_GET['genre']) : null;
    $categorie = isset($_GET['categorie']) ? sanitizeInput($_GET['categorie']) : null;
    $equipe_id = isset($_GET['equipe_id']) ? (int)$_GET['equipe_id'] : null;
    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
    
    // Construire la requête
    $whereConditions = ["m.statut = 'actif'"];
    $params = [];
    
    if ($genre && $genre !== 'tous') {
        $whereConditions[] = "m.genre = ?";
        $params[] = $genre;
    }
    
    if ($categorie && $categorie !== 'tous') {
        $whereConditions[] = "m.categorie = ?";
        $params[] = $categorie;
    }
    
    if ($equipe_id) {
        $whereConditions[] = "m.equipe_id = ?";
        $params[] = $equipe_id;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    
    $stmt = $pdo->prepare("
        SELECT m.*, e.nom as equipe_nom, e.description as equipe_description 
        FROM membres m 
        LEFT JOIN equipes e ON m.equipe_id = e.id 
        $whereClause 
        ORDER BY m.genre ASC, m.categorie ASC, m.nom ASC, m.prenom ASC
        LIMIT ?
    ");
    $params[] = $limite;
    $stmt->execute($params);
    
    $joueurs = $stmt->fetchAll();
    
    // Organiser les données par genre et catégorie
    $result = [
        'garcons' => [],
        'filles' => [],
        'statistiques' => [
            'total_garcons' => 0,
            'total_filles' => 0,
            'total_joueurs' => 0,
            'categories' => []
        ]
    ];
    
    foreach ($joueurs as $joueur) {
        $genre = $joueur['genre'] ?? 'garcon';
        $categorie = $joueur['categorie'] ?? 'N/A';
        
        $joueurData = [
            'id' => $joueur['id'],
            'nom' => $joueur['nom'],
            'prenom' => $joueur['prenom'],
            'genre' => $genre,
            'categorie' => $categorie,
            'equipe_nom' => $joueur['equipe_nom'] ?? 'Sans équipe',
            'equipe_description' => $joueur['equipe_description'] ?? '',
            'date_naissance' => $joueur['date_naissance'],
            'telephone' => $joueur['telephone'],
            'email' => $joueur['email']
        ];
        
        if ($genre === 'fille') {
            $result['filles'][] = $joueurData;
            $result['statistiques']['total_filles']++;
        } else {
            $result['garcons'][] = $joueurData;
            $result['statistiques']['total_garcons']++;
        }
        
        $result['statistiques']['total_joueurs']++;
        
        // Compter par catégorie
        if (!isset($result['statistiques']['categories'][$categorie])) {
            $result['statistiques']['categories'][$categorie] = ['garcons' => 0, 'filles' => 0];
        }
        $genreKey = ($genre === 'garcon') ? 'garcons' : 'filles';
        $result['statistiques']['categories'][$categorie][$genreKey]++;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'count' => count($joueurs),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de base de données',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
