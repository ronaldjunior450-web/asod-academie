<?php
// Script pour récupérer les photos en base64
header('Content-Type: application/json');

if (!isset($_GET['photo'])) {
    echo json_encode(['error' => 'Photo non spécifiée']);
    exit;
}

$photo_path = $_GET['photo'];

// Vérifier que le fichier existe
if (!file_exists($photo_path)) {
    echo json_encode(['error' => 'Fichier non trouvé']);
    exit;
}

// Lire le fichier et le convertir en base64
$image_data = file_get_contents($photo_path);
$base64 = base64_encode($image_data);
$mime_type = mime_content_type($photo_path);

echo json_encode([
    'success' => true,
    'data' => 'data:' . $mime_type . ';base64,' . $base64
]);
?>





