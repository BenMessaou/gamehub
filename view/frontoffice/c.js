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
document.querySelectorAll(".form-group input, .form-group textarea").forEach(input => {
  input.addEventListener("focus", () => {
    input.classList.add("active");
  });

  input.addEventListener("blur", () => {
    if (input.value === "") {
      input.classList.remove("active");
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
