<?php
/**
 * API pour récupérer les actualités du site principal
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Paramètres de la requête
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $categorie = isset($_GET['categorie']) ? sanitizeInput($_GET['categorie']) : null;
    $statut = isset($_GET['statut']) ? sanitizeInput($_GET['statut']) : 'publie';
    
    // Construire la requête
    $whereConditions = ["statut = ?"];
    $params = [$statut];
    
    if ($categorie && $categorie !== 'tous') {
        $whereConditions[] = "categorie = ?";
        $params[] = $categorie;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    
    $stmt = $pdo->prepare("SELECT id, titre, resume, contenu, image_path, auteur, categorie, date_publication, date_modification FROM actualites $whereClause ORDER BY date_publication DESC LIMIT ?");
    $params[] = $limit;
    $stmt->execute($params);
    
    $actualites = $stmt->fetchAll();
    
    // Formater les données pour le site
    $result = [];
    foreach ($actualites as $actualite) {
        $result[] = [
            'id' => $actualite['id'],
            'titre' => $actualite['titre'],
            'resume' => $actualite['resume'],
            'contenu' => $actualite['contenu'],
            'image' => $actualite['image_path'] ? 'http://localhost/ges_asod/' . $actualite['image_path'] : null,
            'auteur' => $actualite['auteur'],
            'categorie' => $actualite['categorie'],
            'date_publication' => $actualite['date_publication'],
            'date_modification' => $actualite['date_modification'],
            'url' => 'http://localhost/ges_asod/actualite.php?id=' . $actualite['id']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'count' => count($result),
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




