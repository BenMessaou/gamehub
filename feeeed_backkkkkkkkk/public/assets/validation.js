// validation.js - Alertes personnalisées compatibles avec le template

// Fonction pour créer et afficher une alerte personnalisée
function showCustomAlert(type, title, message) {
    // Supprimer les alertes existantes
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());

    // Créer l'alerte
    const alert = document.createElement('div');
    alert.className = `custom-alert ${type}`;

    // Icône selon le type
    let icon = '⚠️';
    if (type === 'success') icon = '✅';
    if (type === 'error') icon = '❌';

    // Contenu de l'alerte
    alert.innerHTML = `
        <div class="custom-alert-icon">${icon}</div>
        <div class="custom-alert-content">
            <div class="custom-alert-title">${title}</div>
            <div class="custom-alert-message">${message}</div>
        </div>
        <button class="custom-alert-close" onclick="this.parentElement.remove()">×</button>
    `;

    // Ajouter au body
    document.body.appendChild(alert);

    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        alert.classList.add('hiding');
        setTimeout(() => alert.remove(), 300);
    }, 5000);

    // Faire défiler vers le haut pour voir l'alerte
    alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Fonction pour mettre en évidence un champ invalide
function highlightField(fieldId, isValid) {
    const field = document.getElementById(fieldId);
    if (field) {
        if (isValid) {
            field.style.borderColor = '#00ff88';
            field.style.boxShadow = '0 0 10px rgba(0, 255, 136, 0.3)';
        } else {
            field.style.borderColor = '#ff4757';
            field.style.boxShadow = '0 0 10px rgba(255, 71, 87, 0.3)';
            // Retirer le style après 2 secondes
            setTimeout(() => {
                field.style.borderColor = '';
                field.style.boxShadow = '';
            }, 2000);
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const feedbackForm = document.getElementById('feedback-form');
    const contactForm = document.getElementById('contact-form');

    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function (e) {
            let isValid = true;
            let firstError = null;

            // Validation du pseudo
            const pseudo = document.getElementById('pseudo').value.trim();
            if (!pseudo) {
                showCustomAlert('error', 'Pseudo requis', 'Veuillez entrer votre pseudo.');
                highlightField('pseudo', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('pseudo');
            } else if (pseudo.length < 2) {
                showCustomAlert('error', 'Pseudo invalide', 'Le pseudo doit contenir au moins 2 caractères.');
                highlightField('pseudo', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('pseudo');
            } else if (pseudo.length > 50) {
                showCustomAlert('error', 'Pseudo trop long', 'Le pseudo ne doit pas dépasser 50 caractères.');
                highlightField('pseudo', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('pseudo');
            } else {
                highlightField('pseudo', true);
            }

            // Validation de l'email
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) {
                showCustomAlert('error', 'Email requis', 'Veuillez entrer votre adresse email.');
                highlightField('email', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('email');
            } else if (!emailRegex.test(email)) {
                showCustomAlert('error', 'Email invalide', 'Veuillez entrer une adresse email valide (exemple: nom@domaine.com).');
                highlightField('email', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('email');
            } else if (email.length > 255) {
                showCustomAlert('error', 'Email trop long', 'L\'email ne doit pas dépasser 255 caractères.');
                highlightField('email', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('email');
            } else {
                highlightField('email', true);
            }

            // Validation du jeu
            const game = document.getElementById('game').value.trim();
            if (!game) {
                showCustomAlert('error', 'Nom du jeu requis', 'Veuillez entrer le nom du jeu.');
                highlightField('game', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('game');
            } else if (game.length > 150) {
                showCustomAlert('error', 'Nom trop long', 'Le nom du jeu ne doit pas dépasser 150 caractères.');
                highlightField('game', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('game');
            } else {
                highlightField('game', true);
            }

            // Validation de la note
            const rating = document.querySelector('input[name="rating"]:checked');
            if (!rating) {
                showCustomAlert('error', 'Note requise', 'Veuillez sélectionner une note en cliquant sur les étoiles.');
                isValid = false;
            }

            // Validation du message
            const message = document.getElementById('message').value.trim();
            if (!message) {
                showCustomAlert('error', 'Avis requis', 'Veuillez écrire votre avis.');
                highlightField('message', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('message');
            } else if (message.length < 5) {
                showCustomAlert('error', 'Avis trop court', 'Votre avis doit contenir au moins 5 caractères.');
                highlightField('message', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('message');
            } else if (message.length > 1000) {
                showCustomAlert('error', 'Avis trop long', 'Votre avis ne doit pas dépasser 1000 caractères.');
                highlightField('message', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('message');
            } else {
                highlightField('message', true);
            }

            if (!isValid) {
                e.preventDefault();
                // Faire défiler vers le premier champ en erreur
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    }

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            let isValid = true;
            let firstError = null;

            // Validation du nom
            const name = document.getElementById('name').value.trim();
            if (!name) {
                showCustomAlert('error', 'Nom requis', 'Veuillez entrer votre nom.');
                highlightField('name', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('name');
            } else if (name.length < 2) {
                showCustomAlert('error', 'Nom invalide', 'Le nom doit contenir au moins 2 caractères.');
                highlightField('name', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('name');
            } else if (name.length > 50) {
                showCustomAlert('error', 'Nom trop long', 'Le nom ne doit pas dépasser 50 caractères.');
                highlightField('name', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('name');
            } else {
                highlightField('name', true);
            }

            // Validation de l'email
            const email = document.getElementById('email-contact').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) {
                showCustomAlert('error', 'Email requis', 'Veuillez entrer votre adresse email.');
                highlightField('email-contact', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('email-contact');
            } else if (!emailRegex.test(email)) {
                showCustomAlert('error', 'Email invalide', 'Veuillez entrer une adresse email valide (exemple: nom@domaine.com).');
                highlightField('email-contact', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('email-contact');
            } else {
                highlightField('email-contact', true);
            }

            // Validation du message de contact
            const contactMessage = document.getElementById('message-contact').value.trim();
            if (!contactMessage) {
                showCustomAlert('error', 'Message requis', 'Veuillez écrire votre message.');
                highlightField('message-contact', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('message-contact');
            } else if (contactMessage.length < 5) {
                showCustomAlert('error', 'Message trop court', 'Votre message doit contenir au moins 5 caractères.');
                highlightField('message-contact', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('message-contact');
            } else if (contactMessage.length > 1000) {
                showCustomAlert('error', 'Message trop long', 'Votre message ne doit pas dépasser 1000 caractères.');
                highlightField('message-contact', false);
                isValid = false;
                if (!firstError) firstError = document.getElementById('message-contact');
            } else {
                highlightField('message-contact', true);
            }

            if (!isValid) {
                e.preventDefault();
                // Faire défiler vers le premier champ en erreur
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    }
});
