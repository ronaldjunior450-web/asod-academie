<?php
// Routeur MVC simple - Utilise l'existant
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/php/config.php';

// Vérifier la session admin
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Récupérer les paramètres
$controller = $_GET['controller'] ?? 'Dashboard';
$action = $_GET['action'] ?? 'show';
$id = $_GET['id'] ?? null;

// Nettoyer les paramètres
$controller = ucfirst(strtolower($controller));
$action = strtolower($action);

// Mapper les actions en français
$actionMap = [
    'view' => 'voir',
    'edit' => 'modifier', 
    'delete' => 'supprimer',
    'add' => 'ajouter',
    'list' => 'liste'
];
$action = $actionMap[$action] ?? $action;

// Vérifier que le contrôleur existe
$controllerFile = __DIR__ . "/controllers/{$controller}Controller.php";
if (!file_exists($controllerFile)) {
    // Fallback sur les sections existantes
    $sectionFile = __DIR__ . "/sections/" . strtolower($controller) . ".php";
    if (file_exists($sectionFile)) {
        include $sectionFile;
        exit;
    }
    
    http_response_code(404);
    echo '<div class="alert alert-danger">Contrôleur non trouvé: ' . htmlspecialchars($controller) . '</div>';
    exit;
}

// Inclure le contrôleur
require_once $controllerFile;

// Vérifier que la classe existe
$controllerClass = $controller . 'Controller';
if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo '<div class="alert alert-danger">Classe contrôleur non trouvée: ' . htmlspecialchars($controllerClass) . '</div>';
    exit;
}

// Instancier le contrôleur
try {
    $controllerInstance = new $controllerClass();
    
    // Vérifier que la méthode existe
    if (!method_exists($controllerInstance, $action)) {
        http_response_code(404);
        echo '<div class="alert alert-danger">Action non trouvée: ' . htmlspecialchars($action) . '</div>';
        exit;
    }
    
    // Appeler la méthode
    if ($id) {
        $controllerInstance->$action($id);
    } else {
        $controllerInstance->$action();
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo '<div class="alert alert-danger">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
