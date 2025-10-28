<?php
/**
 * Script de traitement des soumissions de témoignages
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

try {
    // Récupérer et valider les données
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $temoignage = trim($_POST['temoignage'] ?? '');
    $note = !empty($_POST['note']) ? (int)$_POST['note'] : null;
    
    // Validation des données
    $errors = [];
    
    if (empty($nom)) {
        $errors[] = 'Le nom est obligatoire';
    } elseif (strlen($nom) < 2) {
        $errors[] = 'Le nom doit contenir au moins 2 caractères';
    } elseif (strlen($nom) > 50) {
        $errors[] = 'Le nom ne peut pas dépasser 50 caractères';
    }
    
    if (empty($prenom)) {
        $errors[] = 'Le prénom est obligatoire';
    } elseif (strlen($prenom) < 2) {
        $errors[] = 'Le prénom doit contenir au moins 2 caractères';
    } elseif (strlen($prenom) > 50) {
        $errors[] = 'Le prénom ne peut pas dépasser 50 caractères';
    }
    
    if (empty($temoignage)) {
        $errors[] = 'Le témoignage est obligatoire';
    } elseif (strlen($temoignage) < 10) {
        $errors[] = 'Le témoignage doit contenir au moins 10 caractères';
    } elseif (strlen($temoignage) > 1000) {
        $errors[] = 'Le témoignage ne peut pas dépasser 1000 caractères';
    }
    
    if ($note !== null && ($note < 1 || $note > 5)) {
        $errors[] = 'La note doit être comprise entre 1 et 5';
    }
    
    // Si il y a des erreurs, les retourner
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Nettoyer les données
    $nom = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');
    $prenom = htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8');
    $temoignage = htmlspecialchars($temoignage, ENT_QUOTES, 'UTF-8');
    
    // Connexion à la base de données
    $pdo = getDBConnection();
    
    // Vérifier si un témoignage similaire existe déjà (protection contre le spam)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM temoignages 
        WHERE nom = ? AND prenom = ? AND temoignage = ? 
        AND date_creation > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $stmt->execute([$nom, $prenom, $temoignage]);
    $existing = $stmt->fetch();
    
    if ($existing['count'] > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Un témoignage identique a déjà été soumis récemment. Veuillez attendre avant de soumettre à nouveau.'
        ]);
        exit;
    }
    
    // Insérer le témoignage en base
    $stmt = $pdo->prepare("
        INSERT INTO temoignages (nom, prenom, temoignage, note, statut, date_creation) 
        VALUES (?, ?, ?, ?, 'en_attente', NOW())
    ");
    
    $result = $stmt->execute([$nom, $prenom, $temoignage, $note]);
    
    if ($result) {
        // Log de l'activité
        error_log("Nouveau témoignage soumis: {$nom} {$prenom} - ID: " . $pdo->lastInsertId());
        
        echo json_encode([
            'success' => true,
            'message' => 'Votre témoignage a été soumis avec succès ! Il sera publié après modération par notre équipe.'
        ]);
    } else {
        throw new Exception('Erreur lors de l\'insertion en base de données');
    }
    
} catch (Exception $e) {
    error_log("Erreur témoignage: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue lors de l\'enregistrement de votre témoignage. Veuillez réessayer plus tard.'
    ]);
}
?>





