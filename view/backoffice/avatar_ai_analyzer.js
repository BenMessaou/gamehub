// Avatar AI Image Analyzer
// Analyse les images uploadées pour générer automatiquement des avatars

class AvatarAIAnalyzer {
    constructor() {
        this.skinTonePalette = [
            { name: 'light', colors: ['#ffdbac', '#f4c2a1', '#e6c19a'] },
            { name: 'medium', colors: ['#d4a574', '#c68642', '#b8763a'] },
            { name: 'tan', colors: ['#c68642', '#a0724a', '#8d5524'] },
            { name: 'dark', colors: ['#8d5524', '#6b4423', '#4a2c1a'] }
        ];
        
        this.hairColorPalette = [
            { name: 'black', colors: ['#1a1a1a', '#2d2d2d', '#333333'] },
            { name: 'brown', colors: ['#6f4e37', '#8b4513', '#654321'] },
            { name: 'blonde', colors: ['#ffd700', '#ffed4e', '#f4c430'] },
            { name: 'red', colors: ['#ff4500', '#dc143c', '#b22222'] },
            { name: 'blue', colors: ['#4a90e2', '#4169e1', '#1e90ff'] },
            { name: 'purple', colors: ['#7b68ee', '#9370db', '#8a2be2'] },
            { name: 'pink', colors: ['#ff69b4', '#ff1493', '#ff69b4'] },
            { name: 'cyan', colors: ['#00ced1', '#20b2aa', '#48d1cc'] }
        ];
        
        this.expressionMap = {
            'happy': ['smile', 'laugh'],
            'neutral': ['neutral'],
            'surprised': ['surprised'],
            'sad': ['sad'],
            'cool': ['cool', 'wink']
        };
    }
    
    /**
     * Analyse une image et extrait les caractéristiques pour générer un avatar
     * @param {string} imageSrc - URL de l'image (data URL ou URL)
     * @returns {Promise<Object>} Configuration d'avatar générée
     */
    async analyzeImage(imageSrc) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            
            img.onload = () => {
                try {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    
                    // Analyser différentes zones de l'image
                    const analysis = {
                        skinColor: this.extractSkinColor(canvas, ctx),
                        hairColor: this.extractHairColor(canvas, ctx),
                        dominantColors: this.extractDominantColors(canvas, ctx),
                        expression: this.detectExpression(canvas, ctx),
                        hasGlasses: this.detectGlasses(canvas, ctx),
                        hasHat: this.detectHat(canvas, ctx),
                        confidence: 0.8
                    };
                    
                    // Générer la configuration d'avatar
                    const avatarConfig = this.generateAvatarConfig(analysis);
                    
                    resolve(avatarConfig);
                } catch (error) {
                    reject(error);
                }
            };
            
            img.onerror = () => {
                reject(new Error('Impossible de charger l\'image'));
            };
            
            img.src = imageSrc;
        });
    }
    
    /**
     * Extrait la couleur de peau depuis la zone du visage
     */
    extractSkinColor(canvas, ctx) {
        const width = canvas.width;
        const height = canvas.height;
        
        // Zone du visage (centre de l'image, partie médiane)
        const faceX = width * 0.25;
        const faceY = height * 0.3;
        const faceWidth = width * 0.5;
        const faceHeight = height * 0.4;
        
        // Échantillonner plusieurs points dans la zone du visage
        const samples = [];
        const sampleCount = 50;
        
        for (let i = 0; i < sampleCount; i++) {
            const x = faceX + Math.random() * faceWidth;
            const y = faceY + Math.random() * faceHeight;
            const pixel = ctx.getImageData(x, y, 1, 1).data;
            
            // Filtrer les pixels trop sombres ou trop clairs (probablement pas de la peau)
            const r = pixel[0];
            const g = pixel[1];
            const b = pixel[2];
            const brightness = (r + g + b) / 3;
            
            if (brightness > 50 && brightness < 240) {
                samples.push({ r, g, b });
            }
        }
        
        if (samples.length === 0) {
            return '#ffdbac'; // Couleur par défaut
        }
        
        // Calculer la couleur moyenne
        const avgR = Math.round(samples.reduce((sum, s) => sum + s.r, 0) / samples.length);
        const avgG = Math.round(samples.reduce((sum, s) => sum + s.g, 0) / samples.length);
        const avgB = Math.round(samples.reduce((sum, s) => sum + s.b, 0) / samples.length);
        
        return this.rgbToHex(avgR, avgG, avgB);
    }
    
    /**
     * Extrait la couleur des cheveux depuis la zone supérieure de l'image
     */
    extractHairColor(canvas, ctx) {
        const width = canvas.width;
        const height = canvas.height;
        
        // Zone des cheveux (partie supérieure de l'image)
        const hairX = width * 0.2;
        const hairY = height * 0.1;
        const hairWidth = width * 0.6;
        const hairHeight = height * 0.25;
        
        const samples = [];
        const sampleCount = 30;
        
        for (let i = 0; i < sampleCount; i++) {
            const x = hairX + Math.random() * hairWidth;
            const y = hairY + Math.random() * hairHeight;
            const pixel = ctx.getImageData(x, y, 1, 1).data;
            
            const r = pixel[0];
            const g = pixel[1];
            const b = pixel[2];
            
            samples.push({ r, g, b });
        }
        
        if (samples.length === 0) {
            return '#333333'; // Couleur par défaut
        }
        
        // Calculer la couleur moyenne
        const avgR = Math.round(samples.reduce((sum, s) => sum + s.r, 0) / samples.length);
        const avgG = Math.round(samples.reduce((sum, s) => sum + s.g, 0) / samples.length);
        const avgB = Math.round(samples.reduce((sum, s) => sum + s.b, 0) / samples.length);
        
        return this.rgbToHex(avgR, avgG, avgB);
    }
    
    /**
     * Extrait les couleurs dominantes de l'image
     */
    extractDominantColors(canvas, ctx) {
        const width = canvas.width;
        const height = canvas.height;
        const imageData = ctx.getImageData(0, 0, width, height);
        const data = imageData.data;
        
        // Échantillonner tous les 10 pixels pour la performance
        const colorMap = new Map();
        const step = 10;
        
        for (let i = 0; i < data.length; i += step * 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            
            // Quantifier les couleurs (arrondir à des valeurs de 20)
            const qR = Math.round(r / 20) * 20;
            const qG = Math.round(g / 20) * 20;
            const qB = Math.round(b / 20) * 20;
            
            const colorKey = `${qR},${qG},${qB}`;
            colorMap.set(colorKey, (colorMap.get(colorKey) || 0) + 1);
        }
        
        // Trier par fréquence
        const sortedColors = Array.from(colorMap.entries())
            .sort((a, b) => b[1] - a[1])
            .slice(0, 5)
            .map(([colorKey]) => {
                const [r, g, b] = colorKey.split(',').map(Number);
                return this.rgbToHex(r, g, b);
            });
        
        return sortedColors;
    }
    
    /**
     * Détecte l'expression faciale (simplifié)
     */
    detectExpression(canvas, ctx) {
        // Pour une vraie détection, on utiliserait une API ML
        // Ici, on simule en analysant la luminosité de la zone de la bouche
        const width = canvas.width;
        const height = canvas.height;
        
        // Zone de la bouche (bas du visage)
        const mouthX = width * 0.35;
        const mouthY = height * 0.65;
        const mouthWidth = width * 0.3;
        const mouthHeight = height * 0.1;
        
        const samples = [];
        for (let i = 0; i < 20; i++) {
            const x = mouthX + Math.random() * mouthWidth;
            const y = mouthY + Math.random() * mouthHeight;
            const pixel = ctx.getImageData(x, y, 1, 1).data;
            const brightness = (pixel[0] + pixel[1] + pixel[2]) / 3;
            samples.push(brightness);
        }
        
        const avgBrightness = samples.reduce((a, b) => a + b, 0) / samples.length;
        
        // Heuristique simple : plus lumineux = sourire
        if (avgBrightness > 180) {
            return 'happy';
        } else if (avgBrightness > 150) {
            return 'neutral';
        } else {
            return 'surprised';
        }
    }
    
    /**
     * Détecte la présence de lunettes
     */
    detectGlasses(canvas, ctx) {
        // Zone des yeux
        const width = canvas.width;
        const height = canvas.height;
        
        const eyeY = height * 0.4;
        const eyeHeight = height * 0.15;
        
        // Vérifier la présence de formes rectangulaires/circulaires (simplifié)
        // En réalité, on utiliserait une détection de formes plus sophistiquée
        const leftEyeX = width * 0.3;
        const rightEyeX = width * 0.7;
        const eyeWidth = width * 0.15;
        
        // Échantillonner autour des yeux
        let edgeCount = 0;
        for (let i = 0; i < 20; i++) {
            const x = (Math.random() < 0.5 ? leftEyeX : rightEyeX) + Math.random() * eyeWidth;
            const y = eyeY + Math.random() * eyeHeight;
            const pixel = ctx.getImageData(x, y, 1, 1).data;
            
            // Détecter les bords (changements brusques de luminosité)
            const brightness = (pixel[0] + pixel[1] + pixel[2]) / 3;
            if (brightness < 100 || brightness > 200) {
                edgeCount++;
            }
        }
        
        // Si beaucoup de bords détectés, probablement des lunettes
        return edgeCount > 10;
    }
    
    /**
     * Détecte la présence d'un chapeau/casquette
     */
    detectHat(canvas, ctx) {
        // Zone supérieure de l'image
        const width = canvas.width;
        const height = canvas.height;
        
        const hatY = height * 0.05;
        const hatHeight = height * 0.15;
        const hatWidth = width * 0.6;
        const hatX = width * 0.2;
        
        // Vérifier la présence de formes au-dessus de la tête
        const samples = [];
        for (let i = 0; i < 15; i++) {
            const x = hatX + Math.random() * hatWidth;
            const y = hatY + Math.random() * hatHeight;
            const pixel = ctx.getImageData(x, y, 1, 1).data;
            const brightness = (pixel[0] + pixel[1] + pixel[2]) / 3;
            samples.push(brightness);
        }
        
        const avgBrightness = samples.reduce((a, b) => a + b, 0) / samples.length;
        
        // Si la zone est significativement plus sombre ou plus claire que le fond, probablement un chapeau
        return avgBrightness < 100 || avgBrightness > 200;
    }
    
    /**
     * Génère une configuration d'avatar basée sur l'analyse
     */
    generateAvatarConfig(analysis) {
        const config = {
            base: {
                skin_tone: this.mapSkinTone(analysis.skinColor),
                skin_color: analysis.skinColor
            },
            hair: {
                style: this.detectHairStyle(analysis),
                color: this.mapHairColor(analysis.hairColor)
            },
            face: {
                expression: analysis.expression,
                eyes: {
                    style: this.mapEyeStyle(analysis.expression),
                    color: '#000000'
                },
                mouth: {
                    style: this.mapMouthStyle(analysis.expression),
                    color: '#ff6b6b'
                },
                eyebrows: {
                    style: 'soft',
                    color: '#2c2c2c'
                },
                cheeks: true
            },
            body: {
                torso: {
                    color: this.selectClothingColor(analysis.dominantColors),
                    shape: 'rounded'
                },
                arms: {
                    position: 'rest',
                    left_color: analysis.skinColor,
                    right_color: analysis.skinColor,
                    sleeve_color: this.selectClothingColor(analysis.dominantColors)
                },
                legs: {
                    color: this.shadeColor(this.selectClothingColor(analysis.dominantColors), -30),
                    width: 35,
                    height: 80,
                    spacing: 20
                }
            },
            accessories: {
                head: analysis.hasHat ? ['hat'] : [],
                face: analysis.hasGlasses ? ['glasses'] : [],
                body: []
            },
            animation: {
                idle: true,
                type: 'breathe',
                speed: 'normal'
            },
            metadata: {
                generated_from_selfie: true,
                analysis_confidence: analysis.confidence,
                detected_colors: analysis.dominantColors
            }
        };
        
        return config;
    }
    
    /**
     * Mappe une couleur de peau vers une palette
     */
    mapSkinTone(color) {
        const rgb = this.hexToRgb(color);
        if (!rgb) return 'light';
        
        let bestMatch = 'light';
        let minDistance = Infinity;
        
        for (const palette of this.skinTonePalette) {
            for (const paletteColor of palette.colors) {
                const paletteRgb = this.hexToRgb(paletteColor);
                if (paletteRgb) {
                    const distance = this.colorDistance(rgb, paletteRgb);
                    if (distance < minDistance) {
                        minDistance = distance;
                        bestMatch = palette.name;
                    }
                }
            }
        }
        
        return bestMatch;
    }
    
    /**
     * Mappe une couleur de cheveux vers une palette
     */
    mapHairColor(color) {
        const rgb = this.hexToRgb(color);
        if (!rgb) return '#333333';
        
        let bestMatch = '#333333';
        let minDistance = Infinity;
        
        for (const palette of this.hairColorPalette) {
            for (const paletteColor of palette.colors) {
                const paletteRgb = this.hexToRgb(paletteColor);
                if (paletteRgb) {
                    const distance = this.colorDistance(rgb, paletteRgb);
                    if (distance < minDistance) {
                        minDistance = distance;
                        bestMatch = paletteColor;
                    }
                }
            }
        }
        
        return bestMatch;
    }
    
    /**
     * Détecte le style de cheveux (simplifié)
     */
    detectHairStyle(analysis) {
        // Styles disponibles
        const styles = ['short-rounded', 'long-wavy', 'spiky', 'afro', 'bangs', 'mohawk', 'bun', 'ponytail', 'bald'];
        
        // Pour une vraie détection, on analyserait la forme et la texture
        // Ici, on sélectionne aléatoirement parmi les styles communs
        const commonStyles = ['short-rounded', 'long-wavy', 'spiky', 'bangs'];
        return commonStyles[Math.floor(Math.random() * commonStyles.length)];
    }
    
    /**
     * Mappe une expression vers un style d'yeux
     */
    mapEyeStyle(expression) {
        const map = {
            'happy': 'happy',
            'laugh': 'happy',
            'surprised': 'surprised',
            'sad': 'sad',
            'neutral': 'neutral',
            'cool': 'cool',
            'wink': 'wink'
        };
        return map[expression] || 'happy';
    }
    
    /**
     * Mappe une expression vers un style de bouche
     */
    mapMouthStyle(expression) {
        const map = {
            'happy': 'smile',
            'laugh': 'laugh',
            'surprised': 'surprised',
            'sad': 'sad',
            'neutral': 'neutral',
            'cool': 'smile',
            'wink': 'smile'
        };
        return map[expression] || 'smile';
    }
    
    /**
     * Sélectionne une couleur de vêtement basée sur les couleurs dominantes
     */
    selectClothingColor(dominantColors) {
        // Filtrer les couleurs de peau et de cheveux
        const clothingColors = dominantColors.filter(color => {
            const rgb = this.hexToRgb(color);
            if (!rgb) return false;
            
            // Exclure les tons de peau (beiges, roses)
            const isSkinTone = rgb.r > 180 && rgb.g > 150 && rgb.b > 120;
            // Exclure les tons très sombres (probablement cheveux)
            const isHair = (rgb.r + rgb.g + rgb.b) < 100;
            
            return !isSkinTone && !isHair;
        });
        
        if (clothingColors.length > 0) {
            return clothingColors[0];
        }
        
        // Couleurs par défaut attrayantes
        const defaultColors = ['#4a90e2', '#e24a4a', '#2ecc71', '#f39c12', '#9b59b6'];
        return defaultColors[Math.floor(Math.random() * defaultColors.length)];
    }
    
    /**
     * Utilitaires de conversion de couleurs
     */
    rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    }
    
    hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }
    
    colorDistance(rgb1, rgb2) {
        const rDiff = rgb1.r - rgb2.r;
        const gDiff = rgb1.g - rgb2.g;
        const bDiff = rgb1.b - rgb2.b;
        return Math.sqrt(rDiff * rDiff + gDiff * gDiff + bDiff * bDiff);
    }
    
    shadeColor(color, percent) {
        const rgb = this.hexToRgb(color);
        if (!rgb) return color;
        
        const r = Math.max(0, Math.min(255, rgb.r + (rgb.r * percent / 100)));
        const g = Math.max(0, Math.min(255, rgb.g + (rgb.g * percent / 100)));
        const b = Math.max(0, Math.min(255, rgb.b + (rgb.b * percent / 100)));
        
        return this.rgbToHex(Math.round(r), Math.round(g), Math.round(b));
    }
}

// Instance globale
const avatarAIAnalyzer = new AvatarAIAnalyzer();

