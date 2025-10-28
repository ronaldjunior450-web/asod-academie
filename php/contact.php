<?php
// Script de traitement du formulaire de contact
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

ob_clean();

require_once 'config.php';

// Vérifier si c'est une soumission POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Récupérer et valider les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation des données
    $errors = [];
    
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email est requis et doit être valide";
    }
    
    if (empty($sujet)) {
        $errors[] = "Le sujet est requis";
    }
    
    if (empty($message)) {
        $errors[] = "Le message est requis";
    }
    
    // Si il y a des erreurs, les retourner en JSON
    if (!empty($errors)) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'error' => implode(' | ', $errors)
        ]);
        exit;
    }
    
    // Insérer le message dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO contacts (nom, prenom, email, telephone, sujet, message, date_creation, statut) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), 'nouveau')
    ");
    
    $result = $stmt->execute([$nom, $prenom, $email, $telephone, $sujet, $message]);
    
    if ($result) {
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Message envoyé avec succès ! Nous vous répondrons bientôt.'
        ]);
    } else {
        throw new Exception("Erreur lors de l'insertion en base de données");
    }
    
} catch (Exception $e) {
    ob_clean();
    error_log("Erreur contact.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur est survenue. Veuillez réessayer.'
    ]);
}

ob_end_flush();
?>