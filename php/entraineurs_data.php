<?php
require_once 'config.php';

function getEntraineursData() {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->query("
            SELECT e.*, 
                   COUNT(ee.id) as nb_equipes,
                   GROUP_CONCAT(eq.nom SEPARATOR ', ') as equipes_noms
            FROM entraineurs e
            LEFT JOIN equipe_entraineurs ee ON e.id = ee.entraineur_id AND ee.actif = 1
            LEFT JOIN equipes eq ON ee.equipe_id = eq.id AND eq.actif = 1
            WHERE e.actif = 1
            GROUP BY e.id
            ORDER BY e.specialite, e.nom, e.prenom
            LIMIT 6
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getEntraineursStats() {
    $pdo = getDBConnection();
    
    try {
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN actif = 1 THEN 1 END) as actifs,
                COUNT(DISTINCT specialite) as specialites
            FROM entraineurs
        ");
        return $stmt->fetch();
    } catch (Exception $e) {
        return ['total' => 0, 'actifs' => 0, 'specialites' => 0];
    }
}
?>


