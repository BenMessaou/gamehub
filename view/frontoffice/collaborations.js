// Sidebar toggle (si nécessaire)
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');
const mainContent = document.getElementById('main-content');

if (sidebarToggle && sidebar && mainContent) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('shifted');
    });
}

// Smooth scrolling for navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
        // Close sidebar on mobile after clicking a link
        if (window.innerWidth <= 768 && sidebar) {
            sidebar.classList.remove('show');
            if (mainContent) {
                mainContent.classList.remove('shifted');
            }
        }
    });
});

// Close sidebar when clicking outside on mobile
if (sidebar && sidebarToggle) {
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && 
            sidebar && 
            !sidebar.contains(e.target) && 
            sidebarToggle && 
            !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('show');
            if (mainContent) {
                mainContent.classList.remove('shifted');
            }
        }
    });
}

// S'assurer que le bouton Add Collab est cliquable
document.addEventListener('DOMContentLoaded', function() {
    const addCollabBtn = document.querySelector('.super-button');
    if (addCollabBtn) {
        addCollabBtn.style.zIndex = '9999';
        addCollabBtn.style.position = 'relative';
        addCollabBtn.style.pointerEvents = 'auto';
        addCollabBtn.style.cursor = 'pointer';
        
        // Test de clic
        addCollabBtn.addEventListener('click', function(e) {
            console.log('Bouton Add Collab cliqué!');
        });
    }
});

// Header scroll effect
window.addEventListener("scroll", () => {
    const header = document.querySelector("header");
    if (header) {
        if (window.scrollY > 50) {
            header.style.boxShadow = "0 5px 20px rgba(0, 255, 136, 0.3)";
        } else {
            header.style.boxShadow = "none";
        }
    }
});

// Animation des cartes au survol
document.querySelectorAll('.collab-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

