<?php
// Test direct du contrôleur ContactInfo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/php/config.php';

// Simuler la session admin pour le test
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'test';

echo "<h2>Test du contrôleur ContactInfo</h2>";

// Test 1: Vérifier si le fichier existe
$controllerFile = __DIR__ . "/controllers/ContactInfoController.php";
echo "<p>Fichier contrôleur: " . ($controllerFile) . "</p>";
echo "<p>Existe: " . (file_exists($controllerFile) ? "OUI" : "NON") . "</p>";

// Test 2: Vérifier si la classe existe
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    echo "<p>Classe ContactInfoController: " . (class_exists('ContactInfoController') ? "OUI" : "NON") . "</p>";
    
    if (class_exists('ContactInfoController')) {
        $controller = new ContactInfoController();
        echo "<p>Méthode liste: " . (method_exists($controller, 'liste') ? "OUI" : "NON") . "</p>";
        
        // Test 3: Appeler la méthode
        try {
            echo "<h3>Résultat de l'appel:</h3>";
            ob_start();
            $controller->liste();
            $output = ob_get_clean();
            echo "<div style='border: 1px solid #ccc; padding: 10px;'>";
            echo $output;
            echo "</div>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
        }
    }
}

// Test 4: Vérifier la base de données
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_info");
    $result = $stmt->fetch();
    echo "<p>Nombre d'enregistrements dans contact_info: " . $result['count'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur DB: " . $e->getMessage() . "</p>";
}
?>
