// Avatar Presets - 3 Pre-made Avatars for Demo
// Réaliste, Cartoon, Gamer

const AvatarPresets = {
    // Avatar Réaliste - Style professionnel et naturel
    realistic: {
        base: {
            skin_tone: 'medium',
            skin_color: '#d4a574'
        },
        hair: {
            style: 'short-rounded',
            color: '#4a2c1a'
        },
        face: {
            expression: 'neutral',
            eyes: {
                style: 'happy',
                color: '#2c2c2c',
                size: 'medium'
            },
            mouth: {
                style: 'neutral',
                color: '#666666'
            },
            eyebrows: {
                style: 'soft',
                color: '#1a1a1a'
            },
            nose: {
                style: 'normal',
                size: 'medium'
            },
            cheeks: false
        },
        body: {
            torso: {
                color: '#2c3e50',
                shape: 'rounded'
            },
            arms: {
                position: 'rest',
                left_color: '#d4a574',
                right_color: '#d4a574',
                sleeve_color: '#2c3e50'
            },
            legs: {
                color: '#1a252f',
                width: 35,
                height: 80,
                spacing: 20
            }
        },
        accessories: {
            head: [],
            face: ['glasses'],
            body: []
        },
        animation: {
            idle: true,
            type: 'breathe',
            speed: 'normal'
        },
        metadata: {
            preset_type: 'realistic',
            description: 'Avatar réaliste et professionnel'
        }
    },
    
    // Avatar Cartoon - Style coloré et expressif
    cartoon: {
        base: {
            skin_tone: 'light',
            skin_color: '#ffdbac'
        },
        hair: {
            style: 'spiky',
            color: '#ffd700'
        },
        face: {
            expression: 'happy',
            eyes: {
                style: 'happy',
                color: '#000000',
                size: 'large'
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
                color: '#4a90e2',
                shape: 'rounded'
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
        metadata: {
            preset_type: 'cartoon',
            description: 'Avatar cartoon coloré et expressif'
        }
    },
    
    // Avatar Gamer - Style gaming avec accessoires
    gamer: {
        base: {
            skin_tone: 'light',
            skin_color: '#ffdbac'
        },
        hair: {
            style: 'mohawk',
            color: '#ff00ff'
        },
        face: {
            expression: 'cool',
            eyes: {
                style: 'cool',
                color: '#00ffff',
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
            cheeks: false
        },
        body: {
            torso: {
                color: '#1a1a2e',
                shape: 'rounded'
            },
            arms: {
                position: 'rest',
                left_color: '#ffdbac',
                right_color: '#ffdbac',
                sleeve_color: '#1a1a2e'
            },
            legs: {
                color: '#0f0f1e',
                width: 35,
                height: 80,
                spacing: 20
            }
        },
        accessories: {
            head: ['headset'],
            face: [],
            body: []
        },
        animation: {
            idle: true,
            type: 'breathe',
            speed: 'normal'
        },
        metadata: {
            preset_type: 'gamer',
            description: 'Avatar gamer avec style gaming'
        }
    }
};

// Export
if (typeof window !== 'undefined') {
    window.AvatarPresets = AvatarPresets;
}

