# 📸 Guide du Système Photo Unifié

## 🎯 Objectif
Éliminer les conflits de noms de fichiers et standardiser la gestion des photos dans tout le projet.

## 🏗️ Architecture

### PhotoManager Class
- **Fichier**: `php/PhotoManager.php`
- **Fonction**: Gestion centralisée des uploads, validation, et nettoyage
- **Format de nom**: `category_timestamp_random.extension`

### Exemple de nommage
```
bureau_1760029676_6ca33435.png
entraineurs_1760029676_696bb95b.jpg
membres_1760029676_591bc01c.png
```

## 📁 Structure des dossiers
```
images/
├── bureau/          # Photos du bureau
├── entraineurs/     # Photos des entraîneurs
├── membres/         # Photos des membres
├── equipes/         # Photos des équipes
├── actualites/      # Photos des actualités
└── galerie/         # Photos de la galerie
```

## 🔧 Utilisation

### 1. Initialisation
```php
require_once 'php/PhotoManager.php';
$photoManager = new PhotoManager($pdo, 'images/');
```

### 2. Upload d'une photo
```php
$result = $photoManager->uploadPhoto($_FILES['photo'], 'bureau');
if ($result['success']) {
    $filename = $result['filename']; // bureau_1760029676_6ca33435.png
    $path = $result['path'];         // bureau/bureau_1760029676_6ca33435.png
}
```

### 3. Vérifier l'existence
```php
if ($photoManager->photoExists($filename, 'bureau')) {
    // Photo existe
}
```

### 4. Obtenir l'URL
```php
$url = $photoManager->getPhotoUrl($filename, 'bureau');
// Retourne: images/bureau/bureau_1760029676_6ca33435.png
```

### 5. Supprimer une photo
```php
$photoManager->deletePhoto($filename, 'bureau');
```

### 6. Nettoyer les orphelins
```php
$deletedCount = $photoManager->cleanupOrphanPhotos('bureau', 'bureau', 'photo');
```

## 🛡️ Sécurité

### Validation des fichiers
- **Types autorisés**: jpg, jpeg, png, gif, webp
- **Taille max**: 5MB
- **Vérification MIME**: Double validation
- **Noms uniques**: Timestamp + hash aléatoire

### Protection contre les conflits
- **Noms prévisibles**: Format standardisé
- **Vérification existence**: Avant affichage
- **Nettoyage automatique**: Suppression des orphelins

## 📋 Intégration dans les contrôleurs

### BureauAPI_New.php
- Utilise PhotoManager pour tous les uploads
- Gestion des transactions (rollback en cas d'erreur)
- Suppression automatique des anciennes photos

### Pattern d'utilisation
```php
// Dans le contrôleur
$uploadResult = $this->photoManager->uploadPhoto($files['photo'], 'bureau');
if ($uploadResult['success']) {
    $data['photo'] = $uploadResult['filename'];
} else {
    throw new Exception($uploadResult['error']);
}
```

## 🎨 Affichage dans les vues

### Code robuste
```php
<?php if (!empty($membre['photo']) && file_exists('images/bureau/' . $membre['photo'])): ?>
    <img src="images/bureau/<?= htmlspecialchars($membre['photo']) ?>" alt="Photo">
<?php else: ?>
    <i class="fas fa-user-circle"></i> <!-- Icône par défaut -->
<?php endif; ?>
```

## 🚀 Avantages

1. **✅ Plus de conflits de noms**
2. **✅ Noms de fichiers prévisibles**
3. **✅ Validation automatique**
4. **✅ Nettoyage des orphelins**
5. **✅ Gestion d'erreurs robuste**
6. **✅ Code réutilisable**
7. **✅ Sécurité renforcée**

## 🔄 Migration

Le système a été migré automatiquement :
- ✅ 4 photos du bureau renommées
- ✅ Dossiers créés pour toutes les catégories
- ✅ Noms standardisés selon le nouveau format
- ✅ Système opérationnel

## 📝 Notes importantes

- **Toujours utiliser PhotoManager** pour les nouveaux uploads
- **Vérifier l'existence** avant affichage
- **Nettoyer régulièrement** les orphelins
- **Respecter le format** de nommage standard











