<?php
/**
 * API pour récupérer les actualités publiées pour le site public
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=asod_fc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les actualités publiées
    $stmt = $pdo->prepare("
        SELECT 
            id,
            titre,
            resume,
            contenu,
            image_path,
            categorie,
            auteur,
            date_creation,
            date_publication,
            statut
        FROM actualites 
        WHERE statut = 'publie' 
        ORDER BY date_publication DESC, date_creation DESC
        LIMIT 10
    ");
    
    $stmt->execute();
    $actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Traiter les données pour le site public
    foreach ($actualites as &$actualite) {
        // Nettoyer le contenu HTML
        $actualite['contenu'] = strip_tags($actualite['contenu']);
        $actualite['contenu'] = html_entity_decode($actualite['contenu']);
        
        // Limiter le résumé
        if (empty($actualite['resume'])) {
            $actualite['resume'] = substr($actualite['contenu'], 0, 150) . '...';
        }
        
        // Formater les dates
        $actualite['date_creation_formatted'] = date('d/m/Y', strtotime($actualite['date_creation']));
        $actualite['date_publication_formatted'] = $actualite['date_publication'] ? 
            date('d/m/Y', strtotime($actualite['date_publication'])) : null;
        
        // URL de l'image
        $actualite['image_url'] = $actualite['image_path'] ? 
            'images/news/' . basename($actualite['image_path']) : null;
        
        // URL de détail
        $actualite['detail_url'] = 'news_detail.php?id=' . $actualite['id'];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $actualites,
        'count' => count($actualites)
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des actualités: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>


