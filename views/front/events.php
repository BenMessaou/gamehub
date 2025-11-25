<?php
// views/front/events.php

require_once __DIR__ . '/../../controllers/EventController.php';

$eventC = new EventC();
$events = $eventC->afficherEventsParStatut("accepted");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - CyberDeals</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
    .badge {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
        text-transform: uppercase;
    }

    .badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge.accepted {
        background: #d4edda;
        color: #155724;
    }

    .badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .event-card {
        background: #111827;
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 14px;
        color: white;
        display: flex;
        gap: 16px;
        align-items: center;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .35);
    }

    .event-card img {
        width: 180px;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
        flex-shrink: 0;
    }

    .event-title {
        font-size: 18px;
        margin: 0;
    }

    /* ===== MODAL ===== */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 16px;
    }

    .modal {
        background: #0b1220;
        color: white;
        width: 100%;
        max-width: 650px;
        border-radius: 14px;
        padding: 18px;
        position: relative;
        animation: pop .12s ease;
    }

    @keyframes pop {
        from {
            transform: scale(.96);
            opacity: .4;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-close {
        position: absolute;
        top: 10px;
        right: 12px;
        font-size: 22px;
        cursor: pointer;
        opacity: .8;
    }

    .modal-close:hover {
        opacity: 1;
    }

    .modal-img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 12px;
    }

    .modal h4 {
        margin: 0 0 6px;
        font-size: 22px;
    }

    .modal p {
        margin: 6px 0;
        opacity: .95;
    }

    .modal .meta {
        font-size: 14px;
        opacity: .85;
    }

    .modal-actions {
        margin-top: 14px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 9px 14px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        border: none;
        cursor: pointer;
    }

    .btn-register {
        background: #16a34a;
        color: white;
    }

    .btn-edit {
        background: #2563eb;
        color: white;
    }

    .btn-delete {
        background: #dc2626;
        color: white;
    }

    .availability-ok {
        color: #a7f3d0;
        font-weight: 600;
    }

    .availability-no {
        color: #fca5a5;
        font-weight: 600;
    }
    </style>
</head>

<body>

    <header>
        <div class="container">
            <img src="logo.png" alt="CyberDeals" class="logo">
            <nav>
                <ul>
                    <li><a href="index.html" class="super-button">Home</a></li>
                    <li><a href="#events" class="super-button">Events</a></li>
                    <li><a href="#deals" class="super-button">Deals</a></li>
                    <li><a href="#contact" class="super-button">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="events" class="events">
        <div class="container">
            <h3>Upcoming Events</h3>

            <div class="event-controls">
                <input type="text" id="search-bar" placeholder="Search events..." class="search-input">
                <select id="sort-bar" class="sort-select">
                    <option value="date">Sort by Date Released</option>
                    <option value="popular">Sort by Popularity</option>
                    <option value="name">Sort by Name</option>
                </select>
                <a href="addevent.php" class="create-event-btn">Create Event</a>
            </div>

            <div id="event-list" class="event-list">

                <?php if (!empty($events)) { ?>
                <?php foreach ($events as $event) { ?>

                <!-- CARD shows only image + title -->
                <div class="event-card" data-event='<?= htmlspecialchars(json_encode($event), ENT_QUOTES, "UTF-8") ?>'>

                    <img src="<?= !empty($event['imageURL']) ? $event['imageURL'] : 'https://via.placeholder.com/180x120' ?>"
                        alt="event image">

                    <h4 class="event-title"><?= htmlspecialchars($event["title"]) ?></h4>
                </div>

                <?php } ?>
                <?php } else { ?>
                <p style="color:white;">No events found.</p>
                <?php } ?>

            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <p>&copy; 2025 CyberDeals. All rights reserved.</p>
            <p>Contact: info@cyberdeals.com</p>
        </div>
    </footer>

    <!-- ===== MODAL HTML (hidden until click) ===== -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal" id="modal">
            <div class="modal-close" id="modalClose">✖</div>

            <img id="modalImg" class="modal-img" src="" alt="event image">

            <h4 id="modalTitle"></h4>

            <div id="modalStatus"></div>

            <p id="modalDescription"></p>

            <p class="meta" id="modalMeta1"></p>
            <p class="meta" id="modalMeta2"></p>
            <p class="meta" id="modalMeta3"></p>

            <p class="meta" id="modalAvailability"></p>

            <div class="modal-actions" id="modalActions">
                <!-- Buttons inserted by JS -->
            </div>
        </div>
    </div>

    <script>
    const overlay = document.getElementById("modalOverlay");
    const closeBtn = document.getElementById("modalClose");

    const modalImg = document.getElementById("modalImg");
    const modalTitle = document.getElementById("modalTitle");
    const modalStatus = document.getElementById("modalStatus");
    const modalDescription = document.getElementById("modalDescription");
    const modalMeta1 = document.getElementById("modalMeta1");
    const modalMeta2 = document.getElementById("modalMeta2");
    const modalMeta3 = document.getElementById("modalMeta3");
    const modalAvailability = document.getElementById("modalAvailability");
    const modalActions = document.getElementById("modalActions");

    function openModal(eventData) {
        modalImg.src = eventData.imageURL || "https://via.placeholder.com/650x240";
        modalTitle.textContent = eventData.title;

        modalStatus.innerHTML = `
            <span class="badge ${eventData.status}">
                ${eventData.status}
            </span>
        `;

        modalDescription.textContent = eventData.description || "";

        modalMeta1.innerHTML = `<strong>Type:</strong> ${eventData.eventType} | 
                                <strong>Platform:</strong> ${eventData.platform || '-' } | 
                                <strong>Location:</strong> ${eventData.location || '-'}`;

        modalMeta2.innerHTML = `<strong>Start:</strong> ${eventData.startDate} | 
                                <strong>End:</strong> ${eventData.endDate}`;

        modalMeta3.innerHTML = `<strong>Ticket:</strong> ${eventData.ticketPrice} DT | 
                                <strong>Prize Pool:</strong> ${eventData.prizePool} DT`;

        // Availability
        if (eventData.availability === null || eventData.availability === "") {
            modalAvailability.innerHTML = `<span class="availability-ok">Unlimited places</span>`;
        } else if (parseInt(eventData.availability) > 0) {
            modalAvailability.innerHTML = `<span class="availability-ok">${eventData.availability} places left</span>`;
        } else {
            modalAvailability.innerHTML = `<span class="availability-no">No places left</span>`;
        }

        // Buttons
        modalActions.innerHTML = "";

        // Register button (Inscrire)
        const registerBtn = document.createElement("a");
        registerBtn.href = `inscrire_event.php?id=${eventData.id}`;
        registerBtn.className = "btn btn-register";
        registerBtn.textContent = "📝 Inscrire";
        modalActions.appendChild(registerBtn);

        // Optional edit/delete for admins or owners (kept here)
        const editBtn = document.createElement("a");
        editBtn.href = `edit_event.php?id=${eventData.id}`;
        editBtn.className = "btn btn-edit";
        editBtn.textContent = "✏ Edit";
        modalActions.appendChild(editBtn);

        const deleteBtn = document.createElement("a");
        deleteBtn.href = `delete_event.php?id=${eventData.id}`;
        deleteBtn.className = "btn btn-delete";
        deleteBtn.textContent = "🗑 Delete";
        deleteBtn.onclick = () => confirm("Are you sure you want to delete this event?");
        modalActions.appendChild(deleteBtn);

        overlay.style.display = "flex";
    }

    document.querySelectorAll(".event-card").forEach(card => {
        card.addEventListener("click", () => {
            const data = JSON.parse(card.dataset.event);
            openModal(data);
        });
    });

    closeBtn.addEventListener("click", () => overlay.style.display = "none");
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) overlay.style.display = "none";
    });
    </script>

    <script src="js/script.js"></script>
</body>

</html>