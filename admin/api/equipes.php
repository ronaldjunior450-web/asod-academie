<?php
session_start();
require_once '../../php/config.php';

header('Content-Type: application/json');

// Vérifier la session admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            listEquipes();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}

function listEquipes() {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT id, nom, categorie, genre
        FROM equipes 
        WHERE statut = 'actif'
        ORDER BY genre, categorie, nom
    ");
    $stmt->execute();
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'equipes' => $equipes]);
}
?>