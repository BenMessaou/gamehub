// Cartoon Avatar Renderer - Complete Character System

class CartoonAvatarRenderer {
    constructor(containerId, config = {}) {
        this.container = typeof containerId === 'string' 
            ? document.getElementById(containerId) 
            : containerId;
        this.config = this.mergeConfig(config);
        this.init();
    }
    
    mergeConfig(userConfig) {
        const defaultConfig = {
            base: {
                skin_tone: 'light',
                skin_color: '#ffdbac'
            },
            hair: {
                style: 'short-rounded',
                color: '#000000'
            },
            face: {
                expression: 'happy',
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
                cheeks: true,
                blush_color: '#ffb3ba'
            },
            body: {
                torso: {
                    color: '#4a90e2',
                    shape: 'rounded',
                    width: 90,
                    height: 100
                },
                arms: {
                    position: 'rest',
                    left_color: '#ffdbac',
                    right_color: '#ffdbac',
                    sleeve_color: '#4a90e2'
                },
                legs: {
                    color: '#2c5aa0',
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
            style: {
                shadow: true,
                glow: false,
                outline: true,
                outline_color: '#ffffff',
                outline_width: 2
            }
        };
        
        return this.deepMerge(defaultConfig, userConfig);
    }
    
    deepMerge(target, source) {
        const output = { ...target };
        if (this.isObject(target) && this.isObject(source)) {
            Object.keys(source).forEach(key => {
                if (this.isObject(source[key])) {
                    if (!(key in target)) {
                        Object.assign(output, { [key]: source[key] });
                    } else {
                        output[key] = this.deepMerge(target[key], source[key]);
                    }
                } else {
                    Object.assign(output, { [key]: source[key] });
                }
            });
        }
        return output;
    }
    
    isObject(item) {
        return item && typeof item === 'object' && !Array.isArray(item);
    }
    
    init() {
        if (!this.container) {
            console.error('Container not found');
            return;
        }
        this.render();
    }
    
    render() {
        this.container.innerHTML = '';
        this.container.className = 'avatar-cartoon';
        
        // Add animation class
        if (this.config.animation && this.config.animation.idle) {
            if (this.config.animation.type === 'wiggle') {
                this.container.classList.add('wiggle');
            } else {
                this.container.classList.add(`idle-${this.config.animation.type}`);
            }
        } else {
            // Default bounce animation
            this.container.classList.add('idle-bounce');
        }
        
        // Add style classes
        if (this.config.style.shadow) {
            this.container.classList.add('shadow-soft');
        }
        if (this.config.style.glow) {
            this.container.classList.add('glow');
        }
        
        // Create container
        const container = document.createElement('div');
        container.className = 'avatar-cartoon-container';
        
        // Head
        const head = this.createHead();
        
        // Hair (must be on head, before face)
        const hair = this.createHair();
        if (hair) {
            head.appendChild(hair);
        }
        
        // Face (must be inside head for proper positioning)
        const face = this.createFace();
        head.appendChild(face);
        
        container.appendChild(head);
        
        // Torso (must be created before arms for positioning)
        const torso = this.createTorso();
        
        // Arms (must be attached to torso for proper positioning)
        const arms = this.createArms();
        torso.appendChild(arms);
        
        container.appendChild(torso);
        
        // Legs
        const legs = this.createLegs();
        container.appendChild(legs);
        
        // Accessories (pass head reference)
        this.createAccessories(container, head);
        
        this.container.appendChild(container);
    }
    
    createHead() {
        const head = document.createElement('div');
        head.className = 'avatar-head-cartoon';
        head.classList.add(`skin-${this.config.base.skin_tone}`);
        
        // Add idle animation to head
        if (this.config.animation && this.config.animation.idle) {
            head.classList.add('idle');
        }
        
        // Set custom skin color if provided
        if (this.config.base.skin_color) {
            head.style.background = `linear-gradient(135deg, ${this.config.base.skin_color}, ${this.shadeColor(this.config.base.skin_color, -20)})`;
        }
        
        return head;
    }
    
    createHair() {
        if (!this.config.hair || !this.config.hair.style || this.config.hair.style === 'bald') {
            return null;
        }
        
        const hair = document.createElement('div');
        hair.className = 'avatar-hair-cartoon';
        hair.classList.add(`hair-${this.config.hair.style}`);
        
        // Apply hair color
        if (this.config.hair.color) {
            hair.style.background = this.config.hair.color;
        }
        
        return hair;
    }
    
    createFace() {
        const faceContainer = document.createElement('div');
        faceContainer.className = 'avatar-face-svg';
        
        const faceSVG = CartoonFaces.generateFace(
            this.config.face.expression,
            {
                eyeColor: this.config.face.eyes.color,
                mouthColor: this.config.face.mouth.color,
                eyebrowColor: this.config.face.eyebrows.color,
                cheeks: this.config.face.cheeks
            }
        );
        
        faceContainer.innerHTML = faceSVG;
        return faceContainer;
    }
    
    createArms() {
        const armsContainer = document.createElement('div');
        armsContainer.className = 'avatar-arms';
        armsContainer.classList.add(`arms-${this.config.body.arms.position}`);
        
        // Add idle animation
        if (this.config.animation && this.config.animation.idle) {
            armsContainer.classList.add('idle');
        }
        
        // Left arm
        const leftArm = document.createElement('div');
        leftArm.className = 'arm';
        leftArm.style.background = `linear-gradient(135deg, ${this.config.body.arms.left_color}, ${this.shadeColor(this.config.body.arms.left_color, -20)})`;
        
        const leftSleeve = document.createElement('div');
        leftSleeve.className = 'arm-sleeve';
        leftSleeve.style.background = this.config.body.arms.sleeve_color;
        leftArm.appendChild(leftSleeve);
        
        const leftHand = document.createElement('div');
        leftHand.className = 'arm-hand';
        leftHand.style.background = `linear-gradient(135deg, ${this.config.body.arms.left_color}, ${this.shadeColor(this.config.body.arms.left_color, -20)})`;
        leftArm.appendChild(leftHand);
        
        // Right arm
        const rightArm = document.createElement('div');
        rightArm.className = 'arm';
        rightArm.style.background = `linear-gradient(135deg, ${this.config.body.arms.right_color}, ${this.shadeColor(this.config.body.arms.right_color, -20)})`;
        
        const rightSleeve = document.createElement('div');
        rightSleeve.className = 'arm-sleeve';
        rightSleeve.style.background = this.config.body.arms.sleeve_color;
        rightArm.appendChild(rightSleeve);
        
        const rightHand = document.createElement('div');
        rightHand.className = 'arm-hand';
        rightHand.style.background = `linear-gradient(135deg, ${this.config.body.arms.right_color}, ${this.shadeColor(this.config.body.arms.right_color, -20)})`;
        rightArm.appendChild(rightHand);
        
        armsContainer.appendChild(leftArm);
        armsContainer.appendChild(rightArm);
        
        return armsContainer;
    }
    
    createTorso() {
        const torso = document.createElement('div');
        torso.className = 'avatar-torso-cartoon';
        torso.classList.add(`shape-${this.config.body.torso.shape}`);
        
        // Create gradient from color
        const baseColor = this.config.body.torso.color;
        const darkerColor = this.shadeColor(baseColor, -15);
        const darkestColor = this.shadeColor(baseColor, -30);
        torso.style.background = `linear-gradient(135deg, ${baseColor} 0%, ${darkerColor} 50%, ${darkestColor} 100%)`;
        
        torso.style.width = `${this.config.body.torso.width}px`;
        torso.style.height = `${this.config.body.torso.height}px`;
        
        return torso;
    }
    
    createLegs() {
        const legsContainer = document.createElement('div');
        legsContainer.className = 'avatar-legs-cartoon';
        legsContainer.style.gap = `${this.config.body.legs.spacing}px`;
        
        // Left leg
        const leftLeg = document.createElement('div');
        leftLeg.className = 'leg';
        leftLeg.style.background = this.config.body.legs.color;
        leftLeg.style.width = `${this.config.body.legs.width}px`;
        leftLeg.style.height = `${this.config.body.legs.height}px`;
        
            const leftFoot = document.createElement('div');
            leftFoot.className = 'leg-foot';
            // Apply shoe color if configured
            if (this.config.body.legs.shoes && this.config.body.legs.shoes.color) {
                leftFoot.style.background = this.config.body.legs.shoes.color;
            }
            leftLeg.appendChild(leftFoot);
            
            // Right leg
            const rightLeg = document.createElement('div');
            rightLeg.className = 'leg';
            rightLeg.style.background = this.config.body.legs.color;
            rightLeg.style.width = `${this.config.body.legs.width}px`;
            rightLeg.style.height = `${this.config.body.legs.height}px`;
            
            const rightFoot = document.createElement('div');
            rightFoot.className = 'leg-foot';
            // Apply shoe color if configured
            if (this.config.body.legs.shoes && this.config.body.legs.shoes.color) {
                rightFoot.style.background = this.config.body.legs.shoes.color;
            }
            rightLeg.appendChild(rightFoot);
        
        legsContainer.appendChild(leftLeg);
        legsContainer.appendChild(rightLeg);
        
        return legsContainer;
    }
    
    createAccessories(container, head) {
        if (!head) return;
        
        // Head accessories (attached to head)
        if (this.config.accessories.head) {
            this.config.accessories.head.forEach(accId => {
                const accessory = this.createHeadAccessory(accId);
                if (accessory) head.appendChild(accessory);
            });
        }
        
        // Face accessories (attached to head, above face)
        if (this.config.accessories.face) {
            this.config.accessories.face.forEach(accId => {
                const accessory = this.createFaceAccessory(accId);
                if (accessory) head.appendChild(accessory);
            });
        }
    }
    
    createHeadAccessory(accId) {
        const element = document.createElement('div');
        
        switch(accId) {
            case 'hat':
                element.className = 'avatar-hat-cartoon';
                break;
            case 'headset':
                element.className = 'avatar-headset-cartoon';
                element.innerHTML = `
                    <div class="headset-band"></div>
                    <div class="headset-cup-cartoon left"></div>
                    <div class="headset-cup-cartoon right"></div>
                `;
                break;
            default:
                return null;
        }
        
        return element;
    }
    
    createFaceAccessory(accId) {
        const element = document.createElement('div');
        
        switch(accId) {
            case 'glasses':
                element.className = 'avatar-glasses-cartoon';
                element.innerHTML = `
                    <div class="glasses-frame">
                        <div class="glasses-lens-cartoon"></div>
                        <div class="glasses-bridge-cartoon"></div>
                        <div class="glasses-lens-cartoon"></div>
                    </div>
                `;
                break;
            default:
                return null;
        }
        
        return element;
    }
    
    // Helper: Shade color
    shadeColor(color, percent) {
        const num = parseInt(color.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = Math.min(255, Math.max(0, (num >> 16) + amt));
        const G = Math.min(255, Math.max(0, ((num >> 8) & 0x00FF) + amt));
        const B = Math.min(255, Math.max(0, (num & 0x0000FF) + amt));
        return '#' + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
    }
    
    // Update methods
    updateExpression(expression) {
        this.config.face.expression = expression;
        this.render();
    }
    
    updateColors(torsoColor, legsColor) {
        this.config.body.torso.color = torsoColor;
        this.config.body.legs.color = legsColor;
        this.render();
    }
    
    updateArmsPosition(position) {
        this.config.body.arms.position = position;
        this.render();
    }
    
    updateConfig(newConfig) {
        this.config = this.deepMerge(this.config, newConfig);
        this.render();
    }
}

// Export
if (typeof window !== 'undefined') {
    window.CartoonAvatarRenderer = CartoonAvatarRenderer;
}

