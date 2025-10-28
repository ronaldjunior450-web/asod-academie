<?php
session_start();
require_once '../php/config.php';

// Rediriger si déjà connecté
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE nom_utilisateur = ? AND actif = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['mot_de_passe'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['nom_utilisateur'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_nom'] = $admin['nom_complet'];
                $_SESSION['admin_prenom'] = $admin['nom_complet']; // Pour compatibilité
                $_SESSION['admin_email'] = $admin['email'];
                
                // Mettre à jour la dernière connexion
                $stmt = $pdo->prepare("UPDATE administrateurs SET derniere_connexion = NOW() WHERE id = ?");
                $stmt->execute([$admin['id']]);
                
                // Logger la connexion
                logAdminActivity($admin['id'], 'connexion', 'Connexion réussie');
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect';
                
                // Logger la tentative de connexion échouée
                if ($admin) {
                    logAdminActivity($admin['id'], 'connexion_echouee', 'Tentative de connexion avec mot de passe incorrect');
                }
            }
        } catch (Exception $e) {
            $error = 'Erreur lors de la connexion : ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Administration ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header img {
            max-width: 80px;
            margin-bottom: 20px;
        }
        .login-header h2 {
            margin: 0;
            font-weight: 300;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .btn-login {
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            font-weight: 500;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .forgot-link {
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .forgot-link:hover {
            color: #667eea;
            text-decoration: underline;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .form-floating label {
            color: #6c757d;
        }
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 14px;
        }
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../images/logo.png" alt="ASOD ACADEMIE" class="floating-animation">
            <h2>Administration ASOD ACADEMIE</h2>
            <p class="mb-0">Connectez-vous pour accéder au panel d'administration</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Nom d'utilisateur" required>
                    <label for="username">
                        <i class="fas fa-user me-2"></i>Nom d'utilisateur
                    </label>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                    <label for="password">
                        <i class="fas fa-lock me-2"></i>Mot de passe
                    </label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                    </button>
                </div>
                
                <div class="text-center mt-3">
                    <a href="forgot_password.php" class="forgot-link">
                        <i class="fas fa-key me-1"></i>Mot de passe oublié ?
                    </a>
                </div>
            </form>
        </div>
        
        <div class="login-footer">
            <p class="mb-0">
                <i class="fas fa-shield-alt me-1"></i>
                Connexion sécurisée
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation des champs de formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach((input, index) => {
                input.style.opacity = '0';
                input.style.transform = 'translateY(20px)';
                input.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    input.style.opacity = '1';
                    input.style.transform = 'translateY(0)';
                }, index * 200 + 500);
            });
            
            // Animation du bouton
            const button = document.querySelector('.btn-login');
            button.style.opacity = '0';
            button.style.transform = 'translateY(20px)';
            button.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                button.style.opacity = '1';
                button.style.transform = 'translateY(0)';
            }, 1000);
        });
        
        // Effet de focus sur les champs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>