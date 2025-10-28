<?php
session_start();
require_once '../php/config.php';
require_once '../php/password_reset_mailer.php';

// Rediriger si déjà connecté
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$message = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    
    if (empty($identifier)) {
        $error = 'Veuillez saisir votre nom d\'utilisateur ou email';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Rechercher l'administrateur par nom d'utilisateur ou email
            $stmt = $pdo->prepare("
                SELECT id, nom_utilisateur, email, nom_complet, reset_attempts, reset_requested_at 
                FROM administrateurs 
                WHERE (nom_utilisateur = ? OR email = ?) AND actif = 1
            ");
            $stmt->execute([$identifier, $identifier]);
            $admin = $stmt->fetch();
            
            if (!$admin) {
                $error = 'Aucun compte administrateur trouvé avec ces informations';
            } else {
                // Vérifier les limites de tentatives
                $can_reset = true;
                if ($admin['reset_requested_at']) {
                    $last_request = strtotime($admin['reset_requested_at']);
                    $one_hour_ago = time() - 3600;
                    
                    if ($last_request > $one_hour_ago && $admin['reset_attempts'] >= 3) {
                        $can_reset = false;
                        $error = 'Trop de tentatives de réinitialisation. Veuillez attendre une heure avant de réessayer.';
                    }
                }
                
                if ($can_reset) {
                    // Générer un token sécurisé
                    $reset_token = generateSecureToken();
                    $expires_at = date('Y-m-d H:i:s', time() + 900); // 15 minutes
                    
                    // Mettre à jour la base de données
                    $stmt = $pdo->prepare("
                        UPDATE administrateurs 
                        SET reset_token = ?, 
                            reset_token_expires = ?, 
                            reset_requested_at = NOW(),
                            reset_attempts = CASE 
                                WHEN reset_requested_at < DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 1 
                                ELSE reset_attempts + 1 
                            END
                        WHERE id = ?
                    ");
                    $stmt->execute([$reset_token, $expires_at, $admin['id']]);
                    
                    // Envoyer l'email de réinitialisation
                    $email_sent = sendPasswordResetEmail($admin['email'], $admin['nom_complet'], $reset_token);
                    
                    if ($email_sent) {
                        $success = true;
                        $message = 'Un email de réinitialisation a été envoyé à votre adresse email. Vérifiez votre boîte de réception.';
                        
                        // Logger l'action
                        logAdminActivity($admin['id'], 'password_reset_requested', 'Demande de réinitialisation de mot de passe');
                    } else {
                        $error = 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer ou contacter l\'administrateur.';
                    }
                }
            }
            
        } catch (Exception $e) {
            $error = 'Une erreur est survenue. Veuillez réessayer.';
            error_log("Erreur forgot password: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Administration ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .forgot-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        
        .forgot-header {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .forgot-header .icon {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .forgot-body {
            padding: 40px 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(220, 53, 69, 0.3);
            color: white;
        }
        
        .btn-back {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
            border-radius: 10px;
            padding: 12px 20px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #6c757d;
            color: white;
            text-decoration: none;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        @media (max-width: 576px) {
            .forgot-container {
                margin: 10px;
            }
            
            .forgot-header {
                padding: 30px 20px;
            }
            
            .forgot-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <?php if ($success): ?>
            <!-- État de succès -->
            <div class="forgot-header">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Email Envoyé !</h2>
                <p class="mb-0">Vérifiez votre boîte de réception</p>
            </div>
            
            <div class="forgot-body text-center">
                <div class="alert alert-success">
                    <i class="fas fa-envelope me-2"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
                
                <div class="alert alert-info">
                    <strong><i class="fas fa-clock me-2"></i>Important :</strong>
                    <ul class="mb-0 mt-2 text-start">
                        <li>Le lien est valable pendant <strong>15 minutes</strong></li>
                        <li>Vérifiez aussi vos <strong>spams</strong></li>
                        <li>Si vous ne recevez rien, réessayez</li>
                    </ul>
                </div>
                
                <a href="login.php" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la connexion
                </a>
            </div>
            
        <?php else: ?>
            <!-- Formulaire de demande -->
            <div class="forgot-header">
                <div class="icon">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Mot de passe oublié</h2>
                <p class="mb-0">Saisissez vos informations pour recevoir un lien de réinitialisation</p>
            </div>
            
            <div class="forgot-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               id="identifier" 
                               name="identifier" 
                               placeholder="Nom d'utilisateur ou email"
                               value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>"
                               required>
                        <label for="identifier">
                            <i class="fas fa-user me-2"></i>Nom d'utilisateur ou Email
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Saisissez votre nom d'utilisateur ou votre adresse email pour recevoir un lien de réinitialisation.
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-reset">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer le lien de réinitialisation
                    </button>
                </form>
                
                <div class="text-center">
                    <a href="login.php" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la connexion
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-focus sur le champ
        document.addEventListener('DOMContentLoaded', function() {
            const identifierField = document.getElementById('identifier');
            if (identifierField) {
                identifierField.focus();
            }
        });
        
        // Animation du bouton
        document.querySelector('.btn-reset')?.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
        });
    </script>
</body>
</html>












