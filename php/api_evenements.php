<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Récupérer tous les événements sauf ceux supprimés ou en brouillon
    $stmt = $pdo->query("
        SELECT 
            id,
            titre,
            description,
            date_debut,
            date_fin,
            lieu,
            statut,
            type_evenement,
            image_path,
            date_creation
        FROM evenements 
        WHERE statut NOT IN ('supprime', 'brouillon')
        ORDER BY date_debut DESC
    ");
    
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Traiter chaque événement
    foreach ($evenements as $key => $evenement) {
        // Déterminer le statut d'affichage
        $dateDebut = new DateTime($evenement['date_debut']);
        $dateFin = new DateTime($evenement['date_fin']);
        $now = new DateTime();
        
        // Si le statut est explicitement défini dans la base, l'utiliser
        if ($evenement['statut'] === 'annule') {
            $evenement['statut_affichage'] = 'annule';
            $evenement['statut_label'] = 'Annulé';
            $evenement['statut_class'] = 'danger';
        } elseif ($evenement['statut'] === 'termine') {
            $evenement['statut_affichage'] = 'termine';
            $evenement['statut_label'] = 'Terminé';
            $evenement['statut_class'] = 'secondary';
        } elseif ($evenement['statut'] === 'en_cours') {
            $evenement['statut_affichage'] = 'en_cours';
            $evenement['statut_label'] = 'En cours';
            $evenement['statut_class'] = 'success';
        } else {
            // Calculer le statut selon les dates seulement si pas explicitement défini
            if ($now < $dateDebut) {
                $evenement['statut_affichage'] = 'a_venir';
                $evenement['statut_label'] = 'À venir';
                $evenement['statut_class'] = 'info';
            } elseif ($now >= $dateDebut && $now <= $dateFin) {
                $evenement['statut_affichage'] = 'en_cours';
                $evenement['statut_label'] = 'En cours';
                $evenement['statut_class'] = 'success';
            } else {
                $evenement['statut_affichage'] = 'termine';
                $evenement['statut_label'] = 'Terminé';
                $evenement['statut_class'] = 'secondary';
            }
        }
        
        // Formater les dates
        $evenement['date_debut_formatee'] = $dateDebut->format('d/m/Y à H:i');
        $evenement['date_fin_formatee'] = $dateFin->format('d/m/Y à H:i');
        
        // Vérifier si c'est un événement d'une journée
        if ($dateDebut->format('Y-m-d') === $dateFin->format('Y-m-d')) {
            $evenement['date_affichage'] = $dateDebut->format('d/m/Y');
            $evenement['heure_affichage'] = $dateDebut->format('H:i') . ' - ' . $dateFin->format('H:i');
        } else {
            $evenement['date_affichage'] = $dateDebut->format('d/m/Y') . ' - ' . $dateFin->format('d/m/Y');
            $evenement['heure_affichage'] = '';
        }
        
        // Gérer l'image
        if ($evenement['image_path'] && file_exists(__DIR__ . '/../images/evenements/' . $evenement['image_path'])) {
            $evenement['image_url'] = 'images/evenements/' . $evenement['image_path'];
        } else {
            $evenement['image_url'] = null;
        }
        
        // Nettoyer la description
        $evenement['description_courte'] = substr(strip_tags($evenement['description']), 0, 150) . '...';
        
        // Mettre à jour le tableau
        $evenements[$key] = $evenement;
    }
    
    // Organiser par statut
    $evenementsOrganises = [
        'a_venir' => [],
        'en_cours' => [],
        'termine' => [],
        'annule' => []
    ];
    
    foreach ($evenements as $evenement) {
        $evenementsOrganises[$evenement['statut_affichage']][] = $evenement;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $evenementsOrganises,
        'total' => count($evenements)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des événements: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
