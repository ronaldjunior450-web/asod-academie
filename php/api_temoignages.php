<?php
// API pour récupérer les témoignages publiés
header('Content-Type: application/json');
require_once 'php/config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer les témoignages publiés
    $stmt = $pdo->prepare("
        SELECT 
            id, nom, prenom, fonction, entreprise, photo, 
            temoignage, note, ordre_affichage, date_creation
        FROM temoignages 
        WHERE statut = 'publie' 
        ORDER BY ordre_affichage ASC, date_creation DESC
    ");
    $stmt->execute();
    $temoignages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer les statistiques
    $total = count($temoignages);
    $moyenne = 0;
    if ($total > 0) {
        $somme_notes = array_sum(array_column($temoignages, 'note'));
        $moyenne = round($somme_notes / $total, 1);
    }
    
    $data = [
        'temoignages' => $temoignages,
        'total' => $total,
        'note_moyenne' => $moyenne
    ];
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des témoignages: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>

