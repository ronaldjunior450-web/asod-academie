<?php
session_start();
require_once '../php/config.php';
// Navigation intégrée directement

// Vérifier la session admin
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Récupérer les informations de l'admin connecté
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'] ?? $_SESSION['admin_nom'] ?? 'Admin';
$admin_nom_complet = $_SESSION['admin_nom'] ?? $_SESSION['admin_nom_complet'] ?? $admin_username;
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Structure de navigation simplifiée
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration ASOD ACADEMIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/admin-responsive.css" rel="stylesheet">
    <style>
        /* Architecture Gmail Style */
        :root {
            --gmail-primary: #1a73e8;
            --gmail-secondary: #5f6368;
            --gmail-background: #f8f9fa;
            --gmail-sidebar: #ffffff;
            --gmail-border: #dadce0;
            --gmail-hover: #f1f3f4;
            --gmail-text: #202124;
            --gmail-text-secondary: #5f6368;
        }

        body {
            font-family: 'Google Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--gmail-background);
            color: var(--gmail-text);
            margin: 0;
            padding: 0;
        }

        /* Header Gmail Style */
        .gmail-header {
            background: var(--gmail-sidebar);
            border-bottom: 1px solid var(--gmail-border);
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
        }

        .gmail-logo {
            display: flex;
            align-items: center;
            margin-right: 24px;
        }

        .gmail-logo img {
            height: 40px;
            margin-right: 12px;
        }

        .gmail-logo h1 {
            font-size: 22px;
            font-weight: 400;
            color: var(--gmail-text);
            margin: 0;
        }

        .gmail-search {
            flex: 1;
            max-width: 720px;
            margin: 0 24px;
        }

        .gmail-search input {
            width: 100%;
            height: 48px;
            border: 1px solid var(--gmail-border);
            border-radius: 24px;
            padding: 0 20px 0 48px;
            font-size: 16px;
            background: var(--gmail-background);
            outline: none;
            transition: all 0.2s ease;
        }

        .gmail-search input:focus {
            border-color: var(--gmail-primary);
            box-shadow: 0 2px 8px rgba(26,115,232,0.2);
        }

        .gmail-search i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gmail-text-secondary);
        }

        .gmail-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .gmail-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gmail-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
        }

        /* Sidebar Gmail Style */
        .gmail-sidebar {
            position: fixed;
            left: 0;
            top: 64px;
            width: 256px;
            height: calc(100vh - 64px);
            background: var(--gmail-sidebar);
            border-right: 1px solid var(--gmail-border);
            overflow-y: auto;
            z-index: 999;
        }
        
        @media (max-width: 768px) {
            .gmail-sidebar {
                height: calc(100vh - 56px);
            }
        }
        
        @media (max-width: 480px) {
            .gmail-sidebar {
                height: calc(100vh - 48px);
            }
        }

        .gmail-nav {
            padding: 8px 0;
        }

        .gmail-nav-item {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: var(--gmail-text);
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .gmail-nav-item:hover {
            background: var(--gmail-hover);
        }

        .gmail-nav-item.active {
            background: #e8f0fe;
            color: var(--gmail-primary);
            border-right: 3px solid var(--gmail-primary);
        }

        .gmail-nav-item i {
            width: 20px;
            margin-right: 16px;
            font-size: 20px;
        }

        .gmail-nav-item .badge {
            margin-left: auto;
            background: var(--gmail-primary);
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        .gmail-nav-divider {
            height: 1px;
            background: var(--gmail-border);
            margin: 8px 24px;
        }

        .gmail-nav-logout {
            color: #d93025 !important;
        }

        .gmail-nav-logout:hover {
            background: #fce8e6 !important;
            color: #d93025 !important;
        }

        /* Main Content Gmail Style */
        .gmail-main {
            margin-left: 256px;
            margin-top: 64px;
            min-height: calc(100vh - 64px);
            background: var(--gmail-background);
        }

        .gmail-content {
            padding: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gmail-breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
            color: var(--gmail-text-secondary);
        }

        .gmail-breadcrumb a {
            color: var(--gmail-primary);
            text-decoration: none;
        }

        .gmail-breadcrumb a:hover {
            text-decoration: underline;
        }

        .gmail-breadcrumb i {
            margin: 0 8px;
            font-size: 12px;
        }

        /* Cards Gmail Style */
        .gmail-card {
            background: var(--gmail-sidebar);
            border: 1px solid var(--gmail-border);
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
            margin-bottom: 24px;
        }

        .gmail-card-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--gmail-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .gmail-card-title {
            font-size: 16px;
            font-weight: 500;
            color: var(--gmail-text);
            margin: 0;
        }

        .gmail-card-body {
            padding: 24px;
        }

        /* Buttons Gmail Style */
        .gmail-btn {
            background: var(--gmail-primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .gmail-btn:hover {
            background: #1557b0;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
        }

        .gmail-btn-secondary {
            background: var(--gmail-sidebar);
            color: var(--gmail-text);
            border: 1px solid var(--gmail-border);
        }

        .gmail-btn-secondary:hover {
            background: var(--gmail-hover);
        }

        /* Loading Gmail Style */
        .gmail-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            color: var(--gmail-text-secondary);
        }

        .gmail-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid var(--gmail-border);
            border-top: 3px solid var(--gmail-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Overlay pour fermer le menu mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .gmail-header {
                height: 56px;
                padding: 0 16px;
            }
            
            .gmail-logo h1 {
                font-size: 18px;
            }
            
            .gmail-logo img {
                height: 32px;
            }
            
            .gmail-user span {
                display: none;
            }
            
            .gmail-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 999;
                top: 56px;
            }

            .gmail-sidebar.open {
                transform: translateX(0);
            }

            .gmail-main {
                margin-left: 0;
                margin-top: 56px;
            }
            
            .gmail-content {
                padding: 16px;
            }

            .gmail-search {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .gmail-header {
                height: 48px;
                padding: 0 8px;
            }
            
            .gmail-logo h1 {
                font-size: 16px;
                margin-left: 4px;
            }
            
            .gmail-logo img {
                height: 28px;
            }
            
            .gmail-main {
                margin-top: 48px;
            }
            
            .gmail-sidebar {
                top: 48px;
            }
            
            .gmail-content {
                padding: 8px;
            }
        }

        /* Mobile Menu Button */
        .gmail-mobile-menu {
            display: none;
            background: none;
            border: none;
            color: var(--gmail-text);
            font-size: 20px;
            cursor: pointer;
            margin-right: 16px;
        }

        @media (max-width: 768px) {
            .gmail-mobile-menu {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Header Gmail Style -->
    <header class="gmail-header">
        <button class="gmail-mobile-menu" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="gmail-logo">
            <img src="../images/logo.png" alt="ASOD ACADEMIE">
            <h1>Administration</h1>
        </div>
        
        <div class="gmail-search" style="position: relative;">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher dans l'administration..." id="globalSearch">
        </div>
        
        <div class="gmail-user">
            <div class="gmail-avatar">
                <?= strtoupper(substr($admin_nom_complet, 0, 1)) ?>
            </div>
            <span><?= htmlspecialchars($admin_nom_complet) ?></span>
            <a href="logout.php" class="gmail-btn gmail-btn-secondary">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </header>

    <!-- Overlay pour fermer le menu mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Sidebar Gmail Style -->
    <nav class="gmail-sidebar" id="gmailSidebar">
        <div class="gmail-nav">
            <button class="gmail-nav-item active" data-section="dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </button>
            
            <button class="gmail-nav-item" data-section="actualites">
                <i class="fas fa-newspaper"></i>
                <span>Actualités</span>
            </button>
            
            <button class="gmail-nav-item" data-section="membres">
                <i class="fas fa-users"></i>
                <span>Membres</span>
            </button>
            
            <button class="gmail-nav-item" data-section="transferts">
                <i class="fas fa-exchange-alt"></i>
                <span>Transferts</span>
            </button>
            
            <button class="gmail-nav-item" data-section="equipes">
                <i class="fas fa-futbol"></i>
                <span>Équipes</span>
            </button>
            
            <button class="gmail-nav-item" data-section="evenements">
                <i class="fas fa-calendar-alt"></i>
                <span>Événements</span>
            </button>
            
            <button class="gmail-nav-item" data-section="inscriptions">
                <i class="fas fa-user-plus"></i>
                <span>Inscriptions</span>
                <span class="badge" id="inscriptions-badge" style="display: none;">0</span>
            </button>
            
            <button class="gmail-nav-item" data-section="entraineurs">
                <i class="fas fa-user-tie"></i>
                <span>Entraîneurs</span>
            </button>
            
            <button class="gmail-nav-item" data-section="bureau">
                <i class="fas fa-building"></i>
                <span>Bureau</span>
            </button>
            
            <button class="gmail-nav-item" data-section="messages">
                <i class="fas fa-envelope"></i>
                <span>Messages</span>
                <span class="badge" id="messages-badge" style="display: none;">0</span>
            </button>
            
            <button class="gmail-nav-item" data-section="galerie">
                <i class="fas fa-images"></i>
                <span>Galerie</span>
            </button>
            
            <button class="gmail-nav-item" data-section="partenaires">
                <i class="fas fa-handshake"></i>
                <span>Partenaires</span>
            </button>
            
            <button class="gmail-nav-item" data-section="sponsors">
                <i class="fas fa-star"></i>
                <span>Sponsors</span>
            </button>
            
            <button class="gmail-nav-item" data-section="formations">
                <i class="fas fa-graduation-cap"></i>
                <span>Formations</span>
            </button>
            
            <button class="gmail-nav-item" data-section="temoignages">
                <i class="fas fa-quote-left"></i>
                <span>Témoignages</span>
            </button>
            
            <button class="gmail-nav-item" data-section="paiements">
                <i class="fas fa-credit-card"></i>
                <span>Paiements</span>
            </button>
            
            <button class="gmail-nav-item" data-section="contact_info">
                <i class="fas fa-info-circle"></i>
                <span>Infos Contact</span>
            </button>
            
            <!-- Déconnexion -->
            <div class="gmail-nav-divider"></div>
            <a href="logout.php" class="gmail-nav-item gmail-nav-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </nav>

    <!-- Main Content Gmail Style -->
    <main class="gmail-main">
        <div class="gmail-content">
            <!-- Breadcrumb -->
            <div class="gmail-breadcrumb">
                <a href="#" onclick="loadSection('dashboard')">Dashboard</a>
                <i class="fas fa-chevron-right"></i>
                <span id="current-section">Dashboard</span>
            </div>
            
            <!-- Dynamic Content Area -->
            <div id="dynamic-content">
                <div class="gmail-loading">
                    <div class="gmail-spinner"></div>
                    <p>Chargement du Dashboard...</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Configuration des sections - Retour à l'ancien système
        if (typeof sections === 'undefined') {
            var sections = {
            'dashboard': {
                title: 'Dashboard',
                url: 'mvc_router.php?controller=Dashboard&action=show'
            },
            'actualites': {
                title: 'Actualités',
                url: 'mvc_router.php?controller=Actualites&action=liste'
            },
            'membres': {
                title: 'Membres',
                url: 'mvc_router.php?controller=Membres&action=liste'
            },
            'equipes': {
                title: 'Équipes',
                url: 'mvc_router.php?controller=Equipes&action=liste'
            },
            'evenements': {
                title: 'Événements',
                url: 'sections/evenements.php'
            },
            'inscriptions': {
                title: 'Inscriptions',
                url: 'sections/inscriptions.php'
            },
            'entraineurs': {
                title: 'Entraîneurs',
                url: 'mvc_router.php?controller=Entraineurs&action=liste'
            },
            'bureau': {
                title: 'Bureau',
                url: 'mvc_router.php?controller=Bureau&action=liste'
            },
            'messages': {
                title: 'Messages',
                url: 'mvc_router.php?controller=Messages&action=liste'
            },
            'galerie': {
                title: 'Galerie',
                url: 'sections/galerie.php'
            },
            'partenaires': {
                title: 'Partenaires',
                url: 'mvc_router.php?controller=Partenaires&action=liste'
            },
            'sponsors': {
                title: 'Sponsors',
                url: 'sections/sponsors.php'
            },
            'formations': {
                title: 'Formations',
                url: 'sections/formations.php'
            },
            'temoignages': {
                title: 'Témoignages',
                url: 'mvc_router.php?controller=Temoignages&action=liste'
            },
            'paiements': {
                title: 'Paiements',
                url: 'mvc_router.php?controller=Paiements&action=liste'
            },
            'contact_info': {
                title: 'Infos Contact',
                url: 'sections/contact_info.php'
            },
            'transferts': {
                title: 'Transferts',
                url: 'controllers/TransfertsController.php'
            }
        };
        }
        
        // Fonction pour exécuter les scripts dans le contenu injecté
        function executeScripts(container) {
            console.log('🔧 Exécution des scripts dans le contenu injecté');
            const scripts = container.querySelectorAll('script');
            console.log('📜 Scripts trouvés:', scripts.length);
            
            scripts.forEach((script, index) => {
                console.log(`📜 Exécution du script ${index + 1}:`, script.textContent.substring(0, 100) + '...');
                try {
                    // Vérifier si le script contient des déclarations de variables globales
                    const scriptContent = script.textContent;
                    if (scriptContent.includes('const sections') || scriptContent.includes('var sections')) {
                        console.log('⚠️ Script principal détecté, ignoré pour éviter les conflits');
                        return;
                    }
                    
                    // Créer un nouveau script et l'exécuter
                    const newScript = document.createElement('script');
                    newScript.textContent = scriptContent;
                    document.head.appendChild(newScript);
                    document.head.removeChild(newScript);
                    console.log(`✅ Script ${index + 1} exécuté avec succès`);
                } catch (error) {
                    console.error(`❌ Erreur lors de l'exécution du script ${index + 1}:`, error);
                }
            });
            
        }
        
        // Fonction pour afficher les notifications
        function showNotification(message, type = 'info') {
            // Créer l'élément de notification
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show`;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Ajouter au DOM
            document.body.appendChild(notification);
            
            // Auto-supprimer après 3 secondes
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }
        
        // Charger une section
        async function loadSection(sectionId, action = null, id = null, genre = null) {
            console.log('loadSection appelé:', sectionId, action, id, genre);
            console.log('🔍 Debug loadSection - Genre reçu:', genre);
            
            if (!sections[sectionId]) {
                console.error('Section non trouvée:', sectionId);
                return;
            }
            
            const section = sections[sectionId];
            const contentDiv = document.getElementById('dynamic-content');
            const breadcrumb = document.getElementById('current-section');
            
            console.log('Section trouvée:', section);
            console.log('ContentDiv:', contentDiv);
            console.log('Breadcrumb:', breadcrumb);
            
            if (!contentDiv) {
                console.error('Element dynamic-content non trouvé');
                return;
            }
            
            // Mettre à jour le breadcrumb
            if (breadcrumb) {
                breadcrumb.textContent = section.title;
            }
            
            // Afficher le loading
            contentDiv.innerHTML = `
                <div class="gmail-loading">
                    <div class="gmail-spinner"></div>
                    <p>Chargement de ${section.title}...</p>
                </div>
            `;
            
            try {
                // Construire l'URL avec les paramètres
                let url = section.url;
                console.log('🔍 URL de base:', url);
                console.log('🔍 Paramètres - action:', action, 'id:', id, 'genre:', genre);
                console.log('🔍 URL contient déjà des paramètres:', url.includes('?'));
                
                // Transmettre SEULEMENT les paramètres GET sûrs de l'URL actuelle
                const urlParams = new URLSearchParams(window.location.search);
                const currentParams = {};
                
                // Récupérer seulement les paramètres sûrs (exclure action, id, success, error)
                for (const [key, value] of urlParams.entries()) {
                    if (!['section', 'action', 'id', 'success', 'error'].includes(key)) {
                        currentParams[key] = value;
                    }
                }
                
                // Ajouter les paramètres sûrs à l'URL
                for (const [key, value] of Object.entries(currentParams)) {
                    url += (url.includes('?') ? '&' : '?') + `${key}=${encodeURIComponent(value)}`;
                }
                
                // Si c'est une URL MVC, remplacer l'action
                if (url.includes('mvc_router.php')) {
                    if (action) {
                        // Remplacer l'action dans l'URL MVC
                        url = url.replace(/action=[^&]*/, `action=${action}`);
                        if (id) {
                            url += `&id=${id}`;
                        }
                        if (genre) {
                            url += `&genre=${genre}`;
                        }
                    } else if (genre) {
                        // Si pas d'action mais un genre, l'ajouter
                        url += `&genre=${genre}`;
                    }
                } else {
                    // Pour les URLs classiques
                    if (action) {
                        url += (url.includes('?') ? '&' : '?') + `action=${action}`;
                        if (id) {
                            url += `&id=${id}`;
                        }
                        if (genre) {
                            url += `&genre=${genre}`;
                        }
                    } else if (genre) {
                        url += (url.includes('?') ? '&' : '?') + `genre=${genre}`;
                    }
                }
                
                // Ajouter un paramètre de cache-busting
                url += (url.includes('?') ? '&' : '?') + `_t=${Date.now()}`;
                console.log('🔍 URL finale générée:', url);
                
                console.log('URL finale:', url);
                
                // Charger le contenu via AJAX
                console.log('Envoi de la requête AJAX...');
                const response = await fetch(url);
                console.log('Réponse reçue:', response.status, response.statusText);
                
                if (response.ok) {
                    const contentType = response.headers.get('content-type');
                    console.log('Content-Type:', contentType);
                    
                    // Vérifier si c'est une réponse JSON (pour les suppressions)
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        console.log('Réponse JSON reçue:', data);
                        
                        if (data.success) {
                            // Afficher le message de succès
                            showNotification(data.message, 'success');
                            
                            // Recharger la galerie avec la catégorie
                            if (data.categorie) {
                                loadSection('galerie', null, null, data.categorie);
                            } else {
                                loadSection('galerie');
                            }
                        } else {
                            showNotification(data.message || 'Erreur inconnue', 'error');
                        }
                    } else {
                        // Traitement normal du contenu HTML
                        const content = await response.text();
                        console.log('Contenu reçu:', content.length, 'caractères');
                        contentDiv.innerHTML = content;
                        console.log('Contenu affiché dans le DOM');
                        
                        // Exécuter les scripts dans le contenu injecté
                        executeScripts(contentDiv);
                    }
                    
                    // Initialiser le formulaire de transfert après injection
                    if (sectionId === 'membres' && action === 'transfer') {
                        console.log('🎯 Détection section transfert - Initialisation différée');
                        
                        // Attendre que le contenu soit complètement injecté
                        setTimeout(function() {
                            console.log('🔧 Initialisation du formulaire de transfert après injection');
                            
                            let attempts = 0;
                            const maxAttempts = 20;
                            
                            function tryInitTransferForm() {
                                attempts++;
                                console.log(`Tentative ${attempts}/${maxAttempts} de recherche des éléments`);
                                
                                const radioInterne = document.getElementById('interne');
                                const radioExterne = document.getElementById('externe');
                                const transferForm = document.getElementById('transferForm');
                                const interne = document.getElementById('transfert-interne');
                                const externe = document.getElementById('transfert-externe');
                                
                                console.log('Éléments recherchés:', {
                                    radioInterne: !!radioInterne,
                                    radioExterne: !!radioExterne,
                                    transferForm: !!transferForm,
                                    interne: !!interne,
                                    externe: !!externe
                                });
                                
                                if (radioInterne && radioExterne && transferForm && interne && externe) {
                                    console.log('✅ Tous les éléments du formulaire trouvés');
                                    
                                    // Fonction pour gérer le changement de type
                                    function switchTransferType() {
                                        console.log('🔄 switchTransferType appelée');
                                        if (radioInterne.checked) {
                                            console.log('📋 Affichage transfert interne');
                                            interne.style.display = 'block';
                                            externe.style.display = 'none';
                                        } else if (radioExterne.checked) {
                                            console.log('📋 Affichage transfert externe');
                                            interne.style.display = 'none';
                                            externe.style.display = 'block';
                                        }
                                    }
                                    
                                    // Initialiser l'affichage
                                    switchTransferType();
                                    
                                    // Ajouter les event listeners
                                    radioInterne.addEventListener('change', switchTransferType);
                                    radioExterne.addEventListener('change', switchTransferType);
                                    
                                    // Gérer la soumission du formulaire - Soumission normale (pas AJAX)
                                    transferForm.addEventListener('submit', function(e) {
                                        console.log('📤 Soumission du formulaire de transfert (normale)');
                                        // Ne pas empêcher la soumission par défaut
                                        // Le formulaire sera soumis normalement et la page rechargera
                                    });
                                    
                                    console.log('✅ Formulaire de transfert initialisé avec succès');
                                    return true;
                                } else if (attempts < maxAttempts) {
                                    console.log(`❌ Éléments non trouvés, nouvelle tentative dans 300ms`);
                                    setTimeout(tryInitTransferForm, 300);
                                } else {
                                    console.log('❌ Éléments du formulaire non trouvés après toutes les tentatives');
                                    return false;
                                }
                            }
                            
                            tryInitTransferForm();
                        }, 500); // Délai initial plus long
                    }
                } else {
                    console.error('Erreur HTTP:', response.status, response.statusText);
                    contentDiv.innerHTML = `
                        <div class="gmail-card">
                            <div class="gmail-card-body">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Erreur lors du chargement de ${section.title} (${response.status})
                                </div>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erreur dans loadSection:', error);
                contentDiv.innerHTML = `
                    <div class="gmail-card">
                        <div class="gmail-card-body">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Erreur de connexion lors du chargement de ${section.title}: ${error.message}
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Mettre à jour l'URL
            const url = new URL(window.location);
            url.searchParams.set('section', sectionId);
            if (action) {
                url.searchParams.set('action', action);
                if (id) {
                    url.searchParams.set('id', id);
                }
            }
            window.history.pushState({section: sectionId}, '', url);
            
            // Mettre à jour le menu actif
            updateActiveMenu(sectionId);
        }
        
        // Mettre à jour le menu actif
        function updateActiveMenu(activeSection) {
            // Supprimer la classe active de tous les liens
            document.querySelectorAll('.gmail-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Ajouter la classe active au lien correspondant
            const activeItem = document.querySelector(`.gmail-nav-item[data-section="${activeSection}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }
        }
        
        // Toggle sidebar mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('gmailSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
        
        // Ouvrir le sidebar
        function openSidebar() {
            const sidebar = document.getElementById('gmailSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.add('open');
            overlay.classList.add('show');
            
            // Empêcher le scroll du body
            document.body.style.overflow = 'hidden';
        }
        
        // Fermer le sidebar
        function closeSidebar() {
            const sidebar = document.getElementById('gmailSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            
            // Restaurer le scroll du body
            document.body.style.overflow = '';
        }
        
        // Initialiser la page
        document.addEventListener('DOMContentLoaded', function() {
            // Charger la section par défaut
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section') || 'dashboard';
            loadSection(section);
            
            // Ajouter les événements de clic aux liens du menu
            document.querySelectorAll('.gmail-nav-item').forEach(item => {
                const sectionId = item.getAttribute('data-section');
                if (sectionId) {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        loadSection(sectionId);
                        
                        // Fermer le menu mobile après clic
                        if (window.innerWidth <= 768) {
                            closeSidebar();
                        }
                    });
                }
            });
            
            // Gérer le bouton retour du navigateur
            window.addEventListener('popstate', function(event) {
                if (event.state && event.state.section) {
                    loadSection(event.state.section);
                }
            });
            
            // Fermer le menu avec la touche Échap
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });
            
            // Recherche globale
            document.getElementById('globalSearch').addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                if (query.length > 2) {
                    // Implémenter la recherche globale
                    console.log('Recherche:', query);
                }
            });
            
            // Gestionnaire d'événements global pour les boutons d'action membres avec data-membre-id
            // Utiliser la délégation d'événements pour les éléments dynamiques
            document.addEventListener('click', function(e) {
                // Ignorer les clics sur les éléments de formulaire
                if (e.target.tagName === 'SELECT' || e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'LABEL' || e.target.tagName === 'BUTTON') {
                    return;
                }
                
                console.log('🔍 Clic détecté sur:', e.target);
                
                // Vérifier si c'est un bouton avec data-membre-id
                const target = e.target.closest('button[data-membre-id]');
                console.log('🎯 Bouton trouvé:', target);
                
                if (!target) {
                    console.log('❌ Pas un bouton membre, ignorer');
                    return;
                }
                
                const membreId = parseInt(target.getAttribute('data-membre-id'));
                const genre = target.getAttribute('data-genre');
                console.log('🔘 Bouton membre cliqué pour ID:', membreId, 'Genre:', genre);
                
                if (target.classList.contains('btn-voir-membre')) {
                    e.preventDefault();
                    console.log('👁️ Voir membre ID:', membreId);
                    loadSection('membres', 'voir', membreId, genre);
                } else if (target.classList.contains('btn-modifier-membre')) {
                    e.preventDefault();
                    console.log('✏️ Modifier membre ID:', membreId);
                    loadSection('membres', 'modifier', membreId, genre);
                } else if (target.classList.contains('btn-transfer-membre')) {
                    e.preventDefault();
                    console.log('🎯 Transfert membre ID:', membreId);
                    loadSection('membres', 'transfer', membreId, genre);
                } else if (target.classList.contains('btn-renvoyer-membre')) {
                    e.preventDefault();
                    console.log('❌ Renvoyer membre ID:', membreId);
                    loadSection('membres', 'renvoyer', membreId, genre);
                } else if (target.classList.contains('btn-restaurer-membre')) {
                    e.preventDefault();
                    console.log('✅ Restaurer membre ID:', membreId);
                    console.log('🔍 Classes du bouton:', target.classList.toString());
                    console.log('🔍 Genre extrait:', genre);
                    console.log('🔍 Bouton cliqué:', target.outerHTML);
                    loadSection('membres', 'restaurer', membreId, genre);
                } else {
                    console.log('❓ Bouton non reconnu, classes:', target.classList.toString());
                }
            });
            
            // Gestionnaire d'événements global pour les boutons d'action transferts avec data-transfert-id
            document.addEventListener('click', function(e) {
                // Ignorer les clics sur les éléments de formulaire
                if (e.target.tagName === 'SELECT' || e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'LABEL' || e.target.tagName === 'BUTTON') {
                    return;
                }
                
                console.log('🔍 Clic détecté sur transfert:', e.target);
                
                // Vérifier si c'est un bouton avec data-transfert-id
                const target = e.target.closest('button[data-transfert-id]');
                console.log('🎯 Bouton transfert trouvé:', target);
                
                if (!target) {
                    console.log('❌ Pas un bouton transfert, ignorer');
                    return;
                }
                
                const transfertId = parseInt(target.getAttribute('data-transfert-id'));
                console.log('🔘 Bouton transfert cliqué pour ID:', transfertId);
                
                if (target.classList.contains('btn-voir-transfert')) {
                    e.preventDefault();
                    const genre = target.getAttribute('data-genre') || 'garcons';
                    console.log('👁️ Voir transfert ID:', transfertId, 'Genre:', genre);
                    loadSection('transferts', 'voir', transfertId, genre);
                } else if (target.classList.contains('btn-restaurer-transfert')) {
                    e.preventDefault();
                    const membreId = parseInt(target.getAttribute('data-membre-id'));
                    const membreNom = target.getAttribute('data-membre-nom');
                    console.log('🔄 Restaurer transfert ID:', transfertId, 'Membre:', membreNom);
                    loadSection('transferts', 'restaurer', transfertId);
                } else {
                    console.log('❓ Bouton transfert non reconnu, classes:', target.classList.toString());
                }
            });
            
            // Charger les statistiques des messages non lus
            function loadMessagesStats() {
                fetch('mvc_router.php?controller=Messages&action=getStats')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.non_lus > 0) {
                            const badge = document.getElementById('messages-badge');
                            if (badge) {
                                badge.textContent = data.data.non_lus;
                                badge.style.display = 'inline-block';
                                badge.className = 'badge bg-danger';
                            }
                        }
                    })
                    .catch(error => console.log('Erreur lors du chargement des stats messages:', error));
            }
            
            // Charger les stats au démarrage
            loadMessagesStats();
            
            // Recharger les stats toutes les 30 secondes
            setInterval(loadMessagesStats, 30000);
        });
    </script>
</body>
</html>