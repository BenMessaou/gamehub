
// backoffice/script.js  →  100% JavaScript validation (no HTML5, no required attributes)

function validateBackofficeForm() {
    // Find the error box (you will add it in the HTML – see step 2)
    const errorBox = document.getElementById("errorBox");
    if (errorBox) errorBox.innerHTML = "";
    if (errorBox) errorBox.style.color = "#ff4444";

    let errors = [];

    // === Get field values safely ===
    const name      = (document.querySelector("input[name='name']")?.value || "").trim();
    const lastname  = (document.querySelector("input[name='lastname']")?.value || "").trim();
    const email     = (document.querySelector("input[name='email']")?.value || "").trim();
    const cin       = (document.querySelector("input[name='cin']")?.value || "").trim();
    const tel       = (document.querySelector("input[name='tel']")?.value || "").trim();
    const gender    = document.querySelector("input[name='gender']:checked");
    const role      = document.querySelector("select[name='role']")?.value ||
                      document.querySelector("input[name='role']")?.value || "";
    const password  = (document.querySelector("input[name='password']")?.value || "");

    // === Validation rules ===
    if (name === "")      errors.push("First name is required.");
    else if (!/^[A-Z][a-zA-Z]*$/.test(name))
        errors.push("First name must start with a capital letter and contain only letters.");

    if (lastname === "")  errors.push("Last name is required.");
    else if (!/^[A-Z][a-zA-Z]*$/.test(lastname))
        errors.push("Last name must start with a capital letter and contain only letters.");

    if (email !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
        errors.push("Invalid email format.");

    if (cin !== "" && !/^\d{8}$/.test(cin))
        errors.push("CIN must contain exactly 8 digits.");

    if (tel !== "" && !/^\d{8}$/.test(tel))
        errors.push("Phone must contain exactly 8 digits.");

    // Gender (only if radios exist on the page)
    const genderRadios = document.querySelectorAll("input[name='gender']");
    if (genderRadios.length > 0 && !gender)
        errors.push("Please select a gender.");

    if (role === "")
        errors.push("Role is required.");

    // Password rules only when a password is entered (useful for add_user and optional in update_user)
    if (password !== "") {
        if (password.length < 8)
            errors.push("Password must be at least 8 characters.");
        if (!/^[A-Za-z0-9]+$/.test(password))
            errors.push("Password can only contain letters and numbers.");
    }

    // === Show errors or allow submit ===
    if (errors.length > 0) {
        if (errorBox) {
            errorBox.innerHTML = errors.join("<br>");
        } else {
            alert(errors.join("\n"));
        }
        return false; // stop form submission
    }

    return true; // everything is OK → submit

// backoffice/script.js  →  100% JavaScript validation (no HTML5, no required attributes)

function validateBackofficeForm() {
    // Find the error box (you will add it in the HTML – see step 2)
    const errorBox = document.getElementById("errorBox");
    if (errorBox) errorBox.innerHTML = "";
    if (errorBox) errorBox.style.color = "#ff4444";

    let errors = [];

    // === Get field values safely ===
    const name      = (document.querySelector("input[name='name']")?.value || "").trim();
    const lastname  = (document.querySelector("input[name='lastname']")?.value || "").trim();
    const email     = (document.querySelector("input[name='email']")?.value || "").trim();
    const cin       = (document.querySelector("input[name='cin']")?.value || "").trim();
    const tel       = (document.querySelector("input[name='tel']")?.value || "").trim();
    const gender    = document.querySelector("input[name='gender']:checked");
    const role      = document.querySelector("select[name='role']")?.value ||
                      document.querySelector("input[name='role']")?.value || "";
    const password  = (document.querySelector("input[name='password']")?.value || "");

    // === Validation rules ===
    if (name === "")      errors.push("First name is required.");
    else if (!/^[A-Z][a-zA-Z]*$/.test(name))
        errors.push("First name must start with a capital letter and contain only letters.");

    if (lastname === "")  errors.push("Last name is required.");
    else if (!/^[A-Z][a-zA-Z]*$/.test(lastname))
        errors.push("Last name must start with a capital letter and contain only letters.");

    if (email !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
        errors.push("Invalid email format.");

    if (cin !== "" && !/^\d{8}$/.test(cin))
        errors.push("CIN must contain exactly 8 digits.");

    if (tel !== "" && !/^\d{8}$/.test(tel))
        errors.push("Phone must contain exactly 8 digits.");

    // Gender (only if radios exist on the page)
    const genderRadios = document.querySelectorAll("input[name='gender']");
    if (genderRadios.length > 0 && !gender)
        errors.push("Please select a gender.");

    if (role === "")
        errors.push("Role is required.");

    // Password rules only when a password is entered (useful for add_user and optional in update_user)
    if (password !== "") {
        if (password.length < 8)
            errors.push("Password must be at least 8 characters.");
        if (!/^[A-Za-z0-9]+$/.test(password))
            errors.push("Password can only contain letters and numbers.");
    }

    // === Show errors or allow submit ===
    if (errors.length > 0) {
        if (errorBox) {
            errorBox.innerHTML = errors.join("<br>");
        } else {
            alert(errors.join("\n"));
        }
        return false; // stop form submission
    }

    return true; // everything is OK → submit
}
}