/* =====================================================
   GAMEHUB PRO – JAVASCRIPT GLOBAL
   Compatible avec header, navbar, addgame, admin panel
===================================================== */

/* ----------------------------
   1. HEADER SCROLL EFFECT
----------------------------- */
const header = document.querySelector(".admin-header") || document.querySelector("header");

window.addEventListener("scroll", () => {
    if (window.scrollY > 80) {
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }
});

/* ----------------------------
   2. BURGER MENU (Mobile)
----------------------------- */
const burger = document.querySelector(".burger-container");
const nav = document.querySelector("nav");

if (burger && nav) {
    burger.addEventListener("click", () => {
        burger.classList.toggle("active");
        nav.classList.toggle("active");
    });
}

/* ---------------------------------------------
   3. ANIMATION NEON CLICK SUR BOUTONS
---------------------------------------------- */
document.querySelectorAll("button, .btn-upload, .submit-btn").forEach(btn => {
    btn.addEventListener("mousedown", () => {
        btn.style.transform = "scale(0.96)";
    });
    btn.addEventListener("mouseup", () => {
        btn.style.transform = "scale(1)";
    });
});

/* ---------------------------------------------
   4. FORM CHECK – (Add Game)
---------------------------------------------- */
const addGameForm = document.querySelector(".game-form");

if (addGameForm) {
    addGameForm.addEventListener("submit", (e) => {

        const desc = document.querySelector("#description");
        if (desc.value.trim().length < 150) {
            e.preventDefault();
            alert("❗ La description doit contenir au moins 150 caractères.");
            desc.focus();
            return;
        }

        const title = document.querySelector("#nom");
        if (title.value.trim().length < 3) {
            e.preventDefault();
            alert("❗ Le nom du jeu doit contenir au moins 3 caractères.");
            title.focus();
            return;
        }
    });
}

/* --------------------------------------------------------
   5. FILE INPUT DISPLAY (Nom du fichier sélectionné)
--------------------------------------------------------- */
document.querySelectorAll("input[type='file']").forEach(input => {
    input.addEventListener("change", () => {
        if (input.files.length > 0) {
            input.classList.add("file-selected");
        }
    });
});

/* --------------------------------------------------------
   6. SMOOTH SCROLL (internal links)
--------------------------------------------------------- */
document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener("click", function(e) {
        const target = document.querySelector(this.getAttribute("href"));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    });
});

/* --------------------------------------------------------
   7. DYNAMIC NEON GLOW FOLLOW MOUSE (admin panel)
--------------------------------------------------------- */
const glowElements = document.querySelectorAll(".admin-section, .stat-card");

glowElements.forEach(el => {
    el.addEventListener("mousemove", (e) => {
        const rect = el.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        el.style.setProperty("--x", `${x}px`);
        el.style.setProperty("--y", `${y}px`);
        el.style.background = `
            radial-gradient(circle at var(--x) var(--y), 
                rgba(255, 0, 199, 0.22), 
                rgba(0, 0, 0, 0.15))
        `;
    });

    el.addEventListener("mouseleave", () => {
        el.style.background = "";
    });
});

/* --------------------------------------------------------
   8. CONFIRM DELETE (Admin)
--------------------------------------------------------- */
document.querySelectorAll(".btn-delete").forEach(btn => {
    btn.addEventListener("click", e => {
        if (!confirm("⚠️ Êtes-vous sûr de vouloir supprimer cet élément ?")) {
            e.preventDefault();
        }
    });
});

/* --------------------------------------------------------
   9. AUTO-HIGHLIGHT ACTIVE NAV LINK
--------------------------------------------------------- */
const currentPage = window.location.pathname.split("/").pop();

document.querySelectorAll(".nav-link").forEach(link => {
    if (link.getAttribute("href") === currentPage) {
        link.classList.add("active");
    }
});

/* --------------------------------------------------------
   10. ADMIN TABLE ROW HIGHLIGHT
--------------------------------------------------------- */
document.querySelectorAll("table tr").forEach(row => {
    row.addEventListener("mouseenter", () => {
        row.style.boxShadow = "0 0 18px rgba(255,0,199,0.35)";
    });
    row.addEventListener("mouseleave", () => {
        row.style.boxShadow = "none";
    });
});
