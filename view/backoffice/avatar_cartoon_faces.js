// Cartoon Face Expressions - SVG Generator

const CartoonFaces = {
    // Generate complete face SVG based on expression
    generateFace(expression = 'happy', config = {}) {
        const expressions = {
            happy: this.getHappyFace(config),
            laugh: this.getLaughFace(config),
            surprised: this.getSurprisedFace(config),
            sad: this.getSadFace(config),
            neutral: this.getNeutralFace(config),
            cool: this.getCoolFace(config),
            wink: this.getWinkFace(config),
            star: this.getStarFace(config)
        };
        
        return expressions[expression] || expressions.happy;
    },
    
    // Happy Face
    getHappyFace(config) {
        const eyeColor = config.eyeColor || '#000000';
        const mouthColor = config.mouthColor || '#ff6b6b';
        const eyebrowColor = config.eyebrowColor || '#2c2c2c';
        const showCheeks = config.cheeks !== false;
        
        return `
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Eyebrows -->
                <path d="M 20 25 Q 25 20 30 25" stroke="${eyebrowColor}" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <path d="M 50 25 Q 55 20 60 25" stroke="${eyebrowColor}" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                
                <!-- Eyes - Happy (with blink animation) -->
                <circle cx="25" cy="35" r="6" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="55" cy="35" r="6" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="27" cy="33" r="2" fill="#fff"/>
                <circle cx="57" cy="33" r="2" fill="#fff"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Mouth - Smile -->
                <path d="M 25 55 Q 40 65 55 55" stroke="${mouthColor}" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                
                ${showCheeks ? this.getCheeks() : ''}
            </svg>
        `;
    },
    
    // Laugh Face
    getLaughFace(config) {
        const eyeColor = config.eyeColor || '#000000';
        const mouthColor = config.mouthColor || '#ff6b6b';
        
        return `
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Eyes - Closed (laughing) -->
                <path d="M 20 35 Q 25 30 30 35" stroke="${eyeColor}" stroke-width="3" fill="none" stroke-linecap="round"/>
                <path d="M 50 35 Q 55 30 60 35" stroke="${eyeColor}" stroke-width="3" fill="none" stroke-linecap="round"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Mouth - Big Laugh -->
                <ellipse cx="40" cy="58" rx="15" ry="12" fill="${mouthColor}"/>
                <ellipse cx="40" cy="55" rx="12" ry="8" fill="#fff"/>
                
                ${this.getCheeks()}
            </svg>
        `;
    },
    
    // Surprised Face
    getSurprisedFace(config) {
        const eyeColor = config.eyeColor || '#000000';
        const mouthColor = config.mouthColor || '#ff6b6b';
        const eyebrowColor = config.eyebrowColor || '#2c2c2c';
        
        return `
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Eyebrows - Raised -->
                <path d="M 18 22 Q 25 15 32 22" stroke="${eyebrowColor}" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <path d="M 48 22 Q 55 15 62 22" stroke="${eyebrowColor}" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                
                <!-- Eyes - Wide Open -->
                <circle cx="25" cy="35" r="8" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="55" cy="35" r="8" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="27" cy="33" r="3" fill="#fff"/>
                <circle cx="57" cy="33" r="3" fill="#fff"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Mouth - O -->
                <circle cx="40" cy="58" r="8" fill="${mouthColor}"/>
            </svg>
        `;
    },
    
    // Sad Face
    getSadFace(config) {
        const eyeColor = config.eyeColor || '#000000';
        const mouthColor = config.mouthColor || '#4a90e2';
        const eyebrowColor = config.eyebrowColor || '#2c2c2c';
        
        return `
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Eyebrows - Lowered -->
                <path d="M 20 28 Q 25 33 30 28" stroke="${eyebrowColor}" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <path d="M 50 28 Q 55 33 60 28" stroke="${eyebrowColor}" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                
                <!-- Eyes - Sad -->
                <circle cx="25" cy="35" r="6" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="55" cy="35" r="6" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="27" cy="37" r="2" fill="#fff"/>
                <circle cx="57" cy="37" r="2" fill="#fff"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Mouth - Frown -->
                <path d="M 25 58 Q 40 48 55 58" stroke="${mouthColor}" stroke-width="3.5" fill="none" stroke-linecap="round"/>
            </svg>
        `;
    },
    
    // Neutral Face
    getNeutralFace(config) {
        const eyeColor = config.eyeColor || '#000000';
        const mouthColor = config.mouthColor || '#666';
        
        return `
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Eyes -->
                <circle cx="25" cy="35" r="6" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="55" cy="35" r="6" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="27" cy="33" r="2" fill="#fff"/>
                <circle cx="57" cy="33" r="2" fill="#fff"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Mouth - Line -->
                <line x1="30" y1="58" x2="50" y2="58" stroke="${mouthColor}" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        `;
    },
    
    // Cool Face
    getCoolFace(config) {
        const eyeColor = config.eyeColor || '#000000';
        const mouthColor = config.mouthColor || '#ff6b6b';
        
        return `
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Eyes - Cool (squint) -->
                <path d="M 20 35 Q 25 32 30 35" stroke="${eyeColor}" stroke-width="3" fill="none" stroke-linecap="round"/>
                <path d="M 50 35 Q 55 32 60 35" stroke="${eyeColor}" stroke-width="3" fill="none" stroke-linecap="round"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Mouth - Smirk -->
                <path d="M 30 55 Q 40 58 50 55" stroke="${mouthColor}" stroke-width="3" fill="none" stroke-linecap="round"/>
            </svg>
        `;
    },
    
    // Wink Face
    getWinkFace(config) {
        const eyeColor = config.eyeColor || '#000000';
        const mouthColor = config.mouthColor || '#ff6b6b';
        
        return `
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Left Eye - Open -->
                <circle cx="25" cy="35" r="6" fill="${eyeColor}" class="eye-blink"/>
                <circle cx="27" cy="33" r="2" fill="#fff"/>
                
                <!-- Right Eye - Winking -->
                <path d="M 50 35 Q 55 32 60 35" stroke="${eyeColor}" stroke-width="3" fill="none" stroke-linecap="round"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Mouth - Smile -->
                <path d="M 25 55 Q 40 65 55 55" stroke="${mouthColor}" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                
                ${this.getCheeks()}
            </svg>
        `;
    },
    
    // Star Eyes Face
    getStarFace(config) {
        const eyeColor = config.eyeColor || '#ffd700';
        const mouthColor = config.mouthColor || '#ff6b6b';
        
        return `
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Star Eyes -->
                <path d="M 25 30 L 27 35 L 32 35 L 28 38 L 30 43 L 25 40 L 20 43 L 22 38 L 18 35 L 23 35 Z" fill="${eyeColor}"/>
                <path d="M 55 30 L 57 35 L 62 35 L 58 38 L 60 43 L 55 40 L 50 43 L 52 38 L 48 35 L 53 35 Z" fill="${eyeColor}"/>
                
                <!-- Nose -->
                <ellipse cx="40" cy="45" rx="3" ry="4" fill="rgba(0,0,0,0.1)"/>
                <path d="M 37 45 Q 40 48 43 45" stroke="rgba(0,0,0,0.15)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                
                <!-- Mouth - Big Smile -->
                <path d="M 20 55 Q 40 70 60 55" stroke="${mouthColor}" stroke-width="4" fill="none" stroke-linecap="round"/>
                
                ${this.getCheeks()}
            </svg>
        `;
    },
    
    // Cheeks helper
    getCheeks() {
        return `
            <!-- Cheeks/Blush -->
            <circle cx="15" cy="50" r="8" fill="rgba(255, 179, 186, 0.4)"/>
            <circle cx="65" cy="50" r="8" fill="rgba(255, 179, 186, 0.4)"/>
        `;
    }
};

// Export
if (typeof window !== 'undefined') {
    window.CartoonFaces = CartoonFaces;
}

