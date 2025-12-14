// avis_dynamic.js - Badges et émojis dynamiques

document.addEventListener('DOMContentLoaded', function () {
    const starInputs = document.querySelectorAll('input[name="rating"]');
    const messageInput = document.getElementById('message');
    const feedbackForm = document.getElementById('feedback-form');
    
    // ===== BADGES ÉTOILES =====
    starInputs.forEach(star => {
        star.addEventListener('change', function () {
            updateStarBadge(this.value);
        });
    });
    
    // ===== EMOJIS SENTIMENT =====
    if (messageInput) {
        messageInput.addEventListener('input', function () {
            updateSentimentEmoji(this.value);
        });
    }
});

// Fonction: Mettre à jour le badge des étoiles
function updateStarBadge(rating) {
    // Supprimer les anciens badges
    const oldBadges = document.querySelectorAll('.star-badge');
    oldBadges.forEach(b => b.remove());
    
    // Créer le badge
    const badge = document.createElement('div');
    badge.className = 'star-badge';
    
    let badgeText = '';
    let badgeColor = '';
    let emoji = '';
    
    switch(parseInt(rating)) {
        case 5:
            badgeText = '⭐ GOLD - Excellent !';
            badgeColor = '#FFD700';
            emoji = '🌟';
            break;
        case 4:
            badgeText = '⭐⭐⭐⭐ Silver - Très Bon';
            badgeColor = '#C0C0C0';
            emoji = '👍';
            break;
        case 3:
            badgeText = '⭐⭐⭐ Bronze - Bien';
            badgeColor = '#CD7F32';
            emoji = '👌';
            break;
        case 2:
            badgeText = '⭐⭐ Standard - Pas mal';
            badgeColor = '#808080';
            emoji = '😐';
            break;
        case 1:
            badgeText = '⭐ Faible - À améliorer';
            badgeColor = '#FF6B6B';
            emoji = '😞';
            break;
    }
    
    badge.innerHTML = `<span style="color: ${badgeColor}; font-weight: bold;">${emoji} ${badgeText}</span>`;
    badge.style.padding = '10px 15px';
    badge.style.marginTop = '10px';
    badge.style.borderRadius = '5px';
    badge.style.background = '#1a1f3a';
    badge.style.border = `2px solid ${badgeColor}`;
    badge.style.fontSize = '1.1em';
    
    // Insérer après les étoiles
    const starRating = document.querySelector('.star-rating');
    if (starRating) {
        starRating.parentElement.insertBefore(badge, starRating.nextSibling);
    }
}

// Fonction: Mettre à jour l'emoji du sentiment
function updateSentimentEmoji(text) {
    // Supprimer l'ancien emoji sentiment
    const oldSentiment = document.querySelectorAll('.sentiment-emoji');
    oldSentiment.forEach(e => e.remove());
    
    // Mots clés pour différents sentiments
    const happyWords = ['excellent', 'super', 'génial', 'parfait', 'magnifique', 'incroyable', 'fantastique', 'adorable', 'love', 'happy', 'cool', 'awesome', 'merveilleux', 'formidable'];
    const sadWords = ['mauvais', 'nul', 'horrible', 'terrible', 'décevant', 'trash', 'pire', 'naseuf', 'sad', 'angry', 'déçu', 'frustrant', 'ennuyant'];
    const neutralWords = ['ok', 'bien', 'pas mal', 'normal', 'acceptable', 'moyenne', 'standard'];
    
    const textLower = text.toLowerCase();
    let emoji = '';
    let sentiment = '';
    let color = '';
    
    // Vérifier les sentiments
    if (happyWords.some(word => textLower.includes(word))) {
        emoji = '😄 Happy';
        sentiment = 'Positif';
        color = '#4CAF50';
    } else if (sadWords.some(word => textLower.includes(word))) {
        emoji = '😢 Sad';
        sentiment = 'Négatif';
        color = '#F44336';
    } else if (neutralWords.some(word => textLower.includes(word))) {
        emoji = '😐 Neutre';
        sentiment = 'Neutre';
        color = '#FF9800';
    }
    
    // Afficher l'emoji si détecté
    if (emoji && text.length > 5) {
        const sentimentDiv = document.createElement('div');
        sentimentDiv.className = 'sentiment-emoji';
        sentimentDiv.innerHTML = `<span style="color: ${color}; font-weight: bold; font-size: 1.1em;">${emoji} - Sentiment: ${sentiment}</span>`;
        sentimentDiv.style.marginTop = '10px';
        sentimentDiv.style.padding = '10px';
        sentimentDiv.style.borderRadius = '5px';
        sentimentDiv.style.background = '#1a1f3a';
        sentimentDiv.style.border = `2px solid ${color}`;
        
        // Insérer après le textarea
        const textarea = document.getElementById('message');
        if (textarea) {
            textarea.parentElement.insertBefore(sentimentDiv, textarea.nextSibling);
        }
    }
}
