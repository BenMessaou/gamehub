// Enhanced Avatar System - Model and Functions

// Avatar Data Model
const AvatarModel = {
    // Skin tones
    skinTones: [
        { id: 'light', color: '#ffdbac', name: 'Clair' },
        { id: 'medium', color: '#d4a574', name: 'Moyen' },
        { id: 'tan', color: '#c68642', name: 'Bronzé' },
        { id: 'dark', color: '#8d5524', name: 'Foncé' }
    ],
    
    // Face expressions
    expressions: {
        happy: {
            eyes: '😊',
            mouth: 'smile',
            eyebrows: 'raised',
            intensity: 0.8,
            svg: '<path d="M 30 50 Q 50 60 70 50" stroke="#ff6b6b" stroke-width="3" fill="none" stroke-linecap="round"/>'
        },
        neutral: {
            eyes: '😐',
            mouth: 'line',
            eyebrows: 'default',
            intensity: 0.5,
            svg: '<line x1="30" y1="60" x2="70" y2="60" stroke="#666" stroke-width="2"/>'
        },
        sad: {
            eyes: '😢',
            mouth: 'frown',
            eyebrows: 'lowered',
            intensity: 0.3,
            svg: '<path d="M 30 60 Q 50 50 70 60" stroke="#4a90e2" stroke-width="3" fill="none" stroke-linecap="round"/>'
        },
        surprised: {
            eyes: '😲',
            mouth: 'circle',
            eyebrows: 'raised',
            intensity: 0.9,
            svg: '<circle cx="50" cy="60" r="8" fill="#ff6b6b"/>'
        },
        cool: {
            eyes: '😎',
            mouth: 'smirk',
            eyebrows: 'default',
            intensity: 0.7,
            svg: '<path d="M 30 60 Q 45 55 60 60" stroke="#ff6b6b" stroke-width="2.5" fill="none"/>'
        }
    },
    
    // Hair styles
    hairStyles: [
        { id: 'short', name: 'Court', svg: 'short_hair', color: '#4a4a4a' },
        { id: 'long', name: 'Long', svg: 'long_hair', color: '#4a4a4a' },
        { id: 'spiky', name: 'Épicé', svg: 'spiky_hair', color: '#4a4a4a' },
        { id: 'curly', name: 'Frisé', svg: 'curly_hair', color: '#4a4a4a' },
        { id: 'afro', name: 'Afro', svg: 'afro_hair', color: '#2c2c2c' },
        { id: 'ponytail', name: 'Queue de cheval', svg: 'ponytail_hair', color: '#4a4a4a' },
        { id: 'bun', name: 'Chignon', svg: 'bun_hair', color: '#4a4a4a' },
        { id: 'bald', name: 'Chauve', svg: null, color: null }
    ],
    
    // Hair colors
    hairColors: [
        { id: 'black', color: '#2c2c2c', name: 'Noir' },
        { id: 'brown', color: '#4a4a4a', name: 'Brun' },
        { id: 'blonde', color: '#f4d03f', name: 'Blond' },
        { id: 'red', color: '#e74c3c', name: 'Roux' },
        { id: 'blue', color: '#3498db', name: 'Bleu' },
        { id: 'green', color: '#2ecc71', name: 'Vert' },
        { id: 'purple', color: '#9b59b6', name: 'Violet' },
        { id: 'pink', color: '#e91e63', name: 'Rose' }
    ],
    
    // Accessories
    accessories: {
        head: [
            { id: 'cap_blue', name: 'Casquette Bleue', icon: '🧢', svg: 'cap' },
            { id: 'cap_red', name: 'Casquette Rouge', icon: '🧢', svg: 'cap' },
            { id: 'beanie', name: 'Bonnet', icon: '🎩', svg: 'beanie' },
            { id: 'crown', name: 'Couronne', icon: '👑', svg: 'crown' }
        ],
        face: [
            { id: 'glasses_round', name: 'Lunettes Rondes', icon: '👓', svg: 'glasses_round' },
            { id: 'glasses_square', name: 'Lunettes Carrées', icon: '👓', svg: 'glasses_square' },
            { id: 'sunglasses', name: 'Lunettes de Soleil', icon: '🕶️', svg: 'sunglasses' },
            { id: 'mask', name: 'Masque', icon: '😷', svg: 'mask' }
        ],
        body: [
            { id: 'backpack', name: 'Sac à dos', icon: '🎒', svg: 'backpack' },
            { id: 'scarf', name: 'Écharpe', icon: '🧣', svg: 'scarf' }
        ]
    },
    
    // Level tiers and auras
    levelTiers: {
        bronze: { min: 1, max: 5, color: '#cd7f32', glow: '#cd7f32', name: 'Bronze' },
        silver: { min: 6, max: 10, color: '#c0c0c0', glow: '#e8e8e8', name: 'Argent' },
        gold: { min: 11, max: 15, color: '#ffd700', glow: '#ffed4e', name: 'Or' },
        platinum: { min: 16, max: 20, color: '#e5e4e2', glow: '#ffffff', name: 'Platine' },
        diamond: { min: 21, max: 999, color: '#00ffff', glow: '#00ffff', name: 'Diamant' }
    },
    
    // Badges
    badges: [
        { id: 'achievement', icon: '⭐', name: 'Achievement', color: '#ffd700' },
        { id: 'vip', icon: '👑', name: 'VIP', color: '#ff00c7' },
        { id: 'moderator', icon: '🛡️', name: 'Modérateur', color: '#00ff88' },
        { id: 'creator', icon: '🎨', name: 'Créateur', color: '#00ffea' },
        { id: 'legend', icon: '🌟', name: 'Légende', color: '#ff6b35' }
    ]
};

// Default Avatar Configuration
const defaultAvatar = {
    base: {
        skin_tone: 'light',
        head_shape: 'circle',
        head_size: 120
    },
    face: {
        expression: 'happy',
        expression_intensity: 0.8
    },
    hair: {
        style: 'short',
        color: 'brown'
    },
    body: {
        torso: { color: '#4a90e2' },
        legs: { color: '#2c5aa0' }
    },
    accessories: {
        head: [],
        face: [],
        body: []
    },
    level: {
        current_level: 1,
        tier: 'bronze'
    },
    aura: {
        enabled: true,
        type: 'glow',
        animation: 'pulse'
    },
    badge: {
        enabled: false,
        type: 'achievement'
    },
    animation: {
        idle: 'bounce',
        enabled: true
    }
};

// Generate Random Avatar
function randomAvatar() {
    const avatar = JSON.parse(JSON.stringify(defaultAvatar));
    
    // Random skin tone
    avatar.base.skin_tone = AvatarModel.skinTones[
        Math.floor(Math.random() * AvatarModel.skinTones.length)
    ].id;
    
    // Random expression
    const expressions = Object.keys(AvatarModel.expressions);
    avatar.face.expression = expressions[
        Math.floor(Math.random() * expressions.length)
    ];
    avatar.face.expression_intensity = 0.5 + Math.random() * 0.5;
    
    // Random hair
    avatar.hair.style = AvatarModel.hairStyles[
        Math.floor(Math.random() * AvatarModel.hairStyles.length)
    ].id;
    avatar.hair.color = AvatarModel.hairColors[
        Math.floor(Math.random() * AvatarModel.hairColors.length)
    ].id;
    
    // Random body colors
    const colors = ['#4a90e2', '#e24a4a', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];
    avatar.body.torso.color = colors[Math.floor(Math.random() * colors.length)];
    avatar.body.legs.color = colors[Math.floor(Math.random() * colors.length)];
    
    // Random accessories (30% chance each)
    if (Math.random() > 0.7) {
        const headAcc = AvatarModel.accessories.head[
            Math.floor(Math.random() * AvatarModel.accessories.head.length)
        ];
        avatar.accessories.head.push(headAcc.id);
    }
    
    if (Math.random() > 0.7) {
        const faceAcc = AvatarModel.accessories.face[
            Math.floor(Math.random() * AvatarModel.accessories.face.length)
        ];
        avatar.accessories.face.push(faceAcc.id);
    }
    
    // Random level (1-25)
    avatar.level.current_level = Math.floor(Math.random() * 25) + 1;
    avatar.level.tier = getTierFromLevel(avatar.level.current_level);
    
    // Random badge (20% chance)
    if (Math.random() > 0.8) {
        avatar.badge.enabled = true;
        avatar.badge.type = AvatarModel.badges[
            Math.floor(Math.random() * AvatarModel.badges.length)
        ].id;
    }
    
    return avatar;
}

// Get tier from level
function getTierFromLevel(level) {
    for (const [tier, data] of Object.entries(AvatarModel.levelTiers)) {
        if (level >= data.min && level <= data.max) {
            return tier;
        }
    }
    return 'diamond';
}

// Generate Avatar from Selfie (Pseudo-code)
async function generateAvatarFromSelfie(selfieImage) {
    /*
    PSEUDO-CODE POUR GÉNÉRATION DEPUIS SELFIE:
    
    1. ANALYSE DE L'IMAGE
       - Charger l'image selfie
       - Détecter le visage (Face Detection API ou ML model)
       - Extraire les caractéristiques:
         * Couleur de peau (analyse de la zone du visage)
         * Couleur des cheveux (analyse de la zone supérieure)
         * Forme du visage (ovale, rond, carré)
         * Expression (analyse des points faciaux)
         * Couleur des yeux (si visible)
    
    2. EXTRACTION DES COULEURS
       - Utiliser Color Thief ou similar pour extraire palette dominante
       - skinTone = analyserCouleurPeau(zoneVisage)
       - hairColor = analyserCouleurCheveux(zoneCheveux)
       - eyeColor = analyserCouleurYeux(zoneYeux)
    
    3. DÉTECTION D'EXPRESSION
       - Utiliser Face API.js ou TensorFlow.js
       - Analyser les points faciaux (landmarks)
       - Déterminer expression: happy, neutral, sad, surprised
       - Calculer intensity basée sur l'écart des points
    
    4. DÉTECTION D'ACCESSOIRES
       - Détecter lunettes (formes rectangulaires/circulaires sur les yeux)
       - Détecter casquette/chapeau (formes au-dessus de la tête)
       - Détecter masque (couvrant bouche/nez)
    
    5. GÉNÉRATION DE L'AVATAR
       - Créer configuration avatar basée sur les données extraites
       - Mapper les couleurs détectées aux palettes disponibles
       - Appliquer l'expression détectée
       - Ajouter les accessoires détectés
    
    6. RETOUR
       - Retourner l'objet avatar configuré
       - Sauvegarder l'URL du selfie dans metadata
    */
    
    const avatar = JSON.parse(JSON.stringify(defaultAvatar));
    
    try {
        // Simuler l'analyse (à remplacer par vraie API)
        const analysis = await analyzeSelfieImage(selfieImage);
        
        // Mapper les résultats à l'avatar
        avatar.base.skin_tone = mapSkinTone(analysis.skinColor);
        avatar.hair.color = mapHairColor(analysis.hairColor);
        avatar.hair.style = detectHairStyle(analysis.hairShape);
        avatar.face.expression = mapExpression(analysis.expression);
        avatar.face.expression_intensity = analysis.expressionIntensity;
        
        // Détecter accessoires
        if (analysis.hasGlasses) {
            avatar.accessories.face.push('glasses_round');
        }
        if (analysis.hasCap) {
            avatar.accessories.head.push('cap_blue');
        }
        
        // Metadata
        avatar.metadata = {
            generated_from_selfie: true,
            selfie_url: selfieImage,
            analysis_confidence: analysis.confidence
        };
        
        return avatar;
    } catch (error) {
        console.error('Error generating avatar from selfie:', error);
        return defaultAvatar;
    }
}

// Helper functions for selfie analysis (simulation)
async function analyzeSelfieImage(imageUrl) {
    // SIMULATION - À REMPLACER PAR VRAIE API
    return {
        skinColor: '#ffdbac',
        hairColor: '#4a4a4a',
        hairShape: 'short',
        expression: 'happy',
        expressionIntensity: 0.7,
        hasGlasses: false,
        hasCap: false,
        confidence: 0.85
    };
}

function mapSkinTone(color) {
    // Mapper couleur RGB/HEX à un skin tone ID
    const tones = {
        '#ffdbac': 'light',
        '#d4a574': 'medium',
        '#c68642': 'tan',
        '#8d5524': 'dark'
    };
    return tones[color] || 'light';
}

function mapHairColor(color) {
    const colors = {
        '#2c2c2c': 'black',
        '#4a4a4a': 'brown',
        '#f4d03f': 'blonde',
        '#e74c3c': 'red'
    };
    return colors[color] || 'brown';
}

function detectHairStyle(shape) {
    // Analyser la forme des cheveux détectés
    return 'short'; // Par défaut
}

function mapExpression(expression) {
    const expressions = ['happy', 'neutral', 'sad', 'surprised', 'cool'];
    return expressions.includes(expression) ? expression : 'happy';
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        AvatarModel,
        defaultAvatar,
        randomAvatar,
        generateAvatarFromSelfie,
        getTierFromLevel
    };
}

