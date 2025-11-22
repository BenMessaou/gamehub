/**
 * Validation du formulaire d'ajout de jeu
 * Contrôles de saisie pour tous les champs
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.game-form');
    if (!form) return;

    // Fonction pour afficher les messages d'erreur
    function showError(input, message) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        // Supprimer l'erreur précédente
        const existingError = formGroup.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        // Ajouter le message d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = 'color: #ff6b81; font-size: 0.85rem; margin-top: 5px; display: flex; align-items: center; gap: 5px;';
        errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
        formGroup.appendChild(errorDiv);

        // Ajouter la classe d'erreur au champ
        input.style.borderColor = '#ff6b81';
        input.style.boxShadow = '0 0 10px rgba(255, 107, 129, 0.3)';
    }

    // Fonction pour supprimer les erreurs
    function clearError(input) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        const errorDiv = formGroup.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.remove();
        }

        input.style.borderColor = '';
        input.style.boxShadow = '';
    }

    // Validation du nom du jeu
    function validateNom(nom) {
        if (!nom || nom.trim() === '') {
            return 'Le nom du jeu est requis.';
        }
        if (nom.trim().length < 3) {
            return 'Le nom du jeu doit contenir au moins 3 caractères.';
        }
        if (nom.trim().length > 100) {
            return 'Le nom du jeu ne peut pas dépasser 100 caractères.';
        }
        if (!/^[a-zA-Z0-9\s\-_.,!?()]+$/.test(nom.trim())) {
            return 'Le nom contient des caractères non autorisés.';
        }
        return '';
    }

    // Validation du développeur
    function validateDeveloppeur(developpeur) {
        if (!developpeur || developpeur.trim() === '') {
            return 'Le nom du développeur est requis.';
        }
        if (developpeur.trim().length < 2) {
            return 'Le nom du développeur doit contenir au moins 2 caractères.';
        }
        if (developpeur.trim().length > 100) {
            return 'Le nom du développeur ne peut pas dépasser 100 caractères.';
        }
        return '';
    }

    // Validation de la date de création
    function validateDate(date) {
        if (!date) {
            return 'La date de création est requise.';
        }
        const selectedDate = new Date(date);
        const today = new Date();
        const minDate = new Date('1900-01-01');
        
        if (selectedDate > today) {
            return 'La date de création ne peut pas être dans le futur.';
        }
        if (selectedDate < minDate) {
            return 'La date de création doit être après 1900.';
        }
        return '';
    }

    // Validation de la catégorie
    function validateCategorie(categorie) {
        if (!categorie || categorie === '') {
            return 'Veuillez sélectionner une catégorie.';
        }
        const validCategories = ['Action', 'Aventure', 'RPG', 'Stratégie', 'Puzzle', 
                                'Plateforme', 'Simulation', 'Course', 'Horreur', 'Sport', 'Combat', 'Autres'];
        if (!validCategories.includes(categorie)) {
            return 'Catégorie invalide.';
        }
        return '';
    }

    // Validation de la description
    function validateDescription(description) {
        if (!description || description.trim() === '') {
            return 'La description est requise.';
        }
        if (description.trim().length < 150) {
            return 'La description doit contenir au moins 150 caractères.';
        }
        if (description.trim().length > 5000) {
            return 'La description ne peut pas dépasser 5000 caractères.';
        }
        return '';
    }

    // Validation de l'image
    function validateImage(file) {
        if (!file || !file.files || file.files.length === 0) {
            return 'L\'image de couverture est requise.';
        }
        
        const imageFile = file.files[0];
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 5 * 1024 * 1024; // 5 MB

        if (!allowedTypes.includes(imageFile.type)) {
            return 'Format d\'image non supporté. Utilisez JPG, PNG ou GIF.';
        }
        if (imageFile.size > maxSize) {
            return 'L\'image est trop volumineuse. Taille maximale: 5 MB.';
        }
        return '';
    }

    // Validation des screenshots
    function validateScreenshots(fileInput) {
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            return ''; // Optionnel
        }

        const files = Array.from(fileInput.files);
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 3 * 1024 * 1024; // 3 MB
        const maxFiles = 10;

        if (files.length > maxFiles) {
            return `Vous ne pouvez pas télécharger plus de ${maxFiles} captures d'écran.`;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (!allowedTypes.includes(file.type)) {
                return `Le fichier "${file.name}" n'est pas au format JPG ou PNG.`;
            }
            if (file.size > maxSize) {
                return `Le fichier "${file.name}" est trop volumineux (max 3 MB).`;
            }
        }
        return '';
    }

    // Validation de l'URL du trailer
    function validateTrailer(url) {
        if (!url || url.trim() === '') {
            return 'L\'URL du trailer est requise.';
        }
        
        try {
            const urlObj = new URL(url);
            const validDomains = ['youtube.com', 'www.youtube.com', 'youtu.be', 'www.youtu.be', 'vimeo.com', 'www.vimeo.com'];
            const hostname = urlObj.hostname.replace('www.', '');
            
            if (!validDomains.includes(hostname)) {
                return 'Veuillez fournir une URL YouTube ou Vimeo valide.';
            }
        } catch (e) {
            return 'Veuillez fournir une URL valide.';
        }
        return '';
    }

    // Validation de l'URL de téléchargement (optionnel)
    function validateDownloadLink(url) {
        if (!url || url.trim() === '') {
            return ''; // Optionnel
        }
        
        try {
            new URL(url);
        } catch (e) {
            return 'Veuillez fournir une URL valide.';
        }
        return '';
    }

    // Validation des tags
    function validateTags(tags) {
        if (!tags || tags.trim() === '') {
            return ''; // Optionnel
        }
        
        const tagArray = tags.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
        
        if (tagArray.length > 10) {
            return 'Vous ne pouvez pas ajouter plus de 10 mots-clés.';
        }
        
        for (let tag of tagArray) {
            if (tag.length > 30) {
                return `Le mot-clé "${tag}" est trop long (max 30 caractères).`;
            }
            if (!/^[a-zA-Z0-9\s\-_]+$/.test(tag)) {
                return `Le mot-clé "${tag}" contient des caractères non autorisés.`;
            }
        }
        return '';
    }

    // Validation du lieu
    function validateLieu(lieu) {
        if (!lieu || lieu.trim() === '') {
            return ''; // Optionnel
        }
        if (lieu.trim().length > 100) {
            return 'Le lieu ne peut pas dépasser 100 caractères.';
        }
        return '';
    }

    // Validation en temps réel pour chaque champ
    const nomInput = document.getElementById('nom');
    const developpeurInput = document.getElementById('developpeur');
    const dateInput = document.getElementById('date_creation');
    const categorieInput = document.getElementById('categorie');
    const descriptionInput = document.getElementById('description');
    const imageInput = document.getElementById('image');
    const screenshotsInput = document.getElementById('screenshots');
    const trailerInput = document.getElementById('trailer');
    const downloadLinkInput = document.getElementById('lien_telechargement');
    const tagsInput = document.getElementById('tags');
    const lieuInput = document.getElementById('lieu');

    // Validation en temps réel
    if (nomInput) {
        nomInput.addEventListener('blur', function() {
            const error = validateNom(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
        nomInput.addEventListener('input', function() {
            if (this.value.trim().length >= 3) {
                clearError(this);
            }
        });
    }

    if (developpeurInput) {
        developpeurInput.addEventListener('blur', function() {
            const error = validateDeveloppeur(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    if (dateInput) {
        dateInput.addEventListener('change', function() {
            const error = validateDate(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    if (categorieInput) {
        categorieInput.addEventListener('change', function() {
            const error = validateCategorie(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    if (descriptionInput) {
        descriptionInput.addEventListener('blur', function() {
            const error = validateDescription(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
        descriptionInput.addEventListener('input', function() {
            const charCount = this.value.length;
            const minChars = 150;
            if (charCount >= minChars) {
                clearError(this);
            }
        });
    }

    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const error = validateImage(this);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    if (screenshotsInput) {
        screenshotsInput.addEventListener('change', function() {
            const error = validateScreenshots(this);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    if (trailerInput) {
        trailerInput.addEventListener('blur', function() {
            const error = validateTrailer(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    if (downloadLinkInput) {
        downloadLinkInput.addEventListener('blur', function() {
            const error = validateDownloadLink(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    if (tagsInput) {
        tagsInput.addEventListener('blur', function() {
            const error = validateTags(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    if (lieuInput) {
        lieuInput.addEventListener('blur', function() {
            const error = validateLieu(this.value);
            if (error) {
                showError(this, error);
            } else {
                clearError(this);
            }
        });
    }

    // Validation complète avant soumission
    form.addEventListener('submit', function(e) {
        let hasErrors = false;

        // Valider tous les champs
        const nomError = validateNom(nomInput ? nomInput.value : '');
        if (nomError) {
            showError(nomInput, nomError);
            hasErrors = true;
        }

        const developpeurError = validateDeveloppeur(developpeurInput ? developpeurInput.value : '');
        if (developpeurError) {
            showError(developpeurInput, developpeurError);
            hasErrors = true;
        }

        const dateError = validateDate(dateInput ? dateInput.value : '');
        if (dateError) {
            showError(dateInput, dateError);
            hasErrors = true;
        }

        const categorieError = validateCategorie(categorieInput ? categorieInput.value : '');
        if (categorieError) {
            showError(categorieInput, categorieError);
            hasErrors = true;
        }

        const descriptionError = validateDescription(descriptionInput ? descriptionInput.value : '');
        if (descriptionError) {
            showError(descriptionInput, descriptionError);
            hasErrors = true;
        }

        const imageError = validateImage(imageInput);
        if (imageError) {
            showError(imageInput, imageError);
            hasErrors = true;
        }

        const screenshotsError = validateScreenshots(screenshotsInput);
        if (screenshotsError) {
            showError(screenshotsInput, screenshotsError);
            hasErrors = true;
        }

        const trailerError = validateTrailer(trailerInput ? trailerInput.value : '');
        if (trailerError) {
            showError(trailerInput, trailerError);
            hasErrors = true;
        }

        const downloadLinkError = validateDownloadLink(downloadLinkInput ? downloadLinkInput.value : '');
        if (downloadLinkError) {
            showError(downloadLinkInput, downloadLinkError);
            hasErrors = true;
        }

        const tagsError = validateTags(tagsInput ? tagsInput.value : '');
        if (tagsError) {
            showError(tagsInput, tagsError);
            hasErrors = true;
        }

        const lieuError = validateLieu(lieuInput ? lieuInput.value : '');
        if (lieuError) {
            showError(lieuInput, lieuError);
            hasErrors = true;
        }

        if (hasErrors) {
            e.preventDefault();
            
            // Faire défiler jusqu'au premier champ en erreur
            const firstError = form.querySelector('.error-message');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Afficher un message général
            const messageContainer = document.getElementById('message-container');
            if (messageContainer) {
                messageContainer.style.display = 'block';
                messageContainer.style.background = 'rgba(255, 51, 92, 0.1)';
                messageContainer.style.border = '1px solid rgba(255, 51, 92, 0.5)';
                const messageContent = document.getElementById('message-content');
                if (messageContent) {
                    messageContent.style.color = '#ff6b81';
                    messageContent.innerHTML = '<strong>✗ Erreurs de validation:</strong> Veuillez corriger les erreurs dans le formulaire avant de soumettre.';
                }
            }
            
            return false;
        }

        // Si tout est valide, permettre la soumission
        return true;
    });
});
