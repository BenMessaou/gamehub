/* =======================================================================
   VALIDATION FINALE DU FORMULAIRE
   ======================================================================= */

function validerFormulaire() {
    let nom = document.getElementById("nom").value.trim();
    let developpeur = document.getElementById("developpeur").value.trim();
    let description = document.getElementById("description").value.trim();
    let date_creation = document.getElementById("date_creation").value;
    let categorie = document.getElementById("categorie").value;

    // NOM
    if (nom.length < 3) {
        alert("❌ Le nom doit contenir au moins 3 caractères");
        return false;
    }

    // DEVELOPPEUR
    if (developpeur.length < 3) {
        alert("❌ Le développeur doit contenir au moins 3 caractères");
        return false;
    }

    // DESCRIPTION
    if (description.length < 10) {
        alert("❌ La description doit contenir au moins 10 caractères");
        return false;
    }

    // DATE
    if (!date_creation) {
        alert("❌ La date de création est obligatoire");
        return false;
    }

    // CATEGORIE
    if (categorie === "") {
        alert("❌ Veuillez sélectionner une catégorie");
        return false;
    }

    return true;
}

/* =======================================================================
   VALIDATION EN TEMPS RÉEL
   ======================================================================= */

document.getElementById("nom").addEventListener("keyup", function () {
    let msg = document.getElementById("nom_error");
    if (this.value.trim().length >= 3) {
        msg.style.color = "green";
        msg.innerText = "✔ Nom valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Minimum 3 caractères";
    }
});


document.getElementById("developpeur").addEventListener("keyup", function () {
    let msg = document.getElementById("developpeur_error");
    if (this.value.trim().length >= 3) {
        msg.style.color = "green";
        msg.innerText = "✔ Développeur valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Minimum 3 caractères";
    }
});


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


document.getElementById("date_creation").addEventListener("change", function () {
    let msg = document.getElementById("date_creation_error");
    if (this.value) {
        msg.style.color = "green";
        msg.innerText = "✔ Date valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Date obligatoire";
    }
});


document.getElementById("categorie").addEventListener("change", function () {
    let msg = document.getElementById("categorie_error");
    msg.style.color = "green";
    msg.innerText = "✔ Catégorie sélectionnée";
});


/* =======================================================================
   ENVOI DU FORMULAIRE
   ======================================================================= */

document.getElementById("addProjectForm").addEventListener("submit", function (event) {
    if (!validerFormulaire()) {
        event.preventDefault();
    }
});
