// =========================
//  KEY & PROMO
// =========================

const promoCodes = {
    'GAME10': { type: 'percent', value: 10 },
    'PROMO5': { type: 'fixed', value: 5 },
    'WELCOME': { type: 'percent', value: 15 }
};

// =========================
//  ⭐ SYSTEME D'ÉTOILES
// =========================

function renderStars(rating, starsElementId) {
    const starsContainer = document.getElementById(starsElementId);
    let starsHTML = '';

    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 > 0;
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);

    for (let i = 0; i < fullStars; i++) starsHTML += '⭐️';
    if (halfStar) starsHTML += '🌟';
    for (let i = 0; i < emptyStars; i++) starsHTML += '☆';

    starsHTML += ` <span style="color:#ffd700;font-size:0.9rem;">(${rating})</span>`;
    starsContainer.innerHTML = starsHTML;
}

// =========================
//  ⭐ PANIER OBJET
// =========================

const cart = {
    items: [],
    promoCode: null,
    discount: 0,

    addItem(gameName, price, imageSrc, gameId) {
        const item = this.items.find(i => i.id === gameId);
        if (item) item.quantity++;
        else this.items.push({
            id: gameId,
            name: gameName,
            price: parseFloat(price),
            quantity: 1,
            image: imageSrc
        });

        this.save();
        this.updateUI();
    },

    removeItem(i) {
        this.items.splice(i, 1);
        this.save();
        this.updateUI();
    },

    updateQuantity(i, q) {
        if (q > 0) this.items[i].quantity = q;
        else this.removeItem(i);

        this.save();
        this.updateUI();
    },

    getTotal() {
        return this.items.reduce((t, it) => t + it.price * it.quantity, 0);
    },

    applyPromo(code) {
        const promo = promoCodes[code];
        if (!promo) return false;

        this.promoCode = code;
        const total = this.getTotal();

        if (promo.type === 'percent') this.discount = total * (promo.value / 100);
        else this.discount = Math.min(total, promo.value);

        this.save();
        this.updateUI();
        return true;
    },

    removePromo() {
        this.promoCode = null;
        this.discount = 0;
        this.save();
        this.updateUI();
    },

    save() {
        localStorage.setItem("cart", JSON.stringify(this.items));
        localStorage.setItem("discount", this.discount);
    },

    load() {
        this.items = JSON.parse(localStorage.getItem("cart")) || [];
        this.discount = parseFloat(localStorage.getItem("discount")) || 0;
    },

    updateUI() {
        const list = document.getElementById("cartItems");
        const badge = document.getElementById("cartBadge");
        const totalElem = document.getElementById("cartTotal");
        const finalElem = document.getElementById("finalTotal");

        badge.textContent = this.items.length;
        list.innerHTML = "";

        let total = this.getTotal();
        totalElem.textContent = total.toFixed(2) + " €";

        finalElem.textContent = (total - this.discount).toFixed(2) + " €";

        this.items.forEach((item, i) => {
            list.innerHTML += `
                <div class="cart-item">
                    <img src="${item.image}">
                    <div class="item-details">
                        <h5>${item.name}</h5>
                        <p>${item.price.toFixed(2)} €</p>
                    </div>

                    <input type="number" min="1" value="${item.quantity}"
                           class="quantity-input"
                           onchange="cart.updateQuantity(${i}, this.value)">
                           
                    <button class="remove-btn" onclick="cart.removeItem(${i})">×</button>
                </div>
            `;
        });
    }
};

cart.load();
cart.updateUI();


// =========================
//  ⭐ BOUTONS AJOUT AU PANIER
// =========================

document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', () => {
        const card = btn.closest(".game-card");

        cart.addItem(
            card.dataset.name,
            card.dataset.price,
            card.querySelector("img").src,
            card.dataset.id   // IMPORTANT
        );
    });
});

// =========================
//  ⭐ OUVERTURE PANIER
// =========================

const cartIcon = document.getElementById('cartIcon');
const cartDropdown = document.getElementById('cartDropdown');
const closeCart = document.getElementById('closeCart');

cartIcon.onclick = () => cartDropdown.style.display =
    cartDropdown.style.display === "block" ? "none" : "block";

closeCart.onclick = () => cartDropdown.style.display = "none";

window.addEventListener("click", (e) => {
    if (!e.target.closest(".cart-dropdown") && !e.target.closest(".cart-icon")) {
        cartDropdown.style.display = "none";
    }
});

// =========================
//  ⭐ CLEAR CART
// =========================

document.getElementById("clearCartBtn").onclick = () => {
    if (cart.items.length === 0) return alert("Panier déjà vide !");
    if (!confirm("Vider le panier ?")) return;

    cart.items = [];
    cart.discount = 0;
    cart.save();
    cart.updateUI();
};

// =========================
//  ⭐ FILTRAGE CATÉGORIE + SEARCH
// =========================

document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        const cat = btn.dataset.category;
        const search = document.getElementById("searchInput").value.toLowerCase();

        filterGames(cat, search);
    });
});

document.getElementById("searchInput").addEventListener("input", () => {
    const cat = document.querySelector(".filter-btn.active").dataset.category;
    const search = document.getElementById("searchInput").value.toLowerCase();

    filterGames(cat, search);
});

function filterGames(cat, search) {
    document.querySelectorAll(".game-card").forEach(card => {
        const matchCat = (cat === "all" || card.dataset.category === cat);
        const matchSearch =
            card.dataset.name.toLowerCase().includes(search) ||
            card.querySelector(".description").textContent.toLowerCase().includes(search);

        card.style.display = matchCat && matchSearch ? "block" : "none";
    });
}

// =========================
//  ⭐ ENREGISTRER COMMANDE (NOUVELLE ENTITÉ)
// =========================

const checkoutBtn = document.getElementById("checkoutBtn");

checkoutBtn.addEventListener("click", () => {

    if (cart.items.length === 0) {
        alert("Votre panier est vide.");
        return;
    }

    // Redirection vers la page de paiement
    window.location.href = "payment.php";
});
