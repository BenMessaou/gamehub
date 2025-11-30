/**
 * Système de validation JavaScript avec affichage des conditions sous les champs
 * Remplace la validation HTML5
 */

class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId) || document.querySelector('form');
        this.validationRules = {};
        this.validationMessages = {};
        
        if (!this.form) {
            console.warn('Formulaire non trouvé pour la validation');
            return;
        }
        
        this.init();
    }
    
    init() {
        // Empêcher la soumission HTML5 par défaut
        this.form.setAttribute('novalidate', 'novalidate');
        
        // Gérer la soumission du formulaire
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
    
    /**
     * Ajouter une règle de validation pour un champ
     */
    addRule(fieldId, rules, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        // Retirer tous les attributs HTML5 de validation
        field.removeAttribute('required');
        field.removeAttribute('pattern');
        field.removeAttribute('minlength');
        field.removeAttribute('maxlength');
        field.removeAttribute('min');
        field.removeAttribute('max');
        
        this.validationRules[fieldId] = rules;
        this.validationMessages[fieldId] = message || '';
        
        // Ajouter l'affichage de validation sous le champ
        this.addValidationDisplay(field, rules);
        
        // Ajouter les listeners pour la validation en temps réel
        field.addEventListener('blur', () => this.validateField(fieldId));
        field.addEventListener('input', () => this.validateField(fieldId));
    }
    
    /**
     * Ajouter l'affichage de validation sous un champ
     */
    addValidationDisplay(field, rules) {
        const formGroup = field.closest('.form-group') || field.parentElement;
        if (!formGroup) return;
        
        // Créer le conteneur de validation
        let validationContainer = formGroup.querySelector('.validation-container');
        if (!validationContainer) {
            validationContainer = document.createElement('div');
            validationContainer.className = 'validation-container';
            validationContainer.style.cssText = 'margin-top: 8px; font-size: 0.85rem;';
            formGroup.appendChild(validationContainer);
        }
        
        // Créer la liste des conditions
        const conditionsList = document.createElement('ul');
        conditionsList.className = 'validation-conditions';
        conditionsList.style.cssText = 'list-style: none; padding: 0; margin: 0;';
        
        // Créer les éléments de conditions
        const conditions = this.buildConditionsFromRules(rules);
        conditions.forEach(condition => {
            const li = document.createElement('li');
            li.className = 'validation-condition';
            li.dataset.condition = condition.id;
            li.style.cssText = 'margin: 4px 0; padding: 4px 8px; border-radius: 4px; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;';
            
            const icon = document.createElement('span');
            icon.className = 'validation-icon';
            icon.style.cssText = 'font-weight: bold; font-size: 1rem; color: #888;';
            icon.textContent = '○';
            
            const text = document.createElement('span');
            text.textContent = condition.text;
            text.style.color = '#888';
            
            li.appendChild(icon);
            li.appendChild(text);
            conditionsList.appendChild(li);
        });
        
        validationContainer.innerHTML = '';
        validationContainer.appendChild(conditionsList);
    }
    
    /**
     * Construire la liste des conditions à partir des règles
     */
    buildConditionsFromRules(rules) {
        const conditions = [];
        
        if (rules.required) {
            conditions.push({ id: 'required', text: 'Ce champ est obligatoire' });
        }
        
        if (rules.minLength) {
            conditions.push({ id: 'minLength', text: `Minimum ${rules.minLength} caractères` });
        }
        
        if (rules.maxLength) {
            conditions.push({ id: 'maxLength', text: `Maximum ${rules.maxLength} caractères` });
        }
        
        if (rules.min) {
            conditions.push({ id: 'min', text: `Valeur minimum : ${rules.min}` });
        }
        
        if (rules.max) {
            conditions.push({ id: 'max', text: `Valeur maximum : ${rules.max}` });
        }
        
        if (rules.type === 'email') {
            conditions.push({ id: 'email', text: 'Format email valide (ex: user@example.com)' });
        }
        
        if (rules.type === 'url') {
            conditions.push({ id: 'url', text: 'Format URL valide (ex: https://example.com)' });
        }
        
        if (rules.pattern) {
            conditions.push({ id: 'pattern', text: rules.patternMessage || 'Format invalide' });
        }
        
        return conditions;
    }
    
    /**
     * Valider un champ spécifique
     */
    validateField(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field || !this.validationRules[fieldId]) return false;
        
        const value = field.value.trim();
        const rules = this.validationRules[fieldId];
        const formGroup = field.closest('.form-group') || field.parentElement;
        const validationContainer = formGroup?.querySelector('.validation-container');
        
        let isValid = true;
        const results = {};
        
        // Validation required
        if (rules.required) {
            results.required = value.length > 0;
            if (!results.required) isValid = false;
        }
        
        // Validation minLength
        if (rules.minLength && value.length > 0) {
            results.minLength = value.length >= rules.minLength;
            if (!results.minLength) isValid = false;
        }
        
        // Validation maxLength
        if (rules.maxLength && value.length > 0) {
            results.maxLength = value.length <= rules.maxLength;
            if (!results.maxLength) isValid = false;
        }
        
        // Validation min (pour les nombres)
        if (rules.min !== undefined && value.length > 0) {
            const numValue = parseFloat(value);
            results.min = !isNaN(numValue) && numValue >= rules.min;
            if (!results.min) isValid = false;
        }
        
        // Validation max (pour les nombres)
        if (rules.max !== undefined && value.length > 0) {
            const numValue = parseFloat(value);
            results.max = !isNaN(numValue) && numValue <= rules.max;
            if (!results.max) isValid = false;
        }
        
        // Validation email
        if (rules.type === 'email' && value.length > 0) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            results.email = emailRegex.test(value);
            if (!results.email) isValid = false;
        }
        
        // Validation URL
        if (rules.type === 'url' && value.length > 0) {
            try {
                new URL(value);
                results.url = true;
            } catch {
                results.url = false;
                isValid = false;
            }
        }
        
        // Validation pattern
        if (rules.pattern && value.length > 0) {
            const regex = new RegExp(rules.pattern);
            results.pattern = regex.test(value);
            if (!results.pattern) isValid = false;
        }
        
        // Mettre à jour l'affichage visuel
        this.updateValidationDisplay(field, results, isValid);
        
        return isValid;
    }
    
    /**
     * Mettre à jour l'affichage de validation
     */
    updateValidationDisplay(field, results, isValid) {
        const formGroup = field.closest('.form-group') || field.parentElement;
        const validationContainer = formGroup?.querySelector('.validation-container');
        if (!validationContainer) return;
        
        // Mettre à jour les conditions
        const conditions = validationContainer.querySelectorAll('.validation-condition');
        conditions.forEach(condition => {
            const conditionId = condition.dataset.condition;
            const icon = condition.querySelector('.validation-icon');
            
            if (results[conditionId] !== undefined) {
                if (results[conditionId]) {
                    icon.textContent = '✓';
                    icon.style.color = '#00ff88';
                    condition.style.background = 'rgba(0, 255, 136, 0.1)';
                    condition.style.color = '#00ff88';
                } else {
                    icon.textContent = '✗';
                    icon.style.color = '#ff335c';
                    condition.style.background = 'rgba(255, 51, 92, 0.1)';
                    condition.style.color = '#ff335c';
                }
            } else {
                // Condition non encore vérifiée
                icon.textContent = '○';
                icon.style.color = '#888';
                condition.style.background = 'transparent';
                condition.style.color = '#888';
            }
        });
        
        // Mettre à jour le style du champ
        if (field.value.trim().length > 0) {
            if (isValid) {
                field.style.borderColor = '#00ff88';
                field.style.boxShadow = '0 0 10px rgba(0, 255, 136, 0.3)';
            } else {
                field.style.borderColor = '#ff335c';
                field.style.boxShadow = '0 0 10px rgba(255, 51, 92, 0.3)';
            }
        } else {
            field.style.borderColor = '';
            field.style.boxShadow = '';
        }
    }
    
    /**
     * Valider tout le formulaire
     */
    validateForm() {
        let isFormValid = true;
        
        Object.keys(this.validationRules).forEach(fieldId => {
            if (!this.validateField(fieldId)) {
                isFormValid = false;
            }
        });
        
        return isFormValid;
    }
}

// Export pour utilisation globale
window.FormValidator = FormValidator;

