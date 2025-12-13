# 🎨 Système d'Avatar Enhanced - Documentation

## 📋 Vue d'ensemble

Système d'avatar expressif, vivant et personnalisable pour votre espace collaboratif gamifié. Style minimaliste inspiré de Bitmoji avec des fonctionnalités avancées.

## 🎯 Fonctionnalités

### ✅ Implémentées

1. **Expressions faciales dynamiques** (5 expressions)
2. **Styles de cheveux** (8 styles différents)
3. **Couleurs de cheveux** (8 couleurs)
4. **Accessoires** (lunettes, casquettes, couronne, sac à dos)
5. **Auras par niveau** (5 tiers : Bronze, Argent, Or, Platine, Diamant)
6. **Badges** (5 types avec glow)
7. **Animations** (bounce, float, wiggle)
8. **Génération aléatoire** (`randomAvatar()`)
9. **Génération depuis selfie** (pseudo-code prêt)
10. **Personnalisation complète** (couleur peau, cheveux, expression, corps)

## 📁 Structure des Fichiers

```
view/backoffice/
├── avatar_model.json          # Modèle JSON de l'avatar
├── avatar_enhanced.js         # Modèle de données et fonctions
├── avatar_enhanced.css        # Styles CSS avancés
├── avatar_renderer.js         # Classe de rendu
├── avatar_demo.html           # Page de démonstration
└── AVATAR_SYSTEM_DOC.md       # Cette documentation
```

## 🔧 Modèle JSON

### Structure Complète

```json
{
  "avatar": {
    "base": {
      "skin_tone": "light",
      "head_shape": "circle",
      "head_size": 120
    },
    "face": {
      "expression": "happy",
      "expression_intensity": 0.8
    },
    "hair": {
      "style": "short",
      "color": "brown"
    },
    "body": {
      "torso": { "color": "#4a90e2" },
      "legs": { "color": "#2c5aa0" }
    },
    "accessories": {
      "head": [],
      "face": [],
      "body": []
    },
    "level": {
      "current_level": 5,
      "tier": "silver"
    },
    "aura": {
      "enabled": true,
      "type": "glow",
      "animation": "pulse"
    },
    "badge": {
      "enabled": true,
      "type": "achievement",
      "position": "top-right"
    },
    "animation": {
      "idle": "bounce",
      "enabled": true
    }
  }
}
```

## 🚀 Utilisation

### 1. Créer un Avatar de Base

```javascript
const avatar = new AvatarRenderer('container-id', defaultAvatar);
```

### 2. Créer un Avatar Personnalisé

```javascript
const customAvatar = {
    base: { skin_tone: 'medium' },
    face: { expression: 'cool' },
    hair: { style: 'spiky', color: 'blue' },
    body: {
        torso: { color: '#e24a4a' },
        legs: { color: '#1a1a1a' }
    },
    level: { current_level: 12, tier: 'gold' },
    badge: { enabled: true, type: 'vip' }
};

const renderer = new AvatarRenderer('my-avatar', customAvatar);
```

### 3. Générer un Avatar Aléatoire

```javascript
const random = randomAvatar();
const renderer = new AvatarRenderer('random-avatar', random);
```

### 4. Générer depuis un Selfie

```javascript
// À implémenter avec vraie API
const fromSelfie = await generateAvatarFromSelfie('selfie.jpg');
const renderer = new AvatarRenderer('selfie-avatar', fromSelfie);
```

### 5. Mettre à Jour un Avatar

```javascript
// Changer l'expression
renderer.updateExpression('surprised');

// Changer les cheveux
renderer.updateHair('long', 'blonde');

// Changer le niveau
renderer.updateLevel(15);

// Mise à jour complète
renderer.updateConfig({
    face: { expression: 'happy' },
    hair: { style: 'curly', color: 'red' }
});
```

## 🎨 Expressions Disponibles

- `happy` - Joyeux (sourire)
- `neutral` - Neutre (ligne)
- `sad` - Triste (froncement)
- `surprised` - Surpris (bouche ronde)
- `cool` - Cool (sourire en coin)

## 💇 Styles de Cheveux

- `short` - Court
- `long` - Long
- `spiky` - Épicé
- `curly` - Frisé
- `afro` - Afro
- `ponytail` - Queue de cheval
- `bun` - Chignon
- `bald` - Chauve

## 🎨 Couleurs de Cheveux

- `black` - Noir
- `brown` - Brun
- `blonde` - Blond
- `red` - Roux
- `blue` - Bleu
- `green` - Vert
- `purple` - Violet
- `pink` - Rose

## 🎩 Accessoires

### Tête
- `cap_blue` - Casquette Bleue
- `cap_red` - Casquette Rouge
- `beanie` - Bonnet
- `crown` - Couronne

### Visage
- `glasses_round` - Lunettes Rondes
- `glasses_square` - Lunettes Carrées
- `sunglasses` - Lunettes de Soleil
- `mask` - Masque

### Corps
- `backpack` - Sac à dos
- `scarf` - Écharpe

## 🏆 Tiers de Niveau

| Tier | Niveaux | Couleur Aura |
|------|---------|--------------|
| Bronze | 1-5 | #cd7f32 |
| Argent | 6-10 | #c0c0c0 |
| Or | 11-15 | #ffd700 |
| Platine | 16-20 | #e5e4e2 |
| Diamant | 21+ | #00ffff |

## 🎖️ Badges

- `achievement` - ⭐ Achievement
- `vip` - 👑 VIP
- `moderator` - 🛡️ Modérateur
- `creator` - 🎨 Créateur
- `legend` - 🌟 Légende

## 🎬 Animations

- `bounce` - Rebond léger
- `float` - Flottement
- `wiggle` - Balancement

## 📸 Génération depuis Selfie

### Pseudo-code Implémenté

Le système est prêt pour intégration avec :
- **Face Detection API** (Google Cloud Vision, AWS Rekognition)
- **TensorFlow.js** pour détection faciale côté client
- **Face API.js** pour landmarks et expressions
- **Color Thief** pour extraction de couleurs

### Workflow

1. Upload selfie
2. Détection du visage
3. Extraction des caractéristiques :
   - Couleur de peau
   - Couleur des cheveux
   - Expression faciale
   - Accessoires visibles
4. Mapping vers le modèle avatar
5. Génération de l'avatar stylisé

## 🔌 Intégration dans Collab Room

```javascript
// Dans room_collab.php
<div id="user-avatar-<?php echo $member['user_id']; ?>"></div>

<script>
    // Charger l'avatar depuis la base de données
    const avatarConfig = <?php echo json_encode($member['avatar_config']); ?>;
    new AvatarRenderer('user-avatar-<?php echo $member['user_id']; ?>', avatarConfig);
</script>
```

## 📊 Base de Données

### Table `user_avatars`

```sql
CREATE TABLE user_avatars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    avatar_config JSON NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id)
);
```

## 🎯 Prochaines Étapes

1. **Intégration API Selfie** - Connecter avec vraie API de détection faciale
2. **Système de déblocage** - Items premium et achievements
3. **Export Avatar** - Télécharger en PNG/SVG
4. **Animations avancées** - Interactions au hover/click
5. **Multi-avatars** - Gérer plusieurs avatars par utilisateur

## 📝 Notes

- Le style reste minimaliste mais expressif
- Tous les éléments sont en CSS pur (pas d'images)
- Compatible avec tous les navigateurs modernes
- Responsive design inclus
- Performance optimisée

## 🐛 Debug

Pour déboguer un avatar :

```javascript
console.log('Avatar Config:', renderer.config);
console.log('Tier:', getTierFromLevel(renderer.config.level.current_level));
```

---

**Version:** 2.0  
**Dernière mise à jour:** 2024  
**Auteur:** GameHub Pro Team

