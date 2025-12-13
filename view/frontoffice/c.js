/* ============================
   HEADER SCROLL EFFECT
============================ */
window.addEventListener("scroll", () => {
  const header = document.querySelector("header");
  if (window.scrollY > 50) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

/* ============================
   BURGER MENU
============================ */
const burger = document.querySelector(".burger-container");
const nav = document.querySelector("nav");

if (burger) {
  burger.addEventListener("click", () => {
    burger.classList.toggle("active");
    nav.classList.toggle("active");
  });
}

/* Close menu when clicking a link (mobile) */
document.querySelectorAll("nav a").forEach(link => {
  link.addEventListener("click", () => {
    burger.classList.remove("active");
    nav.classList.remove("active");
  });
});

/* ============================
   SMOOTH SCROLL
============================ */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener("click", function (e) {
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: "smooth" });
    }
  });
});

/* ============================
   GAME FILTER SYSTEM
============================ */
const filterButtons = document.querySelectorAll(".filter-btn");
const gameCards = document.querySelectorAll(".game-card");

filterButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    filterButtons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    const category = btn.dataset.category;

    gameCards.forEach(card => {
      if (category === "all" || card.dataset.category === category) {
        card.style.display = "block";
        card.classList.add("fade-in");
      } else {
        card.style.display = "none";
        card.classList.remove("fade-in");
      }
    });
  });
});

/* ============================
   LABEL FLOTTANT POUR LES INPUTS
============================ */
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll(".form-group input, .form-group textarea, .form-group select").forEach(input => {
    // Sauvegarder le placeholder original
    if (input.placeholder) {
      input.setAttribute("data-placeholder-original", input.placeholder);
    }

    // Fonction pour cacher le placeholder
    const hidePlaceholder = () => {
      if (input.placeholder) {
        input.setAttribute("data-placeholder", input.placeholder);
        input.placeholder = "";
        input.setAttribute("data-has-value", "true");
      }
    };

    // Fonction pour restaurer le placeholder
    const showPlaceholder = () => {
      if (input.hasAttribute("data-placeholder")) {
        input.placeholder = input.getAttribute("data-placeholder");
        input.removeAttribute("data-placeholder");
        input.removeAttribute("data-has-value");
      } else if (input.hasAttribute("data-placeholder-original")) {
        input.placeholder = input.getAttribute("data-placeholder-original");
        input.removeAttribute("data-has-value");
      }
    };

    // Au focus - cacher immédiatement le placeholder
    input.addEventListener("focus", () => {
      input.classList.add("active");
      hidePlaceholder();
    });

    // Pendant la saisie - s'assurer que le placeholder reste caché
    input.addEventListener("input", () => {
      if (input.value && input.value.trim() !== "") {
        hidePlaceholder();
      } else {
        showPlaceholder();
      }
    });

    // Au blur - restaurer seulement si vide
    input.addEventListener("blur", () => {
      if (input.value === "" || input.value.trim() === "") {
        input.classList.remove("active");
        showPlaceholder();
      }
    });

    // Au changement (pour les selects)
    input.addEventListener("change", () => {
      if (input.value && input.value !== "" && input.value !== "0") {
        hidePlaceholder();
      } else {
        showPlaceholder();
      }
    });

    // Vérifier au chargement si l'input a déjà une valeur
    if (input.value && input.value !== "" && input.value !== "0") {
      hidePlaceholder();
    }
  });
});

/* ============================
   FILE INPUT CUSTOM
============================ */
const fileInputs = document.querySelectorAll(".file-input-wrapper input");

fileInputs.forEach(input => {
  const label = input.closest(".file-input-wrapper").querySelector(".file-input-label");

  input.addEventListener("change", () => {
    const fileName = input.files.length > 0 ? input.files[0].name : "Choisir un fichier…";
    label.textContent = fileName;
  });
});

/* ============================
   WISHLIST (localStorage)
============================ */
document.querySelectorAll(".wishlist-btn").forEach(button => {
  button.addEventListener("click", () => {
    const gameName = button.dataset.game;

    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];

    if (!wishlist.includes(gameName)) {
      wishlist.push(gameName);
      localStorage.setItem("wishlist", JSON.stringify(wishlist));
      button.textContent = "✓ Ajouté à la wishlist";
      button.style.borderColor = "#00ffea";
      button.style.color = "#00ffea";
    } else {
      wishlist = wishlist.filter(g => g !== gameName);
      localStorage.setItem("wishlist", JSON.stringify(wishlist));
      button.textContent = "Ajouter à la wishlist";
      button.style.borderColor = "#ff00c7";
      button.style.color = "#ccc";
    }
  });
});

/* ============================
   ANIMATION D’APPARITION (Fade-in)
============================ */
const fadeElements = document.querySelectorAll(".game-card, .team-member, .about-section");

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add("fade-visible");
    }
  });
}, { threshold: 0.2 });

fadeElements.forEach(el => observer.observe(el));

/* Fade-in CSS class auto added */
document.querySelectorAll(".fade-visible").forEach(el => {
  el.style.opacity = 1;
  el.style.transform = "translateY(0)";
});

/* ============================
   BUTTON “ADD GAME” REDIRECTION
============================ */
const addGameBtn = document.querySelector(".add-game-btn");
if (addGameBtn) {
  addGameBtn.addEventListener("click", () => {
    window.location.href = "/add-game.html"; // change selon ton fichier
  });
}

/* ============================
   SCROLL TO TOP BUTTON (Optionnel)
============================ */
const scrollTopBtn = document.createElement("button");
scrollTopBtn.innerText = "▲";
scrollTopBtn.className = "scroll-top-btn";

scrollTopBtn.style.cssText = `
  position: fixed;
  bottom: 26px;
  right: 26px;
  background: #ff00c7;
  color: #fff;
  border: none;
  padding: 12px 18px;
  border-radius: 999px;
  cursor: pointer;
  display: none;
  font-weight: 700;
  box-shadow: 0 0 20px rgba(255, 0, 199, 0.7);
  z-index: 999;
`;

document.body.appendChild(scrollTopBtn);

window.addEventListener("scroll", () => {
  scrollTopBtn.style.display = window.scrollY > 300 ? "block" : "none";
});

scrollTopBtn.addEventListener("click", () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});

/* ============================
   SIDEBAR TOGGLE - Admin Dashboard Template
============================ */
// Sidebar toggle
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');
const mainContent = document.getElementById('main-content');

if (sidebarToggle && sidebar && mainContent) {
  sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    mainContent.classList.toggle('shifted');
  });

  // Smooth scrolling for sidebar navigation (séparé du smooth scroll général)
  const sidebarLinks = sidebar.querySelectorAll('a[href^="#"]');
  sidebarLinks.forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth'
        });
      }
      // Close sidebar on mobile after clicking a link
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('show');
        mainContent.classList.remove('shifted');
      }
    });
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
      sidebar.classList.remove('show');
      mainContent.classList.remove('shifted');
    }
  });
}

/* ============================
   DASHBOARD INTERACTIONS - Admin Dashboard Template
============================ */
// Dashboard interactions (placeholder for future features)
const statCards = document.querySelectorAll('.stat-card');
statCards.forEach(card => {
  card.addEventListener('click', () => {
    // Placeholder for card click interaction
    console.log('Stat card clicked:', card.querySelector('h3').textContent);
  });
});