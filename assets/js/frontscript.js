// Countdown Timer
function updateCountdown() {
    const targetDate = new Date('2023-11-27T00:00:00'); // Cyber Monday date
    const now = new Date();
    const difference = targetDate - now;

    if (difference > 0) {
        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        document.getElementById('days').textContent = days.toString().padStart(2, '0');
        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    } else {
        document.querySelector('.countdown').innerHTML = '<p>The sale has ended!</p>';
    }
}

// Update countdown every second
setInterval(updateCountdown, 1000);
updateCountdown(); // Initial call

// Smooth scrolling for navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Mobile menu toggle (if needed)
const nav = document.querySelector('nav ul');
const toggle = document.createElement('button');
toggle.textContent = 'Menu';
toggle.style.display = 'none';
toggle.addEventListener('click', () => {
    nav.classList.toggle('show');
});
document.querySelector('header .container').appendChild(toggle);

// Show toggle button on mobile
if (window.innerWidth <= 768) {
    toggle.style.display = 'block';
}
