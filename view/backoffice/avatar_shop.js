// Avatar Shop JavaScript

// Avatar data structure
const avatarData = {
    hair: [
        { id: 'hair1', name: 'Court arrondi', icon: '💇', emoji: '👨', style: 'short-rounded' },
        { id: 'hair2', name: 'Long ondulé', icon: '💇‍♀️', emoji: '👩', style: 'long-wavy' },
        { id: 'hair3', name: 'Spiky (piques)', icon: '⚡', emoji: '⚡', style: 'spiky' },
        { id: 'hair4', name: 'Afro', icon: '👨‍🦱', emoji: '👨‍🦱', style: 'afro' },
        { id: 'hair5', name: 'Frange', icon: '👱', emoji: '👱', style: 'bangs' },
        { id: 'hair6', name: 'Mohawk (crête)', icon: '🎸', emoji: '🎸', style: 'mohawk' },
        { id: 'hair7', name: 'Chignon / Bun', icon: '👩‍🦰', emoji: '👩‍🦰', style: 'bun' },
        { id: 'hair8', name: 'Queue de cheval', icon: '👱‍♀️', emoji: '👱‍♀️', style: 'ponytail' },
        { id: 'hair9', name: 'Rasé', icon: '👨‍🦲', emoji: '👨‍🦲', style: 'bald' }
    ],
    face: [
        { id: 'face1', name: 'Sourire', icon: '😊', emoji: '😊' },
        { id: 'face2', name: 'Neutre', icon: '😐', emoji: '😐' },
        { id: 'face3', name: 'Cool', icon: '😎', emoji: '😎' },
        { id: 'face4', name: 'Joyeux', icon: '😄', emoji: '😄' },
        { id: 'face5', name: 'Sérieux', icon: '🤔', emoji: '🤔' }
    ],
    helmet: [
        { id: 'helmet1', name: 'Casque Bleu', icon: '⛑️', emoji: '⛑️', color: '#4a90e2' },
        { id: 'helmet2', name: 'Casque Rouge', icon: '🪖', emoji: '🪖', color: '#e24a4a' },
        { id: 'helmet3', name: 'Casque Or', icon: '👑', emoji: '👑', color: '#ffd700' },
        { id: 'helmet4', name: 'Casquette', icon: '🧢', emoji: '🧢', color: '#8b4513' },
        { id: 'helmet5', name: 'Chapeau', icon: '🎩', emoji: '🎩', color: '#000000' }
    ],
    shirt: [
        { id: 'shirt1', name: 'T-shirt Bleu', icon: '👕', emoji: '👕', color: '#4a90e2' },
        { id: 'shirt2', name: 'T-shirt Rouge', icon: '👕', emoji: '👕', color: '#e24a4a' },
        { id: 'shirt3', name: 'T-shirt Vert', icon: '👕', emoji: '👕', color: '#4ae24a' },
        { id: 'shirt4', name: 'T-shirt Noir', icon: '👕', emoji: '👕', color: '#2c2c2c' },
        { id: 'shirt5', name: 'T-shirt Flammes', icon: '🔥', emoji: '🔥', color: '#ff6b35' }
    ],
    pants: [
        { id: 'pants1', name: 'Jeans', icon: '👖', emoji: '👖', color: '#2c5aa0' },
        { id: 'pants2', name: 'Noir', icon: '👖', emoji: '👖', color: '#1a1a1a' },
        { id: 'pants3', name: 'Bleu Clair', icon: '👖', emoji: '👖', color: '#87ceeb' },
        { id: 'pants4', name: 'Rouge', icon: '👖', emoji: '👖', color: '#8b0000' },
        { id: 'pants5', name: 'Motif', icon: '🎨', emoji: '🎨', color: '#ff6b35' }
    ],
    shoes: [
        { id: 'shoes1', name: 'Baskets', icon: '👟', emoji: '👟', color: '#ffffff' },
        { id: 'shoes2', name: 'Bottes', icon: '👢', emoji: '👢', color: '#8b4513' },
        { id: 'shoes3', name: 'Sandales', icon: '👡', emoji: '👡', color: '#daa520' },
        { id: 'shoes4', name: 'Sneakers', icon: '👟', emoji: '👟', color: '#000000' },
        { id: 'shoes5', name: 'Colorées', icon: '🎨', emoji: '🎨', color: '#ff00c7' }
    ],
    accessories: [
        { id: 'acc1', name: 'Lunettes', icon: '👓', emoji: '👓' },
        { id: 'acc2', name: 'Montre', icon: '⌚', emoji: '⌚' },
        { id: 'acc3', name: 'Casque Audio', icon: '🎧', emoji: '🎧' },
        { id: 'acc4', name: 'Écharpe', icon: '🧣', emoji: '🧣' },
        { id: 'acc5', name: 'Gants', icon: '🧤', emoji: '🧤' }
    ]
};

// Current avatar state - Cartoon System
let currentAvatar = {
    base: {
        skin_tone: 'light',
        skin_color: '#ffdbac'
    },
    hair: {
        style: 'short-rounded',
        color: '#333333'
    },
    face: {
        expression: 'happy',
        eyes: { style: 'happy', color: '#000000' },
        mouth: { style: 'smile', color: '#ff6b6b' },
        eyebrows: { style: 'soft', color: '#2c2c2c' },
        cheeks: true
    },
    body: {
        torso: { color: '#4a90e2', shape: 'rounded', width: 90, height: 100 },
        arms: { position: 'rest', left_color: '#ffdbac', right_color: '#ffdbac', sleeve_color: '#4a90e2' },
        legs: { color: '#2c5aa0', width: 35, height: 80, spacing: 20 }
    },
    accessories: {
        head: [],
        face: [],
        body: []
    },
    animation: {
        idle: true,
        type: 'breathe',
        speed: 'normal'
    },
    // Keep old system for compatibility
    shirt: 'shirt1',
    pants: 'pants1'
};

// Cartoon Avatar Renderer instance
let cartoonAvatarRenderer = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeCategoryButtons();
    loadCategory('hair');
    initializeCartoonAvatar();
    setupSelfieUpload();
});

// Initialize Cartoon Avatar
function initializeCartoonAvatar() {
    const container = document.getElementById('cartoon-avatar-container');
    if (container && typeof CartoonAvatarRenderer !== 'undefined') {
        try {
            cartoonAvatarRenderer = new CartoonAvatarRenderer(container, currentAvatar);
        } catch (error) {
            console.error('Error initializing cartoon avatar:', error);
            // Fallback: try again after a short delay
            setTimeout(() => {
                if (typeof CartoonAvatarRenderer !== 'undefined') {
                    cartoonAvatarRenderer = new CartoonAvatarRenderer(container, currentAvatar);
                }
            }, 100);
        }
    } else {
        console.warn('Cartoon avatar container or renderer not found');
    }
}

// Category buttons
function initializeCategoryButtons() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryButtons.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            // Load category items
            const category = this.dataset.category;
            loadCategory(category);
        });
    });
}

// Load category items
function loadCategory(category) {
    const itemsContainer = document.getElementById('items-container');
    const items = avatarData[category] || [];
    
    itemsContainer.innerHTML = '';
    
    items.forEach(item => {
        const itemCard = document.createElement('div');
        itemCard.className = 'item-card';
        itemCard.dataset.itemId = item.id;
        itemCard.dataset.category = category;
        
        // Check if item is selected
        let isSelected = false;
        if (category === 'hair') {
            // For hair, check the style
            const hairData = avatarData.hair.find(h => h.id === item.id);
            isSelected = currentAvatar.hair?.style === hairData?.style || currentAvatar.hair_id === item.id;
        } else if (category === 'shirt' || category === 'pants' || category === 'shoes') {
            // For clothing items, check the currentAvatar[category] property
            isSelected = currentAvatar[category] === item.id;
        } else if (category === 'face') {
            // For face, check expression mapping
            const faceMap = {
                'face1': 'happy',
                'face2': 'neutral',
                'face3': 'cool',
                'face4': 'laugh',
                'face5': 'surprised'
            };
            isSelected = currentAvatar.face?.expression === faceMap[item.id];
        } else if (Array.isArray(currentAvatar[category])) {
            isSelected = currentAvatar[category].includes(item.id);
        } else {
            isSelected = currentAvatar[category] === item.id;
        }
        
        if (isSelected) {
            itemCard.classList.add('selected');
        }
        
        itemCard.innerHTML = `
            <span class="item-icon">${item.icon || item.emoji}</span>
            <span class="item-name">${item.name}</span>
        `;
        
        itemCard.addEventListener('click', function() {
            selectItem(category, item.id);
        });
        
        itemsContainer.appendChild(itemCard);
    });
}

// Select item
function selectItem(category, itemId) {
    // Map to cartoon system
    if (category === 'hair') {
        const hairData = avatarData.hair.find(h => h.id === itemId);
        if (!currentAvatar.hair) currentAvatar.hair = {};
        if (hairData && hairData.style) {
            currentAvatar.hair.style = hairData.style;
        }
        // Save hair selection for compatibility
        currentAvatar.hair_id = itemId;
    } else if (category === 'face') {
        const faceMap = {
            'face1': 'happy',
            'face2': 'neutral',
            'face3': 'cool',
            'face4': 'laugh',
            'face5': 'surprised'
        };
        if (!currentAvatar.face) currentAvatar.face = {};
        currentAvatar.face.expression = faceMap[itemId] || 'happy';
    } else if (category === 'shirt') {
        const shirtData = avatarData.shirt.find(s => s.id === itemId);
        if (!currentAvatar.body) currentAvatar.body = {};
        if (!currentAvatar.body.torso) currentAvatar.body.torso = {};
        if (!currentAvatar.body.arms) currentAvatar.body.arms = {};
        if (shirtData && shirtData.color) {
            currentAvatar.body.torso.color = shirtData.color;
            currentAvatar.body.arms.sleeve_color = shirtData.color;
        }
        // Save shirt selection for compatibility
        currentAvatar.shirt = itemId;
    } else if (category === 'pants') {
        const pantsData = avatarData.pants.find(p => p.id === itemId);
        if (!currentAvatar.body) currentAvatar.body = {};
        if (!currentAvatar.body.legs) currentAvatar.body.legs = {};
        if (pantsData && pantsData.color) {
            currentAvatar.body.legs.color = pantsData.color;
        }
        // Save pants selection for compatibility
        currentAvatar.pants = itemId;
    } else if (category === 'shoes') {
        const shoesData = avatarData.shoes.find(s => s.id === itemId);
        if (!currentAvatar.body) currentAvatar.body = {};
        if (!currentAvatar.body.legs) currentAvatar.body.legs = {};
        if (!currentAvatar.body.legs.shoes) currentAvatar.body.legs.shoes = {};
        if (shoesData && shoesData.color) {
            currentAvatar.body.legs.shoes.color = shoesData.color;
        }
        // Save shoes selection for compatibility
        currentAvatar.shoes = itemId;
    } else if (category === 'accessories') {
        if (!currentAvatar.accessories) currentAvatar.accessories = { head: [], face: [], body: [] };
        // Map old accessories to new system
        if (itemId === 'acc1' || itemId === 'acc3') {
            const index = currentAvatar.accessories.face.indexOf('glasses');
            if (index > -1) {
                currentAvatar.accessories.face.splice(index, 1);
            } else {
                currentAvatar.accessories.face.push('glasses');
            }
        }
    } else if (category === 'helmet') {
        if (!currentAvatar.accessories) currentAvatar.accessories = { head: [], face: [], body: [] };
        const helmetData = avatarData.helmet.find(h => h.id === itemId);
        if (helmetData) {
            if (helmetData.id === 'helmet4') {
                // Cap
                const index = currentAvatar.accessories.head.indexOf('hat');
                if (index > -1) {
                    currentAvatar.accessories.head.splice(index, 1);
                } else {
                    currentAvatar.accessories.head.push('hat');
                }
            }
        }
    } else {
        // Keep old system for other categories
        if (currentAvatar[category] === itemId) {
            currentAvatar[category] = null;
        } else {
            currentAvatar[category] = itemId;
        }
    }
    
    // Reload category to update selection
    loadCategory(category);
    updateAvatarDisplay();
    updateSpeechBubble();
}

// Update avatar display - Cartoon System
function updateAvatarDisplay() {
    // Ensure renderer is initialized
    if (!cartoonAvatarRenderer) {
        initializeCartoonAvatar();
        if (!cartoonAvatarRenderer) {
            console.error('Failed to initialize cartoon avatar renderer');
            return;
        }
    }
    
    // Ensure cartoonConfig has all required properties with defaults
    const cartoonConfig = {
        base: {
            skin_tone: currentAvatar.base?.skin_tone || 'light',
            skin_color: currentAvatar.base?.skin_color || '#ffdbac'
        },
        hair: {
            style: currentAvatar.hair?.style || 'short-rounded',
            color: currentAvatar.hair?.color || '#333333'
        },
        face: {
            expression: currentAvatar.face?.expression || 'happy',
            eyes: currentAvatar.face?.eyes || { style: 'happy', color: '#000000', size: 'medium' },
            mouth: currentAvatar.face?.mouth || { style: 'smile', color: '#ff6b6b' },
            eyebrows: currentAvatar.face?.eyebrows || { style: 'soft', color: '#2c2c2c' },
            cheeks: currentAvatar.face?.cheeks !== false
        },
        body: {
            torso: {
                color: currentAvatar.body?.torso?.color || '#4a90e2',
                shape: currentAvatar.body?.torso?.shape || 'rounded',
                width: currentAvatar.body?.torso?.width || 90,
                height: currentAvatar.body?.torso?.height || 100
            },
            legs: {
                color: currentAvatar.body?.legs?.color || '#2c5aa0',
                width: currentAvatar.body?.legs?.width || 35,
                height: currentAvatar.body?.legs?.height || 80,
                spacing: currentAvatar.body?.legs?.spacing || 20,
                shoes: currentAvatar.body?.legs?.shoes || { color: '#1a1a1a', style: 'default' }
            },
            arms: {
                position: currentAvatar.body?.arms?.position || 'rest',
                left_color: currentAvatar.body?.arms?.left_color || '#ffdbac',
                right_color: currentAvatar.body?.arms?.right_color || '#ffdbac',
                sleeve_color: currentAvatar.body?.arms?.sleeve_color || '#4a90e2'
            }
        },
        accessories: {
            head: currentAvatar.accessories?.head || [],
            face: currentAvatar.accessories?.face || [],
            body: currentAvatar.accessories?.body || []
        },
        animation: {
            idle: currentAvatar.animation?.idle !== false,
            type: currentAvatar.animation?.type || 'breathe',
            speed: currentAvatar.animation?.speed || 'normal'
        }
    };
    
    // Map shirt color if exists
    if (currentAvatar.shirt) {
        const shirtData = avatarData.shirt.find(s => s.id === currentAvatar.shirt);
        if (shirtData && shirtData.color) {
            cartoonConfig.body.torso.color = shirtData.color;
            cartoonConfig.body.arms.sleeve_color = shirtData.color;
        }
    }
    
    // Map pants color if exists
    if (currentAvatar.pants) {
        const pantsData = avatarData.pants.find(p => p.id === currentAvatar.pants);
        if (pantsData && pantsData.color) {
            cartoonConfig.body.legs.color = pantsData.color;
        }
    }
    
    // Map shoes color if exists
    if (currentAvatar.shoes) {
        const shoesData = avatarData.shoes.find(s => s.id === currentAvatar.shoes);
        if (shoesData && shoesData.color) {
            cartoonConfig.body.legs.shoes = cartoonConfig.body.legs.shoes || {};
            cartoonConfig.body.legs.shoes.color = shoesData.color;
        }
    }
    
    // Map accessories from old system
    if (Array.isArray(currentAvatar.accessories)) {
        currentAvatar.accessories.forEach(accId => {
            if (accId === 'acc1' || accId === 'acc3') {
                if (!cartoonConfig.accessories.face.includes('glasses')) {
                    cartoonConfig.accessories.face.push('glasses');
                }
            }
        });
    }
    
    // Map helmet to hat
    if (currentAvatar.helmet) {
        const helmetData = avatarData.helmet.find(h => h.id === currentAvatar.helmet);
        if (helmetData) {
            if (helmetData.id === 'helmet4') {
                if (!cartoonConfig.accessories.head.includes('hat')) {
                    cartoonConfig.accessories.head.push('hat');
                }
            } else if (helmetData.id === 'helmet3') {
                if (!cartoonConfig.accessories.head.includes('crown')) {
                    cartoonConfig.accessories.head.push('crown');
                }
            }
        }
    }
    
    // Update cartoon avatar
    try {
        cartoonAvatarRenderer.updateConfig(cartoonConfig);
        // Merge back to currentAvatar
        currentAvatar = { ...currentAvatar, ...cartoonConfig };
    } catch (error) {
        console.error('Error updating avatar:', error);
    }
}

// Update speech bubble
function updateSpeechBubble() {
    const speechBubble = document.getElementById('avatar-speech');
    const messages = [
        'Tu assures grave !',
        'Super look !',
        'Stylé !',
        'J\'adore !',
        'Parfait !',
        'Incroyable !',
        'Génial !'
    ];
    const randomMessage = messages[Math.floor(Math.random() * messages.length)];
    speechBubble.textContent = randomMessage;
}

// Selfie upload
function setupSelfieUpload() {
    const selfieInput = document.getElementById('selfie-input');
    const selfiePreview = document.getElementById('selfie-preview');
    const selfieImg = document.getElementById('selfie-img');
    
    selfieInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                selfieImg.src = e.target.result;
                selfiePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
}

function removeSelfie() {
    const selfieInput = document.getElementById('selfie-input');
    const selfiePreview = document.getElementById('selfie-preview');
    selfieInput.value = '';
    selfiePreview.style.display = 'none';
}

// Load preset avatar
function loadPreset(presetType) {
    if (!AvatarPresets || !AvatarPresets[presetType]) {
        console.error('Preset not found:', presetType);
        return;
    }
    
    // Remove active class from all preset buttons
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to selected preset
    const presetBtn = document.querySelector(`[data-preset="${presetType}"]`);
    if (presetBtn) {
        presetBtn.classList.add('active');
    }
    
    // Load preset configuration
    const preset = AvatarPresets[presetType];
    currentAvatar = JSON.parse(JSON.stringify(preset));
    
    // Update avatar display
    updateAvatarDisplay();
    updateSpeechBubble();
    
    // Show notification
    showNotification(`Avatar "${presetType}" chargé ! 🎨`, 'success');
}

// Simulate AI generation workflow
async function simulateAIGeneration() {
    const selfieInput = document.getElementById('selfie-input');
    const selfiePreview = document.getElementById('selfie-preview');
    const selfieImg = document.getElementById('selfie-img');
    
    // Step 1: Simulate selfie upload
    if (!selfieInput.files[0] && !selfieImg.src) {
        // Simulate file upload
        showNotification('📸 Selfie uploadé → Génération en cours...', 'info');
        
        // Activate step 1
        activateWorkflowStep(1);
        
        // Wait a bit then simulate AI generation
        setTimeout(() => {
            // Generate avatar from "AI"
            const aiGeneratedConfig = generateAIAvatar();
            currentAvatar = aiGeneratedConfig;
            updateAvatarDisplay();
            updateSpeechBubble();
            
            // Activate step 2
            activateWorkflowStep(2);
            showNotification('✨ Avatar généré par IA !', 'success');
            
            // Show facial traits editor
            const editor = document.getElementById('facial-traits-editor');
            if (editor) {
                editor.style.display = 'block';
            }
        }, 2000);
    } else {
        // Real AI generation if image is uploaded
        await generateAvatar();
    }
}

// Generate AI avatar (simulation)
function generateAIAvatar() {
    // Simulate AI analysis and generation
    const expressions = ['happy', 'laugh', 'surprised', 'cool', 'wink'];
    const hairStyles = ['short-rounded', 'spiky', 'mohawk', 'long-wavy'];
    const colors = ['#4a90e2', '#e24a4a', '#2ecc71', '#f39c12', '#9b59b6', '#1a1a2e'];
    
    return {
        base: {
            skin_tone: 'light',
            skin_color: '#ffdbac'
        },
        hair: {
            style: hairStyles[Math.floor(Math.random() * hairStyles.length)],
            color: '#333333'
        },
        face: {
            expression: expressions[Math.floor(Math.random() * expressions.length)],
            eyes: {
                style: 'happy',
                color: '#000000',
                size: 'medium'
            },
            mouth: {
                style: 'smile',
                color: '#ff6b6b'
            },
            eyebrows: {
                style: 'soft',
                color: '#2c2c2c'
            },
            nose: {
                style: 'normal',
                size: 'medium'
            },
            cheeks: true
        },
        body: {
            torso: {
                color: colors[Math.floor(Math.random() * colors.length)],
                shape: 'rounded'
            },
            arms: {
                position: 'rest',
                left_color: '#ffdbac',
                right_color: '#ffdbac',
                sleeve_color: colors[Math.floor(Math.random() * colors.length)]
            },
            legs: {
                color: colors[Math.floor(Math.random() * colors.length)],
                width: 35,
                height: 80,
                spacing: 20
            }
        },
        accessories: {
            head: [],
            face: [],
            body: []
        },
        animation: {
            idle: true,
            type: 'breathe',
            speed: 'normal'
        },
        metadata: {
            generated_from_selfie: true,
            ai_generated: true
        }
    };
}

// Activate workflow step
function activateWorkflowStep(stepNumber) {
    // Deactivate all steps
    document.querySelectorAll('.workflow-step').forEach(step => {
        step.classList.remove('active', 'completed');
    });
    
    // Activate current step and mark previous as completed
    for (let i = 1; i <= stepNumber; i++) {
        const step = document.getElementById(`step-${i}`);
        if (step) {
            if (i < stepNumber) {
                step.classList.add('completed');
            } else {
                step.classList.add('active');
            }
        }
    }
}

// Change facial trait
function changeFacialTrait(trait, value) {
    if (!currentAvatar.face) {
        currentAvatar.face = {};
    }
    
    switch(trait) {
        case 'expression':
            currentAvatar.face.expression = value;
            break;
        case 'eyeColor':
            if (!currentAvatar.face.eyes) currentAvatar.face.eyes = {};
            currentAvatar.face.eyes.color = value;
            break;
        case 'mouthColor':
            if (!currentAvatar.face.mouth) currentAvatar.face.mouth = {};
            currentAvatar.face.mouth.color = value;
            break;
        case 'noseSize':
            if (!currentAvatar.face.nose) currentAvatar.face.nose = {};
            currentAvatar.face.nose.size = value;
            break;
    }
    
    updateAvatarDisplay();
    updateSpeechBubble();
    
    // Activate step 3 when adding accessories
    if (trait === 'expression' && value) {
        activateWorkflowStep(3);
    }
}

// Generate avatar with AI
async function generateAvatar() {
    const selfieInput = document.getElementById('selfie-input');
    const selfiePreview = document.getElementById('selfie-preview');
    const selfieImg = document.getElementById('selfie-img');
    
    if (!selfieInput.files[0]) {
        alert('Veuillez d\'abord uploader un selfie !');
        return;
    }
    
    if (!selfieImg.src) {
        alert('Veuillez attendre que l\'image soit chargée !');
        return;
    }
    
    // Vérifier que l'analyzer est disponible
    if (typeof avatarAIAnalyzer === 'undefined') {
        console.error('Avatar AI Analyzer non disponible');
        alert('Erreur : Le système d\'analyse IA n\'est pas disponible');
        return;
    }
    
    const generateBtn = document.querySelector('.generate-btn');
    const originalText = generateBtn.innerHTML;
    generateBtn.innerHTML = '<span class="sparkle">✨</span> Analyse en cours...';
    generateBtn.disabled = true;
    
    try {
        // Afficher une barre de progression
        showProgressBar();
        
        // Analyser l'image avec l'IA
        const avatarConfig = await avatarAIAnalyzer.analyzeImage(selfieImg.src);
        
        // Mettre à jour la configuration de l'avatar
        updateAvatarFromConfig(avatarConfig);
        
        // Mettre à jour l'affichage
        const activeCategory = document.querySelector('.category-btn.active')?.dataset.category || 'hair';
        loadCategory(activeCategory);
        updateAvatarDisplay();
        updateSpeechBubble();
        
        // Afficher les résultats de l'analyse
        showAnalysisResults(avatarConfig.metadata);
        
        generateBtn.innerHTML = originalText;
        generateBtn.disabled = false;
        hideProgressBar();
        
        // Show success message
        showNotification('Avatar généré avec succès ! ✨', 'success');
        
    } catch (error) {
        console.error('Erreur lors de la génération de l\'avatar:', error);
        generateBtn.innerHTML = originalText;
        generateBtn.disabled = false;
        hideProgressBar();
        showNotification('Erreur lors de la génération. Veuillez réessayer.', 'error');
    }
}

// Mettre à jour l'avatar à partir d'une configuration générée
function updateAvatarFromConfig(config) {
    // Mettre à jour la base
    if (config.base) {
        currentAvatar.base = { ...currentAvatar.base, ...config.base };
    }
    
    // Mettre à jour les cheveux
    if (config.hair) {
        currentAvatar.hair = { ...currentAvatar.hair, ...config.hair };
    }
    
    // Mettre à jour le visage
    if (config.face) {
        currentAvatar.face = { ...currentAvatar.face, ...config.face };
    }
    
    // Mettre à jour le corps
    if (config.body) {
        if (!currentAvatar.body) currentAvatar.body = {};
        if (config.body.torso) {
            currentAvatar.body.torso = { ...currentAvatar.body.torso, ...config.body.torso };
        }
        if (config.body.arms) {
            currentAvatar.body.arms = { ...currentAvatar.body.arms, ...config.body.arms };
        }
        if (config.body.legs) {
            currentAvatar.body.legs = { ...currentAvatar.body.legs, ...config.body.legs };
        }
    }
    
    // Mettre à jour les accessoires
    if (config.accessories) {
        currentAvatar.accessories = { ...currentAvatar.accessories, ...config.accessories };
    }
    
    // Mettre à jour les animations
    if (config.animation) {
        currentAvatar.animation = { ...currentAvatar.animation, ...config.animation };
    }
    
    // Sauvegarder les métadonnées
    if (config.metadata) {
        currentAvatar.metadata = config.metadata;
    }
}

// Afficher une barre de progression
function showProgressBar() {
    let progressBar = document.getElementById('ai-progress-bar');
    if (!progressBar) {
        progressBar = document.createElement('div');
        progressBar.id = 'ai-progress-bar';
        progressBar.className = 'ai-progress-bar';
        progressBar.innerHTML = `
            <div class="progress-container">
                <div class="progress-label">Analyse de l'image...</div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <div class="progress-steps">
                    <span class="step active" id="step-1">📸 Chargement</span>
                    <span class="step" id="step-2">🎨 Analyse couleurs</span>
                    <span class="step" id="step-3">👤 Détection visage</span>
                    <span class="step" id="step-4">✨ Génération</span>
                </div>
            </div>
        `;
        document.querySelector('.ai-section').appendChild(progressBar);
    }
    
    progressBar.style.display = 'block';
    
    // Animation de progression
    let progress = 0;
    const interval = setInterval(() => {
        progress += 10;
        const fill = document.getElementById('progress-fill');
        if (fill) {
            fill.style.width = progress + '%';
        }
        
        // Mettre à jour les étapes
        if (progress > 25) document.getElementById('step-1')?.classList.add('completed');
        if (progress > 50) document.getElementById('step-2')?.classList.add('active', 'completed');
        if (progress > 75) document.getElementById('step-3')?.classList.add('active', 'completed');
        if (progress >= 100) {
            document.getElementById('step-4')?.classList.add('active', 'completed');
            clearInterval(interval);
        }
    }, 150);
}

// Masquer la barre de progression
function hideProgressBar() {
    const progressBar = document.getElementById('ai-progress-bar');
    if (progressBar) {
        progressBar.style.display = 'none';
    }
}

// Afficher les résultats de l'analyse
function showAnalysisResults(metadata) {
    if (!metadata) return;
    
    const resultsHtml = `
        <div class="analysis-results">
            <h4>📊 Résultats de l'analyse</h4>
            <div class="result-item">
                <span class="result-label">Confiance:</span>
                <span class="result-value">${Math.round(metadata.analysis_confidence * 100)}%</span>
            </div>
            ${metadata.detected_colors ? `
                <div class="result-item">
                    <span class="result-label">Couleurs détectées:</span>
                    <div class="color-palette">
                        ${metadata.detected_colors.slice(0, 5).map(color => 
                            `<span class="color-swatch" style="background: ${color}"></span>`
                        ).join('')}
                    </div>
                </div>
            ` : ''}
        </div>
    `;
    
    let resultsContainer = document.getElementById('analysis-results');
    if (!resultsContainer) {
        resultsContainer = document.createElement('div');
        resultsContainer.id = 'analysis-results';
        document.querySelector('.ai-section').appendChild(resultsContainer);
    }
    
    resultsContainer.innerHTML = resultsHtml;
    
    // Masquer après 5 secondes
    setTimeout(() => {
        if (resultsContainer) {
            resultsContainer.style.opacity = '0';
            setTimeout(() => {
                resultsContainer.remove();
            }, 500);
        }
    }, 5000);
}

// Randomize avatar - Cartoon System
function randomizeAvatar() {
    // Random expression
    const expressions = ['happy', 'laugh', 'surprised', 'sad', 'neutral', 'cool', 'wink', 'star'];
    if (!currentAvatar.face) currentAvatar.face = {};
    currentAvatar.face.expression = expressions[Math.floor(Math.random() * expressions.length)];
    
    // Random colors
    const colors = ['#4a90e2', '#e24a4a', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#ff6b6b'];
    if (!currentAvatar.body) currentAvatar.body = {};
    if (!currentAvatar.body.torso) currentAvatar.body.torso = {};
    if (!currentAvatar.body.legs) currentAvatar.body.legs = {};
    if (!currentAvatar.body.arms) currentAvatar.body.arms = {};
    
    currentAvatar.body.torso.color = colors[Math.floor(Math.random() * colors.length)];
    currentAvatar.body.legs.color = colors[Math.floor(Math.random() * colors.length)];
    currentAvatar.body.arms.sleeve_color = currentAvatar.body.torso.color;
    
    // Random arm position
    const positions = ['rest', 'open', 'raised'];
    currentAvatar.body.arms.position = positions[Math.floor(Math.random() * positions.length)];
    
    // Random accessories
    if (!currentAvatar.accessories) currentAvatar.accessories = { head: [], face: [], body: [] };
    currentAvatar.accessories.head = [];
    currentAvatar.accessories.face = [];
    
    if (Math.random() > 0.7) {
        currentAvatar.accessories.face.push('glasses');
    }
    if (Math.random() > 0.7) {
        currentAvatar.accessories.head.push('hat');
    }
    
    // Reload current category
    const activeCategory = document.querySelector('.category-btn.active').dataset.category;
    loadCategory(activeCategory);
    updateAvatarDisplay();
    updateSpeechBubble();
    
    showNotification('Avatar randomisé ! 🎲');
}

// Download avatar
async function downloadAvatar() {
    // Trouver le conteneur de l'avatar cartoon
    const avatarContainer = document.getElementById('cartoon-avatar-container');
    const avatarElement = avatarContainer?.querySelector('.avatar-cartoon') || avatarContainer?.querySelector('.avatar-cartoon-container');
    
    if (!avatarContainer && !avatarElement) {
        showNotification('Erreur : Avatar non trouvé', 'error');
        return;
    }
    
    // Cible : l'élément avatar réel ou son conteneur
    const targetElement = avatarElement || avatarContainer;
    
    // Vérifier si html2canvas est disponible
    if (typeof html2canvas === 'undefined') {
        // Charger html2canvas depuis CDN
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
        script.onload = () => captureAndDownload();
        document.head.appendChild(script);
    } else {
        captureAndDownload();
    }
    
    function captureAndDownload() {
        // Afficher un message de chargement
        showNotification('Capture de l\'avatar en cours...', 'info');
        
        // Attendre un peu pour s'assurer que tous les éléments sont rendus
        setTimeout(() => {
            // Calculer les dimensions optimales
            const rect = targetElement.getBoundingClientRect();
            const width = Math.max(rect.width, 400);
            const height = Math.max(rect.height, 500);
            
            // Options pour html2canvas - optimisées pour capturer tous les détails
            const options = {
                backgroundColor: '#1a1a2e', // Fond sombre
                scale: 3, // Très haute qualité pour capturer tous les détails
                logging: false,
                useCORS: true,
                allowTaint: true,
                width: width,
                height: height,
                x: rect.left,
                y: rect.top,
                windowWidth: window.innerWidth,
                windowHeight: window.innerHeight,
                scrollX: 0,
                scrollY: 0,
                // Options importantes pour capturer les pseudo-éléments et les détails CSS
                ignoreElements: (element) => {
                    // Ne pas ignorer les éléments de l'avatar
                    return false;
                },
                onclone: (clonedDoc) => {
                    // S'assurer que tous les styles sont préservés dans le clone
                    const clonedElement = clonedDoc.querySelector('#cartoon-avatar-container') || 
                                        clonedDoc.querySelector('.avatar-cartoon') ||
                                        clonedDoc.querySelector('.avatar-cartoon-container');
                    if (clonedElement) {
                        // Forcer l'affichage de tous les éléments
                        clonedElement.style.display = 'block';
                        clonedElement.style.visibility = 'visible';
                        clonedElement.style.opacity = '1';
                        // S'assurer que tous les enfants sont visibles
                        const allChildren = clonedElement.querySelectorAll('*');
                        allChildren.forEach(child => {
                            child.style.display = '';
                            child.style.visibility = 'visible';
                            child.style.opacity = '1';
                        });
                    }
                }
            };
            
            html2canvas(targetElement, options).then(canvas => {
                // Créer un canvas final avec padding pour un meilleur rendu
                const padding = 40;
                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = canvas.width + (padding * 2);
                finalCanvas.height = canvas.height + (padding * 2);
                const ctx = finalCanvas.getContext('2d');
                
                // Fond sombre élégant
                ctx.fillStyle = '#0a0a0a';
                ctx.fillRect(0, 0, finalCanvas.width, finalCanvas.height);
                
                // Dessiner l'avatar capturé au centre avec padding
                ctx.drawImage(canvas, padding, padding);
                
                // Convertir en image et télécharger
                finalCanvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'mon-avatar-' + Date.now() + '.png';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                    showNotification('Avatar téléchargé avec tous les détails ! ⬇️', 'success');
                }, 'image/png', 1.0); // Qualité maximale
            }).catch(error => {
                console.error('Erreur lors de la capture:', error);
                showNotification('Erreur lors du téléchargement. Veuillez réessayer.', 'error');
            });
        }, 100); // Petit délai pour s'assurer que tout est rendu
    }
}

// Save avatar
function saveAvatar() {
    const saveBtn = document.querySelector('.save-btn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Sauvegarde...';
    saveBtn.disabled = true;
    
    // Prepare data to send
    const dataToSave = {
        avatar_data: currentAvatar,
        avatar_name: 'Mon Qbit'
    };
    
    // Send to server
    fetch('save_avatar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dataToSave)
    })
    .then(response => response.json())
    .then(data => {
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
        
        if (data.success) {
            showModal();
            showNotification('Avatar sauvegardé ! ✅');
            
            // Rediriger vers room_collab.php après 2 secondes pour voir l'avatar dans le cercle
            setTimeout(function() {
                // Essayer de trouver l'ID de collaboration depuis l'URL ou la session
                // Si on vient de room_collab.php, on peut utiliser document.referrer
                const referrer = document.referrer;
                const collabIdMatch = referrer.match(/[?&]id=(\d+)/);
                
                if (collabIdMatch) {
                    // Rediriger vers la room collab avec l'ID trouvé
                    window.location.href = 'collabcrud/room_collab.php?id=' + collabIdMatch[1] + '&avatar_saved=1';
                } else {
                    // Sinon, essayer de trouver dans l'URL actuelle ou rediriger vers les collaborations
                    const currentUrl = window.location.href;
                    const currentMatch = currentUrl.match(/[?&]collab_id=(\d+)/);
                    
                    if (currentMatch) {
                        window.location.href = 'collabcrud/room_collab.php?id=' + currentMatch[1] + '&avatar_saved=1';
                    } else {
                        // Par défaut, retourner à la page précédente ou aux collaborations
                        if (window.history.length > 1) {
                            window.history.back();
                        } else {
                            window.location.href = '../frontoffice/collaborations.php';
                        }
                    }
                }
            }, 2000);
        } else {
            showNotification('Erreur lors de la sauvegarde ❌');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
        showNotification('Erreur lors de la sauvegarde ❌');
    });
}

// Show modal
function showModal() {
    const modal = document.getElementById('success-modal');
    modal.style.display = 'block';
}

function closeModal() {
    const modal = document.getElementById('success-modal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('success-modal');
    if (event.target === modal) {
        closeModal();
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    
    // Définir les couleurs selon le type
    let bgColor, textColor, shadowColor;
    switch(type) {
        case 'success':
            bgColor = 'rgba(46, 204, 113, 0.9)';
            textColor = '#fff';
            shadowColor = 'rgba(46, 204, 113, 0.4)';
            break;
        case 'error':
            bgColor = 'rgba(231, 76, 60, 0.9)';
            textColor = '#fff';
            shadowColor = 'rgba(231, 76, 60, 0.4)';
            break;
        case 'info':
        default:
            bgColor = 'rgba(0, 255, 136, 0.9)';
            textColor = '#000';
            shadowColor = 'rgba(0, 255, 136, 0.4)';
            break;
    }
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: ${textColor};
        padding: 1rem 2rem;
        border-radius: 10px;
        font-weight: 700;
        z-index: 10000;
        box-shadow: 0 5px 20px ${shadowColor};
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 2000);
}

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

