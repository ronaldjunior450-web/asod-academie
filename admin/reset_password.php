<?php
session_start();
require_once '../php/config.php';

// Rediriger si déjà connecté
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$token = $_GET['token'] ?? '';
$message = '';
$error = '';
$success = false;
$admin = null;

// Vérifier le token
if (empty($token)) {
    $error = 'Token de réinitialisation manquant';
} else {
    try {
        $pdo = getDBConnection();
        
        // Vérifier la validité du token
        $stmt = $pdo->prepare("
            SELECT id, nom_utilisateur, email, nom_complet, reset_token_expires 
            FROM administrateurs 
            WHERE reset_token = ? AND actif = 1
        ");
        $stmt->execute([$token]);
        $admin = $stmt->fetch();
        
        if (!$admin) {
            $error = 'Token de réinitialisation invalide ou expiré';
        } elseif ($admin['reset_token_expires'] < date('Y-m-d H:i:s')) {
            $error = 'Token de réinitialisation expiré. Veuillez faire une nouvelle demande.';
            $admin = null;
        }
        
    } catch (Exception $e) {
        $error = 'Erreur lors de la vérification du token';
        error_log("Erreur reset password token: " . $e->getMessage());
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $admin) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password)) {
        $error = 'Veuillez saisir un nouveau mot de passe';
    } elseif (strlen($new_password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        try {
            // Hasher le nouveau mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Mettre à jour le mot de passe et supprimer le token
            $stmt = $pdo->prepare("
                UPDATE administrateurs 
                SET mot_de_passe = ?, 
                    reset_token = NULL, 
                    reset_token_expires = NULL, 
                    reset_requested_at = NULL,
                    reset_attempts = 0
                WHERE id = ?
            ");
            $stmt->execute([$hashed_password, $admin['id']]);
            
            // Logger l'action
            logAdminActivity($admin['id'], 'password_reset_completed', 'Mot de passe réinitialisé avec succès');
            
            $success = true;
            $message = 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.';
            
        } catch (Exception $e) {
            $error = 'Erreur lors de la réinitialisation du mot de passe';
            error_log("Erreur reset password save: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - Administration ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reset-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        
        .reset-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .reset-header.error {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        
        .reset-header.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .reset-header .icon {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .reset-body {
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
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
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
        
        .password-strength {
            margin-top: 10px;
        }
        
        .strength-bar {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .strength-fill {
            height: 100%;
            transition: all 0.3s;
            border-radius: 2px;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        @media (max-width: 576px) {
            .reset-container {
                margin: 10px;
            }
            
            .reset-header {
                padding: 30px 20px;
            }
            
            .reset-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <?php if ($success): ?>
            <!-- État de succès -->
            <div class="reset-header success">
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Mot de passe réinitialisé !</h2>
                <p class="mb-0">Votre nouveau mot de passe est actif</p>
            </div>
            
            <div class="reset-body text-center">
                <div class="alert alert-success">
                    <i class="fas fa-shield-alt me-2"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
                
                <div class="alert alert-info">
                    <strong><i class="fas fa-lightbulb me-2"></i>Conseils de sécurité :</strong>
                    <ul class="mb-0 mt-2 text-start">
                        <li>Utilisez un mot de passe unique</li>
                        <li>Ne le partagez avec personne</li>
                        <li>Déconnectez-vous après utilisation</li>
                    </ul>
                </div>
                
                <a href="login.php" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter maintenant
                </a>
            </div>
            
        <?php elseif ($error): ?>
            <!-- État d'erreur -->
            <div class="reset-header error">
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2>Erreur</h2>
                <p class="mb-0">Impossible de réinitialiser le mot de passe</p>
            </div>
            
            <div class="reset-body text-center">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle me-2"></i>Que faire ?</strong>
                    <ul class="mb-0 mt-2 text-start">
                        <li>Demandez un nouveau lien de réinitialisation</li>
                        <li>Vérifiez que le lien n'a pas expiré</li>
                        <li>Contactez l'administrateur si le problème persiste</li>
                    </ul>
                </div>
                
                <a href="forgot_password.php" class="btn-back">
                    <i class="fas fa-key me-2"></i>Nouvelle demande
                </a>
                
                <a href="login.php" class="btn-back ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Connexion
                </a>
            </div>
            
        <?php else: ?>
            <!-- Formulaire de réinitialisation -->
            <div class="reset-header">
                <div class="icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Nouveau mot de passe</h2>
                <p class="mb-0">Saisissez votre nouveau mot de passe</p>
            </div>
            
            <div class="reset-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <div class="alert alert-info">
                    <strong><i class="fas fa-user me-2"></i>Compte :</strong> 
                    <?= htmlspecialchars($admin['nom_complet'] ?? 'Administrateur') ?>
                </div>
                
                <form method="POST" action="" id="resetForm">
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control" 
                               id="new_password" 
                               name="new_password" 
                               placeholder="Nouveau mot de passe"
                               required
                               minlength="8">
                        <label for="new_password">
                            <i class="fas fa-key me-2"></i>Nouveau mot de passe
                        </label>
                    </div>
                    
                    <div class="password-strength">
                        <small class="text-muted">Force du mot de passe :</small>
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <small class="text-muted" id="strengthText">Saisissez votre mot de passe</small>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="Confirmer le mot de passe"
                               required>
                        <label for="confirm_password">
                            <i class="fas fa-check-double me-2"></i>Confirmer le mot de passe
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Le mot de passe doit contenir au moins 8 caractères.
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-reset" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Enregistrer le nouveau mot de passe
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordField = document.getElementById('new_password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            const submitBtn = document.getElementById('submitBtn');
            
            // Vérification de la force du mot de passe
            function checkPasswordStrength(password) {
                let strength = 0;
                let text = '';
                let color = '';
                
                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                switch(strength) {
                    case 0:
                    case 1:
                        text = 'Très faible';
                        color = '#dc3545';
                        break;
                    case 2:
                        text = 'Faible';
                        color = '#fd7e14';
                        break;
                    case 3:
                        text = 'Moyen';
                        color = '#ffc107';
                        break;
                    case 4:
                        text = 'Fort';
                        color = '#28a745';
                        break;
                    case 5:
                        text = 'Très fort';
                        color = '#20c997';
                        break;
                }
                
                return { strength, text, color };
            }
            
            // Mise à jour de la barre de force
            newPasswordField?.addEventListener('input', function() {
                const result = checkPasswordStrength(this.value);
                const percentage = (result.strength / 5) * 100;
                
                strengthFill.style.width = percentage + '%';
                strengthFill.style.backgroundColor = result.color;
                strengthText.textContent = result.text;
                strengthText.style.color = result.color;
            });
            
            // Vérification de la correspondance des mots de passe
            function checkPasswordMatch() {
                if (confirmPasswordField.value && newPasswordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.setCustomValidity('Les mots de passe ne correspondent pas');
                    confirmPasswordField.classList.add('is-invalid');
                } else {
                    confirmPasswordField.setCustomValidity('');
                    confirmPasswordField.classList.remove('is-invalid');
                }
            }
            
            confirmPasswordField?.addEventListener('input', checkPasswordMatch);
            newPasswordField?.addEventListener('input', checkPasswordMatch);
            
            // Animation du bouton de soumission
            document.getElementById('resetForm')?.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
                submitBtn.disabled = true;
            });
            
            // Auto-focus
            newPasswordField?.focus();
        });
    </script>
</body>
</html>












