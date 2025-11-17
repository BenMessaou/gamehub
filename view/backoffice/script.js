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
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');

    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('shifted');
    });

    // Modal functionality
    const userModal = document.getElementById('userModal');
    const addUserBtn = document.getElementById('addUserBtn');
    const closeButton = document.querySelector('.close-button');
    const userForm = document.getElementById('userForm');
    const modalTitle = document.getElementById('modalTitle');
    const userIdField = document.getElementById('userId');
    const nameField = document.getElementById('name');
    const lastnameField = document.getElementById('lastname');
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    const cinField = document.getElementById('cin');
    const telField = document.getElementById('tel');
    const genderField = document.getElementById('gender');
    const roleField = document.getElementById('role');

    // Open Add User Modal
    addUserBtn.addEventListener('click', function() {
        modalTitle.textContent = 'Add User';
        userForm.action = '../controller/add.php'; // Set action for add
        userIdField.value = ''; // Clear ID for add
        userForm.reset(); // Clear form fields
        userModal.style.display = 'block';
    });

    // Open Update User Modal
    document.getElementById('userTableBody').addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-button')) {
            modalTitle.textContent = 'Update User';
            userForm.action = '../controller/update.php'; // Set action for update
            userModal.style.display = 'block';

            const userId = e.target.dataset.id;
            userIdField.value = userId; // Set hidden ID field

            // Fetch user data via AJAX
            fetch(`../controller/update.php?id=${userId}`)
                .then(response => response.json())
                .then(user => {
                    nameField.value = user.name;
                    lastnameField.value = user.lastname;
                    emailField.value = user.email;
                    // passwordField.value = user.password; // Be careful with pre-filling passwords
                    cinField.value = user.cin;
                    telField.value = user.tel;
                    genderField.value = user.gender;
                    roleField.value = user.role;
                })
                .catch(error => console.error('Error fetching user data:', error));
        }
    });

    // Close Modal
    closeButton.addEventListener('click', function() {
        userModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == userModal) {
            userModal.style.display = 'none';
        }
    });

    // Handle Delete User
    document.getElementById('userTableBody').addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-button')) {
            const userId = e.target.dataset.id;
            if (confirm(`Are you sure you want to delete user with ID ${userId}?`)) {
                window.location.href = `../controller/delete.php?id=${userId}`;
            }
        }
    });

    // Handle form submission (Add/Update)
    userForm.addEventListener('submit', function(e) {
    });
});
