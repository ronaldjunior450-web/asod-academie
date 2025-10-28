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
        case 'view':
            viewMembre();
            break;
        case 'get':
            getMembre();
            break;
        case 'create':
            createMembre();
            break;
        case 'update':
            updateMembre();
            break;
        case 'transfer':
            transferMembre();
            break;
        case 'radier':
            radierMembre();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}

function viewMembre() {
    global $pdo;
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT m.*, e.nom as equipe_nom, e.categorie as equipe_categorie
        FROM membres m
        LEFT JOIN equipes e ON m.equipe_id = e.id
        WHERE m.id = ? AND m.statut != 'supprime'
    ");
    $stmt->execute([$id]);
    $membre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($membre) {
        echo json_encode(['success' => true, 'membre' => $membre]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Membre non trouvé']);
    }
}

function getMembre() {
    global $pdo;
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT * FROM membres 
        WHERE id = ? AND statut != 'supprime'
    ");
    $stmt->execute([$id]);
    $membre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($membre) {
        echo json_encode(['success' => true, 'membre' => $membre]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Membre non trouvé']);
    }
}

function createMembre() {
    global $pdo;
    
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $email = $_POST['email'] ?? '';
    $equipe_id = $_POST['equipe_id'] ?? null;
    $statut = $_POST['statut'] ?? 'actif';
    $adresse = $_POST['adresse'] ?? '';
    
    // Validation
    if (empty($nom) || empty($prenom) || empty($date_naissance) || empty($genre)) {
        echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants']);
        return;
    }
    
    // Vérifier si l'email existe déjà
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM membres WHERE email = ? AND statut != 'supprime'");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            return;
        }
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO membres (nom, prenom, date_naissance, genre, telephone, email, equipe_id, statut, adresse, date_inscription)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$nom, $prenom, $date_naissance, $genre, $telephone, $email, $equipe_id, $statut, $adresse])) {
        echo json_encode(['success' => true, 'message' => 'Membre créé avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
    }
}

function updateMembre() {
    global $pdo;
    
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
        return;
    }
    
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $email = $_POST['email'] ?? '';
    $equipe_id = $_POST['equipe_id'] ?? null;
    $statut = $_POST['statut'] ?? 'actif';
    $adresse = $_POST['adresse'] ?? '';
    
    // Validation
    if (empty($nom) || empty($prenom) || empty($date_naissance) || empty($genre)) {
        echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants']);
        return;
    }
    
    // Vérifier si l'email existe déjà (pour un autre membre)
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM membres WHERE email = ? AND id != ? AND statut != 'supprime'");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé par un autre membre']);
            return;
        }
    }
    
    $stmt = $pdo->prepare("
        UPDATE membres 
        SET nom = ?, prenom = ?, date_naissance = ?, genre = ?, telephone = ?, email = ?, 
            equipe_id = ?, statut = ?, adresse = ?, date_modification = NOW()
        WHERE id = ? AND statut != 'supprime'
    ");
    
    if ($stmt->execute([$nom, $prenom, $date_naissance, $genre, $telephone, $email, $equipe_id, $statut, $adresse, $id])) {
        echo json_encode(['success' => true, 'message' => 'Membre modifié avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification']);
    }
}

function transferMembre() {
    global $pdo;
    
    $membre_id = $_POST['membre_id'] ?? null;
    $equipe_id = $_POST['equipe_id'] ?? null;
    $motif = $_POST['motif'] ?? '';
    
    if (!$membre_id || !$equipe_id) {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        return;
    }
    
    // Vérifier que l'équipe existe
    $stmt = $pdo->prepare("SELECT id FROM equipes WHERE id = ?");
    $stmt->execute([$equipe_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Équipe non trouvée']);
        return;
    }
    
    // Mettre à jour l'équipe du membre
    $stmt = $pdo->prepare("
        UPDATE membres 
        SET equipe_id = ?, date_modification = NOW()
        WHERE id = ? AND statut != 'supprime'
    ");
    
    if ($stmt->execute([$equipe_id, $membre_id])) {
        echo json_encode(['success' => true, 'message' => 'Membre transféré avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors du transfert']);
    }
}

function radierMembre() {
    global $pdo;
    
    $membre_id = $_POST['membre_id'] ?? null;
    $motif = $_POST['motif'] ?? '';
    
    if (!$membre_id) {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
        return;
    }
    
    if (empty($motif)) {
        echo json_encode(['success' => false, 'message' => 'Motif de radiation obligatoire']);
        return;
    }
    
    // Mettre à jour le statut du membre
    $stmt = $pdo->prepare("
        UPDATE membres 
        SET statut = 'radie', date_modification = NOW()
        WHERE id = ? AND statut != 'supprime'
    ");
    
    if ($stmt->execute([$membre_id])) {
        echo json_encode(['success' => true, 'message' => 'Membre radié avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la radiation']);
    }
}
?>