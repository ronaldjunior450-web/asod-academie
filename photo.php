<?php
// Script pour servir les photos des membres
if (!isset($_GET['file'])) {
    http_response_code(404);
    exit('Fichier non trouvé');
}

$filename = $_GET['file'];
$filepath = 'images/membres/' . basename($filename);

// Vérifier que le fichier existe
if (!file_exists($filepath)) {
    http_response_code(404);
    exit('Fichier non trouvé');
}

// Déterminer le type MIME
$extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

$mimeType = $mimeTypes[$extension] ?? 'image/jpeg';

// Envoyer les headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: public, max-age=3600');

// Envoyer le fichier
readfile($filepath);
?>









