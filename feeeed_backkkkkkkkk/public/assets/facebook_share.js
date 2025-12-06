/**
 * Script de partage Facebook pour les avis
 * Gère l'ouverture de la fenêtre de partage Facebook avec les bonnes données
 */

// Fonction principale pour partager un avis sur Facebook
function shareOnFacebook(feedbackId, shareText) {
    // Décoder le texte de partage
    let textToShare = '';
    if (shareText) {
        try {
            textToShare = JSON.parse(shareText);
        } catch(e) {
            textToShare = shareText;
        }
    }
    
    // Construire l'URL de partage complète (dynamique pour XAMPP)
    const baseUrl = window.location.origin;
    // Récupérer le chemin de base depuis l'URL actuelle
    const currentPath = window.location.pathname;
    
    // Extraire le chemin de base du projet
    let basePath = '';
    if (currentPath.includes('/views/')) {
        // Si on est dans /views/, extraire le chemin avant
        basePath = currentPath.substring(0, currentPath.indexOf('/views/'));
    } else if (currentPath.includes('/feeeed_backkkkkkkkk')) {
        // Si le chemin contient le nom du projet
        const projectIndex = currentPath.indexOf('/feeeed_backkkkkkkkk');
        basePath = currentPath.substring(0, projectIndex + '/feeeed_backkkkkkkkk'.length);
    } else {
        // Par défaut, utiliser le chemin actuel jusqu'au dernier /
        basePath = currentPath.substring(0, currentPath.lastIndexOf('/'));
    }
    
    const shareUrl = baseUrl + basePath + '/views/share.php?id=' + feedbackId;
    
    // Afficher une modal avec le texte formaté
    showShareModal(textToShare, shareUrl, feedbackId);
}

// Fonction pour afficher la modal de partage
function showShareModal(textToShare, shareUrl, feedbackId) {
    // Créer la modal
    const modal = document.createElement('div');
    modal.id = 'fb-share-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 10000;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s;
    `;
    
    modal.innerHTML = `
        <div style="
            background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%);
            padding: 30px;
            border-radius: 15px;
            max-width: 650px;
            width: 90%;
            border: 3px solid #00ff88;
            box-shadow: 0 8px 32px rgba(0,255,136,0.5);
            animation: slideUp 0.3s;
        ">
            <h2 style="color: #00ff88; margin-bottom: 20px; text-align: center; font-size: 1.8em;">📘 Partager sur Facebook</h2>
            
            <div style="background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%); padding: 20px; border-radius: 10px; border: 2px solid #00ff88; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <span style="font-size: 2em; margin-right: 10px;">✅</span>
                    <p style="color: #00ff88; font-weight: bold; font-size: 1.1em; margin: 0;">Le texte de votre avis est prêt !</p>
                </div>
                <div style="background: #0a0e27; padding: 15px; border-radius: 8px; margin-top: 15px;">
                    <p style="color: #e0e0e0; font-size: 15px; margin: 8px 0; line-height: 1.6;">
                        <strong style="color: #00ff88;">Étape 1 :</strong> Cliquez sur le bouton bleu "Ouvrir Facebook" ci-dessous
                    </p>
                    <p style="color: #e0e0e0; font-size: 15px; margin: 8px 0; line-height: 1.6;">
                        <strong style="color: #00ff88;">Étape 2 :</strong> Dans la fenêtre Facebook, cliquez dans le champ "Quoi de neuf ?"
                    </p>
                    <p style="color: #e0e0e0; font-size: 15px; margin: 8px 0; line-height: 1.6;">
                        <strong style="color: #00ff88;">Étape 3 :</strong> Appuyez sur <span style="background: #00ff88; color: #000; padding: 3px 8px; border-radius: 4px; font-weight: bold;">Ctrl+V</span> (ou <span style="background: #00ff88; color: #000; padding: 3px 8px; border-radius: 4px; font-weight: bold;">Cmd+V</span> sur Mac)
                    </p>
                    <p style="color: #00ff88; font-size: 15px; margin: 8px 0; line-height: 1.6; font-weight: bold;">
                        ✨ Le texte de votre avis apparaîtra automatiquement !
                    </p>
                </div>
            </div>
            
            <p style="color: #e0e0e0; margin-bottom: 10px; font-size: 14px; font-weight: bold;">📝 Aperçu du texte à partager :</p>
            <textarea id="share-text-area" readonly style="
                width: 100%;
                min-height: 180px;
                padding: 15px;
                background: #0a0e27;
                color: #e0e0e0;
                border: 2px solid #00ff88;
                border-radius: 8px;
                font-size: 14px;
                font-family: Arial, sans-serif;
                resize: vertical;
                margin-bottom: 20px;
                line-height: 1.6;
            ">${textToShare}</textarea>
            
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <button onclick="openFacebookShare('${shareUrl}')" style="
                    background: #1877F2;
                    color: white;
                    border: none;
                    padding: 15px 30px;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    font-size: 18px;
                    transition: all 0.3s;
                    box-shadow: 0 4px 15px rgba(24,119,242,0.4);
                    flex: 1;
                    min-width: 200px;
                " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(24,119,242,0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(24,119,242,0.4)';">
                    📘 Ouvrir Facebook
                </button>
                <button onclick="copyShareText()" style="
                    background: #00ff88;
                    color: #000;
                    border: none;
                    padding: 15px 25px;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    font-size: 16px;
                    transition: all 0.3s;
                " onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
                    📋 Copier
                </button>
                <button onclick="closeShareModal()" style="
                    background: #666;
                    color: white;
                    border: none;
                    padding: 15px 25px;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    font-size: 16px;
                    transition: all 0.3s;
                ">✖️ Fermer</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Copier automatiquement le texte
    setTimeout(() => {
        copyShareText();
    }, 200);
    
    // Fermer en cliquant en dehors
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeShareModal();
        }
    });
}

// Fonction pour copier le texte
function copyShareText() {
    const textarea = document.getElementById('share-text-area');
    if (textarea) {
        textarea.select();
        textarea.setSelectionRange(0, 99999); // Pour mobile
        
        // Utiliser l'API moderne si disponible
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textarea.value).then(() => {
                showCopyNotification();
            });
        } else {
            // Fallback pour anciens navigateurs
            document.execCommand('copy');
            showCopyNotification();
        }
    }
}

// Fonction pour afficher la notification de copie
function showCopyNotification() {
    const btn = document.querySelector('button[onclick="copyShareText()"]');
    if (btn) {
        const originalText = btn.textContent;
        btn.textContent = '✅ Copié !';
        btn.style.background = '#4CAF50';
        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '#00ff88';
        }, 2000);
    }
}

// Fonction pour ouvrir Facebook avec instructions
function openFacebookShare(shareUrl) {
    // S'assurer que le texte est copié
    const textarea = document.getElementById('share-text-area');
    if (textarea) {
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textarea.value).then(() => {
                console.log('Texte copié dans le presse-papier');
            });
        } else {
            document.execCommand('copy');
        }
    }
    
    // Ouvrir Facebook dans une nouvelle fenêtre
    const facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl);
    const width = 800;
    const height = 650;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    
    window.open(
        facebookShareUrl,
        'Partager sur Facebook',
        `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
    );
    
    // Ne pas fermer la modal immédiatement - laisser l'utilisateur voir les instructions
    // La modal restera ouverte pour référence
}

// Fonction pour fermer la modal
function closeShareModal() {
    const modal = document.getElementById('fb-share-modal');
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s';
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ajouter les animations CSS
if (!document.getElementById('fb-share-styles')) {
    const style = document.createElement('style');
    style.id = 'fb-share-styles';
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}

// Fonction alternative pour partager directement depuis la page de partage
function shareDirectly() {
    const currentUrl = window.location.href;
    const facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentUrl);
    
    const width = 600;
    const height = 400;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    
    window.open(
        facebookShareUrl,
        'Partager sur Facebook',
        `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
    );
}

// Ajouter les événements aux boutons de partage après le chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Trouver tous les boutons avec la classe btn-share-fb
    const shareButtons = document.querySelectorAll('.btn-share-fb');
    
    shareButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const feedbackId = this.getAttribute('data-feedback-id');
            if (feedbackId) {
                shareOnFacebook(feedbackId);
            }
        });
    });
});

