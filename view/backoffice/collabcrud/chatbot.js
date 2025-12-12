// ============================================
// CHATBOT - Logique Complète
// ============================================

class ChatBot {
    constructor() {
        this.isOpen = false;
        this.isRecording = false;
        this.recognition = null;
        // Clé API HuggingFace (optionnelle, peut être configurée)
        // Pour obtenir une clé gratuite : https://huggingface.co/settings/tokens
        this.hfApiKey = null; // Définir ici votre clé si vous en avez une : 'YOUR_HF_KEY'
        this.useAI = true; // Activer/désactiver l'IA externe
        this.init();
    }

    init() {
        // Initialiser les événements
        this.initEvents();
        
        // Initialiser la reconnaissance vocale
        this.initSpeechRecognition();
        
        // Message de bienvenue
        this.addWelcomeMessage();
    }

    initEvents() {
        // Toggle chatbot
        const toggleBtn = document.getElementById('chatbot-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleChatbot());
        }

        // Fermer chatbot
        const closeBtn = document.getElementById('chatbot-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeChatbot());
        }

        // Envoyer message
        const sendBtn = document.getElementById('sendMessage');
        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.sendMessage());
        }

        // Entrée dans l'input
        const input = document.getElementById('chatbot-input');
        if (input) {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.sendMessage();
                }
            });
        }

        // Bouton vocal
        const voiceBtn = document.getElementById('voiceButton');
        if (voiceBtn) {
            voiceBtn.addEventListener('click', () => this.toggleVoiceRecording());
        }
    }

    initSpeechRecognition() {
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'fr-FR';
            this.recognition.continuous = false;
            this.recognition.interimResults = false;

            this.recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                document.getElementById('chatbot-input').value = transcript;
                this.sendMessage();
            };

            this.recognition.onerror = (event) => {
                console.error('Erreur de reconnaissance vocale:', event.error);
                this.stopVoiceRecording();
                this.addMessage('Erreur lors de la reconnaissance vocale. Veuillez réessayer.', 'bot');
            };

            this.recognition.onend = () => {
                this.stopVoiceRecording();
            };
        }
    }

    toggleChatbot() {
        const container = document.getElementById('chatbot-container');
        const assistantContainer = document.getElementById('assistant-container');
        const assistantArm = document.querySelector('.assistant-arm');
        const assistantBubble = document.getElementById('assistant-bubble');
        const assistantEyes = document.querySelectorAll('.assistant-eye');
        
        if (container) {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                // Si l'assistant n'est pas déjà visible, l'afficher avec animation
                if (assistantContainer && !assistantContainer.classList.contains('show')) {
                    // Afficher l'assistant
                    assistantContainer.classList.add('show');
                    
                    // Animation coucou
                    if (assistantArm) {
                        assistantArm.classList.add('wave');
                    }
                    
                    // Animation de clignement des yeux
                    if (assistantEyes.length > 0) {
                        setTimeout(() => {
                            assistantEyes.forEach(eye => {
                                eye.classList.add('blink');
                                setTimeout(() => {
                                    eye.classList.remove('blink');
                                }, 300);
                            });
                        }, 500);
                    }
                    
                    // Message personnalisé
                    if (assistantBubble) {
                        // Récupérer le nom d'utilisateur depuis une variable globale ou utiliser un nom par défaut
                        const userName = window.userNameForAssistant || 'Membre';
                        assistantBubble.innerText = "Bonjour " + userName + " ! 👋 Comment puis-je t'aider ? 🤖";
                    }
                    
                    // Voix (optionnel)
                    if ('speechSynthesis' in window && assistantBubble) {
                        const userName = window.userNameForAssistant || 'Membre';
                        const speak = new SpeechSynthesisUtterance("Bonjour " + userName + " !");
                        speak.lang = "fr-FR";
                        speak.rate = 1.0;
                        speak.pitch = 1.0;
                        speechSynthesis.speak(speak);
                    }
                    
                    // Après l'animation (2 secondes), ouvrir le chatbot et faire disparaître l'assistant
                    setTimeout(() => {
                        // Retirer la classe wave
                        if (assistantArm) {
                            assistantArm.classList.remove('wave');
                        }
                        
                        // Ouvrir le chatbot
                        container.classList.remove('chatbot-hidden');
                        
                        // Focus sur l'input
                        setTimeout(() => {
                            const input = document.getElementById('chatbot-input');
                            if (input) input.focus();
                        }, 300);
                        
                        // Faire disparaître l'assistant après un court délai
                        setTimeout(() => {
                            if (assistantContainer) {
                                assistantContainer.classList.remove('show');
                                // Réinitialiser la bulle
                                if (assistantBubble) {
                                    assistantBubble.innerText = "Bonjour ! 👋";
                                }
                            }
                        }, 500);
                    }, 2000);
                } else {
                    // Si l'assistant est déjà visible ou n'existe pas, ouvrir directement le chatbot
                    container.classList.remove('chatbot-hidden');
                    // Focus sur l'input
                    setTimeout(() => {
                        const input = document.getElementById('chatbot-input');
                        if (input) input.focus();
                    }, 300);
                }
            } else {
                container.classList.add('chatbot-hidden');
            }
        }
    }

    closeChatbot() {
        const container = document.getElementById('chatbot-container');
        if (container) {
            container.classList.add('chatbot-hidden');
            this.isOpen = false;
        }
    }

    addWelcomeMessage() {
        setTimeout(() => {
            this.addMessage('Bonjour ! 🎮 Je suis votre assistant IA gaming. Je peux vous aider avec les fonctionnalités de la plateforme ET répondre à vos questions sur le gaming en général (jeux, e-sport, hardware, actualités...). Comment puis-je vous aider ?', 'bot');
        }, 500);
    }

    addMessage(text, sender) {
        const messagesContainer = document.getElementById('chatbot-messages');
        if (!messagesContainer) return;

        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', sender);
        
        const textSpan = document.createElement('span');
        textSpan.textContent = text;
        messageDiv.appendChild(textSpan);

        const timeSpan = document.createElement('span');
        timeSpan.classList.add('message-time');
        timeSpan.textContent = this.getCurrentTime();
        messageDiv.appendChild(timeSpan);

        messagesContainer.appendChild(messageDiv);
        
        // Scroll vers le bas
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    showTyping() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.classList.remove('hidden');
        }
    }

    hideTyping() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.classList.add('hidden');
        }
    }

    async sendMessage() {
        const input = document.getElementById('chatbot-input');
        if (!input) return;

        const text = input.value.trim();
        if (!text) return;

        // Ajouter le message de l'utilisateur
        this.addMessage(text, 'user');
        input.value = '';

        // Afficher l'indicateur de frappe
        this.showTyping();

        try {
            // Obtenir la réponse de l'IA
            const botReply = await this.askAI(text);
            
            // Masquer l'indicateur de frappe
            this.hideTyping();
            
            // Ajouter la réponse du bot
            this.addMessage(botReply, 'bot');
            
            // Lire la réponse à voix haute
            this.speak(botReply);
        } catch (error) {
            console.error('Erreur lors de la communication avec l\'IA:', error);
            this.hideTyping();
            this.addMessage('Désolé, une erreur s\'est produite. Veuillez réessayer.', 'bot');
        }
    }

    async askAI(prompt) {
        // Essayer d'abord l'API HuggingFace pour une réponse intelligente
        try {
            const aiResponse = await this.callHuggingFaceAPI(prompt);
            if (aiResponse && aiResponse.trim().length > 0) {
                return this.cleanAIResponse(aiResponse);
            }
        } catch (error) {
            console.log('API HuggingFace non disponible, utilisation des réponses locales:', error);
        }
        
        // Fallback vers les réponses intelligentes locales si l'API échoue
        return this.getSmartResponse(prompt);
    }
    
    // Appeler l'API HuggingFace pour une réponse intelligente
    async callHuggingFaceAPI(prompt) {
        if (!this.useAI) {
            throw new Error('IA externe désactivée');
        }
        
        // Modèles disponibles (gratuits, sans clé API nécessaire pour certains)
        // Note: Certains modèles peuvent nécessiter une clé API pour plus de requêtes
        const models = [
            'HuggingFaceH4/zephyr-7b-beta',  // Très performant, peut nécessiter une clé
            'microsoft/DialoGPT-medium',     // Bon pour les conversations
            'facebook/blenderbot-400M-distill', // Conversation naturelle
            'google/flan-t5-base'             // Généraliste
        ];
        
        // Utiliser le premier modèle (zephyr-7b-beta est très performant)
        const model = models[0];
        const apiUrl = `https://api-inference.huggingface.co/models/${model}`;
        
        // Préparer les headers
        const headers = {
            'Content-Type': 'application/json'
        };
        
        // Ajouter la clé API si disponible
        if (this.hfApiKey) {
            headers['Authorization'] = `Bearer ${this.hfApiKey}`;
        }
        
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ 
                    inputs: this.formatPromptForAI(prompt),
                    parameters: {
                        max_new_tokens: 200,  // Limiter la longueur de la réponse
                        temperature: 0.7,      // Contrôler la créativité
                        return_full_text: false
                    }
                })
            });
            
            if (!response.ok) {
                // Si le modèle est en train de charger (503), essayer un autre modèle
                if (response.status === 503) {
                    console.log('Modèle en cours de chargement, essai avec un autre modèle...');
                    // Essayer avec un modèle alternatif
                    return await this.tryAlternativeModel(prompt, models.slice(1));
                }
                
                // Si erreur 429 (trop de requêtes), suggérer d'utiliser une clé API
                if (response.status === 429) {
                    console.log('Limite de requêtes atteinte. Utilisation des réponses locales.');
                    console.log('💡 Astuce: Ajoutez une clé API HuggingFace pour plus de requêtes.');
                    throw new Error('Rate limit');
                }
                
                throw new Error(`API error: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Extraire la réponse selon le format de l'API
            let aiResponse = null;
            
            if (Array.isArray(data) && data.length > 0) {
                if (data[0].generated_text) {
                    aiResponse = data[0].generated_text;
                } else if (typeof data[0] === 'string') {
                    aiResponse = data[0];
                } else if (data[0].summary_text) {
                    aiResponse = data[0].summary_text;
                }
            } else if (data.generated_text) {
                aiResponse = data.generated_text;
            } else if (data.summary_text) {
                aiResponse = data.summary_text;
            } else if (typeof data === 'string') {
                aiResponse = data;
            }
            
            // Si on a une réponse valide, la retourner
            if (aiResponse && aiResponse.trim().length > 10) {
                return aiResponse;
            }
            
            // Si le format n'est pas reconnu, retourner null pour utiliser le fallback
            return null;
            
        } catch (error) {
            console.error('Erreur lors de l\'appel à l\'API HuggingFace:', error);
            throw error;
        }
    }
    
    // Essayer un modèle alternatif si le premier échoue
    async tryAlternativeModel(prompt, alternativeModels) {
        if (!alternativeModels || alternativeModels.length === 0) {
            throw new Error('No alternative models');
        }
        
        const model = alternativeModels[0];
        const apiUrl = `https://api-inference.huggingface.co/models/${model}`;
        
        const headers = {
            'Content-Type': 'application/json'
        };
        
        if (this.hfApiKey) {
            headers['Authorization'] = `Bearer ${this.hfApiKey}`;
        }
        
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ 
                    inputs: this.formatPromptForAI(prompt)
                })
            });
            
            if (response.ok) {
                const data = await response.json();
                if (Array.isArray(data) && data.length > 0 && data[0].generated_text) {
                    return data[0].generated_text;
                }
            }
        } catch (error) {
            console.log('Modèle alternatif non disponible, utilisation des réponses locales');
        }
        
        throw new Error('Alternative model failed');
    }
    
    // Formater le prompt pour l'IA avec contexte
    formatPromptForAI(userMessage) {
        // Ajouter du contexte pour que l'IA comprenne qu'elle est un assistant gaming et collaboration
        const context = `Tu es un assistant IA intelligent et passionné de gaming pour une plateforme de collaboration gaming en ligne. 
Tu aides les utilisateurs avec :
- Les fonctionnalités de la plateforme (collaborations, projets, avatars, chat)
- Les questions générales sur le gaming (jeux vidéo, e-sport, hardware, actualités gaming, etc.)
- Les conseils gaming, stratégies, recommandations de jeux
- L'actualité gaming, les sorties de jeux, les événements e-sport
- Le hardware gaming (PC, consoles, périphériques)
- Les genres de jeux (FPS, RPG, MMO, Battle Royale, etc.)

Réponds de manière concise, amicale, passionnée et professionnelle en français. Sois enthousiaste quand on parle de gaming !
Question de l'utilisateur: ${userMessage}
Réponse:`;
        
        return context;
    }
    
    // Nettoyer la réponse de l'IA (enlever le contexte si présent)
    cleanAIResponse(response) {
        if (!response) return '';
        
        // Enlever le prompt si l'IA l'a inclus dans sa réponse
        let cleaned = response;
        
        // Enlever le contexte du début si présent
        const contextMarkers = ['Réponse:', 'Answer:', 'Assistant:', 'AI:'];
        for (const marker of contextMarkers) {
            const index = cleaned.indexOf(marker);
            if (index !== -1) {
                cleaned = cleaned.substring(index + marker.length).trim();
            }
        }
        
        // Enlever les répétitions du prompt
        const lines = cleaned.split('\n');
        const filteredLines = lines.filter(line => {
            const lowerLine = line.toLowerCase();
            return !lowerLine.includes('question de l\'utilisateur') && 
                   !lowerLine.includes('user question') &&
                   !lowerLine.startsWith('question:');
        });
        
        cleaned = filteredLines.join('\n').trim();
        
        // Limiter la longueur de la réponse
        if (cleaned.length > 500) {
            cleaned = cleaned.substring(0, 500) + '...';
        }
        
        return cleaned || response;
    }

    getSmartResponse(prompt) {
        const lowerPrompt = prompt.toLowerCase().trim();
        
        // Salutations
        if (this.matches(lowerPrompt, ['salut', 'bonjour', 'hello', 'hi', 'hey', 'coucou'])) {
            const greetings = [
                'Bonjour ! Je suis votre assistant IA. Comment puis-je vous aider aujourd\'hui ?',
                'Salut ! Je suis là pour vous assister. Que souhaitez-vous savoir ?',
                'Hello ! Ravi de vous rencontrer. En quoi puis-je vous être utile ?'
            ];
            return this.randomChoice(greetings);
        }
        
        // Questions sur l'aide
        if (this.matches(lowerPrompt, ['aide', 'help', 'assistance', 'comment', 'comment faire'])) {
            return 'Je peux vous aider avec :\n\n🎮 GAMING :\n• Recommandations de jeux\n• E-sport et compétitions\n• Hardware et configs PC\n• Actualités gaming\n• Conseils et stratégies\n\n💼 PLATEFORME :\n• Collaborations et projets\n• Personnalisation d\'avatar\n• Utilisation du chat\n• Fonctionnalités\n\nQue souhaitez-vous savoir ?';
        }
        
        // Questions sur les collaborations
        if (this.matches(lowerPrompt, ['collab', 'collaboration', 'projet', 'team', 'équipe', 'membre'])) {
            if (this.matches(lowerPrompt, ['créer', 'nouveau', 'ajouter'])) {
                return 'Pour créer une collaboration :\n1. Allez dans "Gestion Collab"\n2. Cliquez sur "Créer une collaboration"\n3. Remplissez les informations\n4. Invitez des membres\n\nLes membres peuvent ensuite collaborer sur le projet !';
            }
            if (this.matches(lowerPrompt, ['rejoindre', 'participer', 'inviter'])) {
                return 'Pour rejoindre une collaboration :\n• Acceptez une invitation reçue\n• Ou demandez à un membre de vous inviter\n\nUne fois membre, vous pouvez accéder au chat et aux fonctionnalités du projet.';
            }
            return 'Les collaborations permettent de travailler en équipe sur des projets. Vous pouvez créer des projets, inviter des membres, discuter dans le chat, et gérer vos tâches ensemble. Voulez-vous savoir comment créer ou rejoindre une collaboration ?';
        }
        
        // Questions sur l'avatar
        if (this.matches(lowerPrompt, ['avatar', 'qbit', 'profil', 'personnage', 'apparence'])) {
            if (this.matches(lowerPrompt, ['créer', 'personnaliser', 'modifier', 'changer'])) {
                return 'Pour personnaliser votre avatar :\n1. Cliquez sur le bouton "🎨 Avatar" dans la navigation\n2. Choisissez vos couleurs, accessoires, expressions\n3. Utilisez l\'IA pour générer depuis une photo\n4. Cliquez sur "Enregistrer mon Qbit"\n\nVotre avatar apparaîtra ensuite partout sur la plateforme !';
            }
            return 'Votre avatar (Qbit) est votre représentation visuelle sur la plateforme. Vous pouvez le personnaliser avec différents styles, couleurs, accessoires et expressions. Cliquez sur "🎨 Avatar" pour commencer !';
        }
        
        // Questions sur le chat
        if (this.matches(lowerPrompt, ['chat', 'message', 'discuter', 'parler', 'communiquer'])) {
            return 'Le chat permet de communiquer avec les membres de votre collaboration. Vous pouvez :\n• Envoyer des messages texte\n• Envoyer des messages vocaux (🎤)\n• Joindre des fichiers (+)\n• Utiliser des emojis\n\nTous les messages sont modérés pour assurer un environnement respectueux.';
        }
        
        // Questions sur les messages vocaux
        if (this.matches(lowerPrompt, ['vocal', 'voix', 'micro', 'enregistrer', 'audio'])) {
            return 'Pour envoyer un message vocal :\n1. Cliquez sur le bouton 🎤 dans le chat\n2. Maintenez le bouton enfoncé pendant que vous parlez\n3. Relâchez pour envoyer\n\nLe message vocal sera automatiquement partagé avec les membres !';
        }
        
        // Questions sur les badges / gamification
        if (this.matches(lowerPrompt, ['badge', 'niveau', 'score', 'récompense', 'gamification'])) {
            return 'Le système de badges récompense votre activité :\n• Messages envoyés\n• Fichiers partagés\n• Participation aux collaborations\n• Régularité\n\nGagnez des badges en étant actif sur la plateforme !';
        }
        
        // Questions techniques
        if (this.matches(lowerPrompt, ['bug', 'erreur', 'problème', 'ne marche pas', 'fonctionne pas'])) {
            return 'Si vous rencontrez un problème :\n• Vérifiez votre connexion internet\n• Rafraîchissez la page (F5)\n• Videz le cache du navigateur\n• Contactez le support si le problème persiste\n\nJe peux vous aider avec des questions spécifiques sur les fonctionnalités !';
        }
        
        // Questions sur les fonctionnalités
        if (this.matches(lowerPrompt, ['fonctionnalité', 'fonction', 'possibilité', 'peut-on', 'est-ce que'])) {
            return 'Voici les principales fonctionnalités :\n✅ Collaborations en équipe\n✅ Chat avec messages vocaux\n✅ Personnalisation d\'avatar\n✅ Partage de fichiers\n✅ Modération automatique\n✅ Système de badges\n\nQuelle fonctionnalité vous intéresse ?';
        }
        
        // ========== QUESTIONS GAMING GÉNÉRALES ==========
        
        // Questions sur les jeux vidéo en général
        if (this.matches(lowerPrompt, ['jeu', 'game', 'gaming', 'jouer', 'jouer à'])) {
            if (this.matches(lowerPrompt, ['meilleur', 'top', 'recommandation', 'conseil', 'suggérer'])) {
                return 'Voici quelques recommandations de jeux populaires selon les genres :\n\n🎮 FPS : Valorant, CS2, Call of Duty\n🎮 Battle Royale : Fortnite, Apex Legends, PUBG\n🎮 RPG : The Witcher 3, Elden Ring, Baldur\'s Gate 3\n🎮 MMO : World of Warcraft, Final Fantasy XIV\n🎮 Indie : Hades, Celeste, Hollow Knight\n\nQuel genre vous intéresse ?';
            }
            if (this.matches(lowerPrompt, ['nouveau', 'sortie', 'récent', 'dernier'])) {
                return 'Les dernières sorties gaming incluent des titres comme :\n• Baldur\'s Gate 3 (RPG)\n• Alan Wake 2 (Horreur)\n• Spider-Man 2 (Action)\n• Starfield (Sci-Fi RPG)\n• The Legend of Zelda: Tears of the Kingdom\n\nVoulez-vous des infos sur un jeu spécifique ?';
            }
            return 'Le gaming est ma passion ! 🎮 Je peux vous parler de jeux vidéo, e-sport, hardware, actualités gaming, ou vous donner des conseils. Que voulez-vous savoir ?';
        }
        
        // Questions sur l'e-sport
        if (this.matches(lowerPrompt, ['esport', 'e-sport', 'compétition', 'tournoi', 'pro', 'professionnel'])) {
            return 'L\'e-sport est un domaine passionnant ! 🏆\n\nLes principales disciplines :\n• FPS : CS2, Valorant, Overwatch\n• MOBA : League of Legends, Dota 2\n• Battle Royale : Fortnite, Apex Legends\n• Fighting : Street Fighter 6, Tekken 8\n\nLes grands tournois incluent les Worlds (LoL), The International (Dota 2), et les Majors (CS2).\n\nVoulez-vous des infos sur un jeu e-sport spécifique ?';
        }
        
        // Questions sur le hardware
        if (this.matches(lowerPrompt, ['pc', 'ordinateur', 'config', 'hardware', 'composant', 'carte graphique', 'processeur', 'ram', 'ssd'])) {
            if (this.matches(lowerPrompt, ['meilleur', 'recommandation', 'conseil'])) {
                return 'Pour une config gaming, voici les recommandations :\n\n💻 PC Gaming :\n• GPU : RTX 4060/4070 ou RX 7600/7700 (milieu de gamme)\n• CPU : Ryzen 5 5600X ou Intel i5-12400\n• RAM : 16 Go minimum (32 Go recommandé)\n• SSD : NVMe 1 To minimum\n\n🎮 Consoles :\n• PlayStation 5 / Xbox Series X pour le 4K\n• Nintendo Switch pour le portable\n\nQuel budget avez-vous en tête ?';
            }
            return 'Le hardware gaming est essentiel pour une bonne expérience ! Je peux vous conseiller sur PC, consoles, périphériques (souris, clavier, casque), ou moniteurs. Que cherchez-vous ?';
        }
        
        // Questions sur les genres de jeux
        if (this.matches(lowerPrompt, ['fps', 'rpg', 'mmo', 'battle royale', 'moba', 'stratégie', 'horreur', 'indie'])) {
            if (lowerPrompt.includes('fps')) {
                return 'Les FPS (First Person Shooter) sont des jeux de tir à la première personne ! 🎯\n\nJeux populaires :\n• Valorant (tactique)\n• CS2 (compétitif)\n• Call of Duty (action)\n• Overwatch 2 (héro shooter)\n• Apex Legends (Battle Royale FPS)\n\nQuel FPS vous intéresse ?';
            }
            if (lowerPrompt.includes('rpg')) {
                return 'Les RPG (Role Playing Games) sont des jeux de rôle ! ⚔️\n\nJeux populaires :\n• The Witcher 3 (action-RPG)\n• Elden Ring (souls-like)\n• Baldur\'s Gate 3 (tactique)\n• Final Fantasy XVI (JRPG)\n• Cyberpunk 2077 (sci-fi)\n\nQuel type de RPG vous plaît ?';
            }
            if (lowerPrompt.includes('mmo')) {
                return 'Les MMO (Massively Multiplayer Online) sont des jeux multijoueurs massifs ! 🌍\n\nJeux populaires :\n• World of Warcraft (fantasy)\n• Final Fantasy XIV (JRPG MMO)\n• Guild Wars 2 (action)\n• Lost Ark (ARPG MMO)\n• New World (survival)\n\nQuel MMO vous intéresse ?';
            }
            if (lowerPrompt.includes('battle royale')) {
                return 'Les Battle Royale sont des jeux de survie multijoueurs ! 🏝️\n\nJeux populaires :\n• Fortnite (construction)\n• Apex Legends (héros)\n• PUBG (réaliste)\n• Warzone (Call of Duty)\n\nQuel Battle Royale vous plaît ?';
            }
            return 'Je peux vous parler de n\'importe quel genre de jeu ! FPS, RPG, MMO, Battle Royale, MOBA, stratégie, horreur, indie... Lequel vous intéresse ?';
        }
        
        // Questions sur les consoles
        if (this.matches(lowerPrompt, ['console', 'playstation', 'xbox', 'nintendo', 'switch', 'ps5', 'xbox series'])) {
            return 'Les consoles gaming ! 🎮\n\n• PlayStation 5 : Exclusivités (God of War, Spider-Man), DualSense, 4K\n• Xbox Series X/S : Game Pass, rétrocompatibilité, puissance\n• Nintendo Switch : Portable, exclusivités Nintendo (Zelda, Mario)\n• Steam Deck : PC portable pour Steam\n\nQuelle console vous intéresse ?';
        }
        
        // Questions sur l'actualité gaming
        if (this.matches(lowerPrompt, ['actualité', 'news', 'nouvelle', 'info', 'événement', 'annonce'])) {
            return 'L\'actualité gaming est toujours passionnante ! 📰\n\nJe peux vous parler de :\n• Les dernières sorties de jeux\n• Les annonces de nouveaux jeux\n• Les événements e-sport\n• Les mises à jour importantes\n• Les tendances du marché\n\nQuel sujet vous intéresse ?';
        }
        
        // Questions sur les streamers / YouTube gaming
        if (this.matches(lowerPrompt, ['streamer', 'youtube', 'twitch', 'influenceur', 'créateur'])) {
            return 'Les créateurs de contenu gaming sont essentiels à la communauté ! 🎥\n\nPlateformes populaires :\n• Twitch : streaming en direct\n• YouTube : vidéos, guides, let\'s play\n• TikTok : clips courts\n\nLes streamers couvrent tous les genres : FPS, RPG, MMO, e-sport...\n\nQuel type de contenu vous intéresse ?';
        }
        
        // Questions sur les stratégies / conseils
        if (this.matches(lowerPrompt, ['stratégie', 'conseil', 'astuce', 'tip', 'trick', 'comment gagner', 'comment améliorer'])) {
            return 'Je peux vous donner des conseils gaming ! 💡\n\n• Stratégies pour différents jeux\n• Conseils pour améliorer votre gameplay\n• Astuces pour optimiser vos performances\n• Guides pour débutants\n• Meta et builds optimaux\n\nSur quel jeu voulez-vous des conseils ?';
        }
        
        // Questions sur les prix / promotions
        if (this.matches(lowerPrompt, ['prix', 'promotion', 'soldes', 'gratuit', 'free', 'coût'])) {
            return 'Pour les prix et promotions gaming :\n\n💰 Plateformes de vente :\n• Steam (PC)\n• Epic Games Store (promotions fréquentes)\n• PlayStation Store / Xbox Store\n• Humble Bundle (bundles)\n\n🎁 Jeux gratuits populaires :\n• Fortnite, Apex Legends, Valorant\n• Genshin Impact, Warframe\n\nVoulez-vous des infos sur un jeu spécifique ?';
        }
        
        // Questions de politesse
        if (this.matches(lowerPrompt, ['merci', 'thanks', 'thank you', 'gracías'])) {
            const thanks = [
                'De rien ! Je suis là pour vous aider. N\'hésitez pas si vous avez d\'autres questions !',
                'Avec plaisir ! N\'hésitez pas à revenir si besoin.',
                'Pas de problème ! Bonne continuation sur la plateforme !'
            ];
            return this.randomChoice(thanks);
        }
        
        // Questions de départ
        if (this.matches(lowerPrompt, ['au revoir', 'bye', 'à bientôt', 'ciao', 'salut'])) {
            return 'Au revoir ! N\'hésitez pas à revenir si vous avez besoin d\'aide. Bonne journée ! 👋';
        }
        
        // Questions avec "quoi", "qu'est-ce", "comment"
        if (lowerPrompt.startsWith('quoi') || lowerPrompt.startsWith('qu\'est-ce') || lowerPrompt.startsWith('comment')) {
            if (lowerPrompt.includes('collab')) {
                return 'Une collaboration est un espace de travail partagé où plusieurs membres peuvent collaborer sur un projet, discuter, partager des fichiers et gérer des tâches ensemble.';
            }
            if (lowerPrompt.includes('avatar')) {
                return 'Un avatar (ou Qbit) est votre représentation visuelle personnalisée sur la plateforme. Vous pouvez le créer et le modifier selon vos préférences.';
            }
            return 'Je peux vous expliquer comment utiliser les fonctionnalités de la plateforme. Que souhaitez-vous savoir précisément ?';
        }
        
        // Questions avec "où"
        if (lowerPrompt.startsWith('où') || lowerPrompt.startsWith('ou ')) {
            if (this.matches(lowerPrompt, ['avatar', 'qbit'])) {
                return 'Pour accéder à l\'éditeur d\'avatar, cliquez sur le bouton "🎨 Avatar" dans la barre de navigation, ou sur "Personnaliser Avatar" dans votre profil.';
            }
            if (this.matches(lowerPrompt, ['chat', 'message'])) {
                return 'Le chat se trouve dans la section "Chat" de chaque collaboration. Vous pouvez y accéder depuis la page de la collaboration.';
            }
            return 'Je peux vous indiquer où trouver les fonctionnalités. Que cherchez-vous précisément ?';
        }
        
        // Questions avec "qui"
        if (lowerPrompt.startsWith('qui')) {
            return 'Je suis votre assistant IA intégré à la plateforme. Je peux vous aider avec les fonctionnalités, répondre à vos questions et vous guider dans l\'utilisation de la plateforme.';
        }
        
        // Réponses intelligentes par défaut
        if (lowerPrompt.length < 5) {
            return 'Pouvez-vous reformuler votre question ? Je serai ravi de vous aider avec plus de détails.';
        }
        
        // Analyser l'intention et donner une réponse contextuelle
        const keywords = this.extractKeywords(lowerPrompt);
        if (keywords.length > 0) {
            return this.generateContextualResponse(prompt, keywords);
        }
        
        // Réponse par défaut améliorée
        return `Je comprends votre question : "${prompt}". Pourriez-vous être plus précis ? Je peux vous aider avec les collaborations, les avatars, le chat, ou toute autre fonctionnalité de la plateforme.`;
    }
    
    matches(text, keywords) {
        return keywords.some(keyword => text.includes(keyword));
    }
    
    randomChoice(array) {
        return array[Math.floor(Math.random() * array.length)];
    }
    
    extractKeywords(text) {
        const commonWords = ['le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'est', 'sont', 'être', 'avoir', 'faire', 'comment', 'quoi', 'où', 'qui', 'pourquoi'];
        const words = text.split(/\s+/).filter(word => 
            word.length > 2 && !commonWords.includes(word)
        );
        return words;
    }
    
    generateContextualResponse(originalPrompt, keywords) {
        // Générer une réponse basée sur les mots-clés détectés
        const responses = [
            `D'après votre question sur "${keywords[0]}", je peux vous dire que cette fonctionnalité est disponible sur la plateforme. `,
            `Concernant "${keywords[0]}", voici ce que je peux vous expliquer : `,
            `Je vois que vous vous intéressez à "${keywords[0]}". `
        ];
        
        let response = this.randomChoice(responses);
        
        // Ajouter des suggestions basées sur les mots-clés
        if (keywords.some(k => ['projet', 'tâche', 'collab'].includes(k))) {
            response += 'Les collaborations permettent de gérer vos projets en équipe. Voulez-vous savoir comment créer ou rejoindre une collaboration ?';
        } else if (keywords.some(k => ['avatar', 'profil', 'image'].includes(k))) {
            response += 'Vous pouvez personnaliser votre avatar dans la section dédiée. Voulez-vous des instructions détaillées ?';
        } else if (keywords.some(k => ['message', 'chat', 'discuter'].includes(k))) {
            response += 'Le chat permet de communiquer avec votre équipe. Vous pouvez envoyer des messages texte ou vocaux.';
        } else {
            response += 'Pouvez-vous me donner plus de détails pour que je puisse mieux vous aider ?';
        }
        
        return response;
    }

    speak(text) {
        if ('speechSynthesis' in window) {
            // Arrêter toute synthèse en cours
            window.speechSynthesis.cancel();
            
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'fr-FR';
            utterance.pitch = 1;
            utterance.rate = 1;
            utterance.volume = 0.8;
            
            window.speechSynthesis.speak(utterance);
        }
    }

    toggleVoiceRecording() {
        if (!this.recognition) {
            this.addMessage('La reconnaissance vocale n\'est pas disponible dans votre navigateur.', 'bot');
            return;
        }

        if (this.isRecording) {
            this.stopVoiceRecording();
        } else {
            this.startVoiceRecording();
        }
    }

    startVoiceRecording() {
        if (this.recognition) {
            this.isRecording = true;
            const voiceBtn = document.getElementById('voiceButton');
            if (voiceBtn) {
                voiceBtn.classList.add('recording');
                voiceBtn.querySelector('.mic-icon').textContent = '⏹️';
            }
            
            try {
                this.recognition.start();
                this.addMessage('🎤 Enregistrement en cours... Parlez maintenant.', 'bot');
            } catch (error) {
                console.error('Erreur lors du démarrage de l\'enregistrement:', error);
                this.stopVoiceRecording();
            }
        }
    }

    stopVoiceRecording() {
        this.isRecording = false;
        const voiceBtn = document.getElementById('voiceButton');
        if (voiceBtn) {
            voiceBtn.classList.remove('recording');
            voiceBtn.querySelector('.mic-icon').textContent = '🎤';
        }
        
        if (this.recognition && this.isRecording) {
            this.recognition.stop();
        }
    }
}

// Initialiser le chatbot au chargement de la page
let chatbot;
document.addEventListener('DOMContentLoaded', function() {
    chatbot = new ChatBot();
});

// Exporter pour utilisation globale
if (typeof window !== 'undefined') {
    window.ChatBot = ChatBot;
    window.chatbot = chatbot;
}
