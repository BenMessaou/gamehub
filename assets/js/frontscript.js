// assets/js/frontscript.js (CODE FINAL CORRIGÉ ET INCLUANT TTS)

// ------------------------------------------------------------------
// Logique 1 : Compte à Rebours (CORRECTION POUR ÉVITER LE BLOCAGE)
// ------------------------------------------------------------------
const countdownContainer = document.querySelector('.countdown');

if (countdownContainer) {
    // Si l'élément existe, nous définissons et lançons la fonction
    const daysEl = document.getElementById('days');
    const hoursEl = document.getElementById('hours');
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');
    
    // Vérification de sécurité supplémentaire
    if (daysEl && hoursEl && minutesEl && secondsEl) {
        
        function updateCountdown() {
            const targetDate = new Date('2023-11-27T00:00:00'); 
            const now = new Date();
            const difference = targetDate - now;

            if (difference > 0) {
                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                daysEl.textContent = days.toString().padStart(2, '0');
                hoursEl.textContent = hours.toString().padStart(2, '0');
                minutesEl.textContent = minutes.toString().padStart(2, '0');
                secondsEl.textContent = seconds.toString().padStart(2, '0');
            } else {
                countdownContainer.innerHTML = '<p>The sale has ended!</p>';
            }
        }

        setInterval(updateCountdown, 1000);
        updateCountdown(); 
    }
}
// ------------------------------------------------------------------
// Logique 2 & 3 : Smooth scrolling, Mobile menu et TTS
// ------------------------------------------------------------------

document.addEventListener('DOMContentLoaded', () => {
    // Smooth scrolling pour la navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Mobile menu toggle 
    const nav = document.querySelector('nav ul');
    const toggle = document.createElement('button');
    toggle.textContent = 'Menu';
    toggle.style.display = 'none';
    toggle.addEventListener('click', () => {
        if (nav) nav.classList.toggle('show');
    });
    const headerContainer = document.querySelector('header .container');
    if (headerContainer) {
        headerContainer.appendChild(toggle);
    }

    if (window.innerWidth <= 768) {
        toggle.style.display = 'block';
    }
    
    
    // ------------------------------------------------------------------
    // Logique TTS : Lecture Son des Articles (ACTIVATION)
    // ------------------------------------------------------------------

    const listenButton = document.getElementById('listen-article-btn');
    const stopButton = document.getElementById('stop-article-btn');
    const articleTextElement = document.getElementById('article-to-read');
    
    console.log("Vérification : Tentative d'initialisation de la logique TTS."); 
    
    if (listenButton && articleTextElement && 'speechSynthesis' in window) {
        
        const textToRead = articleTextElement.textContent || articleTextElement.innerText;
        const synth = window.speechSynthesis;
        let utterance = null;
        
        const checkAndEnableTTS = () => {
            if (synth.getVoices().length === 0) {
                setTimeout(checkAndEnableTTS, 100); 
                return;
            }

            listenButton.style.display = 'inline-block';
            console.log("TTS : Fonctionnalité activée. Le bouton Écouter est visible.");

            function initializeUtterance() {
                utterance = new SpeechSynthesisUtterance(textToRead);
                
                const frenchVoice = synth.getVoices().find(voice => voice.lang.startsWith('fr'));
                if (frenchVoice) {
                    utterance.voice = frenchVoice;
                    utterance.lang = frenchVoice.lang;
                } else {
                    utterance.lang = 'fr-FR'; 
                }
                
                utterance.rate = 1.0; 
                utterance.volume = 1.0;

                utterance.onstart = () => {
                    listenButton.style.display = 'none';
                    stopButton.style.display = 'inline-block';
                };

                utterance.onend = () => {
                    listenButton.style.display = 'inline-block';
                    stopButton.style.display = 'none';
                };
            }

            listenButton.addEventListener('click', () => {
                if (synth.speaking) {
                    synth.cancel();
                }
                initializeUtterance();
                synth.speak(utterance);
            });

            stopButton.addEventListener('click', () => {
                if (synth.speaking) {
                    synth.cancel(); 
                }
                listenButton.style.display = 'inline-block';
                stopButton.style.display = 'none';
            });
        };
        
        synth.onvoiceschanged = checkAndEnableTTS;
        checkAndEnableTTS();


    } else if (listenButton) {
        listenButton.textContent = "Lecture vocale non supportée.";
        listenButton.style.backgroundColor = '#555';
        listenButton.disabled = true;
        listenButton.style.display = 'inline-block';
    }
});