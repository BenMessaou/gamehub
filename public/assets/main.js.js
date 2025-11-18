// assets/main.js
document.addEventListener('DOMContentLoaded', function () {

  /* ---------------------------
     Code pour index.html (recherche)
     --------------------------- */
  const searchInput = document.getElementById('game-search');
  const searchButton = document.querySelector('.hero .search-bar button');

  if (searchInput && searchButton) {
    function rechercherJeu() {
      const query = searchInput.value.trim();
      if (query === '') {
        alert("Veuillez entrer le nom d'un jeu.");
        return;
      }
      // Exemple de message de recherche (à remplacer par fetch/ajax si besoin)
      alert(`Recherche pour "${query}"... (Fonctionnalité à intégrer plus tard)`);
      searchInput.value = '';
    }

    // clic sur le bouton
    searchButton.addEventListener('click', rechercherJeu);

    // Entrée clavier (Enter)
    searchInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        rechercherJeu();
      }
    });
  }

  /* ---------------------------
     Code pour avis.html (formulaires feedback + contact)
     --------------------------- */
  const feedbackForm = document.getElementById('feedback-form');
  const feedbackList = document.getElementById('feedback-list');
  const contactForm = document.getElementById('contact-form');

  if (feedbackForm) {
    feedbackForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const pseudo = document.getElementById('pseudo').value.trim();
      const game = document.getElementById('game').value.trim();
      const selectedRating = document.querySelector('input[name="rating"]:checked');
      const ratingValue = selectedRating ? selectedRating.value : null;
      const message = document.getElementById('message').value.trim();

      if (!ratingValue) {
        alert("Veuillez sélectionner une note.");
        return;
      }

      if (!pseudo || !game || !message) {
        alert("Tous les champs sont obligatoires.");
        return;
      }

      let stars = '';
      for (let i = 1; i <= 5; i++) {
        stars += i <= ratingValue ? '★' : '☆';
      }

      const feedback = document.createElement('div');
      feedback.className = 'feedback-item';
      feedback.innerHTML = `
        <h4>${escapeHtml(pseudo)} 🎮 (${escapeHtml(game)})</h4>
        <p><strong>Note : </strong>${stars} (${ratingValue}/5)</p>
        <p>${escapeHtml(message).replace(/\n/g, '<br>')}</p>
      `;

      if (feedbackList) feedbackList.prepend(feedback);
      feedbackForm.reset();
    });
  }

  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('name').value.trim();
      const email = document.getElementById('email').value.trim();
      const message = document.getElementById('message-contact').value.trim();

      if (!name || !email || !message) {
        alert("Tous les champs du contact sont obligatoires.");
        return;
      }

      // Ici on pourrait envoyer via fetch() vers un endpoint backend
      alert(`Merci ${name}, ton message a été envoyé !`);
      contactForm.reset();
    });
  }

  // Simple escape pour éviter injection HTML dans le contenu ajouté dynamiquement
  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

});
