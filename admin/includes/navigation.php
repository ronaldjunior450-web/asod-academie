<?php
// Navigation centralisée pour l'administration ASOD ACADEMIE
// Ce fichier évite la répétition du code de navigation

// Déterminer la page active
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Menu de navigation principal - 15 fonctionnalités + Déconnexion
$navigation_items = [
    'dashboard' => [
        'title' => 'Dashboard',
        'icon' => 'fas fa-tachometer-alt',
        'url' => '#',
        'description' => 'Tableau de bord principal'
    ],
    'actualites' => [
        'title' => 'Actualités',
        'icon' => 'fas fa-newspaper',
        'url' => '#',
        'description' => 'Gestion des actualités'
    ],
    'membres' => [
        'title' => 'Membres',
        'icon' => 'fas fa-users',
        'url' => '#',
        'description' => 'Gestion des membres'
    ],
    'equipes' => [
        'title' => 'Équipes',
        'icon' => 'fas fa-cogs',
        'url' => '#',
        'description' => 'Gestion des équipes'
    ],
    'bureau' => [
        'title' => 'Bureau',
        'icon' => 'fas fa-user',
        'url' => '#',
        'description' => 'Gestion du bureau'
    ],
    'inscriptions' => [
        'title' => 'Inscriptions',
        'icon' => 'fas fa-clipboard-list',
        'url' => '#',
        'description' => 'Gestion des inscriptions'
    ],
    'contact' => [
        'title' => 'Contact',
        'icon' => 'fas fa-envelope',
        'url' => '#',
        'description' => 'Gestion du contact',
        'submenu' => [
            'contacts' => [
                'title' => 'Messages',
                'icon' => 'fas fa-envelope',
                'url' => '#',
                'description' => 'Gestion des messages'
            ],
            'contact_info' => [
                'title' => 'Informations',
                'icon' => 'fas fa-info-circle',
                'url' => '#',
                'description' => 'Informations de contact'
            ]
        ]
    ],
    'galerie' => [
        'title' => 'Galerie',
        'icon' => 'fas fa-images',
        'url' => '#',
        'description' => 'Gestion de la galerie'
    ],
    'evenements' => [
        'title' => 'Événements',
        'icon' => 'fas fa-calendar-alt',
        'url' => '#',
        'description' => 'Gestion des événements'
    ],
    'temoignages' => [
        'title' => 'Témoignages',
        'icon' => 'fas fa-quote-left',
        'url' => '#',
        'description' => 'Gestion des témoignages'
    ],
    'formations' => [
        'title' => 'Formations',
        'icon' => 'fas fa-graduation-cap',
        'url' => '#',
        'description' => 'Gestion des formations'
    ],
    'paiements' => [
        'title' => 'Paiements',
        'icon' => 'fas fa-credit-card',
        'url' => '#',
        'description' => 'Gestion des paiements'
    ],
];

// Fonction pour générer la navbar
function generateNavbar($admin_nom_complet = 'Admin', $admin_role = 'admin') {
    // S'assurer que les variables ne sont pas vides
    $admin_nom_complet = $admin_nom_complet ?: 'Administrateur';
    $admin_role = $admin_role ?: 'admin';
    
    return '
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%); box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="../images/logo.png" alt="ASOD ACADEMIE" style="height: 30px; margin-right: 10px;">
                <span class="d-none d-sm-inline">ASOD ACADEMIE - Administration</span>
                <span class="d-inline d-sm-none">ASOD ADMIN</span>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2"></i>
                        <span class="d-none d-md-inline">' . htmlspecialchars($admin_nom_complet) . '</span>
                        <span class="d-inline d-md-none">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text">Connecté en tant que <strong>' . htmlspecialchars($admin_role) . '</strong></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Voir le site web
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                        </a></li>
                    </ul>
                </div>
            </div>
            <button class="navbar-toggler" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>';
}

// Fonction pour générer la sidebar
function generateSidebar($navigation_items, $current_page, $admin_nom_complet = 'Admin', $admin_role = 'admin') {
    $sidebar_html = '
    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebar" style="background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%); min-height: calc(100vh - 60px);">
        <div class="position-sticky pt-3">
            <div class="text-center mb-4">
                <img src="../images/logo.png" alt="ASOD ACADEMIE" style="height: 40px; margin-bottom: 10px;">
                <h6 class="text-white">Administration</h6>
            </div>
            <ul class="nav flex-column">';
    
    foreach ($navigation_items as $key => $item) {
        $active_class = ($current_page === $key) ? 'active' : '';
        $sidebar_html .= '
                <li class="nav-item">
                    <a class="nav-link text-white ' . $active_class . '" href="#" data-section="' . $key . '" title="' . $item['description'] . '">
                        <i class="' . $item['icon'] . ' me-2"></i>' . $item['title'] . '
                    </a>
                </li>';
    }
    
    $sidebar_html .= '
                <li class="nav-item mt-3">
                    <a class="nav-link text-white" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </nav>';
    
    return $sidebar_html;
}

// Fonction pour générer le CSS commun
function generateCommonCSS() {
    return '
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar {
            background: linear-gradient(135deg, #0d6efd 0%, #1a1a2e 100%);
            min-height: calc(100vh - 60px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.2);
            border-right: 3px solid #fff;
        }
        
        .main-content {
            margin-top: 0;
            padding-top: 0;
            height: calc(100vh - 60px);
            margin-left: 0;
        }
        
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }
        
        /* Espacement des titres */
        .main-content h1,
        .main-content h2,
        .main-content h3,
        .main-content h4,
        .main-content h5,
        .main-content h6 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .main-content h1 {
            font-size: 2rem;
            margin-top: 2rem;
        }
        
        .main-content h2 {
            font-size: 1.75rem;
        }
        
        .main-content h3 {
            font-size: 1.5rem;
        }
        
        /* Espacement des sections */
        .main-content .card,
        .main-content .alert,
        .main-content .table-responsive {
            margin-bottom: 1.5rem;
        }
        
        .main-content .card-header {
            padding: 1rem 1.5rem;
        }
        
        .main-content .card-body {
            padding: 1.5rem;
        }
        
        /* Alignement du contenu à côté du menu */
        .container-fluid .row {
            margin: 0;
        }
        
        .container-fluid .row > .col-md-3,
        .container-fluid .row > .col-lg-2 {
            padding: 0;
        }
        
        .container-fluid .row > .col-md-9,
        .container-fluid .row > .col-lg-10 {
            padding-left: 0;
            padding-right: 0;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 60px;
                left: -100%;
                width: 280px;
                height: calc(100vh - 60px);
                z-index: 1000;
                transition: left 0.3s ease;
                overflow-y: auto;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                padding: 1rem;
                margin-left: 0;
            }
            
            .navbar-toggler {
                display: block;
            }
            
            .mobile-menu-overlay {
                position: fixed;
                top: 60px;
                left: 0;
                width: 100%;
                height: calc(100vh - 60px);
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            
            .mobile-menu-overlay.show {
                display: block;
            }
        }
        
        @media (min-width: 769px) {
            .navbar-toggler {
                display: none;
            }
        }
    </style>';
}

// Fonction pour générer le JavaScript commun
function generateCommonJS() {
    return '
    <script>
        // Gestion du menu mobile
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarToggle = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebar");
            const mobileMenuOverlay = document.getElementById("mobileMenuOverlay");
            
            if (sidebarToggle && sidebar && mobileMenuOverlay) {
                sidebarToggle.addEventListener("click", function() {
                    sidebar.classList.toggle("show");
                    mobileMenuOverlay.classList.toggle("show");
                });
                
                mobileMenuOverlay.addEventListener("click", function() {
                    sidebar.classList.remove("show");
                    mobileMenuOverlay.classList.remove("show");
                });
                
                // Fermer le menu lors du clic sur un lien
                const navLinks = sidebar.querySelectorAll(".nav-link");
                navLinks.forEach(link => {
                    link.addEventListener("click", function() {
                        sidebar.classList.remove("show");
                        mobileMenuOverlay.classList.remove("show");
                    });
                });
            }
        });
    </script>';
}

// Fonction pour générer la structure HTML de base
function generatePageStructure($page_title, $current_page, $admin_nom_complet = 'Admin', $admin_role = 'admin') {
    global $navigation_items;
    
    $navbar = generateNavbar($admin_nom_complet, $admin_role);
    $sidebar = generateSidebar($navigation_items, $current_page, $admin_nom_complet, $admin_role);
    $css = generateCommonCSS();
    $js = generateCommonJS();
    
    return [
        'navbar' => $navbar,
        'sidebar' => $sidebar,
        'css' => $css,
        'js' => $js
    ];
}
?>
