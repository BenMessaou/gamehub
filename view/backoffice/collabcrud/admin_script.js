// Sidebar toggle from admin template
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');
const mainContent = document.getElementById('main-content');

if (sidebar && sidebarToggle && mainContent) {
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('shifted');
    });
}


