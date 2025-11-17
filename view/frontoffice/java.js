function updateCountdown() {
const targetDate = new Date('2023-11-27T00:00:00');
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

setInterval(updateCountdown, 1000);
updateCountdown(); // Initial call

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

const nav = document.querySelector('nav ul');
const toggle = document.createElement('button');
toggle.textContent = 'Menu';
toggle.style.display = 'none';
toggle.addEventListener('click', () => {
    nav.classList.toggle('show');
});
document.querySelector('header .container').appendChild(toggle);

if (window.innerWidth <= 768) {
    toggle.style.display = 'block';
}
//bio
document.addEventListener("DOMContentLoaded", () => {
    const bioInput = document.getElementById("bio-input");
    const savedBio = localStorage.getItem("userBio");

    if (savedBio) {
        bioInput.value = savedBio;
    }

    document.getElementById("save-bio").addEventListener("click", () => {
        const newBio = bioInput.value.trim();
        localStorage.setItem("userBio", newBio);

        alert(" Your bio has been saved!");
    });
});


function saisie() {
    
    let name = document.getElementById("name").value;
    let lastname = document.getElementById("lastname").value;
    let email = document.getElementById("email").value;
    let pass1 = document.getElementById("password").value; // Use the correct ID "password"
    let pass2 = document.getElementById("password2").value; // And the new ID "password2"
    let cin = document.getElementById("cin").value;
    let tel = document.getElementById("tel").value;

    let errorBox = document.getElementById("errorBox");
    errorBox.innerHTML = ""; // Clear previous errors
    errorBox.style.color = "red"; // Make errors red and visible

    
    if (name.length > 0 && name[0] !== name[0].toUpperCase()) {
        errorBox.innerHTML = "Name must start with a capital letter.";
        return false; 
    }

    if (lastname.length > 0 && lastname[0] !== lastname[0].toUpperCase()) {
        errorBox.innerHTML = "Lastname must start with a capital letter.";
        return false; 
    }

    let emailFormat = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailFormat.test(email)) {
        errorBox.innerHTML = "Email format is invalid.";
        return false; 
    }
    
    if (pass1.length < 8) {
        errorBox.innerHTML = "Password must be at least 8 characters long.";
        return false; 
    }

    if (pass1 !== pass2) {
        errorBox.innerHTML = "Passwords do not match.";
        return false; 
    }

    if (cin.length !== 8 || isNaN(cin)) {
        errorBox.innerHTML = "CIN must contain exactly 8 numbers.";
        return false; 
    }

    if (tel.length !== 8 || isNaN(tel)) {
        errorBox.innerHTML = "Telephone must contain exactly 8 numbers.";
        return false; 
    }

    // If all checks pass, allow the form to submit
    return true; 
}
function submit() {
    let role = document.querySelector("input[name='role']:checked");
    let errorBox = document.getElementById("errorBox");

    if (!role) {
        errorBox.innerHTML = "Please select a role.";
        return;
    }

    errorBox.innerHTML = "";

    if (role.value === "admin") {
        window.location.href = "admin.html";
    } else {
        window.location.href = "form.html";
    }
}

