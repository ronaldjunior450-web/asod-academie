<?php
// Démarrer la capture de sortie pour éviter les erreurs PHP
ob_start();

// Désactiver l'affichage des erreurs pour éviter la pollution JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Vider le buffer pour éviter les erreurs
ob_clean();

require_once 'config.php';

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Récupérer les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? $_POST['naissance'] ?? '';
    $sexe = $_POST['sexe'] ?? '';
    $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $pays = trim($_POST['pays'] ?? '');
    $equipe = trim($_POST['equipe'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $poste = trim($_POST['poste'] ?? '');
    $nom_parent = trim($_POST['nom_parent'] ?? '');
    $prenom_parent = trim($_POST['prenom_parent'] ?? '');
    $telephone_parent = trim($_POST['telephone_parent'] ?? '');
    $email_parent = trim($_POST['email_parent'] ?? '');
    $profession_parent = trim($_POST['profession_parent'] ?? '');
    $adresse_parent = trim($_POST['adresse_parent'] ?? '');
    $assurance = isset($_POST['assurance']) ? 1 : 0;
    $reglement = isset($_POST['reglement']) ? 1 : 0;
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validation des champs obligatoires (formulaire simplifié)
    $errors = [];
    
    if (empty($nom)) $errors[] = 'Le nom est obligatoire';
    if (empty($prenom)) $errors[] = 'Le prénom est obligatoire';
    if (empty($date_naissance)) $errors[] = 'La date de naissance est obligatoire';
    if (empty($sexe)) $errors[] = 'Le sexe est obligatoire';
    if (empty($telephone)) $errors[] = 'Le téléphone est obligatoire';
    if (empty($email)) $errors[] = 'L\'email est obligatoire';
    if (empty($adresse)) $errors[] = 'L\'adresse est obligatoire';
    if (empty($ville)) $errors[] = 'La ville est obligatoire';
    if (empty($pays)) $errors[] = 'Le pays est obligatoire';
    if (empty($equipe)) $errors[] = 'L\'équipe est obligatoire';
    if (empty($genre)) $errors[] = 'Le genre est obligatoire';
    if (!$assurance) $errors[] = 'L\'assurance personnelle doit être acceptée';
    if (!$reglement) $errors[] = 'Le règlement intérieur doit être accepté';
    
    // Les champs parentaux sont optionnels dans le formulaire simplifié
    // Ils seront complétés lors de la validation par l'admin
    
    // Validation de l'email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email n\'est pas valide';
    }
    
    // Validation de l'email parent
    if (!empty($email_parent) && !filter_var($email_parent, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email du parent n\'est pas valide';
    }
    
    // Vérifier si l'email existe déjà
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM inscriptions WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cet email est déjà utilisé pour une inscription';
        }
    }
    
    // Si des erreurs existent, les retourner
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'error' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Utiliser directement le nom de l'équipe (méthode simple)
    $equipe_nom = $equipe;
    
    // Déterminer la catégorie basée sur le nom de l'équipe (correspondant à l'admin)
    $categorie = '';
    if ($equipe === 'U11') {
        $categorie = 'U11';
    } elseif ($equipe === 'U13') {
        $categorie = 'U13';
    } elseif ($equipe === 'U15') {
        $categorie = 'U15';
    } elseif ($equipe === 'U17') {
        $categorie = 'U17';
    } elseif ($equipe === 'U20') {
        $categorie = 'U20';
    } elseif ($equipe === 'Seniors') {
        $categorie = 'Seniors';
    } else {
        $categorie = 'Inconnue';
    }
    
    // Calculer l'âge pour validation
    $age = date_diff(date_create($date_naissance), date_create('today'))->y;
    
    // Générer un numéro CIP unique
    $numero_cip = 'CIP' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Insérer dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO inscriptions (
            nom, prenom, date_naissance, sexe, lieu_naissance, telephone, email,
            adresse, ville, code_postal, pays, equipe, genre, poste,
            nom_parent, prenom_parent, telephone_parent, email_parent,
            profession_parent, adresse_parent, assurance, reglement, newsletter,
            categorie, numero_cip, statut, date_inscription
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente', NOW()
        )
    ");
    
    $result = $stmt->execute([
        $nom, $prenom, $date_naissance, $sexe, $lieu_naissance, $telephone, $email,
        $adresse, $ville, $code_postal, $pays, $equipe_nom, $genre, $poste,
        $nom_parent, $prenom_parent, $telephone_parent, $email_parent,
        $profession_parent, $adresse_parent, $assurance, $reglement, $newsletter,
        $categorie, $numero_cip
    ]);
    
    if ($result) {
        // Récupérer l'ID de l'inscription créée
        $inscription_id = $pdo->lastInsertId();
        
        // Tenter l'envoi des emails sans faire échouer l'inscription
        $emailWarning = null;
        try {
            require_once 'email_config.php';
            
            // Email de confirmation au candidat
            $email_content = getInscriptionConfirmationTemplate($nom, $prenom, $email, $telephone, $equipe, $inscription_id);
            sendEmail($email, "Confirmation d'inscription ASOD ACADEMIE", $email_content);
            
            // Notification à l'admin
            $admin_email_content = getInscriptionNotificationTemplate($nom, $prenom, $email, $telephone, $equipe, $inscription_id);
            sendEmail(ADMIN_EMAIL, "Nouvelle inscription reçue", $admin_email_content);
        } catch (Exception $mailEx) {
            // Ne pas interrompre le flux : journaliser et renvoyer un avertissement côté client
            $emailWarning = 'Inscription enregistrée, mais l\'email n\'a pas pu être envoyé pour le moment.';
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Inscription enregistrée avec succès ! Nous vous contacterons bientôt.',
            'numero_cip' => $numero_cip,
            'warning' => $emailWarning
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Erreur lors de l\'enregistrement de l\'inscription'
        ]);
    }
    
} catch (Exception $e) {
    // Nettoyer le buffer avant d'envoyer la réponse d'erreur
    ob_clean();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}

// S'assurer qu'aucun contenu supplémentaire n'est envoyé
ob_end_flush();
?>

