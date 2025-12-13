/**
 * Emoji Picker pour le chat
 * Système d'insertion d'emojis dans les messages
 */

class EmojiPicker {
    constructor(textareaId, buttonId) {
        this.textarea = document.getElementById(textareaId);
        this.button = document.getElementById(buttonId);
        this.picker = null;
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        if (!this.textarea || !this.button) return;
        
        // Créer le picker
        this.createPicker();
        
        // Event listeners
        this.button.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggle();
        });
        
        // Fermer si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (this.picker && !this.picker.contains(e.target) && !this.button.contains(e.target)) {
                this.close();
            }
        });
    }
    
    createPicker() {
        // Créer le conteneur du picker
        this.picker = document.createElement('div');
        this.picker.className = 'emoji-picker';
        this.picker.style.display = 'none';
        
        // Catégories d'emojis
        const categories = {
            '😀 Smileys': ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔'],
            '😢 Emotions': ['😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '😶‍🌫️', '😵', '😵‍💫', '🤯', '🤠', '🥳', '😎', '🤓', '🧐'],
            '👋 Gestes': ['👋', '🤚', '🖐', '✋', '🖖', '👌', '🤌', '🤏', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👍', '👎', '✊', '👊', '🤛', '🤜', '👏', '🙌', '👐', '🤲', '🤝'],
            '❤️ Cœurs': ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❤️‍🔥', '❤️‍🩹', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️'],
            '⭐ Autres': ['⭐', '🌟', '✨', '💫', '🔥', '💥', '💢', '💯', '💨', '💦', '💤', '🎉', '🎊', '🎈', '🎁', '🏆', '🥇', '🥈', '🥉', '⚽', '🏀', '🏈', '⚾', '🎾', '🏐', '🏉', '🎱', '🏓', '🏸', '🥊'],
            '🎮 Gaming': ['🎮', '🕹️', '🎯', '🎲', '🎰', '🃏', '🀄', '🎴', '🎭', '🖼️', '🎨', '🖌️', '🖍️', '✏️', '✒️', '🖊️', '🖋️', '📝', '💼', '📁'],
            '✅ Actions': ['✅', '❌', '✔️', '✖️', '➕', '➖', '➗', '🟰', '🔴', '🟠', '🟡', '🟢', '🔵', '🟣', '⚫', '⚪', '🟤', '🔶', '🔷', '🔸', '🔹', '🔺', '🔻', '💠', '🔘', '🔳', '🔲', '▪️', '▫️', '◾']
        };
        
        // Créer les onglets de catégories
        const tabsContainer = document.createElement('div');
        tabsContainer.className = 'emoji-picker-tabs';
        
        const contentContainer = document.createElement('div');
        contentContainer.className = 'emoji-picker-content';
        
        let activeTab = null;
        
        // Créer les onglets et contenus
        Object.keys(categories).forEach((categoryName, index) => {
            // Onglet
            const tab = document.createElement('button');
            tab.className = 'emoji-tab';
            tab.textContent = categoryName.split(' ')[0]; // Juste l'emoji
            tab.title = categoryName;
            if (index === 0) {
                tab.classList.add('active');
                activeTab = categoryName;
            }
            
            tab.addEventListener('click', () => {
                // Retirer active de tous les onglets
                tabsContainer.querySelectorAll('.emoji-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                activeTab = categoryName;
                this.showCategory(contentContainer, categoryName, categories[categoryName]);
            });
            
            tabsContainer.appendChild(tab);
            
            // Contenu de la catégorie
            if (index === 0) {
                this.showCategory(contentContainer, categoryName, categories[categoryName]);
            }
        });
        
        this.picker.appendChild(tabsContainer);
        this.picker.appendChild(contentContainer);
        
        // Insérer le picker dans le DOM (près du textarea)
        if (this.textarea.parentElement) {
            this.textarea.parentElement.style.position = 'relative';
            this.textarea.parentElement.appendChild(this.picker);
        }
    }
    
    showCategory(container, categoryName, emojis) {
        container.innerHTML = '';
        
        const grid = document.createElement('div');
        grid.className = 'emoji-grid';
        
        emojis.forEach(emoji => {
            const emojiBtn = document.createElement('button');
            emojiBtn.className = 'emoji-item';
            emojiBtn.textContent = emoji;
            emojiBtn.title = emoji;
            
            emojiBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.insertEmoji(emoji);
            });
            
            grid.appendChild(emojiBtn);
        });
        
        container.appendChild(grid);
    }
    
    insertEmoji(emoji) {
        if (!this.textarea) return;
        
        const start = this.textarea.selectionStart;
        const end = this.textarea.selectionEnd;
        const text = this.textarea.value;
        
        // Insérer l'emoji à la position du curseur
        this.textarea.value = text.substring(0, start) + emoji + text.substring(end);
        
        // Repositionner le curseur après l'emoji
        const newPosition = start + emoji.length;
        this.textarea.setSelectionRange(newPosition, newPosition);
        this.textarea.focus();
        
        // Déclencher l'event input pour les listeners
        this.textarea.dispatchEvent(new Event('input'));
    }
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
    
    open() {
        if (!this.picker) return;
        
        this.picker.style.display = 'block';
        this.isOpen = true;
        this.button.classList.add('active');
    }
    
    close() {
        if (!this.picker) return;
        
        this.picker.style.display = 'none';
        this.isOpen = false;
        this.button.classList.remove('active');
    }
}

// Auto-initialisation si les éléments existent
document.addEventListener('DOMContentLoaded', function() {
    // Pour room_collab.php
    if (document.getElementById('chatMessageInput')) {
        const emojiButton = document.createElement('button');
        emojiButton.type = 'button';
        emojiButton.id = 'emoji-picker-btn';
        emojiButton.className = 'emoji-picker-button';
        emojiButton.textContent = '😀';
        emojiButton.title = 'Insérer un emoji';
        
        const chatForm = document.querySelector('.chat-form');
        if (chatForm) {
            const textarea = document.getElementById('chatMessageInput');
            if (textarea && textarea.parentElement) {
                textarea.parentElement.insertBefore(emojiButton, textarea);
            }
        }
        
        new EmojiPicker('chatMessageInput', 'emoji-picker-btn');
    }
    
    // Pour view_collab.php (chat flottant)
    if (document.getElementById('chat-message-input')) {
        const emojiButton = document.createElement('button');
        emojiButton.type = 'button';
        emojiButton.id = 'emoji-picker-btn-floating';
        emojiButton.className = 'emoji-picker-button';
        emojiButton.textContent = '😀';
        emojiButton.title = 'Insérer un emoji';
        
        const chatForm = document.getElementById('chatMessageForm');
        if (chatForm) {
            const textarea = document.getElementById('chat-message-input');
            if (textarea && textarea.parentElement) {
                textarea.parentElement.insertBefore(emojiButton, textarea);
            }
        }
        
        new EmojiPicker('chat-message-input', 'emoji-picker-btn-floating');
    }
});

