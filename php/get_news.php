<?php
/**
 * Script pour récupérer les actualités publiées pour le site principal
 */

header('Content-Type: application/json');

// Configuration de la base de données
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'asod_fc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Récupérer les actualités publiées
    $stmt = $pdo->query("SELECT n.*, u.full_name as author_name FROM news n LEFT JOIN admin_users u ON n.author_id = u.id WHERE n.status = 'published' ORDER BY n.created_at DESC LIMIT 10");
    $news = $stmt->fetchAll();
    
    // Formater les données pour l'affichage
    $formattedNews = [];
    foreach ($news as $article) {
        $formattedNews[] = [
            'id' => $article['id'],
            'title' => $article['title'],
            'content' => $article['content'],
            'image_path' => $article['image_path'],
            'author' => $article['author_name'] ?: 'ASOD ACADEMIE',
            'created_at' => $article['created_at'],
            'date_formatted' => date('d/m/Y', strtotime($article['created_at']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'news' => $formattedNews,
        'count' => count($formattedNews)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>




