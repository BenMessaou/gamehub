// Sidebar toggle
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');
const mainContent = document.getElementById('main-content');

sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    mainContent.classList.toggle('shifted');
});

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

// Dashboard interactions (placeholder for future features)
const statCards = document.querySelectorAll('.stat-card');
statCards.forEach(card => {
    card.addEventListener('click', () => {
        // Placeholder for card click interaction
        console.log('Stat card clicked:', card.querySelector('h3').textContent);
    });
});