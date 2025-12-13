// Avatar Renderer - Main Component to Render Enhanced Avatars

class AvatarRenderer {
    constructor(containerId, avatarConfig) {
        this.container = document.getElementById(containerId);
        this.config = avatarConfig || defaultAvatar;
        this.init();
    }
    
    init() {
        this.render();
    }
    
    render() {
        if (!this.container) return;
        
        const avatar = this.createAvatarElement();
        this.container.innerHTML = '';
        this.container.appendChild(avatar);
    }
    
    createAvatarElement() {
        const avatarWrapper = document.createElement('div');
        avatarWrapper.className = 'avatar-enhanced';
        
        // Add animation class
        if (this.config.animation && this.config.animation.enabled) {
            avatarWrapper.classList.add(`animate-${this.config.animation.idle}`);
        }
        
        // Aura
        if (this.config.aura && this.config.aura.enabled) {
            const aura = this.createAura();
            avatarWrapper.appendChild(aura);
        }
        
        // Container
        const container = document.createElement('div');
        container.className = 'avatar-container';
        
        // Head
        const head = this.createHead();
        container.appendChild(head);
        
        // Hair
        if (this.config.hair && this.config.hair.style !== 'bald') {
            const hair = this.createHair();
            container.appendChild(hair);
        }
        
        // Accessories - Head
        if (this.config.accessories && this.config.accessories.head) {
            this.config.accessories.head.forEach(accId => {
                const accessory = this.createHeadAccessory(accId);
                if (accessory) container.appendChild(accessory);
            });
        }
        
        // Accessories - Face
        if (this.config.accessories && this.config.accessories.face) {
            this.config.accessories.face.forEach(accId => {
                const accessory = this.createFaceAccessory(accId);
                if (accessory) container.appendChild(accessory);
            });
        }
        
        // Body
        const body = this.createBody();
        container.appendChild(body);
        
        // Accessories - Body
        if (this.config.accessories && this.config.accessories.body) {
            this.config.accessories.body.forEach(accId => {
                const accessory = this.createBodyAccessory(accId);
                if (accessory) container.appendChild(accessory);
            });
        }
        
        // Badge
        if (this.config.badge && this.config.badge.enabled) {
            const badge = this.createBadge();
            container.appendChild(badge);
        }
        
        avatarWrapper.appendChild(container);
        return avatarWrapper;
    }
    
    createHead() {
        const head = document.createElement('div');
        head.className = 'avatar-head-enhanced';
        
        // Skin tone
        const skinTone = this.config.base.skin_tone || 'light';
        head.classList.add(`skin-${skinTone}`);
        
        // Expression
        const expression = this.config.face.expression || 'happy';
        head.classList.add(`expression-${expression}`);
        
        // Face features
        const features = document.createElement('div');
        features.className = 'avatar-face-features';
        
        // Eyes
        const eyes = document.createElement('div');
        eyes.className = 'avatar-eyes-enhanced';
        const eye1 = document.createElement('div');
        eye1.className = 'eye';
        const eye2 = document.createElement('div');
        eye2.className = 'eye';
        eyes.appendChild(eye1);
        eyes.appendChild(eye2);
        features.appendChild(eyes);
        
        // Eyebrows
        const eyebrows = document.createElement('div');
        eyebrows.className = 'avatar-eyebrows';
        const eyebrow1 = document.createElement('div');
        eyebrow1.className = 'eyebrow';
        const eyebrow2 = document.createElement('div');
        eyebrow2.className = 'eyebrow';
        
        const expData = AvatarModel.expressions[expression];
        if (expData && expData.eyebrows === 'raised') {
            eyebrow1.classList.add('raised');
            eyebrow2.classList.add('raised');
        } else if (expData && expData.eyebrows === 'lowered') {
            eyebrow1.classList.add('lowered');
            eyebrow2.classList.add('lowered');
        }
        
        eyebrows.appendChild(eyebrow1);
        eyebrows.appendChild(eyebrow2);
        features.appendChild(eyebrows);
        
        // Mouth
        const mouth = document.createElement('div');
        mouth.className = 'avatar-mouth';
        const mouthShape = document.createElement('div');
        mouthShape.className = `mouth-${expData ? expData.mouth : 'smile'}`;
        mouth.appendChild(mouthShape);
        features.appendChild(mouth);
        
        head.appendChild(features);
        return head;
    }
    
    createHair() {
        const hair = document.createElement('div');
        hair.className = 'avatar-hair';
        
        const hairStyle = this.config.hair.style || 'short';
        const hairColor = this.config.hair.color || 'brown';
        
        const hairElement = document.createElement('div');
        hairElement.className = `hair-${hairStyle} hair-${hairColor}`;
        
        // Special handling for spiky hair
        if (hairStyle === 'spiky') {
            for (let i = 0; i < 4; i++) {
                const spike = document.createElement('div');
                spike.className = 'hair-spike';
                hairElement.appendChild(spike);
            }
        }
        
        hair.appendChild(hairElement);
        return hair;
    }
    
    createHeadAccessory(accId) {
        const acc = AvatarModel.accessories.head.find(a => a.id === accId);
        if (!acc) return null;
        
        const element = document.createElement('div');
        
        switch(accId) {
            case 'cap_blue':
            case 'cap_red':
                element.className = 'avatar-cap';
                element.style.background = accId === 'cap_blue' ? '#4a90e2' : '#e24a4a';
                break;
            case 'beanie':
                element.className = 'avatar-beanie';
                break;
            case 'crown':
                element.className = 'avatar-crown';
                for (let i = 0; i < 3; i++) {
                    const spike = document.createElement('div');
                    spike.className = 'crown-spike';
                    element.appendChild(spike);
                }
                break;
        }
        
        return element;
    }
    
    createFaceAccessory(accId) {
        const acc = AvatarModel.accessories.face.find(a => a.id === accId);
        if (!acc) return null;
        
        const element = document.createElement('div');
        element.className = 'avatar-glasses';
        
        switch(accId) {
            case 'glasses_round':
                element.innerHTML = `
                    <div class="glasses-round">
                        <div class="glasses-lens"></div>
                        <div class="glasses-bridge"></div>
                        <div class="glasses-lens"></div>
                    </div>
                `;
                break;
            case 'glasses_square':
                element.innerHTML = `
                    <div class="glasses-round glasses-square">
                        <div class="glasses-lens"></div>
                        <div class="glasses-bridge"></div>
                        <div class="glasses-lens"></div>
                    </div>
                `;
                break;
            case 'sunglasses':
                element.innerHTML = `
                    <div class="glasses-round glasses-sunglasses">
                        <div class="glasses-lens"></div>
                        <div class="glasses-bridge"></div>
                        <div class="glasses-lens"></div>
                    </div>
                `;
                break;
        }
        
        return element;
    }
    
    createBody() {
        const bodyWrapper = document.createElement('div');
        
        // Torso
        const torso = document.createElement('div');
        torso.className = 'avatar-torso-enhanced';
        const torsoColor = this.config.body.torso.color || '#4a90e2';
        torso.style.background = torsoColor;
        bodyWrapper.appendChild(torso);
        
        // Legs
        const legs = document.createElement('div');
        legs.className = 'avatar-legs-enhanced';
        const legsColor = this.config.body.legs.color || '#2c5aa0';
        legs.style.background = legsColor;
        bodyWrapper.appendChild(legs);
        
        return bodyWrapper;
    }
    
    createBodyAccessory(accId) {
        if (accId === 'backpack') {
            const backpack = document.createElement('div');
            backpack.className = 'avatar-backpack-enhanced';
            const strap = document.createElement('div');
            strap.className = 'backpack-strap';
            backpack.appendChild(strap);
            return backpack;
        }
        return null;
    }
    
    createAura() {
        const aura = document.createElement('div');
        aura.className = 'avatar-aura';
        
        const tier = this.config.level.tier || 'bronze';
        const tierData = AvatarModel.levelTiers[tier];
        
        aura.classList.add(`aura-${tier}`);
        
        if (this.config.aura.animation) {
            aura.classList.add(`aura-${this.config.aura.animation}`);
        }
        
        aura.style.color = tierData ? tierData.glow : '#cd7f32';
        
        return aura;
    }
    
    createBadge() {
        const badge = document.createElement('div');
        badge.className = 'avatar-badge';
        
        const badgeType = this.config.badge.type || 'achievement';
        const badgeData = AvatarModel.badges.find(b => b.id === badgeType);
        
        if (badgeData) {
            badge.classList.add(`badge-${badgeType}`);
            badge.textContent = badgeData.icon;
            badge.style.borderColor = badgeData.color;
            
            if (this.config.badge.glow) {
                badge.classList.add('badge-glow');
            }
            
            const position = this.config.badge.position || 'top-right';
            badge.classList.add(position);
        }
        
        return badge;
    }
    
    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
        this.render();
    }
    
    updateExpression(expression) {
        this.config.face.expression = expression;
        this.render();
    }
    
    updateHair(style, color) {
        this.config.hair.style = style;
        this.config.hair.color = color;
        this.render();
    }
    
    updateLevel(level) {
        this.config.level.current_level = level;
        this.config.level.tier = getTierFromLevel(level);
        this.render();
    }
}

// Export
if (typeof window !== 'undefined') {
    window.AvatarRenderer = AvatarRenderer;
}

