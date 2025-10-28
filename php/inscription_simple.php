<?php
// Version simplifiée sans emails pour tester
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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
    
    // Validation des champs obligatoires
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
    
    // Validation de l'email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email n\'est pas valide';
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
        ob_clean();
        echo json_encode([
            'success' => false,
            'error' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Trouver l'ID de l'équipe correspondante dans la base
    $equipe_id = null;
    $equipe_nom = $equipe;
    $categorie = '';
    
    try {
        // Construire le nom de l'équipe avec le genre
        $equipe_recherche = 'ASOD ' . $equipe . ' ' . $genre;
        
        $stmt_equipe = $pdo->prepare("SELECT id, nom, categorie FROM equipes WHERE nom = ? AND actif = 1");
        $stmt_equipe->execute([$equipe_recherche]);
        $equipe_data = $stmt_equipe->fetch(PDO::FETCH_ASSOC);
        
        if ($equipe_data) {
            $equipe_id = $equipe_data['id'];
            $equipe_nom = $equipe_data['nom'];
            $categorie = $equipe_data['categorie'];
        } else {
            // Fallback si l'équipe n'est pas trouvée
            $equipe_id = null;
            $categorie = $equipe;
        }
    } catch (Exception $e) {
        // En cas d'erreur, utiliser les valeurs par défaut
        $equipe_id = null;
        $categorie = $equipe;
    }
    
    // Générer un numéro CIP unique
    $numero_cip = 'CIP' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Insérer dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO inscriptions (
            nom, prenom, date_naissance, sexe, lieu_naissance, telephone, email,
            adresse, ville, code_postal, pays, equipe_id, genre, poste,
            nom_parent, prenom_parent, telephone_parent, email_parent,
            profession_parent, adresse_parent, assurance, reglement, newsletter,
            categorie, numero_cip, statut, date_inscription
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente', NOW()
        )
    ");
    
    $result = $stmt->execute([
        $nom, $prenom, $date_naissance, $sexe, $lieu_naissance, $telephone, $email,
        $adresse, $ville, $code_postal, $pays, $equipe_id, $genre, $poste,
        $nom_parent, $prenom_parent, $telephone_parent, $email_parent,
        $profession_parent, $adresse_parent, $assurance, $reglement, $newsletter,
        $categorie, $numero_cip
    ]);
    
    if ($result) {
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Inscription enregistrée avec succès ! Nous vous contacterons bientôt.',
            'numero_cip' => $numero_cip
        ]);
    } else {
        ob_clean();
        echo json_encode([
            'success' => false,
            'error' => 'Erreur lors de l\'enregistrement de l\'inscription'
        ]);
    }
    
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}

ob_end_flush();
?>
