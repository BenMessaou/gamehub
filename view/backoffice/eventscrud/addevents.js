/* =======================================================================
   VALIDATION FINALE DU FORMULAIRE
   ======================================================================= */

function validerFormulaire() {
    let title = document.getElementById("title").value.trim();
    let description = document.getElementById("description").value.trim();
    let startDate = document.getElementById("start_date").value;
    let endDate = document.getElementById("end_date").value;
    let locationField = document.getElementById("location").value.trim();
    let capacity = document.getElementById("capacity").value;

    // TITLE
    if (title.length < 3) {
        alert("❌ Le titre doit contenir au moins 3 caractères");
        return false;
    }

    // DESCRIPTION
    if (description.length < 10) {
        alert("❌ La description doit contenir au moins 10 caractères");
        return false;
    }

    // DATE DE DÉBUT
    if (!startDate) {
        alert("❌ La date de début est obligatoire");
        return false;
    }

    // DATE DE FIN
    if (!endDate) {
        alert("❌ La date de fin est obligatoire");
        return false;
    }

    if (startDate >= endDate) {
        alert("❌ La date de fin doit être AFTER la date de début !");
        return false;
    }

    // LOCATION
    if (locationField.length < 3) {
        alert("❌ Le lieu / lien doit contenir au moins 3 caractères");
        return false;
    }

    // CAPACITE
    if (capacity <= 0) {
        alert("❌ La capacité doit être un nombre supérieur à 0");
        return false;
    }

    return true;
}

/* =======================================================================
   VALIDATION EN TEMPS RÉEL
   ======================================================================= */

// TITLE
document.getElementById("title").addEventListener("keyup", function () {
    let msg = document.getElementById("title_error");
    if (this.value.trim().length >= 3) {
        msg.style.color = "green";
        msg.innerText = "✔ Titre valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Minimum 3 caractères";
    }
});

// DESCRIPTION
document.getElementById("description").addEventListener("keyup", function () {
    let msg = document.getElementById("description_error");
    if (this.value.trim().length >= 10) {
        msg.style.color = "green";
        msg.innerText = "✔ Description valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Minimum 10 caractères";
    }
});

// START DATE
document.getElementById("start_date").addEventListener("change", function () {
    let msg = document.getElementById("start_date_error");
    if (this.value) {
        msg.style.color = "green";
        msg.innerText = "✔ Date de début valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Date obligatoire";
    }
});

// END DATE
document.getElementById("end_date").addEventListener("change", function () {
    let msg = document.getElementById("end_date_error");
    if (this.value && this.value > document.getElementById("start_date").value) {
        msg.style.color = "green";
        msg.innerText = "✔ Date de fin valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Date invalide ou antérieure";
    }
});

// LOCATION
document.getElementById("location").addEventListener("keyup", function () {
    let msg = document.getElementById("location_error");
    if (this.value.trim().length >= 3) {
        msg.style.color = "green";
        msg.innerText = "✔ Lieu valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Minimum 3 caractères";
    }
});

// CAPACITY
document.getElementById("capacity").addEventListener("keyup", function () {
    let msg = document.getElementById("capacity_error");
    if (this.value > 0) {
        msg.style.color = "green";
        msg.innerText = "✔ Capacité valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Capacité > 0 obligatoire";
    }
});

/* =======================================================================
   ENVOI DU FORMULAIRE
   ======================================================================= */

document.getElementById("addEventForm").addEventListener("submit", function (event) {
    if (!validerFormulaire()) {
        event.preventDefault();
    }
});
